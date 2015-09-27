/*global ActionModel */
var ReadScrudManager;
(function(){
    /**
    * @class TemplateManager
    * @constructor
    * @property {ActionModel} action Instance of ActionModel
    * @description  Manage template
    */
    ReadScrudManager = function ReadScrudManager(){
        extendSingleton(ReadScrudManager);
        this.type = "read";
        this.action = "update";
        this.form = FormHelper.getInstance();
        this.container = $("#readForm.template");
    };

    ReadScrudManager.getInstance = function(){
        return getSingleton(ReadScrudManager);
    };

    ReadScrudManager.prototype.validForm = function(element, event){
        this.form.valid(this.type, event);
    };

    ReadScrudManager.prototype.cancelForm = function(element, event){
        this.form.cancel(event);
    };
    
})();