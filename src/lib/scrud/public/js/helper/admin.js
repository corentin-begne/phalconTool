/*global extendSingleton, getSingleton, isDefined, ActionModel */
var AdminHelper;
(function(){
    "use strict";
    /**
    * @name AdminHelper
    * @description To make ajax call
    * @property {String} [basePath] Base path used for ajax call
    * @constructor
    */
    AdminHelper = function(){
        extendSingleton(AdminHelper);
        this.isAvailable = true;
        this.action = ActionModel.getInstance();
        this.popup = PopupHelper.getInstance();
    };

    /**
     * @member AdminHelper#getInstance
     * @description get the single class instance
     * @return {AdminHelper} the single class instance
     */
    AdminHelper.getInstance = function(){
        return getSingleton(AdminHelper);
    };

    AdminHelper.prototype.init = function() {
        var that = this;
        var lastPos;
        var lastCat;

        $(".link .arrow").dblclick(editLinkName);
        $(".category.menu h3").dblclick(editCategoryName);

        $(".sortable").sortable({
            items: ".link",
            revert: true,
            update: confirmSort,
            start: savePos
        });

        function editCategoryName(event){
            var category = $(this);
            
            wrapInput({
                container:category,
                title:category,
                value:category.text(),
                confirm:confirmEdit
            });

            event.preventDefault();

            function confirmEdit(value, cb){
                that.popup.confirm({
                    title:"Mise à jour",
                    text:"Voulez vous vraiment renommer cette categorie ?",
                    valid:valid
                });

                function valid(){
                    that.action.sendData("link/updateCategoryName", {
                        id:category.parent().attr("id"),
                        name:value},
                    cb);
                }
            }
        }

        function editLinkName(event){
            var link = $(this).parent();
            var title = link.find("a");
            
            wrapInput({
                container:link,
                title:title,
                value:title.text(),
                confirm:confirmEdit
            });

            event.preventDefault();

            function confirmEdit(value, cb){
                that.popup.confirm({
                    title:"Mise à jour",
                    text:"Voulez vous vraiment renommer cet article ?",
                    valid:valid
                });

                function valid(){
                    that.action.sendData("link/updateLinkName", {
                        id:link.attr("id"),
                        name:value},
                    cb);
                    
                }
            }
        }

        function wrapInput(data){
            if(data.container.find("input").length > 0){
                return false;
            }
            var value = "";
            var input = $("<input type='text' value='"+$.trim(data.value)+"' />");
            data.container.append(input);
            input.keyup(confirmEdit);
            input.focus();

            function confirmEdit(event){
                value = $(this).val();
                if(event.keyCode === 13 && value !== ""){
                    data.confirm(value, done);
                } else if(event.keyCode === 27){
                    input.remove();
                }
            }
            function done(result){
                that.popup.alert({
                    title:"Mise à jour",
                    text:result.title
                });
                data.title.text(value);
                input.remove();
            }
        }

        function savePos(event, ui){
            lastPos = ui.item.index();
            lastCat = ui.item.parent().attr("id");
        }

        function confirmSort(event, ui){

            if(!that.isAvailable || lastCat !== ui.item.parent().attr("id")){
                cancel();
                return false;
            }

            var pos = ui.item.index();
            that.popup.confirm({
                title:"Mise à jour",
                text:"Voulez vous déplacer l'article <b>"+ui.item.text()+"</b> de la position <b>"+lastPos+"</b> à la position <b>"+pos+"</b> ?",
                valid:valid,
                cancel:cancel
            });

            function cancel(){
                $(".sortable").sortable("cancel");
            }

            function valid(){
                that.isAvailable = false;
                that.action.sendData("link/sort", {
                    category_id: lastCat,
                    id:ui.item.attr('id'),
                    newPos:pos,
                    lastPos:lastPos
                }, that.updateDone.bind(that));
            }
        }
    };

    AdminHelper.prototype.updateLink = function(data){
        var that = this;
        if(!this.isAvailable){
            return false;
        }

        this.popup.confirm({
            title:"Mise à jour",
            text:"Voulez vous vraiment mettre à jour cet article ?",
            valid:valid
        });

        function valid(){
            that.isAvailable = false;
            that.action.sendData("link/update", {
                id:data.id,
                content:data.content
            }, that.updateDone.bind(that));
        }

    };

    AdminHelper.prototype.updateDone = function(data){
        this.isAvailable = true;
        this.popup.alert({
            title:"Mise à jour",
            text:data.title
        });
    };

})();