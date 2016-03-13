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
            require(["bower_components/cb-helpers/js.min", "bower_components/cb-models/manager.min"], loaded);

            function loaded(){
                new JsHelper();
                ManagerModel.getInstance().init();
                [name]Manager = [className]Manager.getInstance();
            }      
        }
    }
})();