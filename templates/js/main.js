/*global [className]Manager, require */
var [name]Manager;
(function(){
    require.config({
        baseUrl: "/",
        urlArgs: "v="+($("body").is("[version]") ? $("body").attr("version") : (new Date()).getTime())
    });
    require(["bower_components/jquery/dist/jquery"], ready);

    function ready(){
        /** on document ready */
        $(document).ready(init);

        /**
         * @name main#init[className]
         * @event
         * @description initialize [name]
         */
        function init(){                
            require(["bower_components/cb-helpers/js"], loaded);

            function loaded(){
                new JsHelper();
                [name]Manager = [className]Manager.getInstance();            
            }      
        }
    }
})();