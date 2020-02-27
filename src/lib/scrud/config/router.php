<?
    $object->add("/".APP."/scrud/",[
        'controller' => 'scrud',
        'action'     => 'index'
    ]);
    $object->add("/".APP."/scrud/{model}/:action",[
        'controller' => 'scrud',
        'action'     => 2
    ]);
    $object->add("/".APP."/scrud/{model}/", [
        'controller' => 'scrud',
        'action'     => 'search'
    ]);
    $object->add("/".APP."/custom_scrud/{model}/:action",[
        'controller' => 'custom_scrud',
        'action'     => 2
    ]);
    $object->add("/".APP."/custom_scrud/{model}/", [
        'controller' => 'custom_scrud',
        'action'     => 'search'
    ]);