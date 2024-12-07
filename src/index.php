<!doctype html>
<?php
	session_set_cookie_params(86400);
	session_start();
?>
<meta http-equiv=”Pragma” content=”no-cache”>
<meta http-equiv=”Expires” content=”-1″>
<meta http-equiv=”CACHE-CONTROL” content=”NO-CACHE”>


<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css" integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ==" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js" integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ==" crossorigin=""></script>
<script src="https://unpkg.com/leaflet-ant-path@1.3.0/dist/leaflet-ant-path.js" type="text/javascript"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<script src="https://use.fontawesome.com/4ecc3dbb0b.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.0/css/fontawesome.min.css" integrity="sha384-z4tVnCr80ZcL0iufVdGQSUzNvJsKjEtqYZjiQrrYKlpGow+btDHDfQWkFjoaz/Zr" crossorigin="anonymous">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<script>
// save these original methods before they are overwritten
var proto_initIcon = L.Marker.prototype._initIcon;
var proto_setPos = L.Marker.prototype._setPos;

var oldIE = (L.DomUtil.TRANSFORM === 'msTransform');

L.Marker.addInitHook(function () {
	var iconOptions = this.options.icon && this.options.icon.options;
	var iconAnchor = iconOptions && this.options.icon.options.iconAnchor;
	if (iconAnchor) {
		iconAnchor = (iconAnchor[0] + 'px ' + iconAnchor[1] + 'px');
	}
	this.options.rotationOrigin = this.options.rotationOrigin || iconAnchor || 'center bottom' ;
	this.options.rotationAngle = this.options.rotationAngle || 0;

	// Ensure marker keeps rotated during dragging
	this.on('drag', function(e) { e.target._applyRotation(); });
});

L.Marker.include({
	_initIcon: function() {
		proto_initIcon.call(this);
	},

	_setPos: function (pos) {
		proto_setPos.call(this, pos);
		this._applyRotation();
	},

	_applyRotation: function () {
		if(this.options.rotationAngle) {
			this._icon.style[L.DomUtil.TRANSFORM+'Origin'] = this.options.rotationOrigin;

			if(oldIE) {
				// for IE 9, use the 2D rotation
				this._icon.style[L.DomUtil.TRANSFORM] = 'rotate(' + this.options.rotationAngle + 'deg)';
			} else {
				// for modern browsers, prefer the 3D accelerated version
				this._icon.style[L.DomUtil.TRANSFORM] += ' rotateZ(' + this.options.rotationAngle + 'deg)';
			}
		}
	},

	setRotationAngle: function(angle) {
		this.options.rotationAngle = angle;
		this.update();
		return this;
	},

	setRotationOrigin: function(origin) {
		this.options.rotationOrigin = origin;
		this.update();
		return this;
	}
})


</script>

<html>

<head>
<style>
	body {
	  font-family: "Lato", sans-serif;
	}
	#btn{
		display: inline-block;
		z-index: 1;
		position: fixed;
		right: 2%;
		top: 5%;
		background-color:white;
	}
	
	#btn:hover{
	  	background-color:whitesmoke;
	}
	
	#btn_follow{
		display: none;
		z-index: 1;
		position: fixed;
		left: 0.9%;
		top: 50%;
		background-color:white;
	}
	
	#btn_follow:hover{
	  	background-color:whitesmoke;
	}
	

	.sidenav {
	  align-content: center;
	  height: 100%;
	  width: 0;
	  position: fixed;
	  z-index: 2;
	  top: 0;
	  right: 0;
	  background-color: #111;
	  overflow-x: hidden;
	  transition: 0.5s;
	  padding-top: 60px;
	}

	.sidenav a {
	  padding: 8px 8px 8px 32px;
	  text-decoration: none;
	  font-size: 25px;
	  display: block;
	  transition: 0.3s;
	  color: #818181;
	}
	
	.sidenav .titles{
	  padding: 8px 8px 8px 32px;
	  text-decoration:bold;
	  font-size: 35px;
	  color: #818181;
	  display: block;
	  transition: 0.8s;
		
	}
	.sidenav .loggedIn{
	  padding: 8px 8px 8px 32px;
	  text-decoration:none;
	  font-size: 25px;
	  color: #818181;
	  background-color:lawngreen;
	  display: block;
	  transition: 0.8s;
	  margin:5%;
	}
	
	.sidenav .trackstatus{
	  padding: 8px 8px 8px 32px;
	  text-decoration:none;
	  font-size: 25px;
	  color: #818181;
	  display: block;
	  transition: 0.8s;
		
	}
	
	.sidenav .snippet{
	  padding: 8px 8px 8px 32px;
	  text-decoration:bold;
	  font-size: 17px;
	  color: #818181;
	  display: block;
	  transition: 0.8s;
		
	}

	.sidenav a:hover {
	  color: #f1f1f1;
	}

	.sidenav .closebtn {
	  position: absolute;
	  top: 0;
	  right: 25px;
	  font-size: 36px;
	  margin-left: 50px;
	}

	.sidenav .loginbtn {
		display: inline-block;
	  	text-align: center;
		position: absolute;
	  	top: 0;
		left: 0;
		right: 0;
		font-size: 23px;
		margin: auto;
		max-width: 70%;
	}
	
	.sidenav .bckbtn {
	  position: absolute;
	  top: 0;
	  left: 0;
	  font-size: 36px;
	}
	
	.sidenav .table{
		color: #818181;
	}

	@media screen and (max-height: 450px) {
	  .sidenav {padding-top: 15px;}
	  .sidenav a {font-size: 18px;}
	}

	.sidenav .form-control {
		border-radius: 0;
		box-shadow: none;
		border-color: #d2d6de
	}

	.select2-hidden-accessible {
		border: 0 !important;
		clip: rect(0 0 0 0) !important;
		height: 1px !important;
		margin: -1px !important;
		overflow: hidden !important;
		padding: 0 !important;
		position: absolute !important;
		width: 1px !important
	}

	#mySidenav .form-control {
		display: block;
		width: 100%;
		height: 34px;
		padding: 6px 12px;
		font-size: 14px;
		line-height: 1.42857143;
		color: #555;
		background-color: #fff;
		background-image: none;
		border: 1px solid #ccc;
		border-radius: 4px;
		-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
		box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
		-webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
		-o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
		transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s
	}

	.select2-container--default .select2-selection--single,
	.select2-selection .select2-selection--single {
		border: 1px solid #d2d6de;
		border-radius: 0;
		padding: 6px 12px;
		height: 34px
	}

	.select2-container--default .select2-selection--single {
		background-color: #fff;
		border: 1px solid #aaa;
		border-radius: 4px
	}

	.select2-container .select2-selection--single {
		box-sizing: border-box;
		cursor: pointer;
		display: block;
		height: 28px;
		user-select: none;
		-webkit-user-select: none
	}

	.select2-container .select2-selection--single .select2-selection__rendered {
		padding-right: 10px
	}

	.select2-container .select2-selection--single .select2-selection__rendered {
		padding-left: 0;
		padding-right: 0;
		height: auto;
		margin-top: -3px
	}

	.select2-container--default .select2-selection--single .select2-selection__rendered {
		color: #444;
		line-height: 28px
	}

	.select2-container--default .select2-selection--single,
	.select2-selection .select2-selection--single {
		border: 1px solid #d2d6de;
		border-radius: 0 !important;
		padding: 6px 12px;
		height: 40px !important
	}

	.select2-container--default .select2-selection--single .select2-selection__arrow {
		height: 26px;
		position: absolute;
		top: 6px !important;
		right: 1px;
		width: 20px
	}
</style>
<meta charset="utf-8">
<title>Track yer birds!</title>
</head>

<body>
	
<div id="map" style="position:absolute;top:0px;right:0px;bottom:0px;left:0px; z-index: -1;"></div>

<div id="mySidenav" class="sidenav"></div>


<span id="btn" style="font-size:30px;cursor:pointer; border-radius: 5px; padding:0.1%; border-style:outset; border-width:12%;" onclick="openNav()"> &#9776; </span>

<span id="btn_follow" style="font-size:30px;cursor:pointer; border-radius: 5px; padding:0.1%; padding-left: 10px; padding-right:10px; border-style:outset; border-width:12%;" onclick="reFollow()">➤</span>
 <script>
	
	let mapOptions={
		center:[51.5, 4.5],
		zoom: 7,
		maxzoom: 25,
		minzoom: 1,
		//zoomControl: false,  
		editable: true, 
	}
	 
	var map = new L.map('map', mapOptions);   

	let layer = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
							   {maxNativeZoom:18, maxZoom: 18}
							   );
	map.addLayer(layer);
	 

	let customIcon = {
	 iconUrl:'https://earth.google.com/images/kml-icons/track-directional/track-0.png',
	 iconSize:[40,40]
	}
	
	var myIcon = L.icon(customIcon);
	 
	var state="menu"
	var allInfoBoxes;
	var marker;
	var markerDir;
    var currentMark;
	var radiusArea;
	var path;
	var TimeDifferPrev=0
	var gwMarkers
	var trackerloopid;
	var latestCoords;
	var trackedId
       
		/*L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
			*/
        //add more latitude and longitude
