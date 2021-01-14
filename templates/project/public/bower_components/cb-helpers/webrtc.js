/*global extendSingleton, getSingleton, isDefined, Peer, getUserMedia, getScreenId */
var WebrtcHelper;

(function() {
    "use strict";

/**
* @name  WebrtcHelper
* @description  Manage WebRTC connection
* @constructor
*/
WebrtcHelper = function(cb) {
    var that = this;
    this.localStream;
    this.remoteStreams = {};
    this.onCallEvent;
    this.extensionStatus = "installed-enabled";
    extendSingleton(WebrtcHelper);
    require([
        "frontend/js/helper/peer"
        ], ready);

    function ready() {
        PeerHelper.getInstance(ready);

        function ready(instance) {
            that.peer = instance;
            $(window).bind("beforeunload", stopStream);
            if (UserHelper.getInstance().data.type === "admin") {
                testExtension();
            }
            if (cb) {
                cb(that);
            }


            function stopStream(){
                that.stopStream(that.localStream);
                that.stopStream(that.localStreamScreen);
            }

            function testExtension() {
            //        getChromeExtensionStatus(check);

            /*function check(msg){
            that.extensionStatus = msg;
            setInterval(testExtension, 10000);
            }*/
            }

        }
    }
};

/**
* @member WebrtcHelper#getInstance
* @description get the single class instance
* @return {WebrtcHelper} the single class instance
*/
WebrtcHelper.getInstance = function(cb) {
    if (isDefined(cb)) {
        getSingleton(WebrtcHelper, cb);
    } else {
        return getSingleton(WebrtcHelper);
    }
};

WebrtcHelper.prototype.getVideoFromStream = function(stream, cb) {
    var videos = stream.getVideoTracks();
    if(videos.length === 0){
        return false;
    }
    var videoStream = new MediaStream();
    videoStream.addTrack(videos[0]);
    var video = $("<video autoplay playsinline ></video>");
    video.bind("loadeddata", ready);
    video.attr({
        id: stream.id
    });

    try {
        video[0].srcObject = videoStream;
    } catch (error) {
        video[0].src = URL.createObjectURL(videoStream);
    }
    video[0].load();
    return true;

    function ready(){
        if(cb){
            cb(video[0]);
        }
    }
};

WebrtcHelper.prototype.getAudioFromStream = function(stream, context) {
    var audios = stream.getAudioTracks();
    if(audios.length === 0){
        return false;
    }

    var contextAudio = context || new (window.AudioContext || window.webkitAudioContext)();
    var gainVolume = contextAudio.createGain();   
    var source = contextAudio.createMediaStreamSource(stream);
    source.connect(gainVolume);
    gainVolume.connect(contextAudio.destination);
    return {
        source:source,
        gainVolume:gainVolume
    };
};

WebrtcHelper.prototype.callAll = function(options, complete) {
    var that = this;
    var total = Object.keys(SocketHelper.getInstance().users).length;
    $.each(SocketHelper.getInstance().users, call);

    function call(id, data) {
        that.peer.call(options, id, ready);

        function ready() {
            total--;
            if (total === 0 && complete) {
                complete();
            }
        }
    }
};

WebrtcHelper.prototype.call = function(options, id, cb) {
    this.peer.call(options, id, cb);
};


/**
* @method WebrtcHelper#init
* @description Init
*/
WebrtcHelper.prototype.initialize = function(data, cb) {
    var that = this;
    this.getUserMedia(data.constraints, ready);

    function ready() {
        that.peer.init();
        if (cb) {
            cb();
        }
    }
};

WebrtcHelper.prototype.stopStream = function(stream) {
    if(!stream){
        return false;
    }
    var i, j, tracks;
    var types = ["Video", "Audio"];
    for(i=0; i<2; i++){
        tracks = stream["get"+types[i]+"Tracks"]();
        for(j=0; j<tracks.length; j++){
            tracks[j].stop();
        }
    }
};

WebrtcHelper.prototype.changeStreamConstraints = function(constraints, cb) {
    var that = this;
    UserHelper.getInstance().data.constraints = constraints;
    if(this.localStream){        
        $.each(this.peer.pcs, removeStream);
        this.stopStream(this.localStream);
    }
    this.localStream = undefined;
    this.getUserMedia({
        audio: constraints.offerToReceiveAudio,
        video: constraints.offerToReceiveVideo
    }, ready);

    function ready() {
        SocketHelper.getInstance().socket.send("update", {
            streamId: that.localStream.id,
            constraints: constraints
        });
        if(that.localStream){
            $.each(that.peer.pcs, addStream);
        }
        that.callAll(constraints, cb);

        function addStream(id, pc) {
            pc.addStream(that.localStream);
        }
    }

    function removeStream(id, pc) {
        pc.removeStream(that.localStream);
    }
};

WebrtcHelper.prototype.stopUserScreen = function(cb) {
    var that = this;
    if (!this.localStreamScreen) {
        return false;
    }
    $.each(that.peer.pcs, removeStream);
    this.stopStream(this.localStreamScreen);
    that.localStreamScreen = undefined;
    SocketHelper.getInstance().socket.send("update", {
        streamScreenId: undefined
    });
    that.callAll(null, cb);

    function removeStream(id, pc) {
        pc.removeStream(that.localStreamScreen);
    }
};

WebrtcHelper.prototype.getUserScreen = function(cb) {
    var that = this;
    if (this.extensionStatus === "installed-enabled") {
        console.log("chrome extension screen sharing already installed");
        getScreenId(getConstraints);
    } else {
        console.log("Launch chrome extension screen sharing install");
        var link = $("<link>");
        var url = "https://chrome.google.com/webstore/detail/hoabddhlkoneohdomlokajbepekbahna";
        link.attr({
            rel: "chrome-webstore-item",
            href: url
        });
        $("head").append(link);
        chrome.webstore.install(url, installSuccess, installError);
    }

    function installError(error) {
        console.error("chrome extension screen sharing failed to install", error);
    }

    function installSuccess() {
        console.log("chrome extension screen sharing succefully installed");
        getScreenId(getConstraints);
    }

    function getConstraints(err, sourceId, constraints) {
        if (err) {
            console.error(err);
            if (cb) {
                cb();
            }
            return false;
        }
        //   constraints.audio = false;
        that.getUserMedia(constraints, complete);

        function complete() {
            SocketHelper.getInstance().socket.send("update", {
                streamScreenId: that.localStreamScreen.id
            });
            $.each(that.peer.pcs, addStream);
            that.callAll(null, cb);


            function addStream(id, pc) {
                //  that.localStreamScreen.getTracks().forEach(addTrack.bind(that.localStreamScreen));
                pc.addStream(that.localStreamScreen);

                /*   function addTrack(track){
                pc.addTrack(track, this);
                }*/
            }
        }
    }
};

WebrtcHelper.prototype.getUserMedia = function(constraints, cb) {
    var that = this;
    var stream;
    navigator.mediaDevices.getUserMedia(constraints).then(success).catch(error);

    function success(stream) {
        if (!that.localStream) {
            that.localStream = stream;
        } else {
            that.localStreamScreen = stream;
        }
        checkCb();
    }

    function error(msg) {
        console.error(msg);
        checkCb();
    }

    function checkCb() {
        if (cb) {
            cb();
        }
    }
};

})();
