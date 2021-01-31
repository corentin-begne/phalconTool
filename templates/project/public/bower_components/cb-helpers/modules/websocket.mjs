import $ from "../../jQuery/src/jquery.js";

class WebsocketHelper{  

    constructor(){
        this.userEvents = {};
        this.instance;
    }

    connect(url, data){
        var that = this;
        return new Promise(run);

        function run(resolve, reject){
            try{
                that.instance = new WebSocket(url+(!data ? "" : "?"+encodeURIComponent(JSON.stringify(data))));
                init(); 
            } catch(exception){
                console.error("can't connect to websocket server");
                reject(exception);
            }

            function init(){
                let events = new Map([
                    ["onopen", onopen],
                    ["onerror", onerror],
                    ["onclose", onclose],
                    ["onmessage", onmessage]
                ]);
        
                for(const [name, cb] of events){
                    that.instance[name] = cb;
                }

                $(window).on("beforeunload", disconnect);

                function disconnect(event) {                           
                    if(that.instance){
                        that.instance.close();                
                    }
                    event.preventDefault();
                }
            }

            function onmessage(event){
                if(!event.data){
                    return;
                }
                var data = JSON.parse(event.data);
                if(!that.userEvents[data.type]){
                    console.error("socket event received but not defined", data);
                    return false;
                }
                that.userEvents[data.type](data.data);
            }            

            function onopen(data){
                console.log("connected to websocket server");
                resolve(data);
            }

            function onerror(data){
                console.error("can't connect to websocket server");
                reject(data);
            }

            function onclose(data){
                console.error("disconnected from websocket server");
                reject(data);
            }
        }
    }

    setEvents(events = {}){
        Object.assign(this.userEvents, events);        
    }

    setEvent(name = "", event = function(){}){
        this.userEvents[name] = event;
    }

    deleteEvent(name = ""){
        if(this.userEvents[name]){
            return;
        }        
        delete this.userEvents[name];
    }

    clearEvents(){
        this.userEvents = {};        
    }

    send(event = "", data = {}){      
        this.instance.send(JSON.stringify({
           type: event,
           data: data
        }));
    }

}
const websocketHelper = new WebsocketHelper();
export default websocketHelper;