<?
    $router->add("/scrud/{model}/:action",[
        'controller' => 'scrud',
        'action'     => 2
    ]);
    $router->add("/scrud/{model}/", [
        'controller' => 'scrud',
        'action'     => 'index'
    ]);