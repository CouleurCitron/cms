
var nc:NetConnection = new NetConnection();
nc.connect(null);

var ns:NetStream = new NetStream(nc);

/*
var _vidName = "golfers.flv";
var _vidURL = "http://www.yourdomain.com/" + _vidName;
var _phpURL = "http://www.yourdomain.com/flvprovider.php";
*/

if (_vidName == undefined){
	_vidName= "David Vincent - RD v4.flv";
	_vidURL= "http://pierre.scle.hephaistos.interne/custom/upload/video/David Vincent - RD v4.flv";
	_phpURL= "http://pierre.scle.hephaistos.interne/backoffice/cms/utils/flvprovider.php";
	
	//_vidName= "natSolsHD.mov";
	//_vidURL= "http://pierre.aws2006.hephaistos.interne/custom/upload/video/"+ _vidName;
	_start = 0;
	_end = 0;
	_autostart = "1";
}

if (String(_start) != "0"){
	_autostartPending = true;
}

var ending = false;
var amountLoaded:Number;
var duration:Number;
var loaderwidth = UI.loader.loadbar._width;

theVideo.attachVideo(ns);
ns.setBufferTime(2);

statusID = setInterval(videoStatus, 200);

this.onEnterFrame = function(){
	if (String(_end) != "0"){
		if (ns.time >= _end){
			jump(_start);
		}
	}
}

ns.onStatus = function(info) {
	trace(info.code);	
	if ((_level0.theVideo.height > 0)&&(_level0.UI._visible == false)){
		//_level0.theVideo._height = _level0.theVideo.height;
		//_level0.theVideo._width = _level0.theVideo.width;

		
		_level0.UI.mute._x = _level0.theVideo._width - 17;
		_level0.UI.controlBar._width = _level0.theVideo._width - 3*19;
		_level0.UI.loader._width = _level0.UI.controlBar._width - 8;
		_level0.UI.controlBar._x = 2*19;
		_level0.UI.loader._x = _level0.UI.controlBar._x +4;
		
		_level0.UI._y = _level0.theVideo._y + _level0.theVideo._height + 1;
		_level0.UI._x = _level0.theVideo._x + _level0.theVideo._width/2 - _level0.UI._width/2;
		
		_level0.UI._visible = true;		
	}
	//trace(_level0.duration);
	if (_level0.duration == undefined) {
		_level0.UI.loader.scrub._visible = false;
	}
	else{
		_level0.UI.loader.scrub._visible = true;
	}
	
	
	if(info.code == "NetStream.Buffer.Full") {
		bufferClip._visible = false;		
		ending = false;
		
		clearInterval( statusID );
		statusID = setInterval(videoStatus, 200);
	}
	if(info.code == "NetStream.Buffer.Empty") {
		if ( !ending ) {
			bufferClip._visible = true;
		}
	}
	if(info.code == "NetStream.Play.Stop") {
		bufferClip._visible = false;
		//ending = true;
	}
	if(info.code == "NetStream.Play.Start") {
		ending = false;
	}
	if(info.code == "NetStream.Buffer.Flush") {
		ending = true;
	}
}

UI.playButton.onRelease = function() {
	ns.pause();
	UI.playButton._visible = false;
	UI.pauseButton._visible = true;
}

UI.pauseButton.onRelease = function() {
	ns.pause();
	UI.pauseButton._visible = false;
	UI.playButton._visible = true;
}

UI.play_btn.onRelease = function() {
	restartIt();
	this._visible = false;
}

play_btn.onRelease = function() {
	trace("click sur play_btn");
	this._visible = false;
	restartIt();
}

UI.rewindButton.onRelease = function() {
	restartIt();
}

ns["onMetaData"] = function(obj) {
	duration = obj.duration;
	trace("onMetaData");
	trace(obj.duration);
	trace(obj.width);
	trace(obj.height);
	
	//jsTr = "javascript:videoSizeUpdate("+_level0.theVideo._width+", "+_level0.theVideo._height+");";
	jsTr = "javascript:videoSizeUpdate("+obj.width+", "+obj.height+");";
	trace(jsTr);
	if(_root._url.indexOf("http") >= 0){ 
		// reset HTML player size (via JS)		
		getURL(jsTr);
	}
	
	_level0.theVideo._height = obj.height;
	_level0.theVideo._width = obj.width;

	
	// suck out the times and filepositions array, this was added by flvmdi27b
	times = obj.keyframes.times;
	positions = obj.keyframes.filepositions;

	if (_autostartPending){
		jump(_start);
		_autostartPending = false;
	}

}

function videoStatus() {
	amountLoaded = ns.bytesLoaded / ns.bytesTotal;
	UI.loader.loadbar._width = amountLoaded * loaderwidth;
	UI.loader.scrub._x = ns.time / duration * loaderwidth;
}

