/*global ReadScrudManager */
var readScrudManager;
(function(){
    "use strict";
    /** on document ready */
    $(document).ready(init);

    /**
     * @name main#initReadScrud
     * @event
     * @description initialize readScrud
     */
    function init(){
        new JsHelper();
        ManagerModel.getInstance().init();
        readScrudManager = ReadScrudManager.getInstance();
    }
    
})();