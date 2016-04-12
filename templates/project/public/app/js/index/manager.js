/*global ActionModel, require */
var IndexManager;
(function(){
    "use strict";
    /**
    * @class TemplateManager
    * @constructor
    * @property {ActionModel} action Instance of ActionModel
    * @description  Manage template
    */
    IndexManager = function(cb){
        var that = this;
        extendSingleton(IndexManager);
        this.basePath = "index/";

        require([
            "bower_components/cb-models/action.min", 
            "bower_components/cb-models/manager.min",
            "bower_components/cb-helpers/websocket.min",
        ], loaded);

        function loaded(){
            ActionModel.getInstance(loadedAction);

            function loadedAction(instance){
                that.action = instance;
                ManagerModel.getInstance(loadedManager);

                function loadedManager(instance){
                    that.manager = instance;
                    WebsocketHelper.getInstance(loadedWebsocket);

                    function loadedWebsocket(instance){
                        that.socket = instance;
                        that.manager.init();
                        that.init();
                        if(isDefined(cb)){
                            cb(that);
                        }
                    }
                }
            }
        }
    };

    IndexManager.getInstance = function(cb){
        if(isDefined(cb)){
            getSingleton(IndexManager, cb);
        } else {
            return getSingleton(IndexManager);
        }
    };

    IndexManager.prototype.init = function() {
        var that = this;
        this.data = this.manager.getVars("body");
        this.socket.connect("ws://"+document.domain+":9000/rts", this.data, connected);

        function connected(){
            that.socket.addEvents({
                "test": test,
                "userConnected": userConnected,
                "userDisconnected": userDisconnected,
                "connected": ready
            });
            that.socket.send("test", {yo:"yo"});
            

            function test(data){
                console.log("receive test event", data);
            }

            function userConnected(data){
                console.log("new user connected", data);
            }

            function userDisconnected(data){
                console.log("user leave room", data);
            }

            function ready(data){
                console.log("connected to room", data);
            }
        }
    };        

    IndexManager.prototype.test = function(element, event, data) {
        console.log($(element), data, event);
    };
    
})();