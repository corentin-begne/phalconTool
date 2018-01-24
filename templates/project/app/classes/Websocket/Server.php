<?
namespace Websocket;

class Server extends \Phalcon\Websocket\Server{

    /**
     * Process a user messsage as an event and delegate to [name]Event function if defined
     * @param  \Phalcon\Websocket\User &$user User instance
     * @param  string &$message User message
     * @return [type]           [description]
     */
    protected function process(&$user, &$message) {        
        $msg = json_decode($message, true);
        $fn = $msg['type'].'Event';         
        if(method_exists($this, $fn)){
            $this->$fn($user, $msg['data']);        
        }
    }

    /**
     * Test event
     * @param  \Phalcon\Websocket\User &$user User instance
     * @param  array $data  Data of the event
     */
    protected function testEvent(&$user, $data) {
        $this->broadcast(json_encode([
            'type'=>'test',
            'data'=>$data
        ]), $user->room);        
    }

    /**
     * Trigger on user connect
     * @param  \Phalcon\Websocket\User &$user User instance
     */
    protected function connected(&$user) {
        // send userConnected event to all user connected in the same room with user data
        $this->broadcast(json_encode([
            'type'=>'userConnected',
            'data'=>$user->data
        ]), $user->room, $user->id);        

        // send connected event to current user with all user connected in his room
        $data = json_encode([
            'type'=>'connected',
            'data'=>$this->getUsers($user)
        ]);
        $this->send($user, $data);
    }

    /**
     * Trigger on user connection closed
     * @param  \Phalcon\Websocket\User &$user User instance
     */
    protected function closed(&$user) {
        // broadcast userDisconnected event to all user in the same room
        $this->broadcast(json_encode([
            'type'=>'userDisconnected',
            'data'=>$user->data
        ]), $user->room, $user->id);
    }

    /**
     * Get all user of the user room except him
     * \Phalcon\Websocket\User &$user User instance
     * @return array   Users of the room
     */
    protected function getUsers(&$user){
        $users = [];        
        foreach($this->rooms[$user->room] as $userId => $id){
            if($id === $user->id){
                continue;
            }
            $users[$userId] = $this->users[$id]->data;
        }
        return $users;
    }

}