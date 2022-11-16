<?

namespace Phalcon\Websocket;
use Phalcon\Tools\Cli;

/**
 * Websocket server supporting https
 */
abstract class Server {

    /**
     * User class name
     * @var string
     */
    protected $userClass = 'User'; // redefine this if you want a custom user class.  The custom user class should inherit from WebSocketUser.
    /**
     * Maximum buffer size
     * @var integer
     */
    protected $maxBufferSize;  
    /**
     * Server socket
     * @var \socket_ressource
     */
    protected $master;
    /**
     * Sockets list indexed by id
     * @var array
     */
    protected $sockets                              = [];
    /**
     * Users list indexed by id
     * @var array
     */
    protected $users                                = [];
    /**
     * Message waiting gor their user handshake
     * @var array
     */
    protected $heldMessages                         = [];
    /**
     * Rooms list indexed by name
     * @var array
     */
    protected $rooms                                = [];
    /**
     * Determine if the server show log message
     * @var boolean
     */
    protected $interactive                          = true;
    /**
     * Determine if the origin must be check
     * @var boolean
     */
    protected $headerOriginRequired                 = false;
    /**
     * Determine if the sec-websocket-protocol must be check
     * @var boolean
     */
    protected $headerSecWebSocketProtocolRequired   = false;

    /**
     * Determine if the sec-websocket-extensions must be check
     * @var boolean
     */
    protected $headerSecWebSocketExtensionsRequired = false;

