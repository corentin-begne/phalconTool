/*global ActionModel, ucfirst, isDefined, extendSingleton, getSingleton, InterfaceHelper */
var ManagerModel;
(function(){
    /**
    * @name ManagerModel
    * @constructor
    * @property {ActionModel} [action = new ActionModel()] Instance of ActionModel
    * @description Manage global manager functions and actions
    */
    ManagerModel = function(){        
        extendSingleton(ManagerModel);
        this.action = ActionModel.getInstance();
        this.container = $("body");
    };

    /**
     * @member ManagerModel#getInstance
     * @description get the single class instance
     * @return {ManagerModel} the single class instance
     */
    ManagerModel.getInstance = function(){
        return getSingleton(ManagerModel);
    };

    ManagerModel.prototype.init = function(manager, container) {
        var that = this;       
        this.container = isDefined(container) ? container : $('body');         
        var selectors = {
            ".action":action,
            ".actionAutocompletion":autocompletion
        };
        $.each(selectors, addMouseDownEvent);

        function addMouseDownEvent(selector, fn){            
            var elements = that.container.find(selector);
            elements.each(addEvent);
            
            function addEvent(i, element){
                var actionType = $(element).attr("actionType");
                if(!isDefined(actionType)){
                    actionType = "mousedown";                    
                }
                if(actionType === 'init'){
                    fn(element);
                } else {
                    $(element).unbind(actionType);
                    $(element).bind(actionType, fn);
                }
            }
        }

        function autocompletion(element){
            var data = $.parseJSON($(element).attr("data"));
            data.container = $(element);
            data.cbFind = manager[data.cbFind];
            data.cbSelect = manager[data.cbSelect];
            data.cbBlur = manager[data.cbBlur];
            new AutocompletionHelper(data);
        }

        function action(event){
            var name = $(this).attr("actionName");
            if(isDefined(manager[name])){ 
                manager[name]($(this),event);
            }
        }
    };
})();