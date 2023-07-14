/*global extendSingleton, getSingleton, ga, Date */
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
        if(window.isAPP || window.ENV === "dev"){
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

    /**
     * @method AnalyticsHelper#trackEvent
     * @description Tracking an user event
     * @param  {String} action   Event action
     * @param  {String} category Event category
     * @param  {Any} data     Event data
     */
    AnalyticsHelper.prototype.trackEvent = function(action, category, data) {
        if(!this.isAvailable){
            return false;
        }
        ga("send", {
            hitType: "event",
            eventCategory: category,
            eventAction: action,
            eventLabel: typeof data === "string" ? data : JSON.stringify(data)
        });
    };

    /**
     * @method AnalyticsHelper#setAccount
     * @description Configure analytics account
     */
    AnalyticsHelper.prototype.setAccount = function() {
        ga("create", this._key, "auto");
    };

    /**
     * @method AnalyticsHelper#trackPage
     * @description Track a page view
     * @param  {String} path Path of the page to track
     */
    AnalyticsHelper.prototype.trackPage = function(path) {
        if(!this.isAvailable){
            return false;
        }
        ga("set", "page", path);
        ga("send", "pageview");
    };

    /**
     * @method AnalyticsHelper#init
     * @description Store the key and configure account
     * @param  {String} key Analytics id
     */
    AnalyticsHelper.prototype.init = function(key) {
        this._key = key;
        if(!this.isAvailable){
            return false;
        }
        this.setAccount();
    };

    /**
     * @method AnalyticsHelper#remove
     * @description Remove the ga tracker
     */
    AnalyticsHelper.prototype.remove = function() {
        if(!this.isAvailable){
            return false;
        }
        ga("remove");
    };

})();