/*global extendSingleton, getSingleton, isDefined */
var PopupHelper;
(function(){
    "use strict";
    /**
    * @name PopupHelper
    * @description To make ajax call
    * @property {String} [basePath] Base path used for ajax call
    * @constructor
    */
    PopupHelper = function(){        
        extendSingleton(PopupHelper);
        this.savedHTML;
    };

    /**
     * @member PopupHelper#getInstance
     * @description get the single class instance
     * @return {PopupHelper} the single class instance
     */
    PopupHelper.getInstance = function(){
        return getSingleton(PopupHelper);
    };

    PopupHelper.prototype.alert = function(data){
        var that = this;
        $("<div id='popup'></div>").html(data.text).dialog({
            title: data.title,
            resizable: false,
            modal: true,
            close: valid,
            buttons: {
                Ok: valid
            }
        });

        function valid(){
            that.savedHTML = $("#popup").clone(true, true);
            $("#popup").dialog('destroy').remove();
            if(isDefined(data.valid)){
                data.valid();
            }
        }
    };

    PopupHelper.prototype.confirm = function(data){
        var that = this;
        $("<div id='popup'></div>").html(data.text).dialog({
            title: data.title,
            resizable: false,
            modal: true,
            close: cancel,
            buttons: {
                Ok: valid,
                Cancel: cancel
            }
        });

        function cancel(){
            that.close();
            if(isDefined(data.cancel)){
                data.cancel();
            }
        }

        function valid(){
            that.savedHTML = $("#popup").clone(true, true);
            $("#popup").dialog('destroy').remove();
            data.valid();
        }
    };

    PopupHelper.prototype.close = function(){
        this.savedHTML = $("#popup").clone(true, true);
        $("#popup").dialog("close");
        $("#popup").dialog('destroy').remove();
    };

})();