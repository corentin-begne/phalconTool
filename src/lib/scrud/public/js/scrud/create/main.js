/*global CreateScrudManager */
var createScrudManager;
(function(){
    /** on document ready */
    $(document).ready(init);

    /**
     * @name main#initCreateScrud
     * @event
     * @description initialize createScrud
     */
    function init(){
        new JsHelper({ManagerHelper:CreateScrudManager});
        createScrudManager = CreateScrudManager.getInstance();
    }
    
})();