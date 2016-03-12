/*global ActionModel */
var IndexIndexManager;
(function(){
    "use strict";
    /**
    * @class TemplateManager
    * @constructor
    * @property {ActionModel} action Instance of ActionModel
    * @description  Manage template
    */
    IndexIndexManager = function(){
        extendSingleton(IndexIndexManager);
        this.basePath = "index/index/";
        this.action = ActionModel.getInstance();
        this.manager = ManagerHelper.getInstance();
    };

    IndexIndexManager.getInstance = function(){
        return getSingleton(IndexIndexManager);
    };
    
})();