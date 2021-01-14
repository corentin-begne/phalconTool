class JsHelper{  

    constructor(){
        this.helpers = new Map();
    }

    init(helpers){
        this.helpers = helpers;
        for (const helper of helpers.values()) {
            if(!helper.instance.init){
                continue;
            }
            helper.instance.init(helper.data);
        }
    }

    add(name, instance){
        this.helpers.set(name, {instance});
    }

}
let jsHelper = new JsHelper();
export default jsHelper;