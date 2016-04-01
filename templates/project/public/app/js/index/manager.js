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
            "bower_components/cb-models/manager.min"
        ], loaded);

        function loaded(){
            ActionModel.getInstance(loadedAction);

            function loadedAction(instance){
                that.action = instance;
                ManagerModel.getInstance(loadedManager);

                function loadedManager(instance){
                    that.manager = instance;
                    if(isDefined(cb)){
                        cb(that);
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
    
})();