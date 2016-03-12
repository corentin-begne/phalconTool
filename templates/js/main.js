/*global [className]Manager, require */
var [name]Manager;
(function(){
    require(["bower_components/jquery/dist/jquery.min"], ready);

    function ready(){
        /** on document ready */
        $(document).ready(init);

        /**
         * @name main#init[className]
         * @event
         * @description initialize [name]
         */
        function init(){
            require(["bower_components/cb-helpers/js.min"], loaded);

            function loaded(){
                new JsHelper();
                [name]Manager = [className]Manager.getInstance();
            }      
        }
    }
})();