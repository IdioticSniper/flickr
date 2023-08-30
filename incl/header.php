<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/config.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/meta.php");

if(isset($_SESSION["id"])) {
	$user_id = $_SESSION["id"];
} else {
	$user_id = NULL;
	$_SESSION["id"] = NULL;
}

if($website["maintenance"] == true) {
	die(require_once($_SERVER["DOCUMENT_ROOT"] . "/maintenance.php"));
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title><?php echo $title; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link href="/css/flickr.css?version=1.195" rel="stylesheet" type="text/css">
	<link rel="shortcut icon" type="image/ico" href="<?php echo $website["favicon"]; ?>" />
</head>
<body onload="if (self.location.href != top.location.href) top.location.href = self.location.href;">
<script language="JavaScript" type="text/javascript" src="/javascript/tooltips.js?version=1.2"></script>
<script language="JavaScript" type="text/javascript" src="/javascript/fpi-init.js"></script>
<script language="JavaScript" type="text/javascript" src="/javascript/fpi-writevb.js"></script>
<script language="JavaScript" type="text/javascript" src="/javascript/fpi-main.js"></script>
<script type="text/javascript" runat="client">



var goodStuff=false;


function checkFlash() {
	if (actualVersion>=6) return true;
	return false;
}
function checkBrowser() {
	var ua= navigator.userAgent;
	if (navigator.appVersion.toLowerCase().indexOf("mac") > 0) {
		if (navigator.userAgent.indexOf('Gecko') == -1) return false;
		return true;
	} else { // windows or unix
		if (ua.indexOf("MSIE 5.6") > 0 || ua.indexOf("MSIE 5.5") > 0 || ua.indexOf("MSIE 6") > 0) {
			return true; // IE 5.5+
		} else {
			if (navigator.userAgent.indexOf('Gecko') > 0) return true; //
			return false;
		}
	}
}

if (checkFlash()) goodStuff=true;

window.focus();
window.name='home';
function openGame(extra) {
	if (!goodStuff) {
		var strA= new Array;
		strA.push('<p style="font-family:verdana"><i>Sorry, but you don\'t have the necessary browser goods! ');
		strA.push('You need to have <a href="http://www.macromedia.com/go/getflashplayer">Flash 6</a> ');
		strA.push('or greater installed. If you think I\'m wrong and that you have the right stuff, well then, ');
		strA.push('<a href="javascript:window.goodStuff=true;openGame()">click here</a></i></p>');

		document.getElementById('launch').innerHTML=strA.join('');
		return false;
	}
	 w = parseInt(screen.availWidth)-10+'';
	 h = parseInt(screen.availHeight)-30+'';
	 if (w>960) w = 960;
	 if (h>630) h = 630;
	window.open('/_chat/chat.gne?'+extra,'launchwin','width='+w+',height='+h+',top=0,left=0,scrollbars=no, status=no, resizable=yes')
}

</script>


<div id="Main">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="topnavi">
			<?php if(!isset($_SESSION["email"])) {
				echo "				| <a href=\"/\">Home</a> 
				| <a href=\"/photos.php\">Recent Photos</a> 
				| <a href=\"/learn_more.php\">Learn More</a> 
				| <a href=\"/register.php\">Register</a> 
				| <a href=\"/login.php\">Log In</a> 
				| <a href=\"/help.php\">Help</a> |";
			} else {
				echo "				| <a href=\"/\">Home</a> 
				| <a href=\"/photos.php\">Photos</a> 
				| <a href=\"/groups.php\">Groups</a> 
				| <a href=\"/mail.php\">" . $website["instance_name"] . " Mail</a> 
				| <a href=\"/invite.php\">Invite</a> 
				| <a href=\"/contacts.php\">Contacts</a>
				| <a href=\"/find_people.php\">Find People</a>
				| <a href=\"/help.php\">Help</a>
				| <a href=\"/logout.php\">Log Out</a> |";
			}
			?>
			</td>
			<td align="right">
				<a href="/"><img src="<?php echo $website["instance_logo"]; ?>" alt="<?php echo $website["instance_name"]; ?> Logo: click to get home" width="106" height="35" style="border: none;"></a>
			</td>
		</tr>
	</table>
	
