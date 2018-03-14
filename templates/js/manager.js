/*global ActionModel, require, extendSingleton, ManagerModel, isDefined, getSingleton */
var [className]Manager;
(function(){
    "use strict";
    /**
    * @name [className]Manager
    * @constructor
    * @property {ActionModel} action Instance of ActionModel
    * @description  Manage [name]
    */
    [className]Manager = function(cb){
        var that = this;
        extendSingleton([className]Manager);
        this.basePath = "[path]";

        require([
            "bower_components/cb-models/action", 
            "bower_components/cb-models/manager"
        ], loaded);

        function loaded(){
            ActionModel.getInstance(loadedAction);            

            function loadedAction(instance){
                that.action = instance;
                ManagerModel.getInstance(loadedManager);

                function loadedManager(instance){
                    that.manager = instance;
                    that.manager.init();
                    if(isDefined(cb)){
                        cb(that);
                    }
                }
            }
        }
    };

    [className]Manager.getInstance = function(cb){
        if(isDefined(cb)){
            getSingleton([className]Manager, cb);
        } else {
            return getSingleton([className]Manager);
        }
    };
    
})();