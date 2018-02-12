
window.projectVersion = 'master';

(function(root) {

    var bhIndex = null;
    var rootPath = '';
    var treeHtml = '        <ul>                <li data-name="namespace:" class="opened">                    <div style="padding-left:0px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href=".html">[Global Namespace]</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:ApiController" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="ApiController.html">ApiController</a>                    </div>                </li>                            <li data-name="class:AssetsPlugin" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="AssetsPlugin.html">AssetsPlugin</a>                    </div>                </li>                            <li data-name="class:GenerateTask" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="GenerateTask.html">GenerateTask</a>                    </div>                </li>                            <li data-name="class:IndexController" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="IndexController.html">IndexController</a>                    </div>                </li>                            <li data-name="class:MessageTask" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="MessageTask.html">MessageTask</a>                    </div>                </li>                            <li data-name="class:MigrationTask" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="MigrationTask.html">MigrationTask</a>                    </div>                </li>                            <li data-name="class:ModelsTask" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="ModelsTask.html">ModelsTask</a>                    </div>                </li>                            <li data-name="class:ReleaseTask" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="ReleaseTask.html">ReleaseTask</a>                    </div>                </li>                            <li data-name="class:Rest" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Rest.html">Rest</a>                    </div>                </li>                            <li data-name="class:ScrudController" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="ScrudController.html">ScrudController</a>                    </div>                </li>                            <li data-name="class:SecurityPlugin" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="SecurityPlugin.html">SecurityPlugin</a>                    </div>                </li>                            <li data-name="class:ServerTask" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="ServerTask.html">ServerTask</a>                    </div>                </li>                            <li data-name="class:UserController" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="UserController.html">UserController</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="namespace:Manager" class="opened">                    <div style="padding-left:0px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Manager.html">Manager</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="namespace:Manager_Social" class="opened">                    <div style="padding-left:18px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Manager/Social.html">Social</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:Manager_Social_GooglePlus" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Manager/Social/GooglePlus.html">GooglePlus</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="class:Manager_User" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Manager/User.html">User</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="namespace:Phalcon" class="opened">                    <div style="padding-left:0px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Phalcon.html">Phalcon</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="namespace:Phalcon_Builder" class="opened">                    <div style="padding-left:18px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Phalcon/Builder.html">Builder</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:Phalcon_Builder_Build" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Phalcon/Builder/Build.html">Build</a>                    </div>                </li>                            <li data-name="class:Phalcon_Builder_Controller" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Phalcon/Builder/Controller.html">Controller</a>                    </div>                </li>                            <li data-name="class:Phalcon_Builder_Css" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Phalcon/Builder/Css.html">Css</a>                    </div>                </li>                            <li data-name="class:Phalcon_Builder_Form" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Phalcon/Builder/Form.html">Form</a>                    </div>                </li>                            <li data-name="class:Phalcon_Builder_Js" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Phalcon/Builder/Js.html">Js</a>                    </div>                </li>                            <li data-name="class:Phalcon_Builder_Less" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Phalcon/Builder/Less.html">Less</a>                    </div>                </li>                            <li data-name="class:Phalcon_Builder_Migration" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Phalcon/Builder/Migration.html">Migration</a>                    </div>                </li>                            <li data-name="class:Phalcon_Builder_Model" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Phalcon/Builder/Model.html">Model</a>                    </div>                </li>                            <li data-name="class:Phalcon_Builder_Task" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Phalcon/Builder/Task.html">Task</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="namespace:Phalcon_Tools" class="opened">                    <div style="padding-left:18px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Phalcon/Tools.html">Tools</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:Phalcon_Tools_Cli" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Phalcon/Tools/Cli.html">Cli</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="namespace:Phalcon_Websocket" class="opened">                    <div style="padding-left:18px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Phalcon/Websocket.html">Websocket</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:Phalcon_Websocket_Server" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Phalcon/Websocket/Server.html">Server</a>                    </div>                </li>                            <li data-name="class:Phalcon_Websocket_User" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Phalcon/Websocket/User.html">User</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="class:Phalcon_ControllerBase" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Phalcon/ControllerBase.html">ControllerBase</a>                    </div>                </li>                            <li data-name="class:Phalcon_Library" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Phalcon/Library.html">Library</a>                    </div>                </li>                            <li data-name="class:Phalcon_ModelBase" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Phalcon/ModelBase.html">ModelBase</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="namespace:Websocket" class="opened">                    <div style="padding-left:0px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Websocket.html">Websocket</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:Websocket_Server" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Websocket/Server.html">Server</a>                    </div>                </li>                </ul></div>                </li>                </ul>';

    var searchTypeClasses = {
        'Namespace': 'label-default',
        'Class': 'label-info',
        'Interface': 'label-primary',
        'Trait': 'label-success',
        'Method': 'label-danger',
        '_': 'label-warning'
    };

    var searchIndex = [
                    
            {"type": "Namespace", "link": ".html", "name": "", "doc": "Namespace "},{"type": "Namespace", "link": "Manager.html", "name": "Manager", "doc": "Namespace Manager"},{"type": "Namespace", "link": "Manager/Social.html", "name": "Manager\\Social", "doc": "Namespace Manager\\Social"},{"type": "Namespace", "link": "Phalcon.html", "name": "Phalcon", "doc": "Namespace Phalcon"},{"type": "Namespace", "link": "Phalcon/Builder.html", "name": "Phalcon\\Builder", "doc": "Namespace Phalcon\\Builder"},{"type": "Namespace", "link": "Phalcon/Tools.html", "name": "Phalcon\\Tools", "doc": "Namespace Phalcon\\Tools"},{"type": "Namespace", "link": "Phalcon/Websocket.html", "name": "Phalcon\\Websocket", "doc": "Namespace Phalcon\\Websocket"},{"type": "Namespace", "link": "Websocket.html", "name": "Websocket", "doc": "Namespace Websocket"},
            
            {"type": "Class",  "link": "ApiController.html", "name": "ApiController", "doc": "&quot;Provide actions to search, create, update and delete models data and autocompletion on field data search&quot;"},
                                                        {"type": "Method", "fromName": "ApiController", "fromLink": "ApiController.html", "link": "ApiController.html#method_beforeExecuteRoute", "name": "ApiController::beforeExecuteRoute", "doc": "&quot;Check url models conformity&quot;"},
                    {"type": "Method", "fromName": "ApiController", "fromLink": "ApiController.html", "link": "ApiController.html#method_findAction", "name": "ApiController::findAction", "doc": "&quot;Get models result filtered by conditions&quot;"},
                    {"type": "Method", "fromName": "ApiController", "fromLink": "ApiController.html", "link": "ApiController.html#method_getTypeAction", "name": "ApiController::getTypeAction", "doc": "&quot;Get the type of models column&quot;"},
                    {"type": "Method", "fromName": "ApiController", "fromLink": "ApiController.html", "link": "ApiController.html#method_completeAction", "name": "ApiController::completeAction", "doc": "&quot;Autocomplete models field data search&quot;"},
                    {"type": "Method", "fromName": "ApiController", "fromLink": "ApiController.html", "link": "ApiController.html#method_createAction", "name": "ApiController::createAction", "doc": "&quot;Create models entry&quot;"},
                    {"type": "Method", "fromName": "ApiController", "fromLink": "ApiController.html", "link": "ApiController.html#method_updateAction", "name": "ApiController::updateAction", "doc": "&quot;Update models entry&quot;"},
                    {"type": "Method", "fromName": "ApiController", "fromLink": "ApiController.html", "link": "ApiController.html#method_deleteAction", "name": "ApiController::deleteAction", "doc": "&quot;Delete models entry&quot;"},
            
            {"type": "Class",  "link": "AssetsPlugin.html", "name": "AssetsPlugin", "doc": "&quot;The Assets plugin manages all assets (CSS\/JS) on application.&quot;"},
                                                        {"type": "Method", "fromName": "AssetsPlugin", "fromLink": "AssetsPlugin.html", "link": "AssetsPlugin.html#method_beforeDispatch", "name": "AssetsPlugin::beforeDispatch", "doc": "&quot;The event is called before the controller action.&quot;"},
                    {"type": "Method", "fromName": "AssetsPlugin", "fromLink": "AssetsPlugin.html", "link": "AssetsPlugin.html#method_afterDispatch", "name": "AssetsPlugin::afterDispatch", "doc": "&quot;&quot;"},
            
            {"type": "Class",  "link": "GenerateTask.html", "name": "GenerateTask", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "GenerateTask", "fromLink": "GenerateTask.html", "link": "GenerateTask.html#method_mainAction", "name": "GenerateTask::mainAction", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "GenerateTask", "fromLink": "GenerateTask.html", "link": "GenerateTask.html#method_appAction", "name": "GenerateTask::appAction", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "GenerateTask", "fromLink": "GenerateTask.html", "link": "GenerateTask.html#method_projectAction", "name": "GenerateTask::projectAction", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "GenerateTask", "fromLink": "GenerateTask.html", "link": "GenerateTask.html#method_modelAction", "name": "GenerateTask::modelAction", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "GenerateTask", "fromLink": "GenerateTask.html", "link": "GenerateTask.html#method_controllerAction", "name": "GenerateTask::controllerAction", "doc": "&quot;generate module, action, css\/js, rest, security&quot;"},
                    {"type": "Method", "fromName": "GenerateTask", "fromLink": "GenerateTask.html", "link": "GenerateTask.html#method_taskAction", "name": "GenerateTask::taskAction", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "GenerateTask", "fromLink": "GenerateTask.html", "link": "GenerateTask.html#method_jsAction", "name": "GenerateTask::jsAction", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "GenerateTask", "fromLink": "GenerateTask.html", "link": "GenerateTask.html#method_cssAction", "name": "GenerateTask::cssAction", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "GenerateTask", "fromLink": "GenerateTask.html", "link": "GenerateTask.html#method_lessAction", "name": "GenerateTask::lessAction", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "GenerateTask", "fromLink": "GenerateTask.html", "link": "GenerateTask.html#method_buildAction", "name": "GenerateTask::buildAction", "doc": "&quot;&quot;"},
            
            {"type": "Class",  "link": "IndexController.html", "name": "IndexController", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "IndexController", "fromLink": "IndexController.html", "link": "IndexController.html#method_indexAction", "name": "IndexController::indexAction", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "Manager\\Social", "fromLink": "Manager/Social.html", "link": "Manager/Social/GooglePlus.html", "name": "Manager\\Social\\GooglePlus", "doc": "&quot;Manage Google Plus connection&quot;"},
                                                        {"type": "Method", "fromName": "Manager\\Social\\GooglePlus", "fromLink": "Manager/Social/GooglePlus.html", "link": "Manager/Social/GooglePlus.html#method_init", "name": "Manager\\Social\\GooglePlus::init", "doc": "&quot;Initialize the Google api&quot;"},
                    {"type": "Method", "fromName": "Manager\\Social\\GooglePlus", "fromLink": "Manager/Social/GooglePlus.html", "link": "Manager/Social/GooglePlus.html#method_createAuthUrl", "name": "Manager\\Social\\GooglePlus::createAuthUrl", "doc": "&quot;Create the authentification url&quot;"},
                    {"type": "Method", "fromName": "Manager\\Social\\GooglePlus", "fromLink": "Manager/Social/GooglePlus.html", "link": "Manager/Social/GooglePlus.html#method_connect", "name": "Manager\\Social\\GooglePlus::connect", "doc": "&quot;Authenticate Google Plus user, create if not exists and connect to  the application&quot;"},
            
            {"type": "Class", "fromName": "Manager", "fromLink": "Manager.html", "link": "Manager/User.html", "name": "Manager\\User", "doc": "&quot;Manage User connection and data&quot;"},
                                                        {"type": "Method", "fromName": "Manager\\User", "fromLink": "Manager/User.html", "link": "Manager/User.html#method_disconnect", "name": "Manager\\User::disconnect", "doc": "&quot;Disconnect user from application and destroy session&quot;"},
                    {"type": "Method", "fromName": "Manager\\User", "fromLink": "Manager/User.html", "link": "Manager/User.html#method_connect", "name": "Manager\\User::connect", "doc": "&quot;Connect an user to the application&quot;"},
                    {"type": "Method", "fromName": "Manager\\User", "fromLink": "Manager/User.html", "link": "Manager/User.html#method_get", "name": "Manager\\User::get", "doc": "&quot;Get user data&quot;"},
                    {"type": "Method", "fromName": "Manager\\User", "fromLink": "Manager/User.html", "link": "Manager/User.html#method_getPermissions", "name": "Manager\\User::getPermissions", "doc": "&quot;Get all user permissions&quot;"},
                    {"type": "Method", "fromName": "Manager\\User", "fromLink": "Manager/User.html", "link": "Manager/User.html#method_havePermission", "name": "Manager\\User::havePermission", "doc": "&quot;Check if user have a permission&quot;"},
                    {"type": "Method", "fromName": "Manager\\User", "fromLink": "Manager/User.html", "link": "Manager/User.html#method_isAuthenticated", "name": "Manager\\User::isAuthenticated", "doc": "&quot;Check if the user is connected to the application&quot;"},
            
            {"type": "Class",  "link": "MessageTask.html", "name": "MessageTask", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "MessageTask", "fromLink": "MessageTask.html", "link": "MessageTask.html#method_mainAction", "name": "MessageTask::mainAction", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "MessageTask", "fromLink": "MessageTask.html", "link": "MessageTask.html#method_importAction", "name": "MessageTask::importAction", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "MessageTask", "fromLink": "MessageTask.html", "link": "MessageTask.html#method_exportAction", "name": "MessageTask::exportAction", "doc": "&quot;&quot;"},
            
            {"type": "Class",  "link": "MigrationTask.html", "name": "MigrationTask", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "MigrationTask", "fromLink": "MigrationTask.html", "link": "MigrationTask.html#method_mainAction", "name": "MigrationTask::mainAction", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "MigrationTask", "fromLink": "MigrationTask.html", "link": "MigrationTask.html#method_runAction", "name": "MigrationTask::runAction", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "MigrationTask", "fromLink": "MigrationTask.html", "link": "MigrationTask.html#method_generateAction", "name": "MigrationTask::generateAction", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "MigrationTask", "fromLink": "MigrationTask.html", "link": "MigrationTask.html#method_rollbackAction", "name": "MigrationTask::rollbackAction", "doc": "&quot;&quot;"},
            
            {"type": "Class",  "link": "ModelsTask.html", "name": "ModelsTask", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "ModelsTask", "fromLink": "ModelsTask.html", "link": "ModelsTask.html#method_mainAction", "name": "ModelsTask::mainAction", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "ModelsTask", "fromLink": "ModelsTask.html", "link": "ModelsTask.html#method_addAction", "name": "ModelsTask::addAction", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "ModelsTask", "fromLink": "ModelsTask.html", "link": "ModelsTask.html#method_modifyAction", "name": "ModelsTask::modifyAction", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "ModelsTask", "fromLink": "ModelsTask.html", "link": "ModelsTask.html#method_dropAction", "name": "ModelsTask::dropAction", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "ModelsTask", "fromLink": "ModelsTask.html", "link": "ModelsTask.html#method_importAction", "name": "ModelsTask::importAction", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "ModelsTask", "fromLink": "ModelsTask.html", "link": "ModelsTask.html#method_exportAction", "name": "ModelsTask::exportAction", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "ModelsTask", "fromLink": "ModelsTask.html", "link": "ModelsTask.html#method_generateAction", "name": "ModelsTask::generateAction", "doc": "&quot;generate all models&quot;"},
            
            {"type": "Class", "fromName": "Phalcon\\Builder", "fromLink": "Phalcon/Builder.html", "link": "Phalcon/Builder/Build.html", "name": "Phalcon\\Builder\\Build", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "Phalcon\\Builder\\Build", "fromLink": "Phalcon/Builder/Build.html", "link": "Phalcon/Builder/Build.html#method___construct", "name": "Phalcon\\Builder\\Build::__construct", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Builder\\Build", "fromLink": "Phalcon/Builder/Build.html", "link": "Phalcon/Builder/Build.html#method_getActions", "name": "Phalcon\\Builder\\Build::getActions", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Builder\\Build", "fromLink": "Phalcon/Builder/Build.html", "link": "Phalcon/Builder/Build.html#method_run", "name": "Phalcon\\Builder\\Build::run", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "Phalcon\\Builder", "fromLink": "Phalcon/Builder.html", "link": "Phalcon/Builder/Controller.html", "name": "Phalcon\\Builder\\Controller", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "Phalcon\\Builder\\Controller", "fromLink": "Phalcon/Builder/Controller.html", "link": "Phalcon/Builder/Controller.html#method___construct", "name": "Phalcon\\Builder\\Controller::__construct", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Builder\\Controller", "fromLink": "Phalcon/Builder/Controller.html", "link": "Phalcon/Builder/Controller.html#method_setActions", "name": "Phalcon\\Builder\\Controller::setActions", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "Phalcon\\Builder", "fromLink": "Phalcon/Builder.html", "link": "Phalcon/Builder/Css.html", "name": "Phalcon\\Builder\\Css", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "Phalcon\\Builder\\Css", "fromLink": "Phalcon/Builder/Css.html", "link": "Phalcon/Builder/Css.html#method___construct", "name": "Phalcon\\Builder\\Css::__construct", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "Phalcon\\Builder", "fromLink": "Phalcon/Builder.html", "link": "Phalcon/Builder/Form.html", "name": "Phalcon\\Builder\\Form", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "Phalcon\\Builder\\Form", "fromLink": "Phalcon/Builder/Form.html", "link": "Phalcon/Builder/Form.html#method_getFields", "name": "Phalcon\\Builder\\Form::getFields", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Builder\\Form", "fromLink": "Phalcon/Builder/Form.html", "link": "Phalcon/Builder/Form.html#method_getDisplayValue", "name": "Phalcon\\Builder\\Form::getDisplayValue", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Builder\\Form", "fromLink": "Phalcon/Builder/Form.html", "link": "Phalcon/Builder/Form.html#method_getLabel", "name": "Phalcon\\Builder\\Form::getLabel", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Builder\\Form", "fromLink": "Phalcon/Builder/Form.html", "link": "Phalcon/Builder/Form.html#method_getTag", "name": "Phalcon\\Builder\\Form::getTag", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "Phalcon\\Builder", "fromLink": "Phalcon/Builder.html", "link": "Phalcon/Builder/Js.html", "name": "Phalcon\\Builder\\Js", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "Phalcon\\Builder\\Js", "fromLink": "Phalcon/Builder/Js.html", "link": "Phalcon/Builder/Js.html#method___construct", "name": "Phalcon\\Builder\\Js::__construct", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "Phalcon\\Builder", "fromLink": "Phalcon/Builder.html", "link": "Phalcon/Builder/Less.html", "name": "Phalcon\\Builder\\Less", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "Phalcon\\Builder\\Less", "fromLink": "Phalcon/Builder/Less.html", "link": "Phalcon/Builder/Less.html#method___construct", "name": "Phalcon\\Builder\\Less::__construct", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Builder\\Less", "fromLink": "Phalcon/Builder/Less.html", "link": "Phalcon/Builder/Less.html#method_globRecursive", "name": "Phalcon\\Builder\\Less::globRecursive", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "Phalcon\\Builder", "fromLink": "Phalcon/Builder.html", "link": "Phalcon/Builder/Migration.html", "name": "Phalcon\\Builder\\Migration", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "Phalcon\\Builder\\Migration", "fromLink": "Phalcon/Builder/Migration.html", "link": "Phalcon/Builder/Migration.html#method___construct", "name": "Phalcon\\Builder\\Migration::__construct", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Builder\\Migration", "fromLink": "Phalcon/Builder/Migration.html", "link": "Phalcon/Builder/Migration.html#method_getKeyInfo", "name": "Phalcon\\Builder\\Migration::getKeyInfo", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Builder\\Migration", "fromLink": "Phalcon/Builder/Migration.html", "link": "Phalcon/Builder/Migration.html#method_normalize", "name": "Phalcon\\Builder\\Migration::normalize", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Builder\\Migration", "fromLink": "Phalcon/Builder/Migration.html", "link": "Phalcon/Builder/Migration.html#method_checkField", "name": "Phalcon\\Builder\\Migration::checkField", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Builder\\Migration", "fromLink": "Phalcon/Builder/Migration.html", "link": "Phalcon/Builder/Migration.html#method_getCurrentVersion", "name": "Phalcon\\Builder\\Migration::getCurrentVersion", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Builder\\Migration", "fromLink": "Phalcon/Builder/Migration.html", "link": "Phalcon/Builder/Migration.html#method_setCurrentVersion", "name": "Phalcon\\Builder\\Migration::setCurrentVersion", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "Phalcon\\Builder", "fromLink": "Phalcon/Builder.html", "link": "Phalcon/Builder/Model.html", "name": "Phalcon\\Builder\\Model", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "Phalcon\\Builder\\Model", "fromLink": "Phalcon/Builder/Model.html", "link": "Phalcon/Builder/Model.html#method___construct", "name": "Phalcon\\Builder\\Model::__construct", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "Phalcon\\Builder", "fromLink": "Phalcon/Builder.html", "link": "Phalcon/Builder/Task.html", "name": "Phalcon\\Builder\\Task", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "Phalcon\\Builder\\Task", "fromLink": "Phalcon/Builder/Task.html", "link": "Phalcon/Builder/Task.html#method___construct", "name": "Phalcon\\Builder\\Task::__construct", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Builder\\Task", "fromLink": "Phalcon/Builder/Task.html", "link": "Phalcon/Builder/Task.html#method_setActions", "name": "Phalcon\\Builder\\Task::setActions", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "Phalcon", "fromLink": "Phalcon.html", "link": "Phalcon/ControllerBase.html", "name": "Phalcon\\ControllerBase", "doc": "&quot;Base class of all controllers&quot;"},
                                                        {"type": "Method", "fromName": "Phalcon\\ControllerBase", "fromLink": "Phalcon/ControllerBase.html", "link": "Phalcon/ControllerBase.html#method_beforeExecuteRoute", "name": "Phalcon\\ControllerBase::beforeExecuteRoute", "doc": "&quot;Set default view data&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\ControllerBase", "fromLink": "Phalcon/ControllerBase.html", "link": "Phalcon/ControllerBase.html#method_getTranslation", "name": "Phalcon\\ControllerBase::getTranslation", "doc": "&quot;Store all current lang texts translations&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\ControllerBase", "fromLink": "Phalcon/ControllerBase.html", "link": "Phalcon/ControllerBase.html#method___", "name": "Phalcon\\ControllerBase::__", "doc": "&quot;Get a translation by his label in controller\/action context&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\ControllerBase", "fromLink": "Phalcon/ControllerBase.html", "link": "Phalcon/ControllerBase.html#method__", "name": "Phalcon\\ControllerBase::_", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "Phalcon", "fromLink": "Phalcon.html", "link": "Phalcon/Library.html", "name": "Phalcon\\Library", "doc": "&quot;Manage internal library access and includes&quot;"},
                                                        {"type": "Method", "fromName": "Phalcon\\Library", "fromLink": "Phalcon/Library.html", "link": "Phalcon/Library.html#method_get", "name": "Phalcon\\Library::get", "doc": "&quot;Include the config type file of each librariries&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Library", "fromLink": "Phalcon/Library.html", "link": "Phalcon/Library.html#method_loadDir", "name": "Phalcon\\Library::loadDir", "doc": "&quot;Load some directories&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Library", "fromLink": "Phalcon/Library.html", "link": "Phalcon/Library.html#method_loadNamespaces", "name": "Phalcon\\Library::loadNamespaces", "doc": "&quot;Load some namespaces&quot;"},
            
            {"type": "Class", "fromName": "Phalcon", "fromLink": "Phalcon.html", "link": "Phalcon/ModelBase.html", "name": "Phalcon\\ModelBase", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "Phalcon\\ModelBase", "fromLink": "Phalcon/ModelBase.html", "link": "Phalcon/ModelBase.html#method_queryOne", "name": "Phalcon\\ModelBase::queryOne", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\ModelBase", "fromLink": "Phalcon/ModelBase.html", "link": "Phalcon/ModelBase.html#method_queryAll", "name": "Phalcon\\ModelBase::queryAll", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\ModelBase", "fromLink": "Phalcon/ModelBase.html", "link": "Phalcon/ModelBase.html#method_getColumnsDescription", "name": "Phalcon\\ModelBase::getColumnsDescription", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\ModelBase", "fromLink": "Phalcon/ModelBase.html", "link": "Phalcon/ModelBase.html#method_getType", "name": "Phalcon\\ModelBase::getType", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\ModelBase", "fromLink": "Phalcon/ModelBase.html", "link": "Phalcon/ModelBase.html#method_returnRelations", "name": "Phalcon\\ModelBase::returnRelations", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\ModelBase", "fromLink": "Phalcon/ModelBase.html", "link": "Phalcon/ModelBase.html#method_getRelations", "name": "Phalcon\\ModelBase::getRelations", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\ModelBase", "fromLink": "Phalcon/ModelBase.html", "link": "Phalcon/ModelBase.html#method_checkHasOne", "name": "Phalcon\\ModelBase::checkHasOne", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\ModelBase", "fromLink": "Phalcon/ModelBase.html", "link": "Phalcon/ModelBase.html#method_getReferencedField", "name": "Phalcon\\ModelBase::getReferencedField", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\ModelBase", "fromLink": "Phalcon/ModelBase.html", "link": "Phalcon/ModelBase.html#method_getColumnsMap", "name": "Phalcon\\ModelBase::getColumnsMap", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\ModelBase", "fromLink": "Phalcon/ModelBase.html", "link": "Phalcon/ModelBase.html#method_filterParams", "name": "Phalcon\\ModelBase::filterParams", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\ModelBase", "fromLink": "Phalcon/ModelBase.html", "link": "Phalcon/ModelBase.html#method_getErrors", "name": "Phalcon\\ModelBase::getErrors", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\ModelBase", "fromLink": "Phalcon/ModelBase.html", "link": "Phalcon/ModelBase.html#method_getPrefix", "name": "Phalcon\\ModelBase::getPrefix", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\ModelBase", "fromLink": "Phalcon/ModelBase.html", "link": "Phalcon/ModelBase.html#method_getRequired", "name": "Phalcon\\ModelBase::getRequired", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\ModelBase", "fromLink": "Phalcon/ModelBase.html", "link": "Phalcon/ModelBase.html#method_getPrimaryKey", "name": "Phalcon\\ModelBase::getPrimaryKey", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\ModelBase", "fromLink": "Phalcon/ModelBase.html", "link": "Phalcon/ModelBase.html#method_getMapped", "name": "Phalcon\\ModelBase::getMapped", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\ModelBase", "fromLink": "Phalcon/ModelBase.html", "link": "Phalcon/ModelBase.html#method_initialize", "name": "Phalcon\\ModelBase::initialize", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "Phalcon\\Tools", "fromLink": "Phalcon/Tools.html", "link": "Phalcon/Tools/Cli.html", "name": "Phalcon\\Tools\\Cli", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "Phalcon\\Tools\\Cli", "fromLink": "Phalcon/Tools/Cli.html", "link": "Phalcon/Tools/Cli.html#method_colorBackgroundString", "name": "Phalcon\\Tools\\Cli::colorBackgroundString", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Tools\\Cli", "fromLink": "Phalcon/Tools/Cli.html", "link": "Phalcon/Tools/Cli.html#method_colorString", "name": "Phalcon\\Tools\\Cli::colorString", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Tools\\Cli", "fromLink": "Phalcon/Tools/Cli.html", "link": "Phalcon/Tools/Cli.html#method_error", "name": "Phalcon\\Tools\\Cli::error", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Tools\\Cli", "fromLink": "Phalcon/Tools/Cli.html", "link": "Phalcon/Tools/Cli.html#method_success", "name": "Phalcon\\Tools\\Cli::success", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Tools\\Cli", "fromLink": "Phalcon/Tools/Cli.html", "link": "Phalcon/Tools/Cli.html#method_warning", "name": "Phalcon\\Tools\\Cli::warning", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Tools\\Cli", "fromLink": "Phalcon/Tools/Cli.html", "link": "Phalcon/Tools/Cli.html#method_errorBg", "name": "Phalcon\\Tools\\Cli::errorBg", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Tools\\Cli", "fromLink": "Phalcon/Tools/Cli.html", "link": "Phalcon/Tools/Cli.html#method_successBg", "name": "Phalcon\\Tools\\Cli::successBg", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Tools\\Cli", "fromLink": "Phalcon/Tools/Cli.html", "link": "Phalcon/Tools/Cli.html#method_warningBg", "name": "Phalcon\\Tools\\Cli::warningBg", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "Phalcon\\Websocket", "fromLink": "Phalcon/Websocket.html", "link": "Phalcon/Websocket/Server.html", "name": "Phalcon\\Websocket\\Server", "doc": "&quot;Websocket server supporting https&quot;"},
                                                        {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method___construct", "name": "Phalcon\\Websocket\\Server::__construct", "doc": "&quot;Initialize master socket&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_process", "name": "Phalcon\\Websocket\\Server::process", "doc": "&quot;Called immediately when the data is recieved.&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_connected", "name": "Phalcon\\Websocket\\Server::connected", "doc": "&quot;Called after the handshake response is sent to the client.&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_closed", "name": "Phalcon\\Websocket\\Server::closed", "doc": "&quot;Called after the connection is closed.&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_connecting", "name": "Phalcon\\Websocket\\Server::connecting", "doc": "&quot;Handle a connecting user, after the instance of the User is created, but before the handshake has completed.&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_openRoom", "name": "Phalcon\\Websocket\\Server::openRoom", "doc": "&quot;Called on room open&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_closeRoom", "name": "Phalcon\\Websocket\\Server::closeRoom", "doc": "&quot;Called on room close&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_send", "name": "Phalcon\\Websocket\\Server::send", "doc": "&quot;Send event to an user&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_broadcast", "name": "Phalcon\\Websocket\\Server::broadcast", "doc": "&quot;Send event to multiple user&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_tick", "name": "Phalcon\\Websocket\\Server::tick", "doc": "&quot;Happen periodically. Will happen at least once per second, but possibly more often.&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method__tick", "name": "Phalcon\\Websocket\\Server::_tick", "doc": "&quot;Core maintenance processes, such as retrying failed messages.&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_run", "name": "Phalcon\\Websocket\\Server::run", "doc": "&quot;Main processing loop&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_connect", "name": "Phalcon\\Websocket\\Server::connect", "doc": "&quot;Connect an user.&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_disconnect", "name": "Phalcon\\Websocket\\Server::disconnect", "doc": "&quot;Disconnect an User.&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_doHandshake", "name": "Phalcon\\Websocket\\Server::doHandshake", "doc": "&quot;Do the handshake client\/server&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_checkHost", "name": "Phalcon\\Websocket\\Server::checkHost", "doc": "&quot;Override and return false if the host is not one that you would expect.&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_checkOrigin", "name": "Phalcon\\Websocket\\Server::checkOrigin", "doc": "&quot;Override and return false if the origin is not one that you would expect.&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_checkWebsocProtocol", "name": "Phalcon\\Websocket\\Server::checkWebsocProtocol", "doc": "&quot;Override and return false if a protocol is not found that you would expect.&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_checkWebsocExtensions", "name": "Phalcon\\Websocket\\Server::checkWebsocExtensions", "doc": "&quot;Override and return false if an extension is not found that you would expect.&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_processProtocol", "name": "Phalcon\\Websocket\\Server::processProtocol", "doc": "&quot;Process a protocol&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_processExtensions", "name": "Phalcon\\Websocket\\Server::processExtensions", "doc": "&quot;Process extensions&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_getUserBySocket", "name": "Phalcon\\Websocket\\Server::getUserBySocket", "doc": "&quot;Get an user by his socket&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_stdout", "name": "Phalcon\\Websocket\\Server::stdout", "doc": "&quot;Log a success message&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_stderr", "name": "Phalcon\\Websocket\\Server::stderr", "doc": "&quot;Log a warning message&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_frame", "name": "Phalcon\\Websocket\\Server::frame", "doc": "&quot;Create a frame from user message&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_split_packet", "name": "Phalcon\\Websocket\\Server::split_packet", "doc": "&quot;Check packet if he have more than one frame and process each frame individually&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_calcoffset", "name": "Phalcon\\Websocket\\Server::calcoffset", "doc": "&quot;Calculate header offset&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_deframe", "name": "Phalcon\\Websocket\\Server::deframe", "doc": "&quot;Process a frame&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_extractHeaders", "name": "Phalcon\\Websocket\\Server::extractHeaders", "doc": "&quot;Extract the headers of a message&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_extractPayload", "name": "Phalcon\\Websocket\\Server::extractPayload", "doc": "&quot;Extract the payload of the message&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_applyMask", "name": "Phalcon\\Websocket\\Server::applyMask", "doc": "&quot;Apply a mask on the payload&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_checkRSVBits", "name": "Phalcon\\Websocket\\Server::checkRSVBits", "doc": "&quot;Override this method if you are using an extension where the RSV bits are used.&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_strtohex", "name": "Phalcon\\Websocket\\Server::strtohex", "doc": "&quot;Convert a string to his hexadecimal value&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\Server", "fromLink": "Phalcon/Websocket/Server.html", "link": "Phalcon/Websocket/Server.html#method_printHeaders", "name": "Phalcon\\Websocket\\Server::printHeaders", "doc": "&quot;Display formated headers on screen&quot;"},
            
            {"type": "Class", "fromName": "Phalcon\\Websocket", "fromLink": "Phalcon/Websocket.html", "link": "Phalcon/Websocket/User.html", "name": "Phalcon\\Websocket\\User", "doc": "&quot;User of the Websocket server&quot;"},
                                                        {"type": "Method", "fromName": "Phalcon\\Websocket\\User", "fromLink": "Phalcon/Websocket/User.html", "link": "Phalcon/Websocket/User.html#method___construct", "name": "Phalcon\\Websocket\\User::__construct", "doc": "&quot;Create an user instance&quot;"},
                    {"type": "Method", "fromName": "Phalcon\\Websocket\\User", "fromLink": "Phalcon/Websocket/User.html", "link": "Phalcon/Websocket/User.html#method_parseData", "name": "Phalcon\\Websocket\\User::parseData", "doc": "&quot;Parse data query string&quot;"},
            
            {"type": "Class",  "link": "ReleaseTask.html", "name": "ReleaseTask", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "ReleaseTask", "fromLink": "ReleaseTask.html", "link": "ReleaseTask.html#method_mainAction", "name": "ReleaseTask::mainAction", "doc": "&quot;&quot;"},
            
            {"type": "Class",  "link": "Rest.html", "name": "Rest", "doc": "&quot;Manage Rest API request \/ response&lt;br&gt;\n&lt;u&gt;&lt;b&gt;Example :&lt;\/b&gt;&lt;\/u&gt;&lt;\/p&gt;\n\n&lt;pre&gt;&lt;code class=\&quot;php\&quot;&gt;Rest::init();\nRest::checkParams([&#039;id&#039;]);\ntry{\n    Rest::renderSuccess();\n} catch(Exception $e){\n    Rest::renderError($e-&amp;gt;getMessage());\n}\n&lt;\/code&gt;&lt;\/pre&gt;\n&quot;"},
                                                        {"type": "Method", "fromName": "Rest", "fromLink": "Rest.html", "link": "Rest.html#method_init", "name": "Rest::init", "doc": "&quot;Check the conformity of the request and get the post data&quot;"},
                    {"type": "Method", "fromName": "Rest", "fromLink": "Rest.html", "link": "Rest.html#method_checkRequest", "name": "Rest::checkRequest", "doc": "&quot;Check if the request is a XMLHttpRequest&quot;"},
                    {"type": "Method", "fromName": "Rest", "fromLink": "Rest.html", "link": "Rest.html#method_checkReferer", "name": "Rest::checkReferer", "doc": "&quot;Check if the referer corresponding to the same server&quot;"},
                    {"type": "Method", "fromName": "Rest", "fromLink": "Rest.html", "link": "Rest.html#method_renderError", "name": "Rest::renderError", "doc": "&quot;Rest response on error&quot;"},
                    {"type": "Method", "fromName": "Rest", "fromLink": "Rest.html", "link": "Rest.html#method_renderSuccess", "name": "Rest::renderSuccess", "doc": "&quot;Rest response on success&quot;"},
                    {"type": "Method", "fromName": "Rest", "fromLink": "Rest.html", "link": "Rest.html#method_renderJson", "name": "Rest::renderJson", "doc": "&quot;Rest response&quot;"},
                    {"type": "Method", "fromName": "Rest", "fromLink": "Rest.html", "link": "Rest.html#method_render", "name": "Rest::render", "doc": "&quot;Normalize data to render&quot;"},
                    {"type": "Method", "fromName": "Rest", "fromLink": "Rest.html", "link": "Rest.html#method_checkParams", "name": "Rest::checkParams", "doc": "&quot;Check the presence of required params&quot;"},
            
            {"type": "Class",  "link": "ScrudController.html", "name": "ScrudController", "doc": "&quot;Manage tables index, model search, create and read&quot;"},
                                                        {"type": "Method", "fromName": "ScrudController", "fromLink": "ScrudController.html", "link": "ScrudController.html#method_initialize", "name": "ScrudController::initialize", "doc": "&quot;Check url models conformity and include\/configure all dependencies&quot;"},
                    {"type": "Method", "fromName": "ScrudController", "fromLink": "ScrudController.html", "link": "ScrudController.html#method_indexAction", "name": "ScrudController::indexAction", "doc": "&quot;List all database tables by alphabetical order&quot;"},
                    {"type": "Method", "fromName": "ScrudController", "fromLink": "ScrudController.html", "link": "ScrudController.html#method_listAction", "name": "ScrudController::listAction", "doc": "&quot;Get current selected models items&quot;"},
                    {"type": "Method", "fromName": "ScrudController", "fromLink": "ScrudController.html", "link": "ScrudController.html#method_searchAction", "name": "ScrudController::searchAction", "doc": "&quot;&lt;ul&gt;\n&lt;li&gt;Display the search interface&lt;\/li&gt;\n&lt;\/ul&gt;\n&quot;"},
                    {"type": "Method", "fromName": "ScrudController", "fromLink": "ScrudController.html", "link": "ScrudController.html#method_readAction", "name": "ScrudController::readAction", "doc": "&quot;Display the read interface&quot;"},
                    {"type": "Method", "fromName": "ScrudController", "fromLink": "ScrudController.html", "link": "ScrudController.html#method_createAction", "name": "ScrudController::createAction", "doc": "&quot;Display the create interface&quot;"},
            
            {"type": "Class",  "link": "SecurityPlugin.html", "name": "SecurityPlugin", "doc": "&quot;The security plugin manages the Access Control List (ACL).&quot;"},
                                                        {"type": "Method", "fromName": "SecurityPlugin", "fromLink": "SecurityPlugin.html", "link": "SecurityPlugin.html#method_beforeException", "name": "SecurityPlugin::beforeException", "doc": "&quot;Redirect user to default route if no controller\/action found&quot;"},
                    {"type": "Method", "fromName": "SecurityPlugin", "fromLink": "SecurityPlugin.html", "link": "SecurityPlugin.html#method_beforeDispatch", "name": "SecurityPlugin::beforeDispatch", "doc": "&quot;Check user permissions vs ACL and redirect to default route if not allowed&quot;"},
            
            {"type": "Class",  "link": "ServerTask.html", "name": "ServerTask", "doc": "&quot;Manage Websocket server&quot;"},
                                                        {"type": "Method", "fromName": "ServerTask", "fromLink": "ServerTask.html", "link": "ServerTask.html#method_mainAction", "name": "ServerTask::mainAction", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "ServerTask", "fromLink": "ServerTask.html", "link": "ServerTask.html#method_startAction", "name": "ServerTask::startAction", "doc": "&quot;Start server&quot;"},
            
            {"type": "Class",  "link": "UserController.html", "name": "UserController", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "UserController", "fromLink": "UserController.html", "link": "UserController.html#method_loginAction", "name": "UserController::loginAction", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "UserController", "fromLink": "UserController.html", "link": "UserController.html#method_connectAction", "name": "UserController::connectAction", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "UserController", "fromLink": "UserController.html", "link": "UserController.html#method_disconnectAction", "name": "UserController::disconnectAction", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "Websocket", "fromLink": "Websocket.html", "link": "Websocket/Server.html", "name": "Websocket\\Server", "doc": "&quot;&quot;"},
                                                        {"type": "Method", "fromName": "Websocket\\Server", "fromLink": "Websocket/Server.html", "link": "Websocket/Server.html#method_process", "name": "Websocket\\Server::process", "doc": "&quot;Process a user messsage as an event and delegate to [name]Event function if defined&quot;"},
                    {"type": "Method", "fromName": "Websocket\\Server", "fromLink": "Websocket/Server.html", "link": "Websocket/Server.html#method_testEvent", "name": "Websocket\\Server::testEvent", "doc": "&quot;Test event&quot;"},
                    {"type": "Method", "fromName": "Websocket\\Server", "fromLink": "Websocket/Server.html", "link": "Websocket/Server.html#method_connected", "name": "Websocket\\Server::connected", "doc": "&quot;Trigger on user connect&quot;"},
                    {"type": "Method", "fromName": "Websocket\\Server", "fromLink": "Websocket/Server.html", "link": "Websocket/Server.html#method_closed", "name": "Websocket\\Server::closed", "doc": "&quot;Trigger on user connection closed&quot;"},
                    {"type": "Method", "fromName": "Websocket\\Server", "fromLink": "Websocket/Server.html", "link": "Websocket/Server.html#method_getUsers", "name": "Websocket\\Server::getUsers", "doc": "&quot;Get all user of the user room except him\n\\Phalcon\\Websocket\\User &amp;amp;$user User instance&quot;"},
            
            
                                        // Fix trailing commas in the index
        {}
    ];

    /** Tokenizes strings by namespaces and functions */
    function tokenizer(term) {
        if (!term) {
            return [];
        }

        var tokens = [term];
        var meth = term.indexOf('::');

        // Split tokens into methods if "::" is found.
        if (meth > -1) {
            tokens.push(term.substr(meth + 2));
            term = term.substr(0, meth - 2);
        }

        // Split by namespace or fake namespace.
        if (term.indexOf('\\') > -1) {
            tokens = tokens.concat(term.split('\\'));
        } else if (term.indexOf('_') > 0) {
            tokens = tokens.concat(term.split('_'));
        }

        // Merge in splitting the string by case and return
        tokens = tokens.concat(term.match(/(([A-Z]?[^A-Z]*)|([a-z]?[^a-z]*))/g).slice(0,-1));

        return tokens;
    };

    root.Sami = {
        /**
         * Cleans the provided term. If no term is provided, then one is
         * grabbed from the query string "search" parameter.
         */
        cleanSearchTerm: function(term) {
            // Grab from the query string
            if (typeof term === 'undefined') {
                var name = 'search';
                var regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
                var results = regex.exec(location.search);
                if (results === null) {
                    return null;
                }
                term = decodeURIComponent(results[1].replace(/\+/g, " "));
            }

            return term.replace(/<(?:.|\n)*?>/gm, '');
        },

        /** Searches through the index for a given term */
        search: function(term) {
            // Create a new search index if needed
            if (!bhIndex) {
                bhIndex = new Bloodhound({
                    limit: 500,
                    local: searchIndex,
                    datumTokenizer: function (d) {
                        return tokenizer(d.name);
                    },
                    queryTokenizer: Bloodhound.tokenizers.whitespace
                });
                bhIndex.initialize();
            }

            results = [];
            bhIndex.get(term, function(matches) {
                results = matches;
            });

            if (!rootPath) {
                return results;
            }

            // Fix the element links based on the current page depth.
            return $.map(results, function(ele) {
                if (ele.link.indexOf('..') > -1) {
                    return ele;
                }
                ele.link = rootPath + ele.link;
                if (ele.fromLink) {
                    ele.fromLink = rootPath + ele.fromLink;
                }
                return ele;
            });
        },

        /** Get a search class for a specific type */
        getSearchClass: function(type) {
            return searchTypeClasses[type] || searchTypeClasses['_'];
        },

        /** Add the left-nav tree to the site */
        injectApiTree: function(ele) {
            ele.html(treeHtml);
        }
    };

    $(function() {
        // Modify the HTML to work correctly based on the current depth
        rootPath = $('body').attr('data-root-path');
        treeHtml = treeHtml.replace(/href="/g, 'href="' + rootPath);
        Sami.injectApiTree($('#api-tree'));
    });

    return root.Sami;
})(window);

$(function() {

    // Enable the version switcher
    $('#version-switcher').change(function() {
        window.location = $(this).val()
    });

    
        // Toggle left-nav divs on click
        $('#api-tree .hd span').click(function() {
            $(this).parent().parent().toggleClass('opened');
        });

        // Expand the parent namespaces of the current page.
        var expected = $('body').attr('data-name');

        if (expected) {
            // Open the currently selected node and its parents.
            var container = $('#api-tree');
            var node = $('#api-tree li[data-name="' + expected + '"]');
            // Node might not be found when simulating namespaces
            if (node.length > 0) {
                node.addClass('active').addClass('opened');
                node.parents('li').addClass('opened');
                var scrollPos = node.offset().top - container.offset().top + container.scrollTop();
                // Position the item nearer to the top of the screen.
                scrollPos -= 200;
                container.scrollTop(scrollPos);
            }
        }

    
    
        var form = $('#search-form .typeahead');
        form.typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        }, {
            name: 'search',
            displayKey: 'name',
            source: function (q, cb) {
                cb(Sami.search(q));
            }
        });

        // The selection is direct-linked when the user selects a suggestion.
        form.on('typeahead:selected', function(e, suggestion) {
            window.location = suggestion.link;
        });

        // The form is submitted when the user hits enter.
        form.keypress(function (e) {
            if (e.which == 13) {
                $('#search-form').submit();
                return true;
            }
        });

    
});


