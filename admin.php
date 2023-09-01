<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/logincheck.php");

if(isset($_SERVER["HTTP_REFERER"])) { 
	$http_referer = $_SERVER["HTTP_REFERER"]; 
} else { 
	$http_refer = "/"; 
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id=:t0");
$stmt->bindParam(':t0', $_SESSION["id"]);
$stmt->execute();
foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $user);

if($usr->isAdmin == 0) {
	header("Location:" . $http_referer);
}

if(isset($_POST["term"])) {
	
	if($_POST["term_id"] == $_SESSION["id"]) { die("You can't ban yourself"); }
	
	$stmt = $conn->prepare("SELECT * FROM users WHERE id=:t0");
	$stmt->bindParam(':t0', $_POST["term_id"]);
	$stmt->execute();
	foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $user);
	
	if($user->isAdmin == 1) { die("User is already banned"); }
	if($user->isBanned == 1) { die("User is already banned"); }
	
	$stmt = $conn->prepare("UPDATE users SET `isBanned`=1 WHERE `id`=:t0");
	$stmt->bindParam(':t0', $_POST["term_id"]);
	$stmt->execute();
	
	$stmt = $conn->prepare("DELETE FROM `comments` WHERE `posted_by`=:t0");
	$stmt->bindParam(':t0', $_POST["term_id"]);
	$stmt->execute();
	
	$stmt = $conn->prepare("SELECT * FROM photos WHERE `uploaded_by`=:t0");
	$stmt->bindParam(':t0', $_POST["term_id"]);
	$stmt->execute();
	
	foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $photo) {
		
		$stmt = $conn->prepare("DELETE FROM comments WHERE `posted_to`=:t0");
		$stmt->bindParam(':t0', $photo->id);
		$stmt->execute();
		
		$stmt = $conn->prepare("DELETE FROM photos WHERE `uploaded_by`=:t0");
		$stmt->bindParam(':t0', $_POST["term_id"]);
		$stmt->execute();
		
		unlink($_SERVER["DOCUMENT_ROOT"] . "/photos/" . $photo->id . ".jpg");
		unlink($_SERVER["DOCUMENT_ROOT"] . "/photos/" . $photo->id . ".t.jpg");
		unlink($_SERVER["DOCUMENT_ROOT"] . "/photos/" . $photo->id . ".m.jpg");
		
		if(file_exists($_SERVER["DOCUMENT_ROOT"] . "/photos/" . $photo->id . ".full.jpg")) {
			unlink($_SERVER["DOCUMENT_ROOT"] . "/photos/" . $photo->id . ".full.jpg");
		} else if(file_exists($_SERVER["DOCUMENT_ROOT"] . "/photos/" . $photo->id . ".full.png")) {
			unlink($_SERVER["DOCUMENT_ROOT"] . "/photos/" . $photo->id . ".full.png");
		} else if(file_exists($_SERVER["DOCUMENT_ROOT"] . "/photos/" . $photo->id . ".full.bmp")) {
			unlink($_SERVER["DOCUMENT_ROOT"] . "/photos/" . $photo->id . ".full.bmp");
		} else if(file_exists($_SERVER["DOCUMENT_ROOT"] . "/photos/" . $photo->id . ".full.tga")) {
			unlink($_SERVER["DOCUMENT_ROOT"] . "/photos/" . $photo->id . ".full.tga");
		}
	}
	echo "User banned";
}

?>

<h1>Admin panel.</h1>
<p>Use all of this stuff w/ caution - Note that on all fields you have to include the ID of what you want to take action on</p>
<hr>

<form method="post">
	<input type="text" name="term_id"><input type="submit" name="term" value="Terminate User">
</form>

<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>