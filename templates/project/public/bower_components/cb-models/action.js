/*global ActionHelper, extendSingleton, getSingleton, isDefined */
var ActionModel;
(function(){
    "use strict";
    /**
    * @name ActionModel
    * @description Manage all http request type
    * @property {ActionHelper} [action = new ActionHelper()] Instance of ActionHelper
    * @constructor
    */
    ActionModel = function(cb){
        var that = this;
        extendSingleton(ActionModel);
        require([
            "bower_components/cb-helpers/action", 
        ], loaded);

        function loaded(){
            ActionHelper.getInstance(loadedAction);

            function loadedAction(instance){
                that.action = instance;
                if(isDefined(cb)){
                    cb(that);
                }
            }
        }
    };

    /**
     * @member ActionModel#getInstance
     * @description Get the single class instance
     * @return {ActionModel} the single class instance
     */
    ActionModel.getInstance = function(cb){
        if(isDefined(cb)){
            getSingleton(ActionModel, cb);
        } else {
            return getSingleton(ActionModel);
        }
    };

    /**
     * @method ActionModel#getHtml
     * @description Make an ajax call with response in plain text
     * @param  {String}   [path] Route to call
     * @param  {Object}   [data] Data to send in post
     * @param  {Function} [cb]   Tiggered on complete with result in param
     * @param  {Boolean} [noload] Specify if loader must be used or no
     */
    ActionModel.prototype.getHtml = function(path, data, cb, noload){
        var options = {
            type: "post",
            action: path,
            cb: cb,
            dataType: "text"
        };
        if(isDefined(noload)){
            options.noload = noload;
        }
        this.action.execute(data, options);
    };

    /**
     * @method ActionModel#getHtmlNoLoad
     * @description Make an ajax call with response in plain text without loader
     * @param  {String}   [path] Route to call
     * @param  {Object}   [data] Data to send in post
     * @param  {Function} [cb]   Tiggered on complete with result in param
     */
    ActionModel.prototype.getHtmlNoLoad = function(path, data, cb){
        this.getHtml(path, data, cb, true);
    };

    /**
     * @method ActionModel#getFormHtml
     * @description Make an ajax call sending a form data with response in plain text
     * @param  {String}   [path] Route to call
     * @param  {FormData} [data] Form data to send in post
     * @param  {Function} [cb]   Tiggered on complete with result in param
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
     * @method ActionModel#sendDataNoLoad
     * @description Make ajax call without loader and with response in json
     * @param  {String}   [path] Action name
     * @param  {Object}   [data] Data to send to the action
     * @param  {Function} [cb]   Callback trigger on success with Object data
     */
    ActionModel.prototype.sendDataNoLoad = function(path, data, cb){
        this.sendData(path, data, cb, true);
    };

    /**
     * @method ActionModel#apiForm
     * @description Make ajax api call sending form data
     * @param  {String}   [path] Action name
     * @param  {FormData} [data] Form data to send to the action
     * @param  {Function} [cb]   Callback trigger on success with Object data
     */
    ActionModel.prototype.apiForm = function(path, data, cb){
        this.sendForm("api/"+path, data, cb);
    };

    /**
     * @method ActionModel#api
     * @description Make ajax api call
     * @param  {String} [path] Action name
     * @param  {Object} [data] Form data to send to the action
     * @param  {Function} [cb]   Callback trigger on success with Object data
     */
    ActionModel.prototype.api = function(path, data, cb){
        this.sendData("api/"+path, data, cb);
    };

    /**
     * @method ActionModel#apiNoLoad
     * @description Make ajax api call without loader
     * @param  {String} [path] Action name
     * @param  {Object} [data] Form data to send to the action
     * @param  {Function} [cb]   Callback trigger on success with Object data
     */
    ActionModel.prototype.apiNoLoad = function(path, data, cb){
        this.sendData("api/"+path, data, cb, true);
    };

    /**
     * @method ActionModel#sendData
     * @description Make ajax call with response in json
     * @param  {String}   [path] Action name
     * @param  {Object}   [data] Data to send to the action
     * @param  {Function} [cb]   Callback trigger on success with Object data
     * @param {Boolean} [noload] Specify if loader must be used or no
     */
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
     * @description Make ajax call sending form data with response in json
     * @param  {String}   [path] Action name
     * @param  {FormData} [data] Form data to send to the action
     * @param  {Function} [cb]   Callback trigger on success with Object data
     * @param  {Boolean} [noload] Specify if loader must be used or no
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
    ActionModel.prototype.sendFormUpload = function(path, data, cb, progress, complete){
        var options = {
            type: "post",
            action: path,
            cb: cb,
            dataType: "json",
            upload:true,
            form: true,
            progress:progress,
            complete:complete
        };
        if(isDefined(progress)){
            options.noload = true;
        }
        this.action.execute(data, options);
    };
    /**
     * @method ActionModel#redirect
     * @description redirect user to a path
     * @param  {String} path Path to redirect to
     */
    ActionModel.prototype.redirect = function(path, type){
        this.action.redirect(path, type);
    };

})();