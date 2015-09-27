/*global ActionModel */
var CreateScrudManager;
(function(){
    /**
    * @class TemplateManager
    * @constructor
    * @property {ActionModel} action Instance of ActionModel
    * @description  Manage template
    */
    CreateScrudManager = function CreateScrudManager(){
        extendSingleton(CreateScrudManager);
        this.type = "create";
        this.action = "create";
        this.form = FormHelper.getInstance();
        this.container = $("#createForm.template");
    };

    CreateScrudManager.getInstance = function(){
        return getSingleton(CreateScrudManager);
    };

    CreateScrudManager.prototype.validForm = function(element, event){
        this.form.valid(this.type, event);
    };

    CreateScrudManager.prototype.cancelForm = function(element, event){
        this.form.cancel(event);
    };
    
})();