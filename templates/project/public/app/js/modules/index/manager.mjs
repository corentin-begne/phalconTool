import js from "../../../../bower_components/cb-helpers/modules/js.mjs";
import action from "../../../../bower_components/cb-models/modules/action.mjs";
import manager from "../../../../bower_components/cb-models/modules/manager.mjs";
import $ from "../../../../bower_components/jQuery/src/jquery.js";
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

    test(element, data, event){
        console.log(data.get("title"));
    }
}
let indexManager = new IndexManager();
export default indexManager;