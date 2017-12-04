/*global extendSingleton, getSingleton, ga */
var AnalyticsHelper;
(function(){
    "use strict";
    /**
    * @name AnalyticsHelper
    * @description To make analytics call
    * @constructor
    */
    AnalyticsHelper = function(){
        extendSingleton(AnalyticsHelper); 
        this.isAvailable = true;
        if(isAPP || ENV !== "prod"){
            this.isAvailable = false;
            return false;
        }
        window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};
        ga.l=+new Date;
        var script = $("<script async src='https://www.google-analytics.com/analytics.js'></script>");
        $("body").prepend(script);
        this._key;
    };

    /**
     * @member AnalyticsHelper#getInstance
     * @description get the single class instance
     * @return {AnalyticsHelper} the single class instance
     */
    AnalyticsHelper.getInstance = function(){
        return getSingleton(AnalyticsHelper);
    };

    AnalyticsHelper.prototype.trackEvent = function(action, category, data) {
        if(!this.isAvailable){
            return false;
        }
        ga("send", {
            hitType: "event",
            eventCategory: category,
            eventAction: action,
            eventLabel: JSON.stringify(data)
        });
    };

    AnalyticsHelper.prototype.setAccount = function() {
        ga("create", this._key, "auto");
    };

    AnalyticsHelper.prototype.trackPage = function(path) {
        if(!this.isAvailable){
            return false;
        }
        ga("set", "page", path);
        ga("send", "pageview");
    };

    AnalyticsHelper.prototype.init = function(key) {
        this._key = key;
        if(!this.isAvailable){
            return false;
        }
        this.setAccount();
    };

})();