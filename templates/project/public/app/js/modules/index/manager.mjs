import js from "../../../../bower_components/cb-helpers/modules/js.mjs";
//import test from "../helper/test.mjs";
import action from "../../../../bower_components/cb-models/modules/action.mjs";
import manager from "../../../../bower_components/cb-models/modules/manager.mjs";
import $ from '../../../../bower_components/jQuery/src/jquery.js';
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
        if (window && window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.unityControl) {
            window.Unity = {
                call: call
            }
        }

        function call(msg){
            window.webkit.messageHandlers.unityControl.postMessage(msg);
        }
    	/*console.log($(element), data, event);
    	action.sendDataNoLoad("test", {test:"test"}).then(success).catch(error);

    	function success(data){
    		console.log(data);
    	}

    	function error(data){
    		console.log(data);
    	}*/
    }
}
let indexManager = new IndexManager();
export default indexManager;