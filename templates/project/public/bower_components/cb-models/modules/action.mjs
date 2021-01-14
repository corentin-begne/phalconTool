import { default as action } from "../../cb-helpers/modules/action.mjs";

class ActionModel{

    constructor(){

    }

    send(path, data, options = {}){
        Object.assign(options, {
            noload:options.noload || false, 
            dataType:options.dataType || "json", 
            form:options.form || false
        });
        return action.execute(data ||(options.form ? new FormData() : {}), Object.assign({
            type: "post",
            action: path
        }, options));
    }

    sendHtml(path, data){
        return this.send(path, data, {dataType:"text"});
    }

    sendFormHtml(path, data){
        return this.send(path, data, {dataType:"text", form:true});
    }

    sendHtmlNoLoad(path, data){
        return this.send(path, data, {dataType:"text", noload:true});
    }

    sendFormHtmlNoLoad(path, data){
        return this.send(path, data, {dataType:"text", noload:true, form:true});
    }

    sendData(path, data){
        return this.send(path, data);
    }

    sendDataNoLoad(path, data){
        return this.send(path, data, {noload:true});
    }

    sendFormDataNoLoad(path, data){
        return this.send(path, data, {noload:true, form:true});
    }

    sendFormData(path, data){
        return this.send(path, data, {form:true});
    }

    apiForm (path, data){
        return this.sendForm("api/"+path, data);
    }

    api(path, data){
        return this.sendData("api/"+path, data);
    }

    apiNoLoad(path, data){
        return this.sendDataNoload("api/"+path, data);
    }    
    
    redirect(data){
        action.redirect(data);
    };

}

let actionModel = new ActionModel();
export default actionModel;