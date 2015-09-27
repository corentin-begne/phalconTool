/*global ActionHelper, extendSingleton, getSingleton */
var ActionModel;
(function(){
    "use strict";
    /**
    * @name ActionModel
    * @description Manage all global manager actions
    * @property {ActionHelper}    [action = new ActionHelper()]       Instance of ActionHelper
    * @property {InterfaceHelper} [interface = new InterfaceHelper()] Instance of InterfaceHelper
    * @constructor
    */
    ActionModel = function(){
        extendSingleton(ActionModel);
        this.action = ActionHelper.getInstance();
    };

    /**
     * @member ActionModel#getInstance
     * @description Get the single class instance
     * @return {ActionModel} the single class instance
     */
    ActionModel.getInstance = function(){
        return getSingleton(ActionModel);
    };

    /**
     * @method ActionModel#sendData
     * @description Send data to the manager action
     * @param  {String}   [path] Action name
     * @param  {Object}   [data] Data to send to the action
     * @param  {Function} [cb]   Callback trigger on success with Object data
     */
    ActionModel.prototype.getHtml = function(path, data, cb){
        var options = {
            type: "post",
            action: path,
            cb: cb,
            dataType: "text"
        };
        this.action.execute(data, options);
    };

    /**
     * @method ActionModel#sendData
     * @description Send data to the manager action
     * @param  {String}   [path] Action name
     * @param  {Object}   [data] Data to send to the action
     * @param  {Function} [cb]   Callback trigger on success with Object data
     */
    ActionModel.prototype.getFormHtml = function(path, data, cb){
        var options = {
            type: "post",
            action: path,
            cb: cb,
            dataType: "text",
            form: true
        };
        this.action.execute(data, options);
    };

    /**
     * @method ActionModel#sendData
     * @description Send data to the manager action
     * @param  {String}   [path] Action name
     * @param  {Object}   [data] Data to send to the action
     * @param  {Function} [cb]   Callback trigger on success with Object data
     */
    ActionModel.prototype.sendDataNoLoad = function(path, data, cb){
        this.sendData(path, data, cb, true);
    };

    ActionModel.prototype.apiForm = function(path, data, cb){
        this.sendForm('api/'+path, data, cb);
    };

    ActionModel.prototype.api = function(path, data, cb){
        this.sendData('api/'+path, data, cb);
    };

    ActionModel.prototype.apiNoLoad = function(path, data, cb){
        this.sendData('api/'+path, data, cb, true);
    };

    ActionModel.prototype.sendData = function(path, data, cb, noload){
        var options = {
            type: "post",
            action: path,
            cb: cb,
            dataType: "json"
        };
        if(isDefined(noload)){
            options.noload = noload;
        }
        this.action.execute(data, options);
    };

    /**
     * @method ActionModel#sendData
     * @description Send data to the manager action
     * @param  {String}   [path] Action name
     * @param  {Object}   [data] Data to send to the action
     * @param  {Function} [cb]   Callback trigger on success with Object data
     */
    ActionModel.prototype.sendForm = function(path, data, cb, noload){
        var options = {
            type: "post",
            action: path,
            cb: cb,
            dataType: "json",
            form: true
        };
        if(isDefined(noload)){
            options.noload = noload;
        }
        this.action.execute(data, options);
    };
    ActionModel.prototype.sendFormUpload = function(path, data, cb){
        var options = {
            type: "post",
            action: path,
            cb: cb,
            dataType: "json",
            upload:true,
            form: true
        };
        this.action.execute(data, options);
    };
    /**
     * @method ActionModel#redirect
     * @description redirect user to a path
     * @param  {String} path Path to redirect to
     */
    ActionModel.prototype.redirect = function(path){
        this.action.redirect(path);
    };

})();