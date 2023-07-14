import js from "../../../../../bower_components/cb-helpers/modules/js.mjs";
import action from "../../../../../bower_components/cb-models/modules/action.mjs";
import manager from "../../../../../bower_components/cb-models/modules/manager.mjs";
import $ from "../../../../../bower_components/jQuery/src/jquery.js";
/**
 * @name LoginUserManager
 * @class
 * @hideconstructor
 * @description Manager of the user login action, initilialize manager
 */
class LoginUserManager{
    constructor(){
    	/*js.init(new Map([
			["test", {
				"instance":test,
				"data":"test data"
			}]
		]));*/
    	js.add("manager", this);
    	manager.init();   	
    }

	/**
	 * @method LoginUserManager#connect
	 * @description Connect user to application
	 * @param {DOMElement} element 
	 */
    async connect(element){
        var that = this;
        $(element).find("input").removeClass("error");
        $(element).find(".error").addClass("none");                
        const result = await action.sendData("user/connect", $(element).serialize());
		if(result.success){
			window.location.reload();
			return;
		}
		$(element).find("input").addClass("error");
		$(element).find(".error").removeClass("none");            
		setTimeout(focus, 0);

		function focus(){
			$(element).find("input:eq(0)").focus();
		}
    }
}
let loginUserManager = new LoginUserManager();
export default loginUserManager;