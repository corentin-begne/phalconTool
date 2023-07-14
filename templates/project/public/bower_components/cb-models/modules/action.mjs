import { default as action } from "../../cb-helpers/modules/action.mjs";

/**
 * @name ActionModel
 * @class
 * @hideconstructor
 * @description Manage all network requests using ActionHelper - singleton
 */
class ActionModel{

    constructor(){

    }

    /**
     * @method ActionModel#send
     * @description Make ajax call
     * @param  {String}   path - Route to call
     * @param  {Object}   [data] Data to send to the action
     * @param  {Object}   [options] Options of the ajax call
     * @param  {Boolean}  [options.noload = false] - Specify loader use
     * @param  {String}   [options.dataType = "json"] - Request data type (xml | html | script | json | jsonp | text)
     * @param  {Boolean}  [options.form = false] - Specify if data are FormData
     * @returns {Promise} Request promise
     */
    send(path, data, options = {}){
        Object.assign(options, {
            noload:options.noload || false, 
            dataType:options.dataType || "json", 
            form:options.form || false
        });
        return action.execute(data ||(options.form ? new FormData() : {}), Object.assign({
            type: "post",
            action: path
        }, options));
    }

    /**
     * @method ActionModel#sendHtml
     * @description Make an ajax call with response in plain text
     * @param  {String}   path - Route to call
     * @param  {Object}   [data] Data to send in post
     * @returns {Promise} Request promise
     */
    sendHtml(path, data){
        return this.send(path, data, {dataType:"text"});
    }

    /**
     * @method ActionModel#sendFormHtml
     * @description Make an ajax call with FormData and response in plain text
     * @param   {String}     path - Route to call
     * @param   {FormData}   [data] Data to send in post
     * @returns {Promise} Request promise
     */
    sendFormHtml(path, data){
        return this.send(path, data, {dataType:"text", form:true});
    }

    /**
     * @method ActionModel#sendHtmlNoLoad
     * @description Make an ajax call without loader and response in plain text
     * @param  {String}   path - Route to call
     * @param  {Object}   [data] Data to send in post
     * @returns {Promise} Request promise
     */
    sendHtmlNoLoad(path, data){
        return this.send(path, data, {dataType:"text", noload:true});
    }

    /**
     * @method ActionModel#sendFormHtmlNoLoad
     * @description Make an ajax call with FormData, without loader and response in plain text
     * @param   {String}     path - Route to call
     * @param   {FormData}   [data] Data to send in post
     * @returns {Promise} Request promise
     */
    sendFormHtmlNoLoad(path, data){
        return this.send(path, data, {dataType:"text", noload:true, form:true});
    }

    /**
     * @method ActionModel#sendData
     * @description Make an ajax call with response in json
     * @param  {String}   path - Route to call
     * @param  {Object}   [data] Data to send in post
     * @returns {Promise} Request promise
     */
    sendData(path, data){
        return this.send(path, data);
    }

    /**
     * @method ActionModel#sendDataNoLoad
     * @description Make an ajax call without loader and response in json
     * @param  {String}   path - Route to call
     * @param  {Object}   [data] Data to send in post
     * @returns {Promise} Request promise
     */
    sendDataNoLoad(path, data){
        return this.send(path, data, {noload:true});
    }

    /**
     * @method ActionModel#sendFormDataNoLoad
     * @description Make an ajax call with FormData, without loader and response in json
     * @param   {String}     path - Route to call
     * @param   {FormData}   [data] Data to send in post
     * @returns {Promise} Request promise
     */
    sendFormDataNoLoad(path, data){
        return this.send(path, data, {noload:true, form:true});
    }

    /**
     * @method ActionModel#sendFormData
     * @description Make an ajax call with FormData and response in json
     * @param   {String}     path - Route to call
     * @param   {FormData}   [data] Data to send in post
     * @returns {Promise} Request promise
     */
    sendFormData(path, data){
        return this.send(path, data, {form:true});
    }

    /**
     * @method ActionModel#apiForm
     * @description Make an api call with FormData
     * @param   {String}     path - Route to call
     * @param   {FormData}   [data] Data to send in post
     * @returns {Promise} Request promise
     */
    apiForm (path, data){
        return this.sendForm("api/"+path, data);
    }

    /**
     * @method ActionModel#api
     * @description Make an api call
     * @param   {String}     path - Route to call
     * @param   {FormData}   [data] Data to send in post
     * @returns {Promise} Request promise
     */
    api(path, data){
        return this.sendData("api/"+path, data);
    }

    /**
     * @method ActionModel#apiNoLoad
     * @description Make an api call without loader
     * @param   {String}     path - Route to call
     * @param   {FormData}   [data] Data to send in post
     * @returns {Promise} Request promise
     */
    apiNoLoad(path, data){
        return this.sendDataNoload("api/"+path, data);
    }    
    
    /**
     * @method ActionModel#redirect
     * @description Redirect user to a path or open new tab on it
     * @param   {Map.<String, String>}   data - Data to send in post
     * @param   {String} data.get("path") - Path to redirect 
     * @param   {String} [data.get("type")] - Type of the redirect (top | self | parent | blank), if not set redirect on current page
     */
    redirect(data){
        action.redirect(data);
    };

}

const actionModel = new ActionModel();
export default actionModel;