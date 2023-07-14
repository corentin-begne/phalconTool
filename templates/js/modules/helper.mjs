/**
 * @name [className]Helper
 * @class
 * @hideconstructor
 * @description [className]s helper - singleton
 */
class [className]Helper{  

    constructor(){
        
    }

    /**
     * @method [className]#init
     * @param {*} [data] Data to use for initialiaztion
     * @description Initialized from manager
     */
    init(data){
        console.log(data);
    }

}
const [name]Helper = new [className]Helper();
export default [name]Helper;