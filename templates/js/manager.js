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
    [className]Manager = function(){
        var that = this;
        extendSingleton([className]Manager);
        this.basePath = "[path]";

        require([
            "bower_components/cb-models/action.min", 
            "bower_components/cb-models/manager.min"
        ], loaded);

        function loaded(){
            that.action = ActionModel.getInstance();
            that.manager = ManagerHelper.getInstance();
        }
    };

    [className]Manager.getInstance = function(){
        return getSingleton([className]Manager);
    };
    
})();