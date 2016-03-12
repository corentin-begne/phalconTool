/*global IndexIndexManager */
var indexIndexManager;
(function(){
    "use strict";
    /** on document ready */
    $(document).ready(init);

    /**
     * @name main#initIndexIndex
     * @event
     * @description initialize indexIndex
     */
    function init(){
        new JsHelper();
        indexIndexManager = IndexIndexManager.getInstance();
    }
    
})();