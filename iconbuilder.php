<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php"); 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/logincheck.php"); 

$stmt = $conn->prepare("SELECT * FROM users WHERE id=:t0");
$stmt->bindParam(':t0', $user_id);
$stmt->execute();
foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $user);

if(isset($_POST["upload"])) {
	
	if(!isset($_FILES["icon"])) {
		die("no file");
	}
	
	$upload_extension   =  strtolower(pathinfo($_FILES["icon"]["name"], PATHINFO_EXTENSION));
	$photo_id 			= $user_id;
	$upload_tgt_preload = "buddyicons/$photo_id.full." . $upload_extension;
	$input 				= $upload_tgt_preload;
	$name      			= $_FILES['icon']['name']; 
	$temp_name  		= $_FILES['icon']['tmp_name'];
	
	if(move_uploaded_file($temp_name, $upload_tgt_preload)) {
		if(!in_array($upload_extension, $website["allowed_filetypes"])) {
			unlink($upload_tgt_preload);
			die("<p>Sorry, that filetype is not allowed</p>");
		}
	}
	$gdimage = NULL;

	switch (exif_imagetype($upload_tgt_preload)) {
		case IMAGETYPE_PNG:
			$gdimage = imagecreatefrompng($upload_tgt_preload);
			$bg = imagecreatetruecolor(imagesx($gdimage), imagesy($gdimage));
			imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
			imagealphablending($bg, TRUE);
			imagecopy($bg, $gdimage, 0, 0, 0, 0, imagesx($gdimage), imagesy($gdimage));
			imagedestroy($gdimage);
			$gdimage = $bg;
			break;
		case IMAGETYPE_JPEG:
			$gdimage = imagecreatefromjpeg($upload_tgt_preload);
			break;
		case IMAGETYPE_BMP:
			$gdimage = imagecreatefrombmp($upload_tgt_preload);
			break;	
		default:
			if ($upload_extension == "tga") {
				$gdimage = imagecreatefromtga($upload_tgt_preload);
			} else {
				die("Unsupported format");
			}
	}
	
	imagejpeg(imagescale($gdimage, 48, 48), "buddyicons/$photo_id.jpg");
	$final = "/buddyicons/$user_id.jpg";
	unlink("buddyicons/$photo_id.full.$upload_extension");
	$stmt = $conn->prepare("UPDATE users SET display_picture=:pic WHERE id=$user_id");
	$stmt->bindParam(":pic", $final);
	$stmt->execute();
	header("Location: /iconbuilder.php?success");
}

if(isset($_POST["del"])) {
	$final = "/images/buddyicon.jpg";
	$stmt = $conn->prepare("UPDATE users SET display_picture=:pic WHERE id=$user_id");
	$stmt->bindParam(":pic", $final);
	$stmt->execute();
	header("Location: /iconbuilder.php?success");
}

?>
<h1><a href="#" style="text-decoration: none">Edit your profile</a> / <a href="/iconbuilder.php" style="text-decoration: none">Your buddy icon</a></h1>

	<table>
		<tr>
			<td id="Hint"> 
				<p>Your buddy icon is what we use to represent you when you're in <?php echo $website["instance_name"]; ?>.</p>
				<center>
					<img src="/images/buddyicon.jpg">
					<p>Your icon is <br>48 X 48 pixels in size.
				</center>
				<img src="/images/spaceball.gif" alt="spacer image" width="170" height="1"> 
			</td>
			
			<td id="GoodStuff" valign="top">
			<?php if($user->display_picture !== "/images/buddyicon.jpg") {
				echo "				<div class=\"CurrentIcon\">
					<img src=\"" . $user->display_picture . "\"><span style=\"margin-left: 5px;font-size: 19px;margin-bottom: 10px\">This is your buddy icon at the moment.</span><br>
					<form method=\"post\">
						<input type=\"submit\" name=\"del\" value=\"DELETE\" style=\"margin-left: 5px;margin-bottom: 10px\"><br>
					</form>
				</div>";
			} else {
				echo "We've got an \"Icon Builder\" to help you if you need it. It's a <b>tool</b> which allows you to choose an image, load it into the builder and then crop or resize an area of that image to publish as your icon.</p>";
			}
			?>
				<h3>Where's the image you want to use?</h3>
				<table>
					<tr>
						<td>
							<ul>
								<li><a href="#">In your <?php echo $website["instance_name"]; ?> photos</li>
								<li><a href="#">On your computer</li>
								<li><a href="#">On the web</li>
							</ul>
						</td>
						<td valign="top">
							<p><b>OR</b> if you have a 48x48 icon ready, upload it here.</p>
							<form method="post" enctype="multipart/form-data">
								<input type="file" name="icon"><br>
								<input type="submit" name="upload" class="Butt" value="UPLOAD" style="margin-top: 5px">
							</form>
							<img src="/images/spaceball.gif" alt="spacer image" width="90" height="1" style="border: none;">
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>



<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>