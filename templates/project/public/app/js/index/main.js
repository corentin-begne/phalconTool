/*global IndexManager, require */
var indexManager;
(function(){
    require(["bower_components/jquery/dist/jquery.min"], ready);

    function ready(){
        /** on document ready */
        $(document).ready(init);

        /**
         * @name main#initIndex
         * @event
         * @description initialize index
         */
         function init(){
            require(["bower_components/cb-helpers/js.min"], loaded);

            function loaded(){
                new JsHelper();
                indexManager = IndexManager.getInstance();
            }
        }
    }
})();