/*global isDefined, Response */
var JsHelper;
(function(){
    "use strict";
    /**
    * @name JsHelper
    * @description Globalize some javascript functions/consts
    * @constructor
    */
    JsHelper = function(helpers){
        this.basePath = "/"+$("body").attr("app")+"/";
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
                if(helper.getInstance().init){
                    helper.getInstance().init(params);
                }
            }else{
                new helper(params);
            }
        }
    };

    /**
     * @method JsHelper#loadCss
     * @description Load an external css inside DOM 
     * @param  {String} url Css url to load
     */
    JsHelper.prototype.loadCss = function(url) {
        var link = $(document.createElement("link"));
        link.attr({
            type: "text/css",
            rel: "stylesheet",
            href: url+"?v="+($("body").is("[version]") ? $("body").attr("version") : (new Date()).getTime())
        });
        $("head").append(link);
    };

    /**
     * @method JsHelper#asyncParse
     * @description  Parse a String to JSON asynchronously
     * @param  {String} data String to parse in JSON
     * @param  {String} type One of supported type
     * @return {Response}   Return a new Response object
     */
    JsHelper.prototype.asyncParse = function(data, type){
        return (new Response(data))[type]();
    };

    /**
    * @method JsHelper#isDefined
    * @description  Check if a variable is defined
    * @param  {Any} [obj]  Variable to check
    * @return {Boolean} Result of the check
    */
    JsHelper.prototype.isDefined = function(obj){

        if(typeof obj === "undefined" || typeof obj === "null"){
            return false;
        }

        return true;
    };

    /**
     * @method JsHelper#ifNull
     * @description  Choose the right value to return
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
            if(obj.instance === false){
                if(isDefined(cb)){
                    setTimeout(function (){
                        getSingleton(obj, cb);
                    }, 20);

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

    /**
     * @method JsHelper#isSafari
     * @description Check if Safari is the browser or not
     * @return {Boolean} Result of the check
     */
    JsHelper.prototype.isSafari = function() {
        var ua = navigator.userAgent.toLowerCase();
        return ua.indexOf("safari") !== -1 && ua.indexOf("mac") !== -1;
    };

    /**
     * @method JsHelper#isMobile
     * @description Check if the user device is a mobile or not
     * @return {Boolean} Result of the check
     */
    JsHelper.prototype.isMobile = function() {
        var check = false;
        (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
        return check;
    };

    /**
     * @method JsHelper#isApple
     * @description Check if the user device is Apple or not
     * @return {Boolean} Result of the check
     */
    JsHelper.prototype.isApple = function() {
        return /(iPad|iPhone|iPod|Mac)/g.test(navigator.userAgent);
    };
})();