UI.loader.scrub.onPress = function() {
	clearInterval (statusID );
	ns.pause();
	this.startDrag(false,0,this._y,loaderwidth,this._y);
}

UI.loader.scrub.onRelease = UI.loader.scrub.onReleaseOutside = function() {	
	scrubit();
	this.stopDrag();
}

function jump(tofind){
	scrubit(tofind);
}

function scrubit(tofind) {
	if (tofind == undefined){
		var tofind = Math.floor((UI.loader.scrub._x/loaderwidth)*duration);
	}
	trace("scrubit("+tofind+")");
	
	play_btn._visible = false;
	trace("duration = "+duration);
	
	for (prop in _level0.theVideo){
		trace(prop + " = " + _level0.theVideo[prop]);		
	}
	trace("tofind = "+tofind);
	
	if (tofind <= 0 ) {
		restartIt();
		return;
	}

	for (var i:Number=0; i < times.length; i++){
		var j = i + 1;
		if( (times[i] <= tofind) && (times[j] >= tofind ) ){
			trace("match at " + times[i] + " and " +  positions[i]);
			bufferClip._visible = true;
			
			ns.play( _phpURL + "?file=" + _vidURL + "&position=" + positions[i]);
			trace("play " + _phpURL + "?file=" + _vidURL + "&position=" + positions[i]);
			break;
		}
	}
}

function pauseIt() {
	ns.pause();
}

function stopIt() {
	ns.seek(0);
	ns.pause();
}

function restartIt() {
	ns.play( _vidURL );
	play_btn._visible = false;
	UI.playButton._visible = false;
	UI.pauseButton._visible = true;
}

if (String(_autostart) == "1"){		
	restartIt();
}
else{
	bufferClip._visible = false;
	ns.seek(0);
}

// holds sound
_root.createEmptyMovieClip("vSound",_root.getNextHighestDepth());
vSound.attachAudio(ns);

var so:Sound = new Sound(vSound);
so.setVolume(100);

UI.mute.onRollOver = function() {
	if(so.getVolume()== 100) {
		this.gotoAndStop("onOver");
	}
	else {
		this.gotoAndStop(" muteOver");
	}
}

UI.mute.onRollOut = function() {
	if(so.getVolume()== 100) {
		this.gotoAndStop("on");
	}
	else {
		this.gotoAndStop("mute");
	}
}

UI.mute.onRelease = function() {
	trace(this +  " release, volume is "+ so.getVolume());
	if(so.getVolume()== 100) {
		so.setVolume(0);
		this.gotoAndStop("muteOver");
	}
	else {
		so.setVolume(100);
		this.gotoAndStop("onOver");
	}
}



/*
var theMenu:ContextMenu = new ContextMenu();
theMenu.hideBuiltInItems();
_root.menu = theMenu;

var item1:ContextMenuItem = new ContextMenuItem("::::: Video Controls :::::",trace);
theMenu.customItems[0] = item1;

var item2:ContextMenuItem = new ContextMenuItem("Play / Pause Video",pauseIt,true);
theMenu.customItems[1] = item2;

var item3:ContextMenuItem = new ContextMenuItem("Replay the Video",restartIt);
theMenu.customItems[2] = item3;
	
*/
	

iniW = Stage.width;
iniH = Stage.height;


var stageListener:Object = new Object();
stageListener.onResize = function() {
   trace("resize");
   iniVideoSize();
};
Stage.addListener(stageListener);



function logProps(obj){
	for (prop in obj){
		trace(eval(obj)+"."+prop+" = "+obj[prop]);		
	}
}
/* SID
function logVar(obj){
	log(obj+" = "+this[eval(obj)]);		

}*/

Stage.scaleMode = "noScale";

trace("welcome");trace("sc="+Stage.scaleMode);

//iniVideoSize();


function iniVideoSize(){
	trace("h="+Stage.height);
	trace("w="+Stage.width);
		
	ratioVideoW = Stage.width/iniW;
	ratioVideoH = Stage.height/iniH;
	
	if (ratioVideoW > ratioVideoH){
		ratioVideo = ratioVideoW*100;
	}
	else{
		ratioVideo = ratioVideoH*100;
	}
	
	//vid._xscale  = ratioVideo;
	//vid._yscale  = ratioVideo;
	
	offsetX = (Stage.width - iniW)/2;
	offsetY = (Stage.height - iniH)/2;
	
	theVideo._x =-offsetX;
	theVideo._y =-offsetY;

	//UI._x =-offsetX+theVideo.width;
	_level0.UI._x = _level0.theVideo._x + _level0.theVideo._width/2 - _level0.UI._width/2;
	UI._y =-offsetY+theVideo.height;	
	
	log("ratioVideoW="+ratioVideoW);
	log("ratioVideoH="+ratioVideoH);
	
}



	
	