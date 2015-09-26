/*global isDefined */
var AutocompletionHelper;
(function(){
    "use strict";
    /**
     * @name AutocompletionHelper
     * @constructor
     * @property {Function}     [find]                      Callback trigger on typing timeout to get data
     * @property {Function}     [select]                    Callback trigger when a result is selected
     * @property {Integer}      [_speedTyping = 250]        Typing speed timeout to send search
     * @property {Integer}      [_fadeTyping = 250]         Result container fade speed
     * @property {String}       [placeholder=""]            Default input text
     * @property {DOMElement}   [container]                 Container to append the input
     * @param    {Object}       [params]                    Object containing DOMElement and cb needed
     */
    AutocompletionHelper = function(params){
        this._typingSpeed = 300;
        this._fadeSpeed = 150;
        this.class = isDefined(params.class) ? params.class : "";
        this.restrict = isDefined(params.restrict) ? params.restrict : false;
        this.placeholder = isDefined(params.placeholder) ? params.placeholder : "";
        this.attr = isDefined(params.attr) ? params.attr : "";
        this.id = isDefined(params.id) ? "id='"+params.id+"'" : "";
        this.container = params.container;
        this.find = params.cbFind;
        this.blur = params.cbBlur;
        this.select = params.cbSelect;
        this.init();
    };

    /** 
     * @description Initialize DOMElement and event needed
     * @method AutocompletionHelper#init
     */
    AutocompletionHelper.prototype.init = function(){
        var that = this;
        var isAvailable = true;
        var isCancel = false;
        var timer;

        /** initialize container with input and result container */
        this.container.append(
            "<div>"+
                "<input class='"+this.class+"' "+this.attr+" "+this.id+" type='text' />"+
            "</div>"+
            "<div class='resultContainer'></div>");
        var input = this.container.find("input");
        input.attr("placeholder", this.placeholder);
        var resultContainer = this.container.find(".resultContainer");
        var searchIcon = this.container.find(".icon-search");

        /** assign events */
        input.keydown(selectResult);
        input.keyup(typing);
        input.blur(blur);
        input.focus(showResultContainer);

        function blur(){
            clearTimeout(timer);
            hideResultContainer();            
            if($(this).val() !== ""){                                       
                if(isDefined(that.blur)){
                    that.blur(resultContainer.find(".result:eq(0)"));                
                } 
                $(this).val("");
                resultContainer.empty();             
            }
        }

        /**
         * @event AutocompletionHelper#selectResult
         * @param  {Object} [event] Mousedown event
         * @description Select a result manually (keyboard)
         */
        function selectResult(event){
            isCancel = false;   
            var results = resultContainer.find(".result");
            var resultSelected = resultContainer.find(".result.selected");
            var value = $(this).val();
            if(results.length !== 0){
                switch(event.keyCode){
                    /** prev */
                    case 38 :
                        isCancel = true;
                        changeResult("prev");
                        break;
                    /** next */
                    case 40 :
                        isCancel = true;
                        changeResult("next");
                        break;
                    /** select */
                    case 13 :
                        if(resultSelected.length !== 0){
                            isCancel = true; 
                            resultSelected.mousedown();
                        }
                        break;
                }
                if(isCancel){
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                }
            }

            if(!that.restrict){
                if (event.keyCode === 13 && value !== "") {
                    that.select($("<div>"+value+"</div>"), that.container);
                    $(this).val("");
                    resultContainer.hide().empty();
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                }
            }

            /**
             * @method AutocompletionHelper#changeResult
             * @private
             * @param  {String} [direction] Direction to next selection
             * @description Change result selection manually
             */
            function changeResult(direction){
                var newResult;
                if(resultSelected.length === 0){
                    newResult = resultContainer.find(".result:"+((direction === "prev") ? "last" : "first"));
                }else{
                    newResult = resultSelected[direction]();
                }
                if(newResult.length > 0){
                    resultSelected.removeClass("selected");
                    newResult.addClass("selected");
                }

            }
        }

        /**
         * @event AutocompletionHelper#typing
         * @description Send input val on typing to autocomplete
         */
        function typing(event){
            if(!isAvailable || isCancel || event.keyCode === 37 || event.keyCode === 39){
                return false;
            }

            if(input.val() === ""){
                clearTimeout(timer);
                resultContainer.fadeOut(that._fadeSpeed, resultContainer.empty);
                return false;
            }
            
            clearTimeout(timer);
            timer = setTimeout(typingTimeout, that._typingSpeed);

            /**
             * @method AutocompletionHelper#typingTimeout
             * @private
             * @description Send val to search on timeout
             */
            function typingTimeout(){
                isAvailable = false;
              //  searchIcon.removeClass("icon-search").addClass("icon-loading");
                resultContainer.empty();
                that.find(input.val(), updateResultContainer, that.container);

                /**
                 * @method  AutocompletionHelper#updateResultContainer
                 * @private
                 * @description Fill in the result container with data
                 * @param  {Array} [data] HTML content list 
                 */
                function updateResultContainer(data){
                    data.forEach(addResult);

                    /**
                     * @method AutocompletionHelper#addResult
                     * @private
                     * @description Add a result in the result container
                     */
                    function addResult(element){
                        var result = $(element);

                        result.mouseenter(removeSelected);
                        result.mousedown(selectElement);
                        resultContainer.append(result);

                        function selectElement() {
                            that.select($(this), that.container);
                            input.val("");
                            resultContainer.hide().empty();
                        }

                        /**
                         * @event AutocompletionHelper#removeSelected
                         * @description Remove manual selection
                         */
                        function removeSelected(){
                            resultContainer.find(".selected").removeClass("selected");
                        }
                        
                    }

                    if(data.length > 0 && input.val() !== ""){
                        /** show result container */
                        resultContainer.fadeIn(that._fadeSpeed);
                    }else{
                        hideResultContainer();
                    }
                  //  searchIcon.removeClass("icon-loading").addClass("icon-search");
                    isAvailable = true;
                }
            }

        }

        /**
         * @event AutocompletionHelper#hideResultContainer
         * @description Fade out result container on input blur
         */
        function hideResultContainer(){
            resultContainer.fadeOut(that._fadeSpeed);
        };

        /**
         * @event AutocompletionHelper#showResultContainer
         * @description Fade in result container on input focus
         */
        function showResultContainer(){
            if(resultContainer.find(".result").length > 0 && input.val() !== ""){
                resultContainer.fadeIn(that._fadeSpeed);
            }
        }        
    };
})();