/*global ReadScrudManager */
var readScrudManager;
(function(){
    /** on document ready */
    $(document).ready(init);

    /**
     * @name main#initReadScrud
     * @event
     * @description initialize readScrud
     */
    function init(){
        new JsHelper({ManagerHelper:ReadScrudManager});
        readScrudManager = ReadScrudManager.getInstance();
    }
    
})();