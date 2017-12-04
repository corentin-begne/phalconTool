/*global ActionModel, ucfirst, isDefined, extendSingleton, getSingleton, InterfaceHelper */
var ManagerModel;
(function(){
    "use strict";
    /**
    * @name ManagerModel
    * @constructor
    * @property {ActionModel} [action = new ActionModel()] Instance of ActionModel
    * @description Manage global manager functions and actions
    */
    ManagerModel = function(cb){        
        var that = this;
        extendSingleton(ManagerModel);                
        this.container = $("body");

        require([
            "bower_components/cb-models/action", 
        ], loaded);

        function loaded(){
            ActionModel.getInstance(loadedModel);

            function loadedModel(instance){
                that.actionModel = instance;
                if(isDefined(cb)){
                    cb(that);
                }
            }
        }
    };

    /**
     * @member ManagerModel#getInstance
     * @description get the single class instance
     * @return {ManagerModel} the single class instance
     */
    ManagerModel.getInstance = function(cb){
        if(isDefined(cb)){
            getSingleton(ManagerModel, cb);
        } else {
            return getSingleton(ManagerModel);
        }
    };

    ManagerModel.prototype.getVars = function(container){     
        var data = {};
        $(container).find(".varInterface").each(addData);

        function addData(i, element){
            data[$(element).attr("id")] = $(element).html().trim();
            $(element).remove();
        }
        return data;
    };

    ManagerModel.prototype.init = function(container) {
        var that = this;       
        this.container = isDefined(container) ? container : $('body');                    
        that.container.find(".action").each(addEvent);

        function addEvent(i, element){
            var events = $(element).is("[action-data]") ? $.parseJSON($(element).attr("action-data")) : {};

            if($.isArray(events)){
                events.forEach(createEvent);
            } else {
                createEvent(events);
            }

            function createEvent(data){
                if(isDefined(data.class)){
                    window[data.class].getInstance(loadedClass);                
                } else {
                    init();    
                }

                function loadedClass(instance){
                    data.class = instance;
                    init();
                }

                function init(){
                    if(!isDefined(data.type)){
                        data.type = "click";                    
                    }
                    if(data.type=== "init"){
                        that[data.fn](element, data);
                    } else {
                        $(element).unbind(data.type);
                        $(element).bind(data.type, sendEvent);
                    }

                    function sendEvent(event){                
                        data.fn = isDefined(data.fn) ? data.fn : "action";
                        if($(element).is("[preventDefault]")){
                            event.preventDefault();
                        }
                        if($(element).is("[stopPropagation]")){
                            event.stopPropagation();
                        }
                        if(isDefined(that[data.fn])){
                            that[data.fn](element, data, event);                    
                        }
                    }
                }
            }
            

            
        }
    };

    ManagerModel.prototype.autocompletion = function(element, data, event) {
        data.data.container = $(element);
        data.data.cbFind = data.class[data.data.cbFind].bind(data.class);
        data.data.cbSelect = data.class[data.data.cbSelect].bind(data.class);
        if(isDefined(data.data.cbBlur)){
            data.data.cbBlur = data.class[data.data.cbBlur].bind(data.class);
        }
        new AutocompletionHelper(data.data);
    };

    ManagerModel.prototype.action = function(element, data, event) {
        if(isDefined(data.class) && isDefined(data.class[data.name])){
            data.class[data.name](element, event, data);
        }
    };

    ManagerModel.prototype.pagination = function(element, data, event) {
        data.class.pagination = new PaginationHelper({
            nbPage: data.data.nbPage,
            container: element,
            cb:data.class[data.data.cbContent].bind(data.class)
        });
    };

    ManagerModel.prototype.closeInterface = function(element, data, event) {
        $(element).parents("interface").remove();
    };

    ManagerModel.prototype.getPartial = function(element, data, event) {
        var that = this;
        this.actionModel.getHtml(data.path, data, check);

        function check(html){
            if(html === "error"){
                window.location.href = "/";
            } else {
                $(data.target).html(html);
                that.init($(data.target));
            }
        }
    };

    ManagerModel.prototype.getInterface = function(element, data, event) {
        var that = this;
        var name = data.path.split('/');
        name = ucfirst(name[1])+ucfirst(name[0]);
        $("interface#"+name).remove();
        if($(element).is("[modal]")){ // unique
            $("interface").remove();
        }
        // get css and manager if not exists
        if(!isDefined(window[name+"Manager"])){
            loadCss(basePath+'css/'+data.path+"/main.css");
            require([basePath.replace("/", "")+'js/'+data.path+"/manager.min"], ready);
        } else {
            ready();
        }

        function ready(){
            that.actionModel.getHtml(data.path, data, loaded);

            function loaded(html){
                if(html === "error"){
                    window.location.href = "/";
                } else {
                    var target = data.target || "body";
                    if(data.target){
                        $(target).empty();
                    }
                    $(target).append("<interface id='"+name+"'></interface>");
                    $("interface#"+name).append(html);
                    that.init($("interface#"+name));
                    window[name+"Manager"].getInstance(loadedManager);
                }

                function loadedManager(instance){
                    if(instance.init){
                        instance.init();
                    }
                }
            }
        }
    };

    ManagerModel.prototype.redirect = function(element, data, event) {
        this.actionModel.redirect(data.data.path, $(element).attr("type"));
    };


})();