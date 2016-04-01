<?
use Websocket\Server;
class ServerTask extends \Phalcon\CLI\Task
{
    public function mainAction() {

    }

    public function startAction() {
        $server = new Server('0.0.0.0','9000');
        try {
            $server->run();
        }
        catch (Exception $e) {
            $server->stdout($e->getMessage());
        }
    }

}