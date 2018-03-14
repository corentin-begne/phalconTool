/*global [className]Manager, require, JsHelper */
(function(){    
    require.config({
        baseUrl: "/",
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
            require.config({
                urlArgs: "v="+($("body").is("[version]") ? $("body").attr("version") : (new Date()).getTime())
            });   
            require([
                "bower_components/cb-helpers/js",
                "[app]/js/[path]manager"
            ], loaded);

            function loaded(){
                new JsHelper();
                [className]Manager.getInstance();            
            }      
        }
    }
})();