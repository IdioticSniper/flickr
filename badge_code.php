<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/config.php");

if(!isset($_GET["nsid"]))		{ die("No user has been set"); }
if(!isset($_GET["name"]))		{ die("No name toggle set"); }
if(!isset($_GET["count"]))		{ $_GET["count"] = 10; }
if(!isset($_GET["display"])) 	{ die("No display type set"); }
if(!isset($_GET["size"]))		{ die("No photo size set"); }
if(!isset($_GET["raw"]))		{ $_GET["raw"] = 0; }

$_GET["nsid"] = intval($_GET["nsid"]);
$_GET["name"] = intval($_GET["name"]);
$count = intval($_GET["count"]);


if($_GET["display"] == "random") {
	$display = "rand()";
} else if($_GET["display"] == "latest") {
	$display = "ORDER BY id DESC";
}

if($_GET["size"] == "small") { $size = "t"; } 
if($_GET["size"] == "thumb") { $size = "t"; } 
if($_GET["size"] == "mid") { $size = "m"; }


$stmt = $conn->prepare("SELECT * FROM users WHERE id=:t0");
$stmt->bindParam(':t0', $_GET["nsid"]);
$stmt->execute();
foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $user);

if(substr($user->screen_name, -1) == "s") { 
	$sn_display = htmlspecialchars($user->screen_name) . "\'"; 
} else {
	$sn_display = htmlspecialchars($user->screen_name) . "\'s";
}

$url = "http://" . $_SERVER['SERVER_NAME'];

if($_GET["raw"] == 0) {
	echo "// make sure they're declared

		var flickr_badge_border;
		var flickr_badge_width;
		var flickr_badge_background_color;
		var flickr_badge_text_font;
		var flickr_badge_image_border;
		var flickr_badge_link_color;

		// format them as we need them
		
		var flickr__dbr = flickr_badge_border?'border: '+flickr_badge_border+';':'';
		var flickr__wth = flickr_badge_width?'width: '+flickr_badge_width+';':'';
		var flickr__bg  = flickr_badge_background_color?'background-color: '+flickr_badge_background_color+';':'';
		var flickr__fnt = flickr_badge_text_font?'font: '+flickr_badge_text_font+';':'';
		var flickr__bdr = flickr_badge_image_border?'border: '+flickr_badge_image_border+';':'';
		var flickr__lnk = flickr_badge_link_color?'color: '+flickr_badge_link_color+';':'';

		// write the badge

		document.write('<div style=\"padding: 6px 4px; '+flickr__bg+flickr__wth+flickr__dbr+'\">');
		document.write('	<table cellspacing=\"0\" cellpadding=\"4\" style=\"'+flickr__wth+'\">');
		";

	$stmt = $conn->prepare("SELECT * FROM photos WHERE uploaded_by=:t0 $display LIMIT $count");
	$stmt->bindParam(':t0', $_GET['nsid']);
	$stmt->execute();
	foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $photo) {
		echo "document.write('		<tr align=\"center\"><td style=\"'+flickr__fnt+'\"><a href=\"" . $url . "/photo.php?id=" . $photo->id . "\"><img src=\"" . $url . "/photos/" . $photo->id . "." . $size . ".jpg\" style=\"'+flickr__bdr+'\" /></a></td></tr>');
		";
	}
	if($_GET["name"] == 1) {
		echo "document.write('		<tr><td style=\"'+flickr__fnt+'\" align=\"left\" valign=\"top\"><img src=\"". $url . $user->display_picture . "\" width=\"48\" height=\"48\" align=\"left\" style=\"margin-right: 3px;\"><a href=\"" . $url . "/profile.php?id=" . $photo->uploaded_by . "\" style=\"'+flickr__lnk+'\">More of ". $sn_display . " photos...</a></td></tr>');
		";
	}
	echo "document.write('		<tr><td style=\"'+flickr__fnt+'\" align=\"center\" valign=\"top\"><a href=\"" . $url . "\" style=\"'+flickr__lnk+'\">www.<strong><font color=\"#1E12CA\">snipp</font><font color=\"#EBCA14\">r</font></strong>.win</a></td></tr>');
		document.write('	</table>');
		document.write('</div>');
";
} else {
		$photocount = 0;
		$stmt = $conn->prepare("SELECT * FROM photos WHERE uploaded_by=:t0 $display LIMIT $count");
		$stmt->bindParam(':t0', $_GET['nsid']);
		$stmt->execute();
		foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $photo) {
			$photocount++;
			echo "document.write('<a href=\"" . $url . "/photo.php?id=" . $photo->id . "\"><img src=\"" . $url . "/photos/" . $photo->id . "." . $size . ".jpg\" class=\"flickrimg\" id=\"flickrimg" . $photocount . "\" /></a>');";
		}
}
?>
