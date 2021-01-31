import action from "./action.mjs";
import js from "../../cb-helpers/modules/js.mjs";
import $ from "../../jQuery/src/jquery.js";

class ManagerModel{

    constructor(){
        this.body = $("body");
        this.default = "click";
    }

    getVars(container = this.body){
        const data = new Map();
        for(let element of $(container).find(".varInterface")){
            data.set($(element).attr("id"), $(element).html().trim());
            $(element).remove();
        }
        return data;
    }

    init(container = this.body) {
        var that = this;
        for(var element of $(container).find(".action")){
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

        function bindEvent(data = {}){
            Object.assign(data, {
                type: data.type || that.default,
                data: new Map(data.data || [])
            });
            if(!data.class || !data.name){
                console.error("class and name must be defined for the event", $(element));         
                return;       
            }

            if(data.type === "init"){
                execute();
            } else {
                $(element).unbind(data.type);
                $(element).bind(data.type, sendEvent);
            }

            function sendEvent(event){                
                if(data.preventDefault){
                    event.preventDefault();
                }
                if(data.stopPropagation){
                    event.stopPropagation();
                }
                execute(event);
            }

            function execute(event){
                const fn = that[data.fn] || that.action;
                fn(element, data, event);
            }
        }
    }

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

    redirect(element, data, event){
        action.redirect(data.data);
    }

}

const managerModel = new ManagerModel();
export default managerModel;