/*global extendSingleton, getSingleton, isDefined, require, XMLHttpRequestUpload, loadCss */
var ActionHelper;
(function(){
    "use strict";
    /**
    * @name ActionHelper
    * @description To make ajax call
    * @property {String} [basePath] Base path used for ajax call
    * @property {DOMElement} loadingHtml Can be used to replace the default loader
    * @constructor
    */
    ActionHelper = function(cb){
        var that = this;
        extendSingleton(ActionHelper);
        this.loadingHtml = "";
        this.css = ".backdrop{background:rgba(0,0,0,0.3);width:100%;height:100%;z-index:999999;position:fixed;-webkit-touch-callout:none;-webkit-user-select:none;-khtml-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;top:0;left:0}.backdrop #loader{position:absolute;top:calc(50% - 25px);left:calc(50% - 25px);border:16px solid #f3f3f3;border-top:16px solid #337ab7;border-radius:50%;width:50px;height:50px;animation:spin 2s linear infinite}@keyframes spin{0%{-moz-transform:rotate(0deg);-webkit-transform:rotate(0deg);-o-transform:rotate(0deg);-ms-transform:rotate(0deg);transform:rotate(0deg)}100%{-moz-transform:rotate(360deg);-webkit-transform:rotate(360deg);-o-transform:rotate(360deg);-ms-transform:rotate(360deg);transform:rotate(360deg)}}";
        var script = $("<script type='text/css'></script>");
        script.text(this.css);
        $("head").append(script);
 /*       loadCss((window.baseUrl ? window.baseUrl : "")+"/bower_components/jquery.percentageloader/index.css");
        require([
            "bower_components/jquery-percentageloader/index"
        ], loaded);*/       
        var app = $("body").attr("app");
        this.basePath = "/"+(app==="frontend" ? '' : $("body").attr("app")+"/");
        var hasOnProgress = ("onprogress" in $.ajaxSettings.xhr());
        if (hasOnProgress) {   
            //patch ajax settings to call a progress callback
            var oldXHR = $.ajaxSettings.xhr;
            $.ajaxSettings.xhr = setAjaxSetting;
        }
        if(isDefined(cb)){
            cb(that);
        }

        function setAjaxSetting(){
            var xhr = oldXHR();
            if(xhr instanceof XMLHttpRequest) {
                xhr.addEventListener("progress", this.progress, false);
            }
            
            if(xhr.upload) {
                xhr.upload.addEventListener("progress", this.progress, false);
            }
            
            return xhr;
        }

    /*    function loaded(){
            if(isDefined(cb)){
                cb(that);
            }
        }*/
    };

    /**
     * @member ActionHelper#getInstance
     * @description get the single class instance
     * @return {ActionHelper} the single class instance
     */
    ActionHelper.getInstance = function(cb){
        if(isDefined(cb)){
            getSingleton(ActionHelper, cb);
        } else {
            return getSingleton(ActionHelper);
        }
    };

    /**
     * @method ActionHelper#execute
     * @description Execute an ajax call
     * @param  {Object} [data]    Data to send
     * @param  {Object} [options] Options of the ajax call
     */
    ActionHelper.prototype.execute = function(data, options){
        if(!isDefined(options.noload)){
            if(this.loadingHtml !== ""){
                var div = $("<div class='backdrop'></div>");
                div.append($(this.loadingHtml));
                $("body").append(div);
            } else {
                $("body").append("<div class='backdrop'><div id='loader'></div></div>");
               /* var loader = $("#loader").percentageLoader({
                    width : 128, 
                    height : 128, 
                    progress : 0, 
                    value : "chargement"
                });*/
             /*   if(isDefined(options.upload)){
                    $("body .backdrop").append("<div id='uploader'></div>");
                    var uploader = $("#uploader").percentageLoader({
                        width : 64, 
                        height : 64, 
                        progress : 0, 
                        value : "upload"
                    });
                }*/
            }
        }
        var infos = {
            type: options.type,
            data:data,
            url: this.basePath+options.action,
            dataType:options.dataType,
            success: check,
            error: checkError,
            complete: options.complete || removeBackdrop,
            progress: options.progress || updateLoader
        };        
        if(isDefined(options.form)){
            infos.cache = false;
            infos.contentType = false;
            infos.processData = false;
        }

        $.ajax(infos);

        function updateLoader(event){
            if(isDefined(options.noload) || this.loadingHtml !== ""){
                return false;
            }
            if(event.target instanceof XMLHttpRequest){
               // loader.setProgress(event.loaded / event.total);   
            } 
            if(isDefined(options.upload) && event.target instanceof XMLHttpRequestUpload){
                //uploader.setProgress(event.loaded / event.total);    
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
    ActionHelper.prototype.redirect = function(path, type){
        if(type === undefined){
            window.location.href = this.basePath+path;
        } else {
            window.open(window.location.origin+this.basePath+path, "_"+type);
        }
    };
})();