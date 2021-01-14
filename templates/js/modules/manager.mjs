import js from "../../../../bower_components/cb-helpers/modules/js.mjs";
import action from "../../../../bower_components/cb-models/modules/action.mjs";
import manager from "../../../../bower_components/cb-models/modules/manager.mjs";
import $ from "../../../../bower_components/jQuery/src/jquery.js";
class [className]Manager{
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
}
let [name]Manager = new [className]Manager();
export default [name]Manager;