/*global IndexManager, require, JsHelper */
(function(){    
    require.config({
        baseUrl: "/",
    });
    require(["bower_components/jquery/dist/jquery"], ready);

    function ready(){
        /** on document ready */
        $(document).ready(init);

        /**
         * @name main#initIndex
         * @event
         * @description initialize index
         */
        function init(){             
            require.config({
                urlArgs: "v="+($("body").is("[version]") ? $("body").attr("version") : (new Date()).getTime())
            });   
            require([
                "bower_components/cb-helpers/js",
                "frontend/js/index/manager"
            ], loaded);

            function loaded(){
                new JsHelper();
                IndexManager.getInstance();            
            }      
        }
    }
})();