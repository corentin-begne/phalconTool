/*global ActionModel */
var LoginHelper;
(function(){
    "use strict";
    /**
    * @class LoginHelper
    * @constructor
    * @property {ActionModel} action Instance of ActionModel
    * @description  Manage login
    */
    LoginHelper = function(){
        extendSingleton(LoginHelper);
        this.basePath = "user/";
        this.action = ActionModel.getInstance();
    };

    LoginHelper.getInstance = function(){
        return getSingleton(LoginHelper);
    };

    /** 
     * @method LoginHelper#init
     * @description initialize events
     */
    LoginHelper.prototype.init = function(){
        var that = this;
        var isAvailable = true;
        this.container = $("#userContainer");
        var actions = this.container.find(".actions");
        var actionContainer = this.container.find(".actionContainer");
        var loginContainer = this.container.find("#loginContainer");

        /** assign events */
        loginContainer.find("#connect").mousedown(connect);
        actionContainer.find("#disconnect").mousedown(disconnect);
        loginContainer.find("input").keyup(checkInput);

        function disconnect(){
            if(!isAvailable){
                return false;
            }

            isAvailable = false;
            that.action.sendData(that.basePath+"logout", null, checkUser);

            function checkUser(){
                window.location.reload();
            }
        }

        function connect(){
            var login = loginContainer.find("#login").val();
            var password = loginContainer.find("#password").val();
            if(!isAvailable || login === "" || password === ""){
                return false;
            }

            isAvailable = false;
            var data = {
                name:login, 
                password:$.md5(password)
            };
            that.action.sendData(that.basePath+"login", data, checkUser);

            function checkUser(data){
                isAvailable = true;
                if(!isDefined(data.data)){   
                    window.location.reload();
                    return false;
                }                
                loginContainer.find(".error").remove();
                loginContainer.append("<div class='error'></div>");
                loginContainer.find(".error").text(data.data);
                loginContainer.find("input").val("");
                loginContainer.find("input:first").focus();
            }
        }

        function checkInput(event){
            if(event.keyCode === 13 && $(this).val() !== ""){
                if(loginContainer.find("input:isEmpty").length !== 0){        
                    loginContainer.find("input:isEmpty:first").focus();    
                }else{
                    connect();
                }
            }
        }
    };
})();