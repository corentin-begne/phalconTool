class JsHelper{  

    constructor(){
        this.helpers = new Map();
    }

    init(helpers = new Map()){
        var that = this;
        this.helpers = helpers;
        for (const helper of helpers.values()) {
            if(!helper.instance.init){
                continue;
            }
            helper.instance.init(helper.data);
        }
        Reflect.ownKeys(Reflect.getPrototypeOf(this)).forEach(addGlobalFunction);

        function addGlobalFunction(name){
            if(name !== "init" && name !== "add" && name !== "constructor"){
                if(name.indexOf("is") === 0){
                    window[name] = that[name]();
                } else {
                    window[name] = that[name];
                }                
            }
        }
    }

    add(name, instance){
        this.helpers.set(name, {instance});
    }

    isMobile(){
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    }

    isTablet(){
        return /(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i.test(navigator.userAgent.toLowerCase());
    }

    isApple(){
        return /(iPad|iPhone|iPod|Mac)/g.test(navigator.userAgent);
    }

    ucfirst(str){
        return str.charAt(0).toUpperCase()+str.substr(1);
    }

}
const jsHelper = new JsHelper();
export default jsHelper;