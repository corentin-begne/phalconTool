import $ from "../../jQuery/src/jquery.js";

class TagHelper{  

    constructor(){
    	this.key;
    }

    init(key){
    	this.key = key;
        init(window,document, "script", "dataLayer", key);
        const noscript = $(document.createElement("noscript"));
        const iframe = $(document.createElement("iframe"));
        iframe.attr({
        	src:"https://www.googletagmanager.com/ns.html?id="+key,
        	width:0,
        	height:0
        });
        iframe.css({
            display:"none",
            visibility:"hidden"
        });
        noscript.append(iframe);
        $("body").prepend(noscript);

        function init(w,d,s,l,i){
            w[l]=w[l]||[];
            w[l].push({
                "gtm.start":new Date().getTime(),
                event:"gtm.js"
            });
            var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),
            dl=l!=="dataLayer"?"&l="+l:"";
            j.async=true;j.src="https://www.googletagmanager.com/gtm.js?id="+i+dl;
            f.parentNode.insertBefore(j,f);
        };
    }
}
let tagHelper = new TagHelper();
export default tagHelper;