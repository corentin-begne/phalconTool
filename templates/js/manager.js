/*global ActionModel, require */
var [className]Manager;
(function(){
    "use strict";
    /**
    * @class TemplateManager
    * @constructor
    * @property {ActionModel} action Instance of ActionModel
    * @description  Manage template
    */
    [className]Manager = function(cb){
        var that = this;
        extendSingleton([className]Manager);
        this.basePath = "[path]";

        require([
            "/bower_components/cb-models/action.min.js", 
            "/bower_components/cb-models/manager.min.js"
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