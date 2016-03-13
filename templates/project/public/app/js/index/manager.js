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
    IndexManager = function(){
        var that = this;
        extendSingleton(IndexManager);
        this.basePath = "index/";

        require([
            "bower_components/cb-models/action.min", 
            "bower_components/cb-models/manager.min"
        ], loaded);

        function loaded(){
            that.action = ActionModel.getInstance();
            that.manager = ManagerModel.getInstance();
        }
    };

    IndexManager.getInstance = function(){
        return getSingleton(IndexManager);
    };
    
})();