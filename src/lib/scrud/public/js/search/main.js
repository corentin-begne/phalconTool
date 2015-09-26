/*global SearchScrudManager */
var searchScrudManager;
(function(){
    /** on document ready */
    $(document).ready(init);

    /**
     * @name main#initSearchScrud
     * @event
     * @description initialize searchScrud
     */
    function init(){
        new JsHelper({ManagerHelper:SearchScrudManager});
        searchScrudManager = SearchScrudManager.getInstance();
    }
    
})();