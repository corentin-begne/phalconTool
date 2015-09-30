/*global CreateScrudManager */
var createScrudManager;
(function(){
    "use strict";
    /** on document ready */
    $(document).ready(init);

    /**
     * @name main#initCreateScrud
     * @event
     * @description initialize createScrud
     */
    function init(){
        new JsHelper();
        ManagerModel.getInstance().init();
        createScrudManager = CreateScrudManager.getInstance();
    }
    
})();