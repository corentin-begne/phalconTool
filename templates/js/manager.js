/*global ActionModel */
var [className]Manager;
(function(){
    /**
    * @class TemplateManager
    * @constructor
    * @property {ActionModel} action Instance of ActionModel
    * @description  Manage template
    */
    [className]Manager = function [className]Manager(){
        extendSingleton([className]Manager);
        this.basePath = "[path]";
        this.action = ActionModel.getInstance();
        this.manager = ManagerHelper.getInstance();
    };

    [className]Manager.getInstance = function(){
        return getSingleton([className]Manager);
    };
    
})();