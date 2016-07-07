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
            requirejs.onError = function (err) {
                throw err;
            };        
            require.config({
                urlArgs: "v="+($("body").is("[version]") ? $("body").attr("version") : (new Date()).getTime())
            });   
            require(["/bower_components/cb-helpers/js.min.js"], loaded);

            function loaded(){
                new JsHelper();
                [name]Manager = [className]Manager.getInstance();            
            }      
        }
    }
})();