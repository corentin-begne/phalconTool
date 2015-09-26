/* global isDefined */
var PaginationHelper;
(function(){
    "use strict";
    /**
     * @name PaginationHelper
     * @constructor
     * @property {Integer} nbPage Number of total page.
     * @property {Function} getPageContent The callback.
     * @property {DomElement} container Container where to append pagination.
     * @property {Integer} range The number of page to show before and after the current page.
     * @property {Integer} limit The limit of item to display.
     * @property {Object} data Data to init pagination.
     * @property {String} html Pagination html.
     */
    PaginationHelper = function(params){
        this.nbPage = Number(params.nbPage);
        if (this.nbPage <= 1) {
            return false;
        }
        this.getPageContent = params.cb;
        this.container = params.container;
        this.range = 1;
        this.html = "<div align='center' class='blockPagination'>"+
                        "<span class='pagination'>"+
                            "<span id='first'><<</span>"+
                            "<span id='prev'><</span>"+
                            "<span id='next'>></span>"+
                            "<span id='last'>>></span>"+
                        "</span>"+
                    "</div>";
        this.init((isDefined(params.currentPage) ? params.currentPage : 1));
    };

    /** 
     * @description Initialize DOMElement and event needed
     * @method PaginationHelper#init
     * @param {Integer} currentPage Current pagination page.
     */
    PaginationHelper.prototype.init = function(currentPage){
        var that = this;
        var currentHtml = $(that.html);
        var next = currentHtml.find("#next");
        var min = (currentPage === 1) ? 1 : currentPage - that.range;
        var max = (currentPage === that.nbPage) ? currentPage : currentPage + that.range;

        checkRange();

        for (var i = min; i <= max; i++) {
            next.before("<span"+((i === currentPage) ? " class='active'" : '')+">"+i+"</span>");
        }

        checkNavigation();
        currentHtml.find(".pagination span:not(.disabled):not(.active)").mousedown(updateCurrentPage);
        that.container.empty();
        that.container.append(currentHtml);

        /**
         * @method PaginationHelper#checkRange
         * @private
         * @description Initialize the min & max values for range.
         */
        function checkRange(){
            min = (currentPage === that.nbPage) ? currentPage - that.range * 2 : min;
            max = (currentPage === 1) ? currentPage + that.range * 2 : max;
            if(min < 1){
                min = 1;
            }
            if(max > that.nbPage){
                max = that.nbPage;
            }
        }

        /**
         * @method PaginationHelper#checkNavigation
         * @private
         * @description Enable or disabled pagination arrow / element.
         */
        function checkNavigation(){
            if (currentPage === 1) {
                currentHtml.find("#first, #prev").addClass("disabled");
                currentHtml.find("#next, #last").removeClass("disabled");
                return false;
            }

            if (currentPage === that.nbPage) {
                currentHtml.find("#first, #prev").removeClass("disabled");
                currentHtml.find("#next, #last").addClass("disabled");
                return false;
            }
        }

        /**
         * @event PaginationHelper#updateCurrentpage
         * @description Update the current page value and call the callback function.
         */
        function updateCurrentPage() {
            switch ($(this).attr("id")) {
                case "first":
                    currentPage = 1;
                break;
                case "prev":
                    currentPage--;
                break;
                case "next":
                    currentPage++;
                break;
                case "last":
                    currentPage = that.nbPage;
                break;
                default:
                    currentPage = Number($(this).text());
            }

            that.getPageContent(currentPage);
        }
    };
})();