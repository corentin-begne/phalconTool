/*global ActionModel */
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
        extendSingleton([className]Manager);
        this.basePath = "[path]";
        this.action = ActionModel.getInstance();
        this.manager = ManagerHelper.getInstance();
    };

    [className]Manager.getInstance = function(){
        return getSingleton([className]Manager);
    };
    
})();