<?
namespace Phalcon\Websocket;

/**
 * User of the Websocket server
 */
class User {

    /**
     * User socket
     * @var \socket_ressouce
     */
    public $socket;
    /**
     * User id
     * @var string
     */
    public $id;
    /**
     * Headers of the user current message
     * @var array
     */
    public $headers = [];
    /**
     * User room
     * @var string
     */
    public $room;
    /**
     * User data
     * @var array
     */
    public $data;
    /**
     * Indicate if the User processing handshake
     * @var boolean
     */
    public $handshake = false;
    /**
     * Indicate if the User is handling a partial packet
     * @var boolean
     */
    public $handlingPartialPacket = false;
    /**
     * Content of the partial buffer
     * @var string
     */
    public $partialBuffer = "";

    /**
     * Determine if the message is finished or not
     * @var boolean
     */
    public $sendingContinuous = false;
    /**
     * Content of the partial messsage
     * @var string
     */
    public $partialMessage = "";

    /**
     * To know if the User has sent a close message
     * @var boolean
     */
    public $hasSentClose = false;

    /**
     * Create an user instance
     * @param string $id     User id
     * @param \socket_ressource $socket User socket
     */
    function __construct($id, $socket) {
        $this->id = $id;
        $this->socket = $socket;
    }

    /**
     * Parse data query string
     */
    public function parseData(){
        list($room, $data) = explode('?', $this->requestedResource);
        $this->room = trim($room, '/');
        $data = rawurldecode($data);
        $this->data = json_decode($data, true);
    }
}