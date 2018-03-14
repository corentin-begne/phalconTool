/*global ActionModel, require, extendSingleton, ManagerModel, isDefined, getSingleton */
var IndexManager;
(function(){
    "use strict";
    /**
    * @name IndexManager
    * @constructor
    * @property {ActionModel} action Instance of ActionModel
    * @description  Manage index
    */
    IndexManager = function(cb){
        var that = this;
        extendSingleton(IndexManager);
        this.basePath = "index/";

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

    IndexManager.getInstance = function(cb){
        if(isDefined(cb)){
            getSingleton(IndexManager, cb);
        } else {
            return getSingleton(IndexManager);
        }
    };
    
})();