import action from "./action.mjs";
import js from "../../cb-helpers/modules/js.mjs";
import $ from "../../jQuery/src/jquery.js";

/**
* @name ManagerModel
* @class
* @hideconstructor
* @property {DOMElement} [body = $("body")] Root Dom element to parse actions
* @property {String} [default = "click"] Default event binded
* @description Manage global manager/helpers functions and actions - singleton
*/
class ManagerModel{
    
    constructor(){
        this.body = $("body");
        this.default = "click";
    }

    /**
     * @method  ManagerModel#getVars
     * @description Get all data append by the server under elements having class varInterface and remove them
     * @param  {DOMElement} [container] Root element to start parsing
     * @return {Map.<String, String>} All data parsed
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
     * import manager from "[...]/cb-models/modules/manager.mjs";
     * const data = manager.getVars("body");
     * ```
     */
    getVars(container = this.body){
        const data = new Map();
        for(let element of $(container).find(".varInterface")){
            data.set($(element).attr("id"), $(element).html().trim());
            $(element).remove();
        }
        return data;
    }

    /**
     * @method ManagerModel#init
     * @description Parse and map all defined event in DOM (with class action and attribute action-data to define events data).
     * @param  {DOMElement} [container] Root element to start the parsing
     * @example
     * ```
     * <div class='action' action-data='<?=json_encode([
     *   'type' => 'mousedown', // optional - javascript event or init (executed on bind), this.default by default (click)
     *   'class' => 'user', // optional - class used to map the function, only helpers added in manager are allowed. Current manager is used by default
     *   'name' => 'check', // required - function to use in the specified class to map to
     *   'stopPropagation' => true, // optional - to stop or not the event propagation, default false
     *   'preventDefault' => true, // optional - to prevent or not the event native function, default false
     *   'data' => [
     *      ['foo' => 'bar']
     *    ] // can be any type of data but respect this format, it will be parsed in Map type
     * ])'></div>
     * ```
     * Can also be an array of events to define multiple
     * ```
     * <div class='action' action-data='<?=json_encode([
     *    [
     *       'type' => 'mouseout',
     *       'name' => 'removeActive'
     *   ],
     *   [
     *       'type' => 'mouseover',
     *       'name' => 'addActive'
     *   ]
     * ])'></div>
     * ```
     */
    init(container = this.body) {
        var that = this;
        const main = container !== this.body ? $("<div></div>").append(container) : container;
        for(var element of main.find(".action")){
            if(!$(element).is("[action-data]")){
                continue;
            }
            let events = JSON.parse($(element).attr("action-data"));
            if(Array.isArray(events)){
                events.forEach(bindEvent);
            } else {
                bindEvent(events);
            }
        }  

        /**
         * @method ManagerModel#bindEvent
         * @private
         * @description Bind parsed Event
         * @param {Object} [data] 
         * @param {String} [data.type = this.default] Event to bind
         * @param {String} [data.class = "manager"] Helper class that will be used
         * @param {Object} [data.data = []] Must be an object that can be parsed in Map
         */
        function bindEvent(data = {}){
            Object.assign(data, {
                type: data.type || that.default,
                data: new Map(data.data || []),
                class: data.class || "manager"
            });
           /* if(!data.name){
                console.error("name must be defined for the event", $(element));         
                return;       
            }*/

            if(data.type === "init"){
                execute();
            } else {
                $(element).unbind(data.type);
                $(element).bind(data.type, sendEvent);
            }

            /**
             * @event ManagerModel#sendEvent
             * @description Execute binded Event
             * @param {Event} event Current browser Event 
             */
            function sendEvent(event){      
                element = this;           
                if(data.preventDefault){
                    event.preventDefault();
                }
                if(data.stopPropagation){
                    event.stopPropagation();
                }
                if(data.name){
                    execute(event);
                }
            }

            /**
             * @method ManagerModel#execute
             * @private
             * @description Execute public internal function, cannot be configured for now, only action is used.
             * @param {Event} event Current browser Event 
             */
            function execute(event){
                const fn = that[data.fn] || that.action;
                fn(element, data, event);
            }
        }
    }

    /**
     * @method ManagerModel#action
     * @description Trigger the function associated with the defined event
     * @param  {DOMElement} element DOMElement associated with the event
     * @param  {Object} data Event data
     * @param {String} data.class Helper class to map
     * @param {String} data.name Name of the helper function to map
     * @param {Map.<String, *>} data.data Custom event data
     * @param  {Event} event DOM Event source
     */
    action(element, data, event){
        if(!js.helpers.has(data.class)){
            console.error(`class ${data.class} not found in helpers`);
            return;
        }
        const instance = js.helpers.get(data.class).instance;
        if(!instance[data.name]){
            console.error(`function ${data.name} must exist in ${data.class}`);
            return;
        }
        instance[data.name](element, data.data, event);
    }
}

const managerModel = new ManagerModel();
export default managerModel;