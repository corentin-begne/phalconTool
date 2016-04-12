/*global [className]Manager, require */
var [name]Manager;
(function(){
    require(["/bower_components/jquery/dist/jquery.min.js"], ready);

    function ready(){
        /** on document ready */
        $(document).ready(init);

        /**
         * @name main#init[className]
         * @event
         * @description initialize [name]
         */
        function init(){
            require(["/bower_components/cb-helpers/js.min.js", "/bower_components/cb-models/manager.min.js"], loaded);

            function loaded(){
                new JsHelper();
                ManagerModel.getInstance(loadedManager).init();

                function loadedManager(instance){
                    instance.init();
                    [name]Manager = [className]Manager.getInstance();
                }               
            }      
        }
    }
})();