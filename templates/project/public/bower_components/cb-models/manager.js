/*global ActionModel, ucfirst, isDefined, extendSingleton, getSingleton, AutocompletionHelper, loadCss, PaginationHelper, basePath */
var ManagerModel;
(function(){
    "use strict";
    /**
    * @name ManagerModel
    * @constructor
    * @property {DOMElement} [container = $("body")] Root Dom element to parse events definition
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

    /**
     * @method  ManagerModel#getVars
     * @description Get all data append by the server under elements having class varInterface and remove them
     * @param  {DOMElement}  [container] Root element to start the parse
     * @return {Object} All data parsed
     * @example 
     * <b><u>view :</u></b>
     * ```
     * <?=$this->partial('/data', [
     *     'data' => [
     *         'id'=>1,
     *         'name'=>'test'      
     *     ]
     * ])?>
     * ```
     * <b><u>manager.js :</u></b>
     * ```
     * var data = this.manager.getVars("body");
     * ```
     */
    ManagerModel.prototype.getVars = function(container){     
        var data = {};
        $(container).find(".varInterface").each(addData);

        function addData(i, element){
            data[$(element).attr("id")] = $(element).html().trim();
            $(element).remove();
        }
        return data;
    };

    /**
     * @method ManagerModel#init
     * @description Parse and map all defined event in DOM (with class action and attribute action-data to define event data) preventDefault and stopPropagation can be used as attribute.
     * @param  {DOMElement} [container] Root element to start the parse
     * @example
     * ```
     * <div class='action' action-data='<?=json_encode([
     *   'type' => 'mouseup', // javascript event, click by default can be init to be executed on initialisation
     *   'fn' => 'action', // action type [action|autocompletion|pagination|closeInterface|getPartial|getInterface|redirect]
     *   'class' => 'TestSampleManager', // class with a getInstance function
     *   'name' => 'save' // function to use in the specified class to map to
     *   'data' => [] // can be any type of data by dont forget to $.parseJSON if its an array
     * ])'></div>
     * ```
     * Can also be an array of events to define multiple
     * ```
     * <div class='action' action-data='<?=json_encode([
     *    [
     *       'type' => 'mouseout', // javascript event, click by default can be init to be executed on initialisation
     *       'fn' => 'action',
     *       'class' => 'TestSampleManager', // class with a getInstance function
     *       'name' => 'removeActive' // function to use in the specified class to map to
     *   ],
     *   [
     *       'type' => 'mouseover',
     *       'fn' => 'action',
     *       'class' => 'TestSampleManager',
     *       'name' => 'addActive'
     *   ]
     * ])'></div>
     * ```
     * Add attributes to stopPropagation or preventDefault event
     * ```
     * <div class='action' preventDefault stopPropagation action-data='<?=json_encode([
     *   'type' => 'mouseup',
     *   'fn' => 'action',
     *   'class' => 'TestSampleManager',
     *   'name' => 'save'
     * ])'></div>
     * ```
     */
    ManagerModel.prototype.init = function(container) {
        var that = this;       
        this.container = isDefined(container) ? container : $("body");                    
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

    /**
     * @method ManagerModel#autocompletion
     * @description Initialize an autocompletion functionality to a DOMElement
     * @param  {DOMElement} [element] DOMElement to use
     * @param  {Object} [data] Contain all data supported and callback needed by AutocompletionHelper
     * @example
     * ```
     * <div class='action' action-data='<?=json_encode([
     *   'type' => 'init',
     *   'fn' => 'autocompletion',
     *   'class' => 'TestSampleManager',
     *   'data' => [
     *       'cbSelect' => 'select',
     *       'cbFind' => 'find',
     *       'cbBlur' => 'blur'
     *   ] 
     * ])'></div>
     * ```
     */
    ManagerModel.prototype.autocompletion = function(element, data) {
        data.data.container = $(element);
        data.data.cbFind = data.class[data.data.cbFind].bind(data.class);
        data.data.cbSelect = data.class[data.data.cbSelect].bind(data.class);
        if(isDefined(data.data.cbBlur)){
            data.data.cbBlur = data.class[data.data.cbBlur].bind(data.class);
        }
        data.class.autocompletion = new AutocompletionHelper(data.data);
    };

    /**
     * @method ManagerModel#action
     * @description Trigger the function associated with the defined event
     * @param  {DOMElement} [element] DOMElement associated with the event
     * @param  {Object} [data] Event data
     * @param  {MouseEvent} [event]   DOM Event
     * @example
     * ```
     * <div class='action' action-data='<?=json_encode([
     *   'type' => 'mouseup', // javascript event, click by default can be set to init to be executed on initialisation
     *   'fn' => 'action',
     *   'class' => 'TestSampleManager', // class with a getInstance function
     *   'name' => 'save' // function to use in the specified class to map to
     * ])'></div>
     * ```
     */
    ManagerModel.prototype.action = function(element, data, event) {
        if(isDefined(data.class) && isDefined(data.class[data.name])){
            data.class[data.name](element, event, data);
        }
    };

    /**
     * @method ManagerModel#pagination
     * @param  {DOMElement} [element] DOMElement container for the pagination
     * @param  {Object} [data] Contain all data supported and callback needed by PaginationHelper
     * @example
     * ```
     * <div class='action' action-data='<?=json_encode([
     *   'type' => 'init',
     *   'fn' => 'pagination',
     *   'class' => 'TestSampleManager',
     *   'data' => [
     *       'cbContent' => 'content',
     *       'nbPage' => 5
     *   ] 
     * ])'></div>
     * ```
     */
    ManagerModel.prototype.pagination = function(element, data) {
        data.class.pagination = new PaginationHelper({
            nbPage: data.data.nbPage,
            container: element,
            cb:data.class[data.data.cbContent].bind(data.class)
        });
    };

    /**
     * @method ManagerModel#closeInterface
     * @description  Remove an interface
     * @param  {DOMElement} [element] DOMElement used to close the interface, must be inside
     * @example
     * ```
     * <div class='action' action-data='<?=json_encode([
     *   'fn' => 'closeInterface',
     *   'class' => 'TestSampleManager' 
     * ])'></div>
     * ```
     */
    ManagerModel.prototype.closeInterface = function(element) {
        $(element).parents("interface").remove();
    };

    /**
     * @method ManagerModel#getPartial
     * @description Get HTML block and append it to the target DOMElement 
     * @param  {DOMElement} [element] DOMElement associated with the event
     * @param  {Object} [data]  Contain at least path and target
     * @example
     * ```
     * <div class='action' action-data='<?=json_encode([
     *   'fn' => 'getPartial',
     *   'class' => 'TestSampleManager',
     *   'data' => [
     *       'path' => 'partial/videos/list',
     *       'target' => '.videos'
     *   ] 
     * ])'></div>
     * ```
     */
    ManagerModel.prototype.getPartial = function(element, data) {
        var that = this;
        this.actionModel.getHtml(data.data.path, data, check);

        function check(html){
            if(html === "error"){
                window.location.href = "/";
            } else {
                $(data.data.target).html(html);
                that.init($(data.data.target));
            }
        }
    };

    /**
     * @method ManagerModel#getInterface
     * @description Get an Interface and append it in DOM, can be modal adding atrtibute to element
     * @param  {DOMElement} [element] DOMElement associated with the event
     * @param  {Object} [data]  Contain at least path and target
     * @example
     * <b><u>simple :</u></b>
     * ```
     * <div class='action' action-data='<?=json_encode([
     *   'fn' => 'getInterface',
     *   'class' => 'TestSampleManager',
     *   'data' => [
     *       'path' => 'interface/video',
     *       'target' => '.content'
     *   ] 
     * ])'></div>
     * ```
     * <b><u>modal :</u></b>
     * ```
     * <div class='action' modal action-data='<?=json_encode([
     *   'fn' => 'getInterface',
     *   'class' => 'TestSampleManager',
     *   'data' => [
     *       'path' => 'interface/video',
     *       'target' => '.content'
     *   ] 
     * ])'></div>
     * ```
     */
    ManagerModel.prototype.getInterface = function(element, data) {
        var that = this;
        var name = data.data.path.split("/");
        name = ucfirst(name[1])+ucfirst(name[0]);
        $("interface#"+name).remove();
        if($(element).is("[modal]")){ // unique
            $("interface").remove();
        }
        // get css and manager if not exists
        if(!isDefined(window[name+"Manager"])){
            loadCss(basePath+"css/"+data.data.path+"/main.css");
            require([basePath.replace("/", "")+"js/"+data.data.path+"/manager.min"], ready);
        } else {
            ready();
        }

        function ready(){
            that.actionModel.getHtml(data.data.path, data, loaded);

            function loaded(html){
                if(html === "error"){
                    window.location.href = "/";
                } else {
                    var target = data.data.target || "body";
                    if(data.data.target){
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

    /**
     * @method ManagerModel#redirect
     * @description  Can redirect an user to an url according to window.open documentation with name specified by the type attribute of the DOMElement
     * @param  {DOMElement} [element] DOMElement associated with the event
     * @param  {Object} data Contain path
     * @example
     * ```
     * <div class='action' type="self" action-data='<?=json_encode([
     *   'fn' => 'redirect',
     *   'class' => 'TestSampleManager',
     *   'data' => [
     *       'path' => 'user/login'
     *   ] 
     * ])'></div>
     * ```
     */
    ManagerModel.prototype.redirect = function(element, data) {
        this.actionModel.redirect(data.data.path, $(element).attr("type"));
    };


})();