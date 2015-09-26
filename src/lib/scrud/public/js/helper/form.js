/*global extendSingleton, getSingleton, isDefined */
var FormHelper;
(function(){
    /**
    * @name FormHelper
    * @description To make ajax call
    * @property {String} [basePath] Base path used for ajax call
    * @constructor
    */
    FormHelper = function(){
        extendSingleton(FormHelper);
        this.action = ActionModel.getInstance();
        this.manager = ManagerHelper.getInstance();
        this.isAvailable = true;
    };

    /**
     * @member FormHelper#getInstance
     * @description get the single class instance
     * @return {FormHelper} the single class instance
     */
    FormHelper.getInstance = function(){
        return getSingleton(FormHelper);
    };

    FormHelper.prototype.valid = function(type, event, cb){
        var that = this;
        event.preventDefault();
        var container = window[ucfirst(type)+"ScrudManager"].getInstance().container;
        if(!this.isAvailable){
            return false;
        }
        this.isAvailable = false;
        this.action.apiForm(container.attr("action")+"/"+window[ucfirst(type)+"ScrudManager"].getInstance().action, new FormData(container[0]), check);

        function check(data){
            that.isAvailable = true;
            if(!data.success){
                console.log(data.error);
            }
            if(isDefined(cb)){
                cb(data);
            }
        }
    };

    FormHelper.prototype.cancel = function(event, cb){
        event.preventDefault();
        console.log("cancel");
    };

})();