function ConvertTime(timeStamp){
	let DT= new Date(timeStamp*1000)
	return DT.toLocaleString("sv-SE")
}
function ConvertDDToDMS(dd, lng){
	dir = dd<0?lng?' W':' S':lng?' E':' N';
	var deg = dd | 0; // truncate dd to get degrees
	var frac = Math.abs(dd - deg); // get fractional part
	var min = (frac * 60) | 0; // multiply fraction by 60 and truncate
	var sec = (frac * 3600 - min * 60)|2;
	//return deg+"°"+min+"'"+sec+"\"N";
	return deg+"°"+min+"'"+sec+"\""+dir;
}
function getLogin(){
	let req = new XMLHttpRequest();
	// WARNING: USE FALSE HERE! MAKE IT SYNCHRONOUS
	req.open("POST", "logingate.php", false);
	req.send()
	if (req.status == 200){
		let resp_data =  req.responseText;
		console.log("getLogin() "+ resp_data )
		let parsed_data = JSON.parse(req.response)
		if(parsed_data['session']==1){
			return parsed_data['Username']
		}else{
			return null
		}
	}
}
function ObtainCoords(){
		//Getting Updates via POSTRq
	
		if(trackedId==""){
			document.getElementById("statusText").innerHTML = "Select a Tracker"
			return 404;
		}
	
		var formData = new FormData();
		formData.append("device",trackedId);
	
		formData.append("origin","<?php echo $_SERVER['REMOTE_ADDR']; ?>");


		var xhr = new XMLHttpRequest();
		let json_data
		// WARNING: USE FALSE HERE! MAKE IT SYNCHRONOUS
		xhr.open("POST", "request.php", false);
		xhr.send(formData);
		if (xhr.status == 200){
			var resp_data =  xhr.responseText;
			console.log(resp_data);
			json_data= JSON.parse(resp_data);
			console.log(json_data);
			console.log(typeof json_data);
			document.getElementById("statusText").innerHTML = json_data['status'];
			if(json_data['status']=="UNREACHABLE"){
				//document.getElementById("A2").style.background = "black";
				//document.getElementById("A2").style.color = "white";
			}
			if(json_data['status']=="NOT TRACKING"){
				//document.getElementById("A2").style.background = "red";
				//document.getElementById("A2").style.color = "white";
			}
			if(json_data['status']=="NO GPS LOCK"){
				//document.getElementById("A2").style.background = "yellow";
				//document.getElementById("A2").style.color = "black";
			}
			if(json_data['status']=="GPS LOCK - EXACT POS"){
				document.getElementById("statusText").innerHTML = "<div style='color:black; background-color:green; padding:1%; display:inline-block; border-radius:5px;'>GPS LOCK - EXACT POS</div>"
				//document.getElementById("A2").style.color = "white";
			}
			var latfull = ConvertDDToDMS(json_data['lat'], false);

			var lngfull = ConvertDDToDMS(json_data['lng'], true);

			if(json_data['speed'] != -666.0){
				document.getElementById("speedBox").innerHTML = "Speed: "+ json_data['speed'] +" Km/h";

			}else{
				document.getElementById("speedBox").innerHTML = "Speed: N/A";

			}
			
			if(json_data['degrees'] != -666.0){
				document.getElementById("degBox").innerHTML = "Degs: "+ json_data['degrees'] +"° ("+json_data['dir']+")";
				markerDir=json_data['degrees'];
			}else{
				document.getElementById("degBox").innerHTML = "Degs: N/A";
				markerDir=0;
			}

			document.getElementById("rawBox").innerHTML = "Raw (Decimal): "+((json_data['lat'] == 666.0) ? "N/A" : json_data['lat']) +" , "+((json_data['lng'] == 666.0) ? "N/A" : json_data['lng'] );

			document.getElementById("latBox").innerHTML = "Latitude: "+ ((json_data['lat'] == 666.0) ? "N/A" : latfull);
			document.getElementById("lngBox").innerHTML = "Longitude: "+ ( (json_data['lng'] == 666.0) ? "N/A" : lngfull);
			//document.getElementById("CorTime").innerHTML = "Last Received: "+ json_data['rxlast']; //Deprecated
			if(json_data['altitude']!=-666){
				document.getElementById("altBox").innerHTML = "Altitude: "+ json_data['altitude'] +" m";
			}else{
				document.getElementById("altBox").innerHTML = "Altitude: N/A";
			}
			
			
			
			//Time convert
			let DTGPS= new Date(json_data['timedata']*1000)
			DTGPS.toLocaleString("sv-SE")
			
			let DTTTN= new Date(json_data['time']*1000)
			DTTTN.toLocaleString("sv-SE")
			
			let TimeDiffer = Math.round((Date.now()-DTTTN)/1000)
			let TDHr = Math.floor(TimeDiffer/3600);
			let TDMi = Math.floor((TimeDiffer%3600)/60);
			let TDSe = ((TimeDiffer%3600)%60)
			
			
			document.getElementById("lastValidGPS").innerHTML = "Last Valid GPS: " + DTGPS.toLocaleString("sv-SE")
			document.getElementById("lastValidRx").innerHTML = "Last Received from TTN: "+ DTTTN.toLocaleString("sv-SE")+", "+TDHr+"h"+TDMi+"m"+TDSe+"s ago"
			
			/* Diagnostic data */
			let battPercentage=(json_data['voltageBattery']-3000)/1200*100;
			document.getElementById("battBox").innerHTML ="Battery: "+battPercentage+ " % ("+ json_data['voltageBattery']+" mV)";
			if(json_data['charging']==0){
				document.getElementById("battBox").innerHTML+= " <div style='color:black; background-color:green; display:inline-block; border-radius:2px; padding:2px;'> NOW CHARGING via "+json_data['voltageSolar']+ " mV</div>"
			}
			document.getElementById("satBox").innerHTML = "Satellites: "+json_data['satnum'];
			document.getElementById("txCountBox").innerHTML = "Tx Counter: "+json_data['txcounter'];
			document.getElementById("rxCountBox").innerHTML = "Rx Counter: "+json_data['rxcounter'];
			
			
			if(TimeDiffer<TimeDifferPrev){

			  let audio = new Audio('ping.mp3');
			  audio.play();
			  document.getElementById("rawBox").innerHTML+=" <div style='color:black; background-color:green; display:inline-block; border-radius:5px;'>NEW 🟢</div>"
			  document.getElementById("lastValidRx").innerHTML+=" 🟢"
			}
			TimeDifferPrev=TimeDiffer
			//document.getElementById("E").innerHTML = "Last Update: "+ json_data['lng'];

			window.close();

			var newLat = json_data["lat"]; //coordinates[0]+(Math.random()/3000);
			var newLng = json_data["lng"]; //coordinates[1]+(Math.random()/3000);
			const obtainedCoords=L.latLng(newLat , newLng);
			//console.log(newLat)
			console.log("PRERETURNED: ")
			console.log(obtainedCoords);
			return obtainedCoords
		}else{
			/* Here add if unallowed to track that bird */
			console.log(resp_data)
			return xhr.status;
		}

		//Update Coords
		/*
		var newLat = json_data["lat"]; //coordinates[0]+(Math.random()/3000);
		var newLng = json_data["lng"]; //coordinates[1]+(Math.random()/3000);
		let obtainedCoords=L.latLng(newLat , newLng);
		console.log(newLat)
		console.log(obtainedCoords);
		return obtainedCoords
		*/
	}
	 
function getDevices(){
	var xhr = new XMLHttpRequest();
	xhr.open("POST", "request.php", false);
	xhr.send();
	if (xhr.status == 200){
		var resp_data =  xhr.responseText;
		//console.log(resp_data);
		json_data= JSON.parse(resp_data);
		//console.log(json_data);
		return json_data["available"];
	}
	else{
		return null;
	}
}
	 
function trackNew(panner=true){
	
	if(typeof latestCoords !== 'undefined' && latestCoords!=null){
		if(map.getCenter()['lat'].toFixed(3) != latestCoords['lat'].toFixed(3)  && map.getCenter()['lat'].toFixed(3) != latestCoords['lat'].toFixed(3)){
		//if(!map.getCenter().equals(latestCoords)){
			panner= false
			console.log("CENTER: ")
			console.log(map.getCenter())
			console.log("LATEST: ")
			console.log(latestCoords)
			//Using jQuery
			$('#btn_follow').fadeIn(500);
		}else{
			$('#btn_follow').fadeOut(500);
			//document.getElementById('btn_follow').style.display="none"
		}
	}
	
	/* Requesting Occurs */
	console.log("FOLLOWER: "+panner)
	latestCoords = ObtainCoords()

	console.log("RETURNED:")
	console.log(latestCoords)
	
	//Error Case
	if (latestCoords!=403 ){
		marker.setLatLng(latestCoords);
		marker.options.rotationAngle=markerDir;
		marker.update();
		if(panner){
			map.panTo(latestCoords);
		}
	}
	return latestCoords
}
function startTracking() {
	if(getLogin()!=null){
		
		console.log("[INFO] Non Null Login - Proceeding with tracking")

	}else{
		
		console.log("[INFO] Null Login")
		return
	}
	
	/* Clean Marker*/
	if (typeof marker !== "undefined"){
		marker.remove(map)
	}
	let iconOptions = {
		 title:'Heres where ur fucking device is, happy now?',
		 draggable:false,
		 icon:myIcon
	}



	marker = new L.Marker([51.958, 9.141],  iconOptions);
	marker.addTo(map);
	
	
	TimeDifferPrev=0;	
	if(trackNew()!=403){
		reFollow()
		$('#btn_follow').fadeIn(500);
		if(trackerloopid  !== "undefined"  ){
			//marker.remove(map)
			
			/* Clear Previous Time Difference*/
			
			clearInterval(trackerloopid);
			
		}
		TimeDifferPrev=0;

		trackerloopid=setInterval( trackNew, 2000 );
	}else{
		latestCoords=null
		console.log("Cannot start tracking")
	}
};
	 
	 
function openNav() {
  	
	if(window.matchMedia("(max-width: 800px)").matches){
		document.getElementById("mySidenav").style.height = "50%";
		document.getElementById("mySidenav").style.width = "100%";
		
	}else{
		document.getElementById("mySidenav").style.width = "25%";
		document.getElementById("mySidenav").style.height = "100%";
		document.getElementById("mySidenav").style.alignContent = "start";
	}
  	if (state=="menu")onMainMenuMode();
}

