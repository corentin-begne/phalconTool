<?
namespace Phalcon\Websocket;

class User {

    public $socket;
    public $id;
    public $headers = [];
    public $room;
    public $data;
    public $handshake = false;

    public $handlingPartialPacket = false;
    public $partialBuffer = "";

    public $sendingContinuous = false;
    public $partialMessage = "";

    public $hasSentClose = false;

    function __construct($id, $socket) {
        $this->id = $id;
        $this->socket = $socket;
    }

    public function parseData(){
        list($room, $data) = explode('?', $this->requestedResource);
        $this->room = trim($room, '/');
        parse_str($data, $this->data);
    }
}