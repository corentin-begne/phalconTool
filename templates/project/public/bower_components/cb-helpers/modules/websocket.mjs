/*global extendSingleton, getSingleton, isDefined, ifNull */
var WebsocketHelper;
(function(){
    "use strict";
    /**
    * @name WebsocketHelper
    * @description Manage socket events and connection
    * @property {Object} [events] Default events
    * @property {Object} [userEvents] User events
    * @property {Function} [cbConnected] Connection callback
    * @constructor
    */
    WebsocketHelper = function(cb){      
        extendSingleton(WebsocketHelper);
        this.events = {
            onopen: this.connected.bind(this),
            onerror: this.error.bind(this),
            onclose: this.disconnected.bind(this),
            onmessage: this.recv.bind(this)
        };        
        this.userEvents = {};       
        this.cbConnected;
        if(cb){
            cb(this);
        }
    };

    /**
     * @member WebsocketHelper#getInstance
     * @description get the single class instance
     * @return {WebsocketHelper} the single class instance
     */
    WebsocketHelper.getInstance = function(cb){
        if(isDefined(cb)){
            getSingleton(WebsocketHelper, cb);
        } else {
            return getSingleton(WebsocketHelper);
        }
    };

    /**
     * @method WebsocketHelper#init
     * @param  {Object} [events] Object containing all events to be set
     * @description Initialize socket events
     */
    WebsocketHelper.prototype.init = function(events){
        var that = this;
        
        $.each(events, addEvent);

   //     $(window).unbind("beforeunload");
        window.addEventListener("beforeunload", disconnect);
        /**
         * @event WebsocketHelper#disconnect
         * @description Disconnect socket on beforeunload event
         */
        function disconnect(e) {               
            if (isDefined(that.socket)){
                that.socket.close();         
            }
            // Cancel the event as stated by the standard.
            e.preventDefault();
        }

        /**
         * @description Add socket event
         * @method WebsocketHelper#addEvent
         * @private
         * @param  {String}   [event] Socket event name
         * @param  {Function} [cb]    Callback of the event
         */
        function addEvent(name, cb){
            that.socket[name] = cb;
        }
    };

    /**
     * @method WebsocketHelper#addEvents
     * @description Add a list of event to user 
     * @param {Object} [events] Events to add
     */
    WebsocketHelper.prototype.addEvents = function(events){        
        $.each(events, this.addEvent.bind(this));
    };

    /**
     * @method  WebsocketHelper#addEvent
     * @description Add an event to user
     * @param {String} [name]  The name of the event
     * @param {Function} [event] Callback associated to the event
     */
    WebsocketHelper.prototype.addEvent = function(name, event){
        this.userEvents[name] = event;
    };

    WebsocketHelper.prototype.removeEvent = function(name){
        if(this.userEvents[name]){
            delete this.userEvents[name];
        }        
    };

    WebsocketHelper.prototype.removeEvents = function(){
        this.userEvents = {};        
    };

    /**
     * @description Send data by event
     * @method WebsocketHelper#send
     * @param  {String} [event] Event name to use
     * @param  {Object} [data]  Data to send
     */
    WebsocketHelper.prototype.send = function(event, data){
        data = ifNull(data, {});       
        this.socket.send(JSON.stringify({
           type: event,
           data: data
        }));
    };

    /**
     * @description Connect to the server
     * @param  {String}   url  Connection url
     * @param  {Object}   data Data to send to the server
     * @param  {Function} cb   Connection callback
     */
    WebsocketHelper.prototype.connect = function(url, data, cb){
        console.log("connecting to socket server ...");
        this.cbConnected = cb;
        try{
            this.socket = new WebSocket(url+(!isDefined(data) ? "" : "?"+encodeURIComponent(JSON.stringify(data))));
            this.init(this.events); 
        } catch(exception){
            console.error(exception);
        }
    };

    /**
     * @description Receive all event and execute the user callback associated with
     * @event WebsocketHelper#recv
     * @param  {Object} event Data event send by the server
     */
    WebsocketHelper.prototype.recv = function(event){
        var data = ifNull(event.data, $.parseJSON(event.data), {});
        if(!isDefined(this.userEvents[data.type])){
            console.error("socket event received but not defined", data);
            return false;
        }
        this.userEvents[data.type](data.data);
    };

    /**
     * @description Triggered when the user is connected to the server
     * @event WebsocketHelper#connected
     * @param  {Object} event Data event send by the server
     */
    WebsocketHelper.prototype.connected = function(event){
        console.log("connected to socket server", event);
        if(!isDefined(this.cbConnected)){
            return false;
        }
        this.cbConnected(event);
    };

    /**
     * @description Trigeered when socket is disconnected
     * @event WebsocketHelper#disconnected
     */
    WebsocketHelper.prototype.disconnected = function(){
        console.error("disconnected from socket server");
    };

    /**
     * @description Triggered when socket can't connect to the server
     * @event WebsocketHelper#error
     */
    WebsocketHelper.prototype.error = function(){
        console.error("can't connect to socket server");
    };

})();