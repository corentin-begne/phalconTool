import $ from "../../jQuery/src/jquery.js";

class ActionHelper{  

    constructor(){
        this.body = $("body");
        this.loader = "<div class='backdrop'><div id='loader'></div></div>";
        this.app = this.body.attr("app");
        this.basePath = "/"+(this.app==="frontend" ? '' : this.app+"/");
        this.progress = 0;
        const hasOnProgress = ("onprogress" in $.ajaxSettings.xhr());
        var oldXHR;
        if (hasOnProgress) {   
            //patch ajax settings to call a progress callback
            oldXHR = $.ajaxSettings.xhr;
            $.ajaxSettings.xhr = setAjaxSetting;
        }

        function setAjaxSetting(){
            const xhr = oldXHR();
            if(xhr instanceof XMLHttpRequest) {
                xhr.addEventListener("progress", this.progress, false);
            }
            
            if(xhr.upload) {
                xhr.upload.addEventListener("progress", this.progress, false);
            }
            
            return xhr;
        }
    }

    execute(data, options){
        var that = this;
        if(!options.noload){
            this.body.append(this.loader);
        }
        const infos = {
            type:options.type,
            data,
            url: this.basePath+options.action,
            dataType:options.dataType
        };        
        if(options.form){
            Object.assign(infos, {
                cache:false, 
                contentType:false, 
                processData:false
            });
        }  

        return new Promise(run);

        function run(resolve, reject){
            Object.assign(infos, {
                error, 
                complete,  
                progress, 
                success
            });
            $.ajax(infos);

            function progress(event){
                that.progress = event.loaded / event.total;     
            }   

            function error(event){
                reject(event);
            }

            function success(data){
                resolve(data);
            }

            function complete(){
                if(!options.noload){
                    $("body > .backdrop").remove();
                }
                that.progress = 0;
            }
        }        
    }

    redirect(data){
        if(data.has("type")){
            window.location.href = data.get("path");
        } else {
            window.open(window.location.origin+data.get("path"), "_"+data.get("type"));
        }
    }

}

let actionHelper = new ActionHelper();
export default actionHelper;