    /**
     * Initialize master socket
     * @param string  $addr         Ip address to listen on
     * @param string  $port         listen port
     * @param array   $ssl          SSL certificate in PEM format 
     * ```
     * [
     *     'pem'=>'',
     *     'crt'=>''
     * ];
     * ```
     * @param integer $bufferLength Length of the buffer
     */
    function __construct($addr, $port, $ssl=[], $bufferLength = 2048) {
        $this->maxBufferSize = $bufferLength;     
        $this->protocol = 'HTTP';
        if(count($ssl) === 0){            
            $this->isSsl = false;
            $this->master = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)  or die('Failed: socket_create()');
            socket_set_option($this->master, SOL_SOCKET, SO_REUSEADDR, 1) or die('Failed: socket_option()');
            socket_bind($this->master, $addr, $port)                      or die('Failed: socket_bind()');
            socket_listen($this->master,20)                               or die('Failed: socket_listen()');
        } else {
            $this->isSsl = true;
            $context = stream_context_create();

            // local_cert must be in PEM format
            stream_context_set_option($context, 'ssl', 'local_cert', $ssl['pem']);
            stream_context_set_option($context, 'ssl', 'passphrase', $ssl['crt']);
            stream_context_set_option($context, 'ssl', 'allow_self_signed', true);
            stream_context_set_option($context, 'ssl', 'verify_peer', false);
            $this->master = stream_socket_server('ssl://'.$addr.':'.$port, $errno, $errstr, STREAM_SERVER_BIND|STREAM_SERVER_LISTEN, $context);
        }
        $this->sockets['m'] = $this->master;
        $this->stdout("Server started\nListening on: $addr:$port\nMaster socket: ".spl_object_id($this->master));
    }

    /**
     * Called immediately when the data is recieved. 
     * @param  \Phalcon\Websocket\User &$user  User instance
     * @param  array &$message Data of the event 
     * ```
     * [
     *     'type'=>'' // name of the event, 
     *     'data'=>''// data of the event
     * ];
     * ```
     */
    abstract protected function process(&$user,&$message);

    /**
     * Called after the handshake response is sent to the client.
     * @param  \Phalcon\Websocket\User &$user User instance
     */
    abstract protected function connected(&$user);

    /**
     * Called after the connection is closed.
     * @param  \Phalcon\Websocket\User &$user User instance
     */
    abstract protected function closed(&$user); 

    /**
     * Handle a connecting user, after the instance of the User is created, but before the handshake has completed.
     * @param  \Phalcon\Websocket\User &$user User instance
     */
    protected function connecting(&$user) {
    }

    /**
     * Called on room open
     * @param  string $name Name of the opened room
     */
    protected function openRoom($name){
        Cli::warning("Room $name opened", true);
    }

    /**
     * Called on room close
     * @param  string $name Name of the opened room
     */
    protected function closeRoom($name){
        Cli::warning("Room $name closed", true);
    }

    /**
     * Send event to an user
     * @param  \Phalcon\Websocket\User &$user  User instance
     * @param  array &$message Data of the event 
     * ```
     * [
     *     'type'=>'' // name of the event, 
     *     'data'=>''// data of the event
     * ];
     * ```
     */
    protected function send(&$user, $message) {
        if ($user->handshake) {
            $message = $this->frame($message,$user);
            if($this->isSsl){
                $result = @fwrite($user->socket, $message, strlen($message));
            } else {
                $result = @socket_write($user->socket, $message, strlen($message));
            }
        } else {
            // User has not yet performed their handshake.  Store for sending later.
            $holdingMessage = array('user' => $user, 'message' => $message);
            $this->heldMessages[] = $holdingMessage;
        }
    }

    /**
     * Send event to multiple user
     * @param  array $message  Data of the event 
     * ```
     * [
     *     'type'=>'' // name of the event, 
     *     'data'=>''// data of the event
     * ];
     * ```
     * @param  string $room     Name of the room, if null send to all user's room
     * @param  string $userFrom Id of an user to exclude to send
     */
    protected function broadcast($message, $room=null, $userFrom=null){
        if(!isset($room)){
            foreach($this->sockets as $id => &$socket){
                if(isset($userFrom) && $userFrom === $id){
                    continue;
                }
                $this->send($this->users[$id], $message);
            }
        } else {
            foreach($this->rooms[$room] as $userId => $id){
                if(isset($userFrom) && $userFrom === $id){
                    continue;
                }
                $this->send($this->users[$id], $message);
            }
        }
    }

    /**
     * Happen periodically. Will happen at least once per second, but possibly more often.
     */
    protected function tick() {
    }

    /**
     * Core maintenance processes, such as retrying failed messages.
     */
    protected function _tick() {
        foreach ($this->heldMessages as $key => $hm) {
            $found = false;
            foreach ($this->users as $currentUser) {
                if($currentUser === null){
                    continue;
                }
                if ($hm['user']->socket == $currentUser->socket) {
                    $found = true;
                    if ($currentUser->handshake) {
                        unset($this->heldMessages[$key]);
                        $this->send($currentUser, $hm['message']);
                    }
                }
            }
            if (!$found) {
                // If they're no longer in the list of connected users, drop the message.
                unset($this->heldMessages[$key]);
            }
        }
    }

    /**
    * Main processing loop
    */
    public function run() {
        while(true) {
            if (empty($this->sockets)) {
                $this->sockets['m'] = $this->master;
            }
            $read = $this->sockets;
            $write = $except = null;
            $this->_tick();
            $this->tick();
            if($this->isSsl){
                @stream_select($read,$write,$except,1);
            } else {
                @socket_select($read,$write,$except,1);
            }
            foreach ($read as $socket) {
                if ($socket == $this->master) {
                    if($this->isSsl){
                        $client = stream_socket_accept($socket);
                    } else{
                        $client = socket_accept($socket);
                    }
                    if ($client < 0) {
                        $this->stderr('Failed: socket_accept()');
                        continue;
                    } else {
                        $this->connect($client);                        
                    }
                } else {
                    if($this->isSsl){
                        $buffer = fread($socket, $this->maxBufferSize);
                        if(!$buffer){
                            $numBytes = false;
                        } else {
                            $numBytes = strlen($buffer);
                        }
                    } else {
                        $numBytes = @socket_recv($socket, $buffer, $this->maxBufferSize, 
                            0); 
                    }
                    if ($numBytes === false && !$this->isSsl) {
                        $sockErrNo = socket_last_error($socket);
                        switch ($sockErrNo){
                            case 102: // ENETRESET    -- Network dropped connection because of reset
                            case 103: // ECONNABORTED -- Software caused connection abort
                            case 104: // ECONNRESET   -- Connection reset by peer
                            case 108: // ESHUTDOWN    -- Cannot send after transport endpoint shutdown -- probably more of an error on our part, if we're trying to write after the socket is closed.  Probably not a critical error, though.
                            case 110: // ETIMEDOUT    -- Connection timed out
                            case 111: // ECONNREFUSED -- Connection refused -- We shouldn't see this one, since we're listening... Still not a critical error.
                            case 112: // EHOSTDOWN    -- Host is down -- Again, we shouldn't see this, and again, not critical because it's just one connection and we still want to listen to/for others.
                            case 113: // EHOSTUNREACH -- No route to host
                            case 121: // EREMOTEIO    -- Rempte I/O error -- Their hard drive just blew up.
                            case 125: // ECANCELED    -- Operation canceled
                                $this->stderr('Unusual disconnect on socket ' . spl_object_id($socket));
                                $this->disconnect($socket, true, $sockErrNo); // disconnect before clearing error, in case someone with their own implementation wants to check for error conditions on the socket.
                                break;
                            default:
                                $this->stderr('Socket error: ' . socket_strerror($sockErrNo));
                        }

                    } elseif ($numBytes == 0) {
                        $this->disconnect($socket);
                        $this->stderr('Client disconnected. TCP connection lost: ' . spl_object_id($socket));
                    } else {
                        $user = $this->users[md5((string)spl_object_id($socket))];
                        if (!$user->handshake) {
                            $tmp = str_replace("\r", '', $buffer);
                            if (strpos($tmp, "\n\n") === false ) {
                                continue; // If the client has not finished sending the header, then wait before sending our upgrade response.
                            }
                            $this->doHandshake($user,$buffer);
                        } else {
                            //split packet into frame and send it to deframe
                            $this->split_packet($numBytes,$buffer, $user);
                        }
                    }
                }
            }
        }
    }

    /**
     * Connect an user.
     * @param  \socket_ressource &$socket User socket
     */
    protected function connect(&$socket) {
        $id = md5((string)spl_object_id($socket));
        $user = new User($id, $socket);
        $this->users[$id] = $user;
        $this->sockets[$user->id] = $socket;
        $this->connecting($user);
    }

    /**
     * Disconnect an User.
     * @param  \socket_ressource  &$socket  Socket to disconnect
     * @param  boolean $triggerClosed Set to true if socket need to be closed
     * @param  integer  $sockErrNo  Error code, present only on error
     */
    protected function disconnect(&$socket, $triggerClosed = true, $sockErrNo = null) {
        $id = md5((string)spl_object_id($socket));
        $disconnectedUser = $this->users[$id];

        if ($disconnectedUser !== null) {
            unset($this->users[$id]);
            unset($this->rooms[$disconnectedUser->room][$disconnectedUser->data['id']]);            

            if (array_key_exists($disconnectedUser->id, $this->sockets)) {
                unset($this->sockets[$disconnectedUser->id]);
            }

            if (!is_null($sockErrNo) && !$this->isSsl) {
                socket_clear_error($socket);
            }

            if ($triggerClosed) {
                $this->stdout("Client disconnected. ".$disconnectedUser->id);
                $this->closed($disconnectedUser);
                if($this->isSsl){
                    fclose($disconnectedUser->socket);
                } else {
                    socket_close($disconnectedUser->socket);
                }
            } else {
                $message = $this->frame('', $disconnectedUser, 'close');
                if($this->isSsl){
                    @fwrite($disconnectedUser->socket, $message, strlen($message));
                } else {
                    @socket_write($disconnectedUser->socket, $message, strlen($message));
                }
            }

            // close room if empty
            if(count($this->rooms[$disconnectedUser->room]) === 0){
                unset($this->rooms[$disconnectedUser->room]);
                $this->closeRoom($disconnectedUser->room);
            }
        }
    }

    /**
     * Do the handshake client/server
     * @param  \Phalcon\Websocket\User &$user  User instance
     * @param  string &$buffer Buffer containing data
     */
    protected function doHandshake(&$user, &$buffer) {
        $magicGUID = "258EAFA5-E914-47DA-95CA-C5AB0DC85B11";
        $headers = [];
        $lines = explode("\n",$buffer);
        foreach ($lines as $line) {
            if (strpos($line,":") !== false) {
                $header = explode(":",$line,2);
                $headers[strtolower(trim($header[0]))] = trim($header[1]);
            }
            elseif (stripos($line,"get ") !== false) {
                preg_match("/GET (.*) ".$this->protocol."/i", $buffer, $reqResource);
                $headers['get'] = trim($reqResource[1]);
            }
        }
        if (isset($headers['get'])) {
            $user->requestedResource = $headers['get'];
            if($user->room === null){
                $user->parseData();
                if(!isset($this->rooms[$user->room])){
                    $this->openRoom($user->room);     
                    $this->rooms[$user->room] = [];               
                }                
                $this->rooms[$user->room][$user->data['id']] = $user->id;
                $this->stdout('Client '.$user->id.' connected inside room '.$user->room);
                $this->stdout('Client '.$user->id.' is user '.$user->data['id']);
            }
        } else {
            // todo: fail the connection
            $handshakeResponse = $this->protocol."/1.1 405 Method Not Allowed\r\n\r\n";     
        }
        if (!isset($headers['host']) || !$this->checkHost($headers['host'])) {
            $handshakeResponse = $this->protocol."/1.1 400 Bad Request";
        }
        if (!isset($headers['upgrade']) || strtolower($headers['upgrade']) != 'websocket') {
            $handshakeResponse = $this->protocol."/1.1 400 Bad Request";
        } 
        if (!isset($headers['connection']) || strpos(strtolower($headers['connection']), 'upgrade') === FALSE) {
            $handshakeResponse = $this->protocol."/1.1 400 Bad Request";
        }
        if (!isset($headers['sec-websocket-key'])) {
            $handshakeResponse = $this->protocol."/1.1 400 Bad Request";
        } else {

        }
        if (!isset($headers['sec-websocket-version']) || strtolower($headers['sec-websocket-version']) != 13) {
            $handshakeResponse = $this->protocol."/1.1 426 Upgrade Required\r\nSec-WebSocketVersion: 13";
        }
        if (($this->headerOriginRequired && !isset($headers['origin']) ) || ($this->headerOriginRequired && !$this->checkOrigin($headers['origin']))) {
            $handshakeResponse = $this->protocol."/1.1 403 Forbidden";
        }
        if (($this->headerSecWebSocketProtocolRequired && !isset($headers['sec-websocket-protocol'])) || ($this->headerSecWebSocketProtocolRequired && !$this->checkWebsocProtocol($headers['sec-websocket-protocol']))) {
            $handshakeResponse = $this->protocol."/1.1 400 Bad Request";
        }
        if (($this->headerSecWebSocketExtensionsRequired && !isset($headers['sec-websocket-extensions'])) || ($this->headerSecWebSocketExtensionsRequired && !$this->checkWebsocExtensions($headers['sec-websocket-extensions']))) {
            $handshakeResponse = $this->protocol."/1.1 400 Bad Request";
        }

        // Done verifying the _required_ headers and optionally required headers.
        if (isset($handshakeResponse)) {
            if($this->isSsl){
                fwrite($user->socket,$handshakeResponse,strlen($handshakeResponse));
            } else {
                socket_write($user->socket,$handshakeResponse,strlen($handshakeResponse));
            }            
            $this->disconnect($user->socket);
            return;
        }

        $user->headers = $headers;
        $user->handshake = $buffer;

        $webSocketKeyHash = sha1($headers['sec-websocket-key'] . $magicGUID);

        $rawToken = "";
        for ($i = 0; $i < 20; $i++) {
            $rawToken .= chr(hexdec(substr($webSocketKeyHash,$i*2, 2)));
        }
        $handshakeToken = base64_encode($rawToken) . "\r\n";

        $subProtocol = (isset($headers['sec-websocket-protocol'])) ? $this->processProtocol($headers['sec-websocket-protocol']) : "";
        $extensions = (isset($headers['sec-websocket-extensions'])) ? $this->processExtensions($headers['sec-websocket-extensions']) : "";

        $handshakeResponse = $this->protocol."/1.1 101 Switching Protocols\r\nUpgrade: websocket\r\nConnection: Upgrade\r\nSec-WebSocket-Accept: $handshakeToken$subProtocol$extensions\r\n";
        if($this->isSsl){
            fwrite($user->socket,$handshakeResponse,strlen($handshakeResponse));
        } else {
            socket_write($user->socket,$handshakeResponse,strlen($handshakeResponse));
        }
        $this->connected($user);
    }

    /**
     * Override and return false if the host is not one that you would expect.
     * @param  String $hostName Name of the host to check
     * @return boolean           Result of the check
     */
    protected function checkHost($hostName) {
        return true;
    }

    /**
     * Override and return false if the origin is not one that you would expect.
     * @param  string $origin Origin to check
     * @return boolean         Result of the check
     */
    protected function checkOrigin($origin) {
        return true;
    }

    /**
     * Override and return false if a protocol is not found that you would expect.
     * @param  string $protocol Name of the protocol
     * @return boolean           Result of the check
     */
    protected function checkWebsocProtocol($protocol) {
        return true; 
    }

    /**
     * Override and return false if an extension is not found that you would expect.
     * @param  string $extensions Extension list
     * @return boolean             Result of the check
     */
    protected function checkWebsocExtensions($extensions) {
        return true;
    }

    /**
     * Process a protocol
     * @param  string $protocol Protocol to process
     * @return string           Either "Sec-WebSocket-Protocol: SelectedProtocolFromClientList\r\n" or return an empty string. The carriage return/newline combo must appear at the end of a non-empty string, and must not appear at the beginning of the string nor in an otherwise empty string, or it will be considered part of the response body, which will trigger an error in the client as it will not be formatted correctly.
     */
    protected function processProtocol($protocol) {
        return "";
    }

    /**
     * Process extensions
     * @param  string $extensions Extensions list
     * @return string             Either "Sec-WebSocket-Extensions: SelectedExtensions\r\n" or return an empty string.
     */
    protected function processExtensions($extensions) {
        return "";
    }

    /**
     * Get an user by his socket
     * @param  \socket_ressource $socket User socket to get
     * @return \Phalcon\Websocket\User         Valid user or null if socket not exists
     */
    protected function getUserBySocket($socket) {
        foreach ($this->users as $user) {
            if ($user->socket == $socket) {
                return $user;
            }
        }
        return null;
    }

    /**
     * Log a success message
     * @param  string $message Message to display
     */
    public function stdout($message) {
        if ($this->interactive) {
            Cli::success($message, true);
        }
    }

    /**
     * Log a warning message
     * @param  string $message Message to display
     */
    public function stderr($message) {
        if ($this->interactive) {
            Cli::warning($message, true);
        }
    }

    /**
     * Create a frame from user message
     * @param  string  &$message         Message data
     * @param  \Phalcon\Websocket\User  &$user   User instance
     * @param  string  $messageType      Type of message
     * @param  boolean $messageContinues Determine if the message is finished or not
     * @return string                    Formated message
     */
    protected function frame(&$message, &$user, $messageType='text', $messageContinues=false) {
        switch ($messageType) {
            case 'continuous':
            $b1 = 0;
            break;
            case 'text':
            $b1 = ($user->sendingContinuous) ? 0 : 1;
            break;
            case 'binary':
            $b1 = ($user->sendingContinuous) ? 0 : 2;
            break;
            case 'close':
            $b1 = 8;
            break;
            case 'ping':
            $b1 = 9;
            break;
            case 'pong':
            $b1 = 10;
            break;
        }
        if ($messageContinues) {
            $user->sendingContinuous = true;
        } else {
            $b1 += 128;
            $user->sendingContinuous = false;
        }

        $length = strlen($message);
        $lengthField = "";
        if ($length < 126) {
            $b2 = $length;
        } elseif ($length < 65536) {
            $b2 = 126;
            $hexLength = dechex($length);
            //$this->stdout("Hex Length: $hexLength");
            if (strlen($hexLength)%2 == 1) {
                $hexLength = '0' . $hexLength;
            } 
            $n = strlen($hexLength) - 2;

            for ($i = $n; $i >= 0; $i=$i-2) {
                $lengthField = chr(hexdec(substr($hexLength, $i, 2))) . $lengthField;
            }
            while (strlen($lengthField) < 2) {
                $lengthField = chr(0) . $lengthField;
            }
        } else {
            $b2 = 127;
            $hexLength = dechex($length);
            if (strlen($hexLength)%2 == 1) {
                $hexLength = '0' . $hexLength;
            } 
            $n = strlen($hexLength) - 2;

            for ($i = $n; $i >= 0; $i=$i-2) {
                $lengthField = chr(hexdec(substr($hexLength, $i, 2))) . $lengthField;
            }
            while (strlen($lengthField) < 8) {
                $lengthField = chr(0) . $lengthField;
            }
        }

        return chr($b1) . chr($b2) . $lengthField . $message;
    }

    /**
     * Check packet if he have more than one frame and process each frame individually
     * @param  integer $length $length of the packet
     * @param  string $packet Packet content
     * @param  \Phalcon\Websocket\User &$user  User instance
     */
    protected function split_packet($length,$packet, &$user) {
        //add PartialPacket and calculate the new $length
        if ($user->handlingPartialPacket) {
            $packet = $user->partialBuffer . $packet;
            $user->handlingPartialPacket = false;
            $length=strlen($packet);
        }
        $fullpacket=$packet;
        $frame_pos=0;
        $frame_id=1;

        while($frame_pos<$length) {
            $headers = $this->extractHeaders($packet);
            $headers_size = $this->calcoffset($headers);
            $framesize=$headers['length']+$headers_size;

            //split frame from packet and process it
            $frame=substr($fullpacket,$frame_pos,$framesize);

            if (($message = $this->deframe($frame, $user,$headers)) !== FALSE) {
                if ($user->hasSentClose) {
                    $this->disconnect($user->socket);
                } else {
                    if ((preg_match('//u', $message)) || ($headers['opcode']==2)) {
                        //$this->stdout("Text msg encoded UTF-8 or Binary msg\n".$message); 
                        $this->process($user, $message);
                    } else {
                        $this->stderr("not UTF-8\n");
                    }
                }
            } 
            //get the new position also modify packet data
            $frame_pos+=$framesize;
            $packet=substr($fullpacket,$frame_pos);
            $frame_id++;
        }
    }

    /**
     * Calculate header offset
     * @param  array &$headers Socket headers
     * @return integer           Offset calculated
     */
    protected function calcoffset(&$headers) {
        $offset = 2;
        if ($headers['hasmask']) {
            $offset += 4;
        }
        if ($headers['length'] > 65535) {
            $offset += 8;
        } elseif ($headers['length'] > 125) {
            $offset += 2;
        }
        return $offset;
    }

    /**
     * Process a frame
     * @param  string &$message Message data
     * @param  \Phalcon\Websocket\User &$user  User instance
     * @return boolean  The frame processed of false on fail
     */
    protected function deframe(&$message, &$user) {
        //echo $this->strtohex($message);
        $headers = $this->extractHeaders($message);
        $pongReply = false;
        $willClose = false;
        switch($headers['opcode']) {
            case 0:
            case 1:
            case 2:
                break;
            case 8:
                // todo: close the connection
                $user->hasSentClose = true;
                return "";
            case 9:
                $pongReply = true;
            case 10:
                break;
            default:
                //$this->disconnect($user); // todo: fail connection
                $willClose = true;
                break;
        }

        /* Deal by split_packet() as now deframe() do only one frame at a time.
        if ($user->handlingPartialPacket) {
        $message = $user->partialBuffer . $message;
        $user->handlingPartialPacket = false;
        return $this->deframe($message, $user);
        }
        */

        if ($this->checkRSVBits($headers,$user)) {
            return false;
        }

        if ($willClose) {
            // todo: fail the connection
            return false;
        }

        $payload = $user->partialMessage . $this->extractPayload($message,$headers);

        if ($pongReply) {
            $reply = $this->frame($payload,$user,'pong');
            if($this->isSsl){
                fwrite($user->socket,$reply,strlen($reply));
            } else {
                socket_write($user->socket,$reply,strlen($reply));
            }
            return false;
        }
        if ($headers['length'] > strlen($this->applyMask($headers,$payload))) {
            $user->handlingPartialPacket = true;
            $user->partialBuffer = $message;
            return false;
        }

        $payload = $this->applyMask($headers,$payload);

        if ($headers['fin']) {
            $user->partialMessage = '';
            return $payload;
        }
        $user->partialMessage = $payload;
        return false;
    }

    /**
     * Extract the headers of a message
     * @param  string &$message Message data
     * @return array  Headers extracted
     */
    protected function extractHeaders(&$message) {
        $header = array('fin'     => $message[0] & chr(128),
            'rsv1'    => $message[0] & chr(64),
            'rsv2'    => $message[0] & chr(32),
            'rsv3'    => $message[0] & chr(16),
            'opcode'  => ord($message[0]) & 15,
            'hasmask' => $message[1] & chr(128),
            'length'  => 0,
            'mask'    => "");
        $header['length'] = (ord($message[1]) >= 128) ? ord($message[1]) - 128 : ord($message[1]);

        if ($header['length'] == 126) {
            if ($header['hasmask']) {
                $header['mask'] = $message[4] . $message[5] . $message[6] . $message[7];
            }
            $header['length'] = ord($message[2]) * 256 
            + ord($message[3]);
        } elseif ($header['length'] == 127) {
            if ($header['hasmask']) {
                $header['mask'] = $message[10] . $message[11] . $message[12] . $message[13];
            }
            $header['length'] = ord($message[2]) * 65536 * 65536 * 65536 * 256 
            + ord($message[3]) * 65536 * 65536 * 65536
            + ord($message[4]) * 65536 * 65536 * 256
            + ord($message[5]) * 65536 * 65536
            + ord($message[6]) * 65536 * 256
            + ord($message[7]) * 65536 
            + ord($message[8]) * 256
            + ord($message[9]);
        } elseif ($header['hasmask']) {
            $header['mask'] = $message[2] . $message[3] . $message[4] . $message[5];
        }
        return $header;
    }

    /**
     * Extract the payload of the message
     * @param  string &$message Message data
     * @param  array &$headers Headers of the message
     * @return string Payload of the message
     */
    protected function extractPayload(&$message,&$headers) {
        $offset = 2;
        if ($headers['hasmask']) {
            $offset += 4;
        }
        if ($headers['length'] > 65535) {
            $offset += 8;
        } 
        elseif ($headers['length'] > 125) {
            $offset += 2;
        }
        return substr($message,$offset);
    }

    /**
     * Apply a mask on the payload
     * @param  array &$headers Headers of the message
     * @param  string &$payload Payload of the message
     * @return string Payload with mask a applied
     */
    protected function applyMask(&$headers,&$payload) {
        $effectiveMask = "";
        if ($headers['hasmask']) {
            $mask = $headers['mask'];
        } 
        else {
            return $payload;
        }

        while (strlen($effectiveMask) < strlen($payload)) {
            $effectiveMask .= $mask;
        }
        while (strlen($effectiveMask) > strlen($payload)) {
            $effectiveMask = substr($effectiveMask,0,-1);
        }
        return $effectiveMask ^ $payload;
    }

    /**
     * Override this method if you are using an extension where the RSV bits are used.
     * @param  array &$headers Headers of the message
     * @param  \Phalcon\Websocket\User &$user User instance
     * @return boolean    Result of the check
     */
    protected function checkRSVBits(&$headers,&$user) { // 
        if (ord($headers['rsv1']) + ord($headers['rsv2']) + ord($headers['rsv3']) > 0) {
            //$this->disconnect($user); // todo: fail connection
            return true;
        }
        return false;
    }

    /**
     * Convert a string to his hexadecimal value
     * @param  string &$str String to convert
     * @return string The converted value
     */
    protected function strtohex(&$str) {
        $strout = "";
        for ($i = 0; $i < strlen($str); $i++) {
            $strout .= (ord($str[$i])<16) ? "0" . dechex(ord($str[$i])) : dechex(ord($str[$i]));
            $strout .= " ";
            if ($i%32 == 7) {
                $strout .= ": ";
            }
            if ($i%32 == 15) {
                $strout .= ": ";
            }
            if ($i%32 == 23) {
                $strout .= ": ";
            }
            if ($i%32 == 31) {
                $strout .= "\n";
            }
        }
        return $strout . "\n";
    }

    /**
     * Display formated headers on screen
     * @param  array &$headers Headers of the message
     */
    protected function printHeaders(&$headers) {
        echo "Array\n(\n";
        foreach ($headers as $key => $value) {
            if ($key == 'length' || $key == 'opcode') {
                echo "\t[$key] => $value\n\n";
            } else {
                echo "\t[$key] => ".$this->strtohex($value)."\n";

            }
        }
        echo ")\n";
    }
}