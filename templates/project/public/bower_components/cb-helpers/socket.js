/*global B, extendSingleton, getSingleton, UserHelper, isDefined, VoiceHelper */
var SocketHelper;
(function(){
    "use strict";
    /**
    * @name SocketHelper
    * @description To define socket events
    * @property {Object} [_socket = B.bClient._socket] Client socket instance
    * @constructor
    */
    SocketHelper = function(cb){  
        var that = this      
        extendSingleton(SocketHelper);
        this.token;
        require(["/bower_components/socket.io-1.4.5/index.js"], loaded);
        this.events = {
            auth:{
                name: "auth.acknowledge",
                cb: this.connected.bind(this)
            },
            connectError:{
                name: "connect_error",
                cb: this.connectError.bind(this)
            },
            disconnected:{
                name: "disconnect",
                cb: this.disconnected.bind(this)
            }
        };

        function loaded(io){
            window.io = io;
            if(isDefined(cb)){
                cb(that);
            }
        }
    };

    /**
     * @member SocketHelper#getInstance
     * @description get the single class instance
     * @return {SocketHelper} the single class instance
     */
    SocketHelper.getInstance = function(cb){
        if(isDefined(cb)){
            getSingleton(SocketHelper, cb);
        } else {
            return getSingleton(SocketHelper);
        }
    };

    /**
     * @method SocketHelper#init
     * @param  {Object} events Object containing all events to be set
     * @description Initialize socket events
     */
    SocketHelper.prototype.init = function(events){
        var that = this;
        
        if (isDefined(this.socket) && isDefined(events)) {
            $.each(events, addEvent);
        }

        $(window).unbind("beforeunload");
        $(window).bind("beforeunload", disconnect);

        /**
         * @event SocketHelper#disconnect
         * @description Disconnect user socket on beforeunload event
         */
        function disconnect(event) {                  
            if (isDefined(that._socket)){
                that.socket.disconnect();         
            }
        }

        /**
         * @description Add socket event
         * @method SocketHelper#addEvent
         * @private
         * @param  {String}   [event] Socket event name
         * @param  {Function} [cb]    Callback of the event
         */
        function addEvent(name, params){
            that.socket.on(params.name, params.cb);
        }
    };

    /**
     * @description Send data by event to RTS
     * @method SocketHelper#send
     * @param  {String} [event] event to use
     * @param  {Object} [data]  data to send
     */
    SocketHelper.prototype.send = function(event, data){
        data = isDefined(data) ? data : {};
        data.token = this.token;
        this.socket.emit(event, data);
    };

    SocketHelper.prototype.connect = function(user, host, port){
        console.log("connecting to socket server ...");
        this.socket = io("http://" + host + ":" + port, {
            reconnection: false, 
            autoConnect: false,
            query: $.param(user)
        });
        this.init(this.events); 
        this.socket.connect(); 
    };

    SocketHelper.prototype.connected = function(data){
        console.log("connected to socket server", data);
    };

    SocketHelper.prototype.disconnected = function(){
        console.error("disconnected from socket server");
    };

    SocketHelper.prototype.connectError = function(){
        console.error("can't connect to socket server");
    };

})();