<?
namespace Websocket;
use Phalcon\Tools\Cli;

class Server extends \Phalcon\Websocket\Server{

    protected function process(&$user, &$message) {        
        $msg = json_decode($message, true);
        $fn = $msg['type'].'Event';         
        if(method_exists($this, $fn)){
            $this->$fn($user, $msg['data']);        
        }
    }

    protected function connected(&$user) {
        $this->broadcast(json_encode([
            'type'=>'userConnected',
            'data'=>$user->data
        ]), $user->room, $user->id);        
        $data = json_encode([
            'type'=>'connected',
            'data'=>$this->getUsers($user)
        ]);
        $this->send($user, $data);
    }

    protected function closed(&$user) {
        $this->broadcast(json_encode([
            'type'=>'userDisconnected',
            'data'=>$user->data
        ]), $user->room, $user->id);
    }

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