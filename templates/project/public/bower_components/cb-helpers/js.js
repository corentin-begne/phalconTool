/*global isDefined */
var JsHelper;
(function(){
    "use strict";
    /**
    * @name JsHelper
    * @description Globalize some javascript functions/consts
    * @constructor
    */
    JsHelper = function(helpers){
        this.basePath = $("body").attr("app")+"/";
        this.init(helpers);
    };

    /** 
    * @description  Initialize global function
    * @method JsHelper#init
    */
    JsHelper.prototype.init = function(helpers){
       
        /** assing functions */
        $.each(this, addGlobalFunction);

        /** instanciate helpers */
        if(isDefined(helpers)){
            $.each(helpers, addHelper);   
        }

        /**
        * @method JsHelper#addGlobalFunction
        * @private
        * @param {String} name  Function name
        * @param {String} value Function to globalize
        * @description Globalize all JsHelper function except init
        */
        function addGlobalFunction(name, value){
            if(name !== "init"){
                window[name] = value;
            }
        }

        function addHelper(helper, params){
            helper = window[helper];
            if(isDefined(helper.getInstance)){
                helper.getInstance().init(params);
            }else{
                new helper(params);
            }
        }
    };

    /**
     * @method JsHelper#loadCss
     * @param  {String} url Css url to load
     * Load an external css inside DOM
     */
    JsHelper.prototype.loadCss = function(url) {
        var link = $(document.createElement("link"));
        link.attr({
            type: "text/css",
            rel: "stylesheet",
            href: url
        });
        $("head").append(link);
    }

    /**
    * @method JsHelper#idDefined
    * @description  Check if a variable is defined
    * @param  {Any} [obj]  Variable to check
    * @return {Boolean}    Result of the check
    */
    JsHelper.prototype.isDefined = function(obj){

        if(typeof obj === "undefined" || typeof obj === "null"){
            return false;
        }

        return true;
    };

    /**
     * @method JsHelper#ifNull
     * @param  {Any} test Value to test
     * @param  {Any} val1 Return value if val2 and test are defined or if val2 is not set and test not defined
     * @param  {Any} val2 Return value if test is not defined
     * @return {Any}      Return good value
     */
    JsHelper.prototype.ifNull = function(test, val1, val2){
        if(isDefined(test)){
            return isDefined(val2) ? val1 : test;
        } else {
            return isDefined(val2) ? val2 : val1;
        }
    };

    /**
    * @method JsHelper#ucfirst
    * @description  Set first letter in uppercase
    * @param  {String} [str] Text to transform
    * @return {String}       Text with first letter in uppercase
    */
    JsHelper.prototype.ucfirst = function(str){
        return str.charAt(0).toUpperCase()+str.substr(1);
    };

    /**
    * @method JsHelper#cloneObject
    * @description  Clone an object
    * @param  {Object} [obj] Object to clone
    * @return {Object}       Object cloned
    */
    JsHelper.prototype.cloneObject = function(obj){
        return $.extend({}, obj);
    };

    /**
    * @method JsHelper#cloneArray
    * @description Clone an array
    * @param  {Array} [arr] Array to clone
    * @return {Array}       Array cloned
    */
    JsHelper.prototype.cloneArray = function(arr){
        return arr.slice(0);
    };

    /**
    * @method JsHelper#getSingleton
    * @description Return the singleton instance of a class
    * @param  {Object} [obj] The instance class needed
    * @return {Object}       Instance of the class
    */
    JsHelper.prototype.getSingleton = function(obj, cb){

        if(!isDefined(obj.instance)){   
            obj.instance = false;
            if(isDefined(cb)){         
                new obj(callback);
            } else {
                return obj.instance = new obj();
            }
        } else {
            if(isDefined(cb)){         
                cb(obj.instance);
            } else {
                return obj.instance;
            }         
        }

        function callback(instance){          
            obj.instance = instance;
            cb(instance);
        }
    };

    /**
    * @method JsHelper#extendSingleton
    * @description  Extend a class to singleton
    * @param  {Object} [obj] Class instance to extend
    */
    JsHelper.prototype.extendSingleton = function(obj){
        if((isDefined(obj.instance) && obj.instance !== false) || !isDefined(obj.instance)){
            throw new Error("This class cannot be instanciated directly");
        }
    };
})();