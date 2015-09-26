/*global ActionModel, ucfirst, isDefined, extendSingleton, getSingleton, InterfaceHelper */
var ManagerHelper;
(function(){
    /**
    * @name ManagerHelper
    * @constructor
    * @property {ActionModel} [action = new ActionModel()] Instance of ActionModel
    * @description Manage global manager functions and actions
    */
    ManagerHelper = function(){
        extendSingleton(ManagerHelper);
        this.manager = ManagerModel.getInstance();
    };

    /**
     * @member ManagerHelper#getInstance
     * @description get the single class instance
     * @return {ManagerHelper} the single class instance
     */
    ManagerHelper.getInstance = function(){
        return getSingleton(ManagerHelper);
    };

    ManagerHelper.prototype.init = function(manager, container) {
        manager = !isDefined(manager) ? window[arguments.callee.caller.caller.name] : manager;
        this.manager.init(manager.getInstance(), container);
    };
})();