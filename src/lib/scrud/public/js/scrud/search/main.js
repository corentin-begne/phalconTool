/*global SearchScrudManager */
var searchScrudManager;
(function(){
    "use strict";
    /** on document ready */
    $(document).ready(init);

    /**
     * @name main#initSearchScrud
     * @event
     * @description initialize searchScrud
     */
    function init(){
        new JsHelper();
        ManagerModel.getInstance().init();
        searchScrudManager = SearchScrudManager.getInstance();
    }
    
})();