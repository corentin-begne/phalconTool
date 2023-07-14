import $ from "../../jQuery/src/jquery.js";

class GtagHelper{  

    constructor(){
    	this.key;
    }

    init(key){
    	this.key = key;
        $("head").prepend("<script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag('js', new Date());gtag('config', '"+key+"');</script>");
        init(window,document,"script","dataLayer",key);

        function init(w,d,s,l,i){
            var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s);
            j.async=true;j.src="https://www.googletagmanager.com/gtag/js?id="+i;
            f.parentNode.insertBefore(j,f);
        };
    }
}
const gtagHelper = new GtagHelper();
export default gtagHelper;