/*global [className]Manager */
var [name]Manager;
(function(){
    /** on document ready */
    $(document).ready(init);

    /**
     * @name main#init[className]
     * @event
     * @description initialize [name]
     */
    function init(){
        new JsHelper({ManagerHelper:[className]Manager});
        [name]Manager = [className]Manager.getInstance();
    }
    
})();