function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
}
	
function logOut(){
	let req = new XMLHttpRequest();
	// WARNING: USE FALSE HERE! MAKE IT SYNCHRONOUS
	req.open("POST", "logout.php", false);
	req.send()
	if (req.status == 200){
		let resp_data =  req.responseText;
		console.log("logOut() "+ resp_data )
		onMainMenuMode()
		/*
		let parsed_data = JSON.parse(req.response)
		
		if(parsed_data['session']==1){
			return parsed_data['Username']
		}else{
			return null
		}
		*/
	}	
	
}

function getCloseButton(){
	let closeButton=document.createElement("a");
	closeButton.href="javascript:void(0)";
	closeButton.className="closebtn";
	closeButton.addEventListener("click", closeNav);
	closeButton.innerHTML="&times;";
	return closeButton;
}
	 
function getLoginButton(){
	result=getLogin()
	console.log("getLoginButton(): "+result)
	if(result==null){
		let closeButton=document.createElement("a");
		closeButton.href="javascript:void(0)";
		closeButton.className="loginbtn";
		closeButton.addEventListener("click", onLoginMode);
		closeButton.innerHTML="Login";
		return closeButton;
	}else{
		let closeButton=document.createElement("a");
		closeButton.href="javascript:void(0)";
		closeButton.className="loginbtn";
		closeButton.addEventListener("click", onUserSettingsMode);
		 <?php
			if ($_SESSION['accountperm']>0){
				echo "closeButton.innerHTML='<i class=\"fa fa-user\" style=\"color:red\" aria-hidden=\"true\"></i> '+result;";
				
			}else{
				echo "closeButton.innerHTML='<i class=\"fa fa-user\" aria-hidden=\"true\"></i> '+result;";
			}
		?>
		
		return closeButton;
	}
}
	
	
	
function getBackButton(){
	let backButton=document.createElement("a");
	backButton.href="javascript:void(0)";
	backButton.className="bckbtn";
	backButton.addEventListener("click", getBack);
	backButton.innerHTML="➥";
	return backButton;
}
function getMenuElement(name, functionhandle="#"){
	let linkButton=document.createElement("a");
	linkButton.href="javascript:void(0)";
	linkButton.className="links"
	//linkButton.href=link;
	if(functionhandle=="#"){
		
	}else{
		 console.log("Succeded")
		 linkButton.addEventListener("click", functionhandle);
	}
	linkButton.innerHTML=name;
	return linkButton;
}
	
function getMenuDiv(className,title){
	let element=document.createElement("div");
	element.className=className;
	element.innerHTML=title;
	return element
}
	
function getSnippet(text){
	let element=document.createElement("div");
	element.className="snippet";
	element.innerHTML=text;
	return element
}

function resetShit(){
	$('#btn_follow').fadeOut(500);
	clearInterval(trackerloopid)
	if(currentMark){
		currentMark.remove(map)
	}
	if(radiusArea){
		radiusArea.remove(map)
	}
	if(marker){
		marker.remove(map)
	}
	if(path){
		path.remove(map)
	}
	if(gwMarkers){
		gwMarkers.remove(map)
	}
	state="menu"
}
	 
function getBack(){
	resetShit()
	onMainMenuMode()
}
function onMainMenuMode(){
	//Arrange various stuff to display tracker mode

	$('#btn_follow').hide();
	
	sideBarElement=document.getElementById("mySidenav")
	sideBarElement.innerHTML="";
	closeButton=getCloseButton();
	sideBarElement.append(closeButton);
	loginButton=getLoginButton();
	sideBarElement.append(loginButton)
	title= getMenuDiv("titles","Main Menu ")
	sideBarElement.append(title)
	if(getLogin()!=null)
	{
		let linkButton1=getMenuElement("Live Device Tracking",onTrackerMode)
		sideBarElement.append(linkButton1)
		let linkButton7=getMenuElement("Issue Commands",onDownlinkMode)
		sideBarElement.append(linkButton7)
		let linkButton2=getMenuElement("Saved Paths", onSavedPathsMode)
		sideBarElement.append(linkButton2)
	}
	let linkButton3=getMenuElement("Wiki & How-Tos", onOpenWiki)
	sideBarElement.append(linkButton3)
	if(getLogin()!=null)
	{
		let linkButton4=getMenuElement("Settings", onOpenSettings)
		sideBarElement.append(linkButton4)
	}
	let linkButton5=getMenuElement("Info & Credits",onOpenCredits)
	sideBarElement.append(linkButton5)
	<?php
	//console.log( "acountpermission = " + accountperm );
	if($_SESSION['accountperm']>0)
	echo "let linkButton6=getMenuElement(\"Admin Page\",onOpenAdmin);
	sideBarElement.append(linkButton6);"
	?>
	
	if(getLogin()!=null){
		linkButton=getMenuElement("Logout",logOut)
		sideBarElement.append(linkButton)
	}
}
	
function reFollow(){
	if( !map.getBounds().contains(marker.getLatLng())){
		map.flyTo(latestCoords,15);
	}
	else{
		map.panTo(latestCoords,15)
	}
}

 <?php
