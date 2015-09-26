/*global ActionModel */
var SearchScrudManager;
(function(){
    /**
    * @class TemplateManager
    * @constructor
    * @property {ActionModel} action Instance of ActionModel
    * @description  Manage template
    */
    SearchScrudManager = function SearchScrudManager(){
        extendSingleton(SearchScrudManager);
        this.basePath = "scrud/";
        this.timer;
        this.action = ActionModel.getInstance();
        this.manager = ManagerModel.getInstance();
    };

    SearchScrudManager.getInstance = function(){
        return getSingleton(SearchScrudManager);
    };

    SearchScrudManager.prototype.findValue = function(value, cb, container){
        var search = $(container).parent();
       /* var data = {
            field:search.find("#field :selected").val(),
            type:search.find(".type:not(.hide) :selected").val(),
            value:value
        };
        var model = search.find("#field :selected").parent().attr("label");*/
        var model = search.find("#field :selected").parent().attr("label");
        var data = {
            field:search.find("#field :selected").val(),
            value:value
        };
        ActionModel.getInstance().apiNoLoad(model+"/complete", data, check);

        function check(data){
            cb(data.data);
        }
    };

    SearchScrudManager.prototype.selectValue = function(element, container){
        var search = $(container).parent();
        var searchContainer = $(container).parent().parent();
        var filter = searchContainer.find(".filter.hide").clone(true, true);
        filter.find("#field").text(search.find("#field :selected").val());
        filter.find("#type").text(search.find(".type:not(.hide) :selected").val());
        filter.find("#value").text($(element).text());
        searchContainer.append(filter.removeClass("hide"));
        search.find("#field option:eq(0)").attr("selected", true).change();
    };

    SearchScrudManager.prototype.removeFilter = function(element){
        $(element).parent().remove();
    };

    SearchScrudManager.prototype.selectField = function(element){
        var that = this;
        var search = $(element).parentsUntil(".search").parent();
        var typeContainer = search.find(".typeContainer");
        var input = search.find("#search");
        var model = $(element).find(':selected').parent().attr("label");
        var field = $(element).find(':selected').val();    

        typeContainer.find("select").addClass("hide");
        clearTimeout(this.timer);
        if(field === ""){
            input.attr('disabled', '').val("");
            return false;
        }
        this.timer = setTimeout(getType, 200);

        function getType(){
            that.action.api(model+'/getType', {field:field}, showSelectType);
        }

        function showSelectType(data){                       
            input.removeAttr('disabled').val("");
            var selectType;
            var type;
            switch(data.data){
                case 'int':
                case 'bigint':
                case 'tinyint':
                case 'float':
                case 'double':
                    selectType = 'numeric';
                    type = 'number';
                    break;
                default :
                    type='text';
                    selectType = 'text';
                    break;
            }
            typeContainer.find("#"+selectType).removeClass("hide");
            typeContainer.find("#"+selectType+" option:eq(0)").attr("selected", true);
            input.attr("type", type);

            setTimeout(asyncFocus, 0);

            function asyncFocus(){
                input.focus();
            }
        }
    };
    
})();