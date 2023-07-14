import js from "../../../../bower_components/cb-helpers/modules/js.mjs";
import action from "../../../../bower_components/cb-models/modules/action.mjs";
import manager from "../../../../bower_components/cb-models/modules/manager.mjs";
import $ from "../../../../bower_components/jQuery/src/jquery.js";
/**
 * @name IndexManager
 * @class
 * @hideconstructor
 * @description Manager index action, initilialize manager
 */
class IndexManager{
    constructor(){
    	/*js.init(new Map([
			["test", {
				"instance":test,
				"data":"test data"
			}]
		]));*/
    	js.add("manager", this);
    	const data = manager.getVars();
    	console.log(data.get("nb"));
    	manager.init();   	
    }

	/**
	 * @method IndexManager#test
	 * @description Mapping test function
	 * @param {DOMElement} [element] DOM element binded
	 * @param {Map.<String, String>} [data] Data in binded element
	 * @param {Event} [event] Event of the action
	 */
    test(element, data, event){
        console.log(data.get("title"));
    }
}
let indexManager = new IndexManager();
export default indexManager;