session_start();
if ($_SESSION["accountperm"]>0){
echo ("

/* If you see this it means you're the admin*/

function getUserDevices(){
	console.log(\"cunt\");
	var xhr = new XMLHttpRequest();
	xhr.open(\"POST\", \"administration.php\", false);
	xhr.setRequestHeader(\"Content-Type\", \"application/x-www-form-urlencoded\");
	xhr.send(\"mode=users\");
	if (xhr.status == 200){
		var resp_data =  xhr.responseText;
		//console.log(resp_data);
		let json_data = JSON.parse(resp_data);
		
		return json_data[\"Data\"];
	}
	else{
		return null;
	}

}

function getDeviceIdList(){
	console.log(\"cunt\");
	var xhr = new XMLHttpRequest();
	xhr.open(\"POST\", \"administration.php\", false);
	xhr.setRequestHeader(\"Content-Type\", \"application/x-www-form-urlencoded\");
	xhr.send(\"mode=devicelist\");
	if (xhr.status == 200){
		var resp_data =  xhr.responseText;
		//console.log(resp_data);
		let json_data = JSON.parse(resp_data);
		
		return json_data[\"Data\"];
	}
	else{
		return null;
	}


}

function changeUserDevices(user, list){

	//list = list.split(\",\").map(Number);

	
	var xhr = new XMLHttpRequest();
	xhr.open(\"POST\", \"administration.php\", false);
	xhr.setRequestHeader(\"Content-Type\", \"application/x-www-form-urlencoded\");
	xhr.send(\"mode=changedevices&user=\"+user+\"&devices=\"+ JSON.stringify(list));
	if (xhr.status == 200){
		var resp_data =  xhr.responseText;
		console.log(resp_data);
		let json_data = JSON.parse(resp_data);
		if(json_data[\"status\"]==\"ok\"){
			return 1;
		}else{
			return 0;
		}
	}
	else{
		return null;
	}

}

function onOpenAdmin(){
	sideBarElement=document.getElementById(\"mySidenav\");
	sideBarElement.innerHTML=\"\";
	backButton=getBackButton();
	sideBarElement.append(backButton);
	loginButton=getLoginButton();
	sideBarElement.append(loginButton);
	let title= getMenuDiv(\"titles\",\"Administration page\");
	sideBarElement.append(title);
	let text= getSnippet(\"You may administer which devices the users may look at and which not.<br>Select the users from the dropdown menu and pick personally from the device list.<br>Click on Submit to send the change, you should receive a confirmation if it was successful. \");
	sideBarElement.append(text);
	
	userList = getUserDevices();
	

	
	let userPickBox=getMenuDiv(\"userPickBox\",\"\");
	userPickBox.style.margin=\"4%\";
	userPickBox.style.padding=\"2%\";
	
	let userPick = document.createElement(\"select\");
	    
	let promptOption = document.createElement(\"option\");
    promptOption.disabled = true;
    promptOption.selected = true;
    promptOption.textContent = \"Select a User among the list\";
    userPick.appendChild(promptOption);
	
	
	
	
	Object.keys(userList).forEach(function(option) {
	  let opt = document.createElement(\"option\");
	  opt.textContent = option;
	  opt.value = option;
	  userPick.appendChild(opt);
	});
	
	
	userPick.id=\"userPick\";
	userPick.style.backgroundColor=\"#818181\";
	userPick.style.textAlign=\"center\";
	userPick.style.color=\"#111\";
	userPick.style.fontWeight=\"bold\";
	userPick.style.fontSize=\"20px\";
	userPick.style.borderRadius=\"10px\";
	userPick.style.padding=\"2%\";
	
	let devicePickBox=document.createElement(\"form\")
	devicePickBox.style.display = \"none\"; /* Temporary Hide */
	
	devicePickBox.id=\"devicePickBox\"
	devicePickBox.setAttribute(\"method\",\"post\")
	devicePickBox.setAttribute(\"action\", \"\")
	devicePickBox.setAttribute(\"onsubmit\",\"return false\")
	
	devicePickBox.style.padding=\"2%\"
	devicePickBox.style.marginLeft=\"8%\"
	devicePickBox.style.marginRight=\"8%\"
	devicePickBox.style.color=\"#111\"
	devicePickBox.style.backgroundColor=\"#818181\"
	devicePickBox.style.borderStyle=\"solid\"
	devicePickBox.style.borderRadius=\"10px\"
	
	
	
	userPick.addEventListener(\"change\", function() {
      if (userPick.value !== \"\") {
        userSelected=userPick.value;
		
		deviceIdList = getDeviceIdList();
		userList = getUserDevices();
		
		userListDevices = userList[userSelected];
		
		devicePickBox.innerHTML=\"\";
		
		let devicePickBoxHead = getMenuDiv(\"\",\"<div id='devicePickBoxHead' style='font-weight:bold; font-size:15px; text-align:center;'>Devices Available to the User</div>\");
		//devicePickBoxHead.innerHTML=\"Devices Available to the User\";
		
		devicePickBox.append(devicePickBoxHead);
		
		let snippetDevBox=getSnippet(\"Please tick the Boxes of the devices you wish the selected user to be authorized to see and track.<br><b>Notice you may only do this on accounts that are below you in rank.<br>Submit via Button.</b>\")
		snippetDevBox.style.color=\"#111\"
		snippetDevBox.style.fontSize=\"12px\"
		snippetDevBox.style.display=\"inline-block\"
		snippetDevBox.style.textAlign=\"left\"
		
		
		/*
		let deviceInputBox = document.createElement(\"input\");
		deviceInputBox.id=\"deviceInputBox\"
		deviceInputBox.type=\"text\"
		deviceInputBox.name=\"deviceInputBox\"
		deviceInputBox.style.maxWidth=\"70%\"
		
		deviceInputBox.value = userListDevices;
		*/
		
		devicePickBox.appendChild(snippetDevBox);
		
		/* Filling the list of Devices */
		
		deviceIdList.forEach(function(option){
		  let tickGroup = document.createElement(\"p\");
		  
		  let tickbox = document.createElement(\"input\");
		  tickbox.type=\"checkbox\"
		  tickbox.textContent = option;
		  tickbox.value = option;
		  tickbox.id = \"device\"+option;
		  tickbox.style.display=\"inline-block\"
		  
		  if(userListDevices.includes(option)){
		  	tickbox.checked = true;
		  }
		  
		  let tickboxLabel = document.createElement(\"label\");
		  tickboxLabel.innerHTML = option;
		  tickboxLabel.for = tickbox.id;
		  
		  tickGroup.appendChild(tickbox);
		  tickGroup.appendChild(tickboxLabel);
		  devicePickBox.appendChild(tickGroup);
		});
		
		let deviceInputBoxBtn = document.createElement(\"input\")
		deviceInputBoxBtn.id=\"deviceInputBoxBtn\"
		deviceInputBoxBtn.value=\"Change\"
		deviceInputBoxBtn.type=\"submit\"
		
		
		
		
		
		//devicePickBox.appendChild(deviceInputBox);
		devicePickBox.appendChild(deviceInputBoxBtn);
		
		devicePickBox.style.display = \"block\";
		

		
      }
    });
	
	$(devicePickBox).submit(function(e) {
		e.preventDefault();
	 	
		let deviceInputList = [];
		
		$('input[type=checkbox]').each(function () {
			   if (this.checked) {
				   deviceInputList.push(Number($(this).val())); 
			   }
		});
		
		let resp = changeUserDevices(userPick.value, deviceInputList )


	});
	
	userPickBox.append(userPick);
	sideBarElement.append(userPickBox);
	sideBarElement.append(devicePickBox);

}
	

");
}
?>
function issueDownlink(device,minutes){
	let reqFetch = new XMLHttpRequest();
	
	// WARNING: USE FALSE HERE! MAKE IT SYNCHRONOUS
	
	reqFetch.open("POST", "downlink.php", false);
	reqFetch.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	reqFetch.send("device="+device+"&minutes="+minutes)
	if (reqFetch.status == 200){
		let resp_data =  reqFetch.responseText;
		console.log("issueDownlink("+device+","+minutes+") "+ resp_data )
		return resp_data
	}
}
function onDownlinkMode(){
	sideBarElement=document.getElementById("mySidenav")
	sideBarElement.innerHTML="";
	backButton=getBackButton()
	sideBarElement.append(backButton);
	closeButton=getCloseButton();
	sideBarElement.append(closeButton);
	loginButton=getLoginButton();
	sideBarElement.append(loginButton)
	title= getMenuDiv("titles","Issue Commands")
	sideBarElement.append(title)
	let text= getSnippet("Select a Device and insert a minutage to issue a change in the duty cycle.<br>The device will be updated with the new dutycycle in the time window that follows moment the device broadcasts new information.</br> Notice that changes may not be immediate and it may require several attempts for them to be applied.")
	sideBarElement.append(text)
	
	/* Selection of available Birds to track */
	devicelist=getDevices();
	/* Device Chosen */
	let pickedDevice=0;
	
	let falconPickDLBox=getMenuDiv("falconPickDLBox","")
	falconPickDLBox.style.margin="4%"
	falconPickDLBox.style.padding="2%"
	
	let falconPickDL = document.createElement("select");
	    
	let promptOption = document.createElement("option");
    promptOption.disabled = true;
    promptOption.selected = true;
    promptOption.textContent = "Select a Tracker Id";
    falconPickDL.appendChild(promptOption);
	
	devicelist.forEach(function(option) {
	  let opt = document.createElement("option");
	  opt.textContent = option;
	  opt.value = option;
	  falconPickDL.appendChild(opt);
	});
	
	falconPickDL.id="falconPickDL"
	falconPickDL.style.backgroundColor="#818181"
	falconPickDL.style.textAlign="center"
	falconPickDL.style.color="#111"
	//statusText.style.borderStyle="solid"
	falconPickDL.style.fontWeight="bold"
	falconPickDL.style.fontSize="20px"
	falconPickDL.style.borderRadius="10px"
	falconPickDL.style.padding="2%"

	falconPickDLBox.append(falconPickDL);
	sideBarElement.append(falconPickDLBox);
	
	let formBox=getMenuDiv("commands","<form id='commandform'><label for='command'>Minutes:</label><br><input type='text' id='minuteperiod' name='minuteperiod'><br><input type='submit' id='commandformbutton'></form>")
	formBox.style.padding="2%"
	formBox.style.marginLeft="5%"
	formBox.style.marginRight="5%"
	formBox.style.marginBottom="3%"
	formBox.style.color="#111"
	formBox.style.backgroundColor="#818181"
	//coordBox.style.borderStyle="solid"
	formBox.style.borderRadius="10px"
	
	formBox.style.display = "none";
	sideBarElement.append(formBox)
	
	const form = document.querySelector('#commandform');
    const button = document.querySelector('#commandformbutton');
    

    form.addEventListener('submit', (event) => {
	  event.preventDefault();
      // Get the name input field from the form
      const minuteInput = document.querySelector('input[name="minuteperiod"]');

      // Get the value of the name input field
      const minuteValue = minuteInput.value;
	
	  let returned=issueDownlink(pickedDevice,minuteValue);
	  console.log(returned);
	
	});

	falconPickDL.addEventListener("change", function() {
      if (falconPickDL.value !== "") {
        pickedDevice=falconPickDL.value
		  
		  
		formBox.style.display="block"
		
      }
    });
}
						  
function onTrackerMode(){
	state="track"
	//Arrange various stuff to display tracker mode
	sideBarElement=document.getElementById("mySidenav")
	sideBarElement.innerHTML="";
	backButton=getBackButton()
	sideBarElement.append(backButton);
	closeButton=getCloseButton();
	sideBarElement.append(closeButton);
	loginButton=getLoginButton();
	sideBarElement.append(loginButton)
	title= getMenuDiv("titles","Live Tracking")
	sideBarElement.append(title)
	let text= getSnippet("Live tracking with position (GPS based if available) of the device.<br>Paths can be created, and logged.<br>Use ➤ to re-center the tracked device.")
	sideBarElement.append(text)
	
	/* Selection of available Birds to track */
	devicelist=getDevices();
	
	/* Default One*/
	trackedId=""
	
	let falconPickBox=getMenuDiv("falconPickBox","")
	falconPickBox.style.margin="4%"
	falconPickBox.style.padding="2%"
	
	let falconPick = document.createElement("select");
	    
	let promptOption = document.createElement("option");
    promptOption.disabled = true;
    promptOption.selected = true;
    promptOption.textContent = "Select a Tracker Id";
    falconPick.appendChild(promptOption);
	
	devicelist.forEach(function(option) {
	  let opt = document.createElement("option");
	  opt.textContent = option;
	  opt.value = option;
	  falconPick.appendChild(opt);
	});
	
	falconPick.id="falconPick"
	falconPick.style.backgroundColor="#818181"
	falconPick.style.textAlign="center"
	falconPick.style.color="#111"
	//statusText.style.borderStyle="solid"
	falconPick.style.fontWeight="bold"
	falconPick.style.fontSize="20px"
	falconPick.style.borderRadius="10px"
	falconPick.style.padding="2%"

	falconPickBox.append(falconPick);
	sideBarElement.append(falconPickBox);
	

	/* Falcon Pick event listener at end of function*/
	
	// All Info Boxes to be Put In Here.
	allInfoBoxes = document.createElement("div");
	
	
	let statusBox=getMenuDiv("trackstatus","")
	statusBox.style.margin="4%"
	statusBox.style.padding="2%"
	
	let statusText=getSnippet("STATUS: N/A")
	statusText.id="statusText"
	statusText.style.backgroundColor="#818181"
	statusText.style.textAlign="center"
	statusText.style.color="#111"
	//statusText.style.borderStyle="solid"
	statusText.style.fontWeight="bold"
	statusText.style.fontSize="20px"
	statusText.style.borderRadius="10px"
	statusText.style.padding="2%"
	
	statusBox.append(statusText)
	
	
	allInfoBoxes.append(statusBox) //1
	
	
	let devBox=getMenuDiv("heading","<div id=devBoxIn' style='font-weight:bold; text-align:center;'>Device Info</div>")
	devBox.style.padding="2%"
	devBox.style.marginLeft="5%"
	devBox.style.marginRight="5%"
	devBox.style.marginBottom="3%"
	devBox.style.color="#111"
	devBox.style.backgroundColor="#818181"
	//headBox.style.borderStyle="solid"
	devBox.style.borderRadius="10px"
	
	let battBox = getSnippet("Battery: N/A %")
	battBox.id="battBox"
	battBox.style.fontWeight="bold"
	battBox.style.marginLeft="5%"
	battBox.style.marginRight="5%"
	battBox.style.color="#818181"
	battBox.style.backgroundColor="#111"
	battBox.style.borderStyle="solid"
	battBox.style.borderRadius="10px"
	battBox.style.fontSize="10px" 
	
	let satBox = getSnippet("Satellites: N/A")
	satBox.id="satBox"
	satBox.style.fontWeight="bold"
	satBox.style.marginLeft="5%"
	satBox.style.marginRight="5%"
	satBox.style.color="#818181"
	satBox.style.backgroundColor="#111"
	satBox.style.borderStyle="solid"
	satBox.style.borderRadius="10px"
	satBox.style.fontSize="10px" 
	
	let txCountBox = getSnippet("Tx Counter: N/A")
	txCountBox.id="txCountBox"
	txCountBox.style.fontWeight="bold"
	txCountBox.style.marginLeft="5%"
	txCountBox.style.marginRight="5%"
	txCountBox.style.color="#818181"
	txCountBox.style.backgroundColor="#111"
	txCountBox.style.borderStyle="solid"
	txCountBox.style.borderRadius="10px"
	txCountBox.style.fontSize="10px" 
	
	let rxCountBox = getSnippet("Rx Counter: N/A")
	rxCountBox.id="rxCountBox"
	rxCountBox.style.fontWeight="bold"
	rxCountBox.style.marginLeft="5%"
	rxCountBox.style.marginRight="5%"
	rxCountBox.style.color="#818181"
	rxCountBox.style.backgroundColor="#111"
	rxCountBox.style.borderStyle="solid"
	rxCountBox.style.borderRadius="10px"
	rxCountBox.style.fontSize="10px" 
	
	

	devBox.append(battBox)
	devBox.append(satBox)
	devBox.append(txCountBox)
	devBox.append(rxCountBox)
				
	
	allInfoBoxes.append(devBox) //2
	
	
	let coordBox=getMenuDiv("coordinates","<div id='coordBoxIn' style='font-weight:bold; text-align:center;'>Position</div>")
	coordBox.style.padding="2%"
	coordBox.style.marginLeft="5%"
	coordBox.style.marginRight="5%"
	coordBox.style.marginBottom="3%"
	coordBox.style.color="#111"
	coordBox.style.backgroundColor="#818181"
	//coordBox.style.borderStyle="solid"
	coordBox.style.borderRadius="10px"
	
	let rawBox = getSnippet("+N/A , +N/A")
	rawBox.id="rawBox"
	rawBox.style.fontWeight="bold"
	rawBox.style.color="#111"
	coordBox.append(rawBox)
	
	let latBox = getSnippet("Latitude: N/A")
	latBox.id="latBox"
	latBox.style.fontWeight="bold"
	latBox.style.marginLeft="5%"
	latBox.style.marginRight="5%"
	latBox.style.color="#818181"
	latBox.style.backgroundColor="#111"
	latBox.style.borderStyle="solid"
	latBox.style.borderRadius="10px"
	let lngBox = getSnippet("Longitude: N/A")
	lngBox.id="lngBox"
	lngBox.style.fontWeight="bold"
	lngBox.style.marginLeft="5%"
	lngBox.style.marginRight="5%"
	lngBox.style.color="#818181"
	lngBox.style.backgroundColor="#111"
	lngBox.style.borderStyle="solid"
	lngBox.style.borderRadius="10px"
	
	let altBox = getSnippet("Altitude: N/A")
	altBox.id="altBox"
	altBox.style.fontWeight="bold"
	altBox.style.marginLeft="5%"
	altBox.style.marginRight="5%"
	altBox.style.color="#818181"
	altBox.style.backgroundColor="#111"
	altBox.style.borderStyle="solid"
	altBox.style.borderRadius="10px"
	
	coordBox.append(latBox)
	coordBox.append(lngBox)
	coordBox.append(altBox)
	allInfoBoxes.append(coordBox) //3
	

	
	//sideBarElement.append(altBox)
	

	

	let headBox=getMenuDiv("heading","<div id='headBoxIn' style='font-weight:bold; text-align:center;'>Heading</div>")
	headBox.style.padding="2%"
	headBox.style.marginLeft="5%"
	headBox.style.marginRight="5%"
	headBox.style.marginBottom="3%"
	headBox.style.color="#111"
	headBox.style.backgroundColor="#818181"
	//headBox.style.borderStyle="solid"
	headBox.style.borderRadius="10px"

	
	let speedBox = getSnippet("Speed: N/A")
	speedBox.id="speedBox"
	speedBox.style.fontWeight="bold"
	speedBox.style.marginLeft="5%"
	speedBox.style.marginRight="5%"
	speedBox.style.color="#818181"
	speedBox.style.backgroundColor="#111"
	speedBox.style.borderStyle="solid"
	speedBox.style.borderRadius="10px"
	
	let degBox = getSnippet("Degs: N/A")
	degBox.id="degBox"
	degBox.style.fontWeight="bold"
	degBox.style.marginLeft="5%"
	degBox.style.marginRight="5%"
	degBox.style.color="#818181"
	degBox.style.backgroundColor="#111"
	degBox.style.borderStyle="solid"
	degBox.style.borderRadius="10px"
	
	
	

	headBox.append(speedBox)
	headBox.append(degBox)
	
	allInfoBoxes.append(headBox) //4
	
	
	let timeBox=getMenuDiv("timestamps","<div id='timeBoxIn' style='font-weight:bold; text-align:center;'>Timestamps</div>")
	timeBox.style.padding="2%"
	timeBox.style.marginLeft="5%"
	timeBox.style.marginRight="5%"
	timeBox.style.marginBottom="5%"
	timeBox.style.color="#111"
	timeBox.style.backgroundColor="#818181"
	timeBox.style.borderStyle="solid"
	timeBox.style.borderRadius="10px"

	
	let lastValidGPS = getSnippet("Last Valid GPS: N/A")
	lastValidGPS.id="lastValidGPS"
	lastValidGPS.style.fontWeight="bold"
	lastValidGPS.style.marginLeft="5%"
	lastValidGPS.style.marginRight="5%"
	lastValidGPS.style.color="#818181"
	lastValidGPS.style.backgroundColor="#111"
	lastValidGPS.style.borderStyle="solid"
	lastValidGPS.style.borderRadius="10px"
	lastValidGPS.style.fontSize="10px"
	
	let lastValidRx = getSnippet("Last Received: N/A")
	lastValidRx.id="lastValidRx"
	lastValidRx.style.fontWeight="bold"
	lastValidRx.style.marginLeft="5%"
	lastValidRx.style.marginRight="5%"
	lastValidRx.style.color="#818181"
	lastValidRx.style.backgroundColor="#111"
	lastValidRx.style.borderStyle="solid"
	lastValidRx.style.borderRadius="10px"
	lastValidRx.style.fontSize="10px" 
	
	
	

	timeBox.append(lastValidGPS)
	timeBox.append(lastValidRx)
	
	allInfoBoxes.append(timeBox) // 5
	
	
	let trackBox=getMenuDiv("trackBox","<div id='headBoxIn' style='font-weight:bold; text-align:center;'>Path-Saving Tool</div>")
	trackBox.style.padding="2%"
	trackBox.style.marginLeft="5%"
	trackBox.style.marginRight="5%"
	trackBox.style.color="#111"
	trackBox.style.backgroundColor="#818181"
	trackBox.style.borderStyle="solid"
	trackBox.style.borderRadius="10px"
	
	let snippetTrack=getSnippet("You may start tracking a path independently from wether you are logged in or not. You can check all your tracked files any time in the dedicated section. <b>No need to insert '.csv' extension</b>.")
	snippetTrack.style.color="#111"
	
	let csvNameBox=document.createElement("form")
	csvNameBox.id="csvNameBox"
	//loginForm.setAttribute("onsubmit","logintest();return false;")
	csvNameBox.setAttribute("method","post")
	csvNameBox.setAttribute("action", "")
	csvNameBox.setAttribute("onsubmit","return false")
	
	
	csvNameBox.style.fontFamily="Verdana, Geneva, sans-serif"
	csvNameBox.style.textAlign="center"

	csvNameBox.style.fontWeight="bold"
	csvNameBox.style.marginLeft="5%"
	csvNameBox.style.marginRight="5%"
	csvNameBox.style.padding="2%"
	csvNameBox.style.color="#818181"
	csvNameBox.style.backgroundColor="#111"
	csvNameBox.style.borderStyle="solid"
	csvNameBox.style.borderRadius="10px"
	
	
	//let csvNameBox=getMenuDiv("csvName","<form id='csvfile' action=''><input id='csvFileName' type='text'>.csv <input id='csvFileBtn' value='Start' type='submit'></form>")
	let csvFileName = document.createElement("input")
	csvFileName.id="csvFileName"
	csvFileName.type="text"
	csvFileName.name="csvFileName"
	csvFileName.style.maxWidth="70%"
	
	let csvFileBtn = document.createElement("input")
	csvFileBtn.id="csvFileBtn"
	csvFileBtn.value="Start"
	csvFileBtn.type="submit"
	
	csvNameBox.append(csvFileName)
	csvNameBox.append(csvFileBtn)
	
	if(checkCSV()==1){
		csvFileBtn.value="Stop"
		csvFileName.disabled=true
	}else{
		csvFileBtn.value="Start"
		csvFileName.disabled=false
	}
	
	$(csvNameBox).submit(function(e) {
		e.preventDefault();
		if(checkCSV()==0){
			createCSV(csvFileName.value)
			if(checkCSV()==1){
				csvFileBtn.value="Stop"
				csvFileName.disabled=true
			}else{
				async function errorReport(){
					csvFileName.style.backgroundColor="red";
					csvFileName.disabled=true;
					csvFileName.value="ERROR: File Exists!"
					await setTimeout(function(){csvFileName.value="";csvFileName.disabled=false;csvFileName.style.backgroundColor="";},2000)
				}
				errorReport();
			}
		}
		else if(checkCSV()==1){
			haltCSV()
			if(checkCSV()==0){
				csvFileBtn.value="Start"
				csvFileName.disabled=false
			}
			
		}else{
			console.log("WTF - Should not happen")
		}

	});
	

	
	
	trackBox.append(snippetTrack)
	trackBox.append(csvNameBox)

	
	allInfoBoxes.append(trackBox) //6
	
	allInfoBoxes.style.display="none" /*Hides it until choice has been made*/
	sideBarElement.append(allInfoBoxes)
	
	
	/*
	let iconOptions = {
		 title:'Heres where ur fucking device is, happy now?',
		 draggable:false,
		 icon:myIcon
	}



	marker = new L.Marker([51.958, 9.141],  iconOptions);
	marker.addTo(map);
	*/


	falconPick.addEventListener("change", function() {
      if (falconPick.value !== "") {
        trackedId=falconPick.value
		  
		if(checkCSV()==1){
			csvFileBtn.value="Stop"
			csvFileName.disabled=true
		}else{
			csvFileBtn.value="Start"
			csvFileName.disabled=false
		}  
		  
		allInfoBoxes.style.display="block"
		startTracking();  
		
      }
    });
	
	
	/*
	
	if(getLogin()!=null){
		console.log("[INFO] Non Null Login - Proceeding with tracking")
		marker = new L.Marker([51.958, 9.141],  iconOptions);
		marker.addTo(map);
		
		// After Everything has been arranged for graphically, it finally starts tracking
		startTracking();
	
	}else{
		console.log("[INFO] Null Login")
	}
	 
	 */
	
	
	
	//linkButton=getMenuElement("Live Device Tracking","#")
	//sideBarElement.append(linkButton)
	//linkButton=getMenuElement("Saved Paths","#")
	//sideBarElement.append(linkButton)
	//linkButton=getMenuElement("Clients","#")
	//sideBarElement.append(linkButton)
	//linkButton=getMenuElement("Contact","#")
	//sideBarElement.append(linkButton)	
}
	 
function fetchTableContent(fileName){
	
	let reqFetch = new XMLHttpRequest();
	
	// WARNING: USE FALSE HERE! MAKE IT SYNCHRONOUS
	
	reqFetch.open("POST", "csvhandler.php", false);
	reqFetch.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	reqFetch.send("file="+fileName)
	if (reqFetch.status == 200){
		let resp_data =  reqFetch.responseText;
		console.log("fetchTableContent("+fileName+") "+ resp_data )
		return resp_data
	}
}

function createCSV(fileName){
	

	let reqFetch = new XMLHttpRequest();
	
	// WARNING: USE FALSE HERE! MAKE IT SYNCHRONOUS
	
	reqFetch.open("POST", "csvhandler.php", false);
	reqFetch.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	reqFetch.send("mode=start&file="+fileName+".csv&deviceid="+trackedId)
	if (reqFetch.status == 200){
		let resp_data =  reqFetch.responseText;
		console.log("createCSV("+fileName+") "+ resp_data )
		return resp_data
	}
}

function haltCSV(){
	

	let reqFetch = new XMLHttpRequest();
	
	// WARNING: USE FALSE HERE! MAKE IT SYNCHRONOUS
	
	reqFetch.open("POST", "csvhandler.php", false);
	reqFetch.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	reqFetch.send("mode=stop&deviceid="+trackedId)
	if (reqFetch.status == 200){
		let resp_data =  reqFetch.responseText;
		console.log("haltCSV() "+ resp_data )
		return resp_data
	}
}

function checkCSV(){
	

	let reqFetch = new XMLHttpRequest();
	
	// WARNING: USE FALSE HERE! MAKE IT SYNCHRONOUS
	
	reqFetch.open("POST", "csvhandler.php", false);
	reqFetch.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	reqFetch.send("mode=check&deviceid="+trackedId)
	if (reqFetch.status == 200){
		let resp_data =  reqFetch.responseText;
		console.log("checkCSV() "+ resp_data )
		return resp_data
	}
}

function listCSV(){
	

	let reqFetch = new XMLHttpRequest();
	
	// WARNING: USE FALSE HERE! MAKE IT SYNCHRONOUS
	
	reqFetch.open("POST", "csvhandler.php", false);
	reqFetch.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	reqFetch.send("mode=list")
	if (reqFetch.status == 200){
		let resp_data =  reqFetch.responseText;
		console.log("listCSV() "+ resp_data )
		return resp_data
	}
}

function selectionCallback(event){
	console.log("Callback Loading CSV Table "+event.target.value)
	//console.log(event.target.value)
	loadSelectedPath(event.target.value)
}


function getBootStrapMenu(menuLabel,selections){
	let element=document.createElement("div");
	element.className="form-group";
	
	let label=document.createElement("label")
	label.innerHTML=menuLabel
	
	let select=document.createElement("select")
	select.className="form-control select2 select2-hidden-accessible"
	select.style="width: 70%;"
	select.tabIndex="-1"
	select.ariaHidden="true"
	
	element.append(label)
	element.append(select)
	
	for (let i=0; i<selections.length; i++){
		let option=document.createElement("option")
		if(i==0){
			option.selected="selected"
			option.innerHTML=selections[0]
		}else{
			option.innerHTML=selections[i]
		}
		select.append(option)
	}
	select.selectedIndex=-1
	return element
}
	 
function loadSelectedPath(fileName){
	state="path"
	if(path){
		path.remove(map)
	}
	if(gwMarkers){
		gwMarkers.remove(map)
	}
	console.log("Path Selected")
	//Load CSV Table
	let tablecont=fetchTableContent(fileName)
	
	$(".table-responsive").html(tablecont)
	tablecont=$(".table-responsive").children(".table")
	
	console.log(tablecont)
	tablecont.id="csvTable"
	
	arrPosMap=[]
	arrUnclearPosMap=[]
	console.log(tablecont[0])
	
	
	for(let i=1; i<tablecont[0].rows.length; i++){
		let latRow=tablecont[0].rows[i].cells[3].innerHTML
		let lngRow=tablecont[0].rows[i].cells[4].innerHTML
		if(tablecont[0].rows[i].cells[10].innerHTML==2){
			arrPosMap.push([latRow,lngRow])
		}else if(tablecont[0].rows[i].cells[10].innerHTML==1){
			arrUnclearPosMap.push([latRow,lngRow])
		}else{
			
		}
	}
	//Get data from table
	console.log(arrPosMap)
	
	let arrs = [
        [44.0567000, 12.5552968],
        [44.056880, 11.555297],
        [44.056955,10.553081],
        [44.057004, 9.551313],
        [44.057039, 8.551160],
        [45.056712, 7.547406],
        [52.056737, 4.547216],
                ];
    
	gwMarkers = new L.layerGroup();
	//Removes Dupes
	arrUnclearPosMap = [...new Set(arrUnclearPosMap)];
	
	//Placing gateways...
	for(let i=0; i<arrUnclearPosMap.length; i++){
		let customIconGW = {
		 iconUrl:'https://www.enthutech.in/web/image/4268/lora_color_icon.png',
		 iconSize:[40,40]
		}

		let myIcon = L.icon(customIconGW);

		let iconOptions = {
			 title:'Gateway Location',
			 draggable:false,
			 icon:myIcon
		}

		marker = new L.Marker(arrUnclearPosMap[i],  iconOptions);
		gwMarkers.addLayer(marker)
		
		
	}
	if(arrUnclearPosMap.length>0){
		gwMarkers.addTo(map);
		
	}
	
	if(arrPosMap.length>0){
		var antPath = L.polyline.antPath;
		path = antPath(arrPosMap, {
			"paused": false,   　　
			"reverse": false,　　
			"delay": 3000,　　　　
			"dashArray": [10, 20],　
			"weight": 5,　　　　
			"opacity": 0.5,　　
			"color": "#0000FF",　
			"pulseColor": "#FFFFFF"　　
		});
		path.addTo(map);
		map.fitBounds(path.getBounds())
	}
	
	
	//Add mark remover system 
	tablecont.on('click', 'tbody tr', function(event) {
		console.log("Event Clicked on Map")
		//Select Row Function contains style + loading
		$(this).css({"background-color":"grey","color":"black"}).siblings().css({"background-color":"","color":""});
		if(typeof currentMark !== "undefined"){
			currentMark.remove(map)
		}
		if(typeof radiusArea !== "undefined"){
			radiusArea.remove(map)
		}
		
		console.log("Selected Row: ")
		console.log($(this))
		
		// Popup Type 
		rxStatPopup=$(this)[0].cells[10].innerHTML
		console.log("Popup Status: "+ rxStatPopup)
		if(rxStatPopup==2){
			currentMark = new L.marker([$(this)[0].cells[3].innerHTML,$(this)[0].cells[4].innerHTML],{
				title: "Position at <b> test </b>",
				riseOnHover: true,
				//Newly Added
				//icon: myIcon
			});
			popupContent="<b>Exact Location</b><br>Time of current Position: "+ConvertTime($(this)[0].cells[2].innerHTML)
			
			//Rotate
			//currentMark.options.rotationAngle=$(this)[0].cells[6].innerHTML;
			//currentMark.update();
			
			
		}else if(rxStatPopup==1.5){
			currentMark = new L.marker([$(this)[0].cells[3].innerHTML,$(this)[0].cells[4].innerHTML],{
				title: "Position at <b> test </b>",
				markerColor: "red"
			});
			radiusArea = new L.circle([$(this)[0].cells[3].innerHTML,$(this)[0].cells[4].innerHTML], 5000, {color: 'yellow'})
			
			popupContent="<b>Estimated LoRa-GW Trianguled Location</b><br>Time Processed: "+ConvertTime($(this)[0].cells[1].innerHTML)
			radiusArea.addTo(map)
		}else if(rxStatPopup==1){
			currentMark = new L.marker([$(this)[0].cells[3].innerHTML,$(this)[0].cells[4].innerHTML],{
				title: "Position at <b> test </b>",
				markerColor: "red"
			});
			radiusArea = new L.circle([$(this)[0].cells[3].innerHTML,$(this)[0].cells[4].innerHTML], 10000, {color: 'red'})
			
			popupContent="<b>Receiving LoRa-GW (Unexact) Location</b><br>Time Processed: "+ConvertTime($(this)[0].cells[1].innerHTML)
			radiusArea.addTo(map)
		}else{
			console.log("Pinged Location")
		}
		
		currentMark.bindPopup(popupContent)
		currentMark.addTo(map)
		
		currentMark.openPopup();
		map.panTo(currentMark.getLatLng())
		
	});
}

function onOpenWiki(){
	window.open("/wiki/");
}

function selectionColor(event){
	let selected=event.target.value
	if(selected=="Light"){
		$(".sidenav").css("background-color","white")
	}
	if(selected=="Dark"){
		$(".sidenav").css("background-color","#111")
	}
	if(selected=="TU Delft"){
		$(".sidenav").css("background-color","#00a6d6")
			
	}
	if(selected=="ETV"){
		$(".sidenav").css("background-color","#b41f21")
			
	}
}
function onOpenSettings(){
	sideBarElement=document.getElementById("mySidenav")
	sideBarElement.innerHTML="";
	backButton=getBackButton()
	sideBarElement.append(backButton);
	loginButton=getLoginButton();
	sideBarElement.append(loginButton)
	let title= getMenuDiv("titles","Settings")
	sideBarElement.append(title)
	let text= getSnippet("Change background color:")
	sideBarElement.append(text)
	
	let colorMenu = document.createElement("select")
	let blackOpt = document.createElement("option")
	blackOpt.innerHTML="Dark"
	let whiteOpt = document.createElement("option")
	whiteOpt.innerHTML="Light"
	let tuOpt = document.createElement("option")
	tuOpt.innerHTML="TU Delft"
	let etvOpt = document.createElement("option")
	etvOpt.innerHTML="ETV"

	colorMenu.append(blackOpt)
	colorMenu.append(whiteOpt)
	colorMenu.append(tuOpt)
	colorMenu.append(etvOpt)
	
	colorMenu.className="selectColor"
	
	colorMenu.style.display="inline-block"
	colorMenu.style.margin="8%"
	
	
	sideBarElement.append(colorMenu)
	$(".selectColor").change(selectionColor)
	
}
function onOpenCredits(){
	sideBarElement=document.getElementById("mySidenav")
	sideBarElement.innerHTML="";
	backButton=getBackButton()
	sideBarElement.append(backButton);
	loginButton=getLoginButton();
	sideBarElement.append(loginButton)
	let title= getMenuDiv("titles","Info & Credits")
	sideBarElement.append(title)
	let text= getSnippet("This Website/WebApp is part of an ongoing research on IoT devices that will be given the purpose of tracking birds, the research started by the aim of performing research on the habitat of the perigrine falcon but has been extended to other bird species as well. The website does not allow registration, if you are truly interested and/or have a request pleae write an email to ........................ ")
	let text2 = getSnippet("Find the research paper related to this project here:<a href='https://slechtvalk.tudelft.nl/A%20Novel%20LoRa-Based%20Wildlife%20Tracker.pdf'>A Novel LoRa-Based Wildlife Tracker</a>.")
	sideBarElement.append(text)
	sideBarElement.append(text2)
}
function onSavedPathsMode(){
	//Arrange various stuff to display tracker mode
	sideBarElement=document.getElementById("mySidenav")
	sideBarElement.innerHTML="";
	backButton=getBackButton()
	sideBarElement.append(backButton);
	closeButton=getCloseButton();
	sideBarElement.append(closeButton);
	loginButton=getLoginButton();
	sideBarElement.append(loginButton)
	let title= getMenuDiv("titles","Saved Paths")
	sideBarElement.append(title)
	let text= getSnippet("Select among one of the possible paths.<br>Paths can be created and started inside Tracking mode.<br> CSV File is available for downloading at the list bottom. <br><br>")
	sideBarElement.append(text)
	let listFilesCsv=listCSV();
	csvMenu=getBootStrapMenu("CSV Files",JSON.parse(listFilesCsv))
	sideBarElement.append(csvMenu)

	$(sideBarElement).ready(function() {
		$('.select2').select2({
			closeOnSelect: false
		});
	});
	
	$('.select2').on('change',selectionCallback)
	
	let csvTable= getMenuDiv("table-responsive","")
	csvTable.style.fontSize="8px"
	
	sideBarElement.append(csvTable)
	
	console.log(csvMenu)
	//csvSelector=csvMenu.getElementsByClassName("selection")
	//csvSelector.options[csvSelector.selectedIndex]
	//linkButton=getMenuElement("Live Device Tracking","#")
	//sideBarElement.append(linkButton)
	//linkButton=getMenuElement("Saved Paths","#")
	//sideBarElement.append(linkButton)
	//linkButton=getMenuElement("Clients","#")
	//sideBarElement.append(linkButton)
	//linkButton=getMenuElement("Contact","#")
	//sideBarElement.append(linkButton)	
}

// Use this to get most session status data (isItTracking? etc...)
function onLoginMode(){
	sideBarElement=document.getElementById("mySidenav")
	sideBarElement.innerHTML="";
	backButton=getBackButton()
	sideBarElement.append(backButton);
	closeButton=getCloseButton();
	sideBarElement.append(closeButton);
	
	let loginForm=document.createElement("form")
	loginForm.id="LoginForm"
	//loginForm.setAttribute("onsubmit","logintest();return false;")
	loginForm.setAttribute("method","post")
	loginForm.setAttribute("action", "")
	loginForm.style.fontFamily="Verdana, Geneva, sans-serif"
	loginForm.style.textAlign="center"
	
	
	let UInputSymb=document.createElement("i")
	let UInput=document.createElement("input")
	
	UInput.setAttribute("type","text")
	UInput.setAttribute("name","Username")
	UInput.setAttribute("required", true)
	//UInput.setAttribute("placeholder","username")
	UInput.style.backgroundColor="none"
	UInput.style.background = "none"
	UInput.style.fontSize="30px"
	
	UInputSymb.style.ariaHidden="true"
	UInputSymb.className="fa fa-user fa-fw fa-2x"

	
	let UPass=document.createElement("input")
	let UPassSymb=document.createElement("i")

	UPass.setAttribute("type","password")
	UPass.setAttribute("name","Password")
	UPass.setAttribute("required", true)
	//UPass.setAttribute("placeholder","password")
	UPass.style.background = "none"
	UPass.style.fontSize="30px"
	
	UPassSymb.style.ariaHidden="true"
	UPassSymb.className="fa fa-key fa-fw fa-2x"
	
	let subBtn=document.createElement("input")
	subBtn.setAttribute("type","submit")
	
	subBtn.style.backgroundColor="none"
	subBtn.style.background = "none"
	subBtn.style.fontSize="30px"
	

	
	
	
	let WrapUInput=getMenuDiv("wrapuser","")
	// Wrapper
	WrapUInput.append(UInputSymb)
	WrapUInput.append(UInput)
	WrapUInput.style.display="inline-block"
	WrapUInput.style.textAlign="center"
	WrapUInput.style.left="0"
	WrapUInput.style.right="0"
	WrapUInput.style.border= "solid rgb(143,143,143)"
	WrapUInput.style.borderRadius="5px"
	WrapUInput.style.backgroundColor="#555"
	
	let WrapUPass=getMenuDiv("wrappass","")
	// Wrapper
	WrapUPass.append(UPassSymb)
	WrapUPass.append(UPass)
	WrapUPass.style.display="inline-block"
	WrapUPass.style.textAlign="center"
	WrapUPass.style.left="0"
	WrapUPass.style.right="0"
	WrapUPass.style.border= "solid rgb(143,143,143)"
	WrapUPass.style.borderRadius="5px"
	WrapUPass.style.backgroundColor="#555"
	
	let WrapsubBtn=getMenuDiv("wrapbtn","")
	WrapsubBtn.append(subBtn)
	WrapsubBtn.style.display="inline-block"
	WrapsubBtn.style.textAlign="center"
	WrapsubBtn.style.left="0"
	WrapsubBtn.style.right="0"
	WrapsubBtn.style.border= "solid rgb(143,143,143)"
	WrapsubBtn.style.borderRadius="5px"
	WrapsubBtn.style.backgroundColor="#555"
	
	
	
	let userSnippet=getSnippet("Username:")
	userSnippet.style.textAlign="center"
	let passSnippet=getSnippet("Password:")
	passSnippet.style.textAlign="center"
	let btnSnippet=getSnippet("-")
	btnSnippet.style.textAlign="center"
	
	loginForm.append(userSnippet)
	loginForm.append(WrapUInput)
	loginForm.append(passSnippet)
	loginForm.append(WrapUPass)
	loginForm.append(btnSnippet)
	loginForm.append(WrapsubBtn)
	
	let loginTitle=getMenuDiv("titles","Login to unleash our power.")
	let loginSnip=getSnippet("You can explore our services yet you will need to login to administer your devices and paths.<br>In order to register to our system you must send an e-mail via contacts.")
	sideBarElement.append(loginTitle)
	sideBarElement.append(loginSnip)
	sideBarElement.append(loginForm)
	
	let debugelem=getMenuDiv("loggedIn","test")
	sideBarElement.append(debugelem)
	$(".loggedIn").hide()
	
	$("#LoginForm").submit(function(e) {
		e.preventDefault();
		login();
	});
	
	function login() {
		console.log("Logging")
		let un = loginForm.Username.value;
		console.log(un)
		let pw = loginForm.Password.value;
		
		let loginFormData = new FormData();
		loginFormData.append("Username",un);
		loginFormData.append("Password",pw);
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.open("post", "logingate.php", false);
		xmlhttp.send(loginFormData)
		if (xmlhttp.status == 200) {
			//loginResults();
			console.log("Login Res")
			console.log(xmlhttp.response)
			if(getLogin()!=null){
				debugelem.innerHTML="Login Successful!"
				$(".loggedIn").css("backgroundColor","lawn green");
				$(".loggedIn").css("color","black");
				$(".loggedIn").css("borderRadius","10px");
				$(".loggedIn").show()
				setTimeout(function(){
					onMainMenuMode()
				},2000)
				//await sleep(2000);
				//sleep(2000).then(() => { onMainMenuMode() });
			}else{
				debugelem.innerHTML="Login has Failed!"
				$(".loggedIn").css("backgroundColor","red");
				$(".loggedIn").show()
				setTimeout(function(){
					onMainMenuMode()
				},2000)
			}

		}else{
			console.log("Some shit happened")
			console.log(xmlhttp.response)
		}	
	}
	

	function loginResults() {
		var loggedIn = document.getElementsByClassName("LoggedIn");
		//var badLogin = document.getElementsByClassName("BadLogin");
		if (xmlhttp.responseText.indexOf("failed") == -1) {
			loggedIn.innerHTML = "Logged in as " + xmlhttp.responseText;
			loggedIn.style.display = "block";
			//form.style.display = "none";
		} else {
			//badLogin.style.display = "block";
			//form.Username.select();
			//form.Username.className = "Highlighted";
			setTimeout(function() {
				//badLogin.style.display = 'none';
			}, 3000);
		}
	}
	
}
	 
	 
function onUserSettingsMode(){
	
	sideBarElement=document.getElementById("mySidenav")
	sideBarElement.innerHTML="";
	backButton=getBackButton()
	sideBarElement.append(backButton);
	closeButton=getCloseButton();
	sideBarElement.append(closeButton);
	
	let UserSettingsTitle=getMenuDiv("titles","User Settings")
	let UserSettingsSnip=getSnippet("You may here Change Password.<br>In order to register to our system you must send an e-mail via contacts.")
	sideBarElement.append(UserSettingsTitle)
	sideBarElement.append(UserSettingsSnip)
	
	let PswChangeForm=document.createElement("form")
	PswChangeForm.id="PswChangeForm"
	//loginForm.setAttribute("onsubmit","logintest();return false;")
	PswChangeForm.setAttribute("method","post")
	PswChangeForm.setAttribute("action", "")
	PswChangeForm.style.fontFamily="Verdana, Geneva, sans-serif"
	PswChangeForm.style.textAlign="center"
	
	
	let OldPassSymb=document.createElement("i")
	let OldPassInput=document.createElement("input")
	
	OldPassInput.setAttribute("type","password")
	OldPassInput.setAttribute("name","oldPassword")
	OldPassInput.setAttribute("required", true)
	OldPassInput.style.backgroundColor="none"
	OldPassInput.style.background = "none"
	OldPassInput.style.fontSize="30px"
	
	OldPassSymb.style.ariaHidden="true"
	OldPassSymb.className="fa fa-key fa-fw fa-2x"

	
	let NewPassInput=document.createElement("input")
	let NewPassSymb=document.createElement("i")

	NewPassInput.setAttribute("type","password")
	NewPassInput.setAttribute("name","newPassword")
	NewPassInput.setAttribute("required", true)
	NewPassInput.style.background = "none"
	NewPassInput.style.fontSize="30px"
	
	NewPassSymb.style.ariaHidden="true"
	NewPassSymb.className="fa fa-key fa-fw fa-2x"
	
	let NewPassInput2=document.createElement("input")
	let NewPassSymb2=document.createElement("i")

	NewPassInput2.setAttribute("type","password")
	NewPassInput2.setAttribute("name","newPassword2")
	NewPassInput2.setAttribute("required", true)
	NewPassInput2.style.background = "none"
	NewPassInput2.style.fontSize="30px"
	
	NewPassSymb2.style.ariaHidden="true"
	NewPassSymb2.className="fa fa-key fa-fw fa-2x"
	
	let subBtn=document.createElement("input")
	subBtn.setAttribute("type","submit")
	
	subBtn.style.backgroundColor="none"
	subBtn.style.background = "none"
	subBtn.style.fontSize="30px"
	

	
	
	
	let WrapOldPass=getMenuDiv("wrapoldpass","")
	// Wrapper
	WrapOldPass.append(OldPassSymb)
	WrapOldPass.append(OldPassInput)
	WrapOldPass.style.display="inline-block"
	WrapOldPass.style.textAlign="center"
	WrapOldPass.style.left="0"
	WrapOldPass.style.right="0"
	WrapOldPass.style.border= "solid rgb(143,143,143)"
	WrapOldPass.style.borderRadius="5px"
	WrapOldPass.style.backgroundColor="#555"
	
	let WrapNewPass=getMenuDiv("wrapnewpass","")
	// Wrapper
	WrapNewPass.append(NewPassSymb)
	WrapNewPass.append(NewPassInput)
	WrapNewPass.style.display="inline-block"
	WrapNewPass.style.textAlign="center"
	WrapNewPass.style.left="0"
	WrapNewPass.style.right="0"
	WrapNewPass.style.border= "solid rgb(143,143,143)"
	WrapNewPass.style.borderRadius="5px"
	WrapNewPass.style.backgroundColor="#555"
	
	let WrapNewPass2=getMenuDiv("wrapnewpass","")
	// Wrapper
	WrapNewPass2.append(NewPassSymb2)
	WrapNewPass2.append(NewPassInput2)
	WrapNewPass2.style.display="inline-block"
	WrapNewPass2.style.textAlign="center"
	WrapNewPass2.style.left="0"
	WrapNewPass2.style.right="0"
	WrapNewPass2.style.border= "solid rgb(143,143,143)"
	WrapNewPass2.style.borderRadius="5px"
	WrapNewPass2.style.backgroundColor="#555"
	
	let WrapsubBtn=getMenuDiv("wrapbtn","")
	WrapsubBtn.append(subBtn)
	WrapsubBtn.style.display="inline-block"
	WrapsubBtn.style.textAlign="center"
	WrapsubBtn.style.left="0"
	WrapsubBtn.style.right="0"
	WrapsubBtn.style.border= "solid rgb(143,143,143)"
	WrapsubBtn.style.borderRadius="5px"
	WrapsubBtn.style.backgroundColor="#555"
	
	
	
	let oldPassSnippet=getSnippet("Old Password:")
	oldPassSnippet.style.textAlign="center"
	let newPassSnippet=getSnippet("New Password:")
	newPassSnippet.style.textAlign="center"
	let newPassSnippet2=getSnippet("Repeat New Password:")
	newPassSnippet2.style.textAlign="center"
	let btnSnippet=getSnippet("-")
	btnSnippet.style.textAlign="center"
	
	PswChangeForm.append(oldPassSnippet)
	PswChangeForm.append(WrapOldPass)
	PswChangeForm.append(newPassSnippet)
	PswChangeForm.append(WrapNewPass)
	PswChangeForm.append(newPassSnippet2)
	PswChangeForm.append(WrapNewPass2)
	PswChangeForm.append(btnSnippet)
	PswChangeForm.append(WrapsubBtn)
	

	sideBarElement.append(PswChangeForm)
	
	let debugelem=getMenuDiv("successfulChange","test")
	sideBarElement.append(debugelem)
	$(".successfulChange").hide()
	
	$("#PswChangeForm").submit(function(e) {
		e.preventDefault();
		changepass();
	});
	
	function changepass() {
		let op = PswChangeForm.oldPassword.value;
		console.log(op)
		let np = PswChangeForm.newPassword.value;
		let np2 = PswChangeForm.newPassword2.value;
		
		if(np!==np2){
			console.log("OK PSW");
			debugelem.innerHTML="The New Password Repetion does not match!";
			$(".successfulChange").css("backgroundColor","red");
			$(".successfulChange").css("color","black");
			$(".successfulChange").css("borderRadius","10px");
			$(".successfulChange").show();
			setTimeout(function(){
				onMainMenuMode()
			},2000)
			return;
		}
		
		let pswFormData = new FormData();
		pswFormData.append("mode","changepsw");
		pswFormData.append("pswold",op);
		pswFormData.append("pswnew",np);
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.open("post", "administration.php", false);
		xmlhttp.send(pswFormData)
		if (xmlhttp.status == 200) {
			//loginResults();
			console.log("Change Password Response")
			console.log(xmlhttp.response)

			if(JSON.parse(xmlhttp.response)["status"]=="ok"){
				console.log("OK PSW");
				debugelem.innerHTML="Change Successful!";
				$(".successfulChange").css("backgroundColor","green");
				$(".successfulChange").css("color","black");
				$(".successfulChange").css("borderRadius","10px");
				$(".successfulChange").show();
				setTimeout(function(){
					onMainMenuMode()
				},2000)
			}else{
				debugelem.innerHTML="Change has Failed!";
				$(".successfulChange").css("backgroundColor","red");
				$(".successfulChange").css("color","black");
				$(".successfulChange").css("borderRadius","10px");
				$(".successfulChange").show();
				setTimeout(function(){
					onMainMenuMode()
				},2000)
			}

		}else{
			console.log("Some shit happened")
			console.log(xmlhttp.response)
		}
	}

}

</script>

		
</body>
 
</html>