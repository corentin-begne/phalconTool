/*global extendSingleton, getSingleton, isDefined */
var ActionHelper;
(function(){
    "use strict";
    /**
    * @name ActionHelper
    * @description To make ajax call
    * @property {String} [basePath] Base path used for ajax call
    * @constructor
    */
    ActionHelper = function(){
        extendSingleton(ActionHelper);
        this.basePath = "/";
        var hasOnProgress = ("onprogress" in $.ajaxSettings.xhr());
        if (!hasOnProgress) {
            return;
        }       
        //patch ajax settings to call a progress callback
        var oldXHR = $.ajaxSettings.xhr;
        $.ajaxSettings.xhr = setAjaxSetting;

        function setAjaxSetting(){
            var xhr = oldXHR();
            if(xhr instanceof XMLHttpRequest) {
                xhr.addEventListener('progress', this.progress, false);
            }
            
            if(xhr.upload) {
                xhr.upload.addEventListener('progress', this.progress, false);
            }
            
            return xhr;
        }
    };

    /**
     * @member ActionHelper#getInstance
     * @description get the single class instance
     * @return {ActionHelper} the single class instance
     */
    ActionHelper.getInstance = function(){
        return getSingleton(ActionHelper);
    };

    /**
     * @method ActionHelper#execute
     * @description Execute an ajax call
     * @param  {Object} [data]    Data to send
     * @param  {Object} [options] Options of the ajax call
     */
    ActionHelper.prototype.execute = function(data, options){
        if(!isDefined(options.noload)){
            $("body").append("<div class='backdrop'><div id='loader'></div></div>");
            $("#loader").percentageLoader({
                width : 128, 
                height : 128, 
                progress : 0, 
                value : 'chargement'
            });
            if(isDefined(options.upload)){
                $("body .backdrop").append("<div id='uploader'></div>");
                $("#uploader").percentageLoader({
                    width : 64, 
                    height : 64, 
                    progress : 0, 
                    value : 'upload'
                });
            }
        }
        var infos = {
            type: options.type,
            data:data,
            url: this.basePath+options.action,
            dataType:options.dataType,
            success: check,
            error: checkError,
            complete: removeBackdrop,
            progress: updateLoader
        };        
        if(isDefined(options.form)){
            infos.cache = false;
            infos.contentType = false;
            infos.processData = false;
        }

        $.ajax(infos);

        function updateLoader(event){
            if(isDefined(options.noload)){
                return false;
            }
            if(event.target instanceof XMLHttpRequest){
                $("#loader").percentageLoader({
                    progress : event.loaded / event.total
                });   
            } 
            if(isDefined(options.upload) && event.target instanceof XMLHttpRequestUpload){
                $("#uploader").percentageLoader({
                    progress : event.loaded / event.total
                });   
            }       
        }

        function checkError(event){
            options.cb({success:false, error:event});
        }

        function check(data){
            options.cb(data);
        }

        function removeBackdrop(){
            if(!isDefined(options.noload)){
                $(".backdrop").remove();
            }
        }
    };

    /**
     * @description Redirect user to an internal url
     * @method ActionHelper#redirect
     * @param  {String} path Path to redirect
     */
    ActionHelper.prototype.redirect = function(path){
        window.location.href = this.basePath+path;
    };
})();