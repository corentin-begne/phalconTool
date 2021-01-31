import js from "../../../../bower_components/cb-helpers/modules/js.mjs";
import action from "../../../../bower_components/cb-models/modules/action.mjs";
import manager from "../../../../bower_components/cb-models/modules/manager.mjs";
import $ from "../../../../bower_components/jQuery/src/jquery.js";
class [className]Manager{
    constructor(){
        this.data = manager.getVars();
        js.init(/*new Map([
            ["test", {
                "instance":test,
                "data":"test data"
            }]
        ])*/);
        js.add("manager", this);
        manager.init();     
    }

    init(){
        var that = this;
        return new Promise(run);

        function run(resolve, reject){

        }
    }
}
let [name]Manager = new [className]Manager();
export default [name]Manager;