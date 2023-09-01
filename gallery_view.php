<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php"); 

if(!isset($_GET["id"])) { die("No user ID set."); }
$_GET["id"] = (int) $_GET["id"]; // better cast to int

$stmt = $conn->prepare("SELECT * FROM users WHERE id=:t0");
$stmt->bindParam(':t0', $_GET["id"]);
$stmt->execute();
foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $user);

$Now = new DateTime($user->last_login);

if(substr($user->screen_name, -1) == "s") { 
	$sn_display = $user->screen_name . "'"; 
} else {
	$sn_display = $user->screen_name . "'s";
}
$Now = new DateTime($user->last_login);

?>

<script language="Javascript">
<!--


function EditDesc(){
	document.getElementById('mygallerydesc').style.display = 'none';
	document.getElementById('mygalleryedit').style.display = 'block';
	return false;
}


//-->
</script>

	<h1><?php echo $sn_display; ?> gallery.</h1>
	<?php if($user->isBanned == 1) {
			echo "<p class=\"Problem\" style=\"margin-top: 30px; margin-left: 60px;\">This person is no longer active on " . $website["instance_name"] . "</p>";
			die(require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"));
	}
	?>
	<table>
		<tr>
			<td id="Hint">
			
				<p><a href="/profile.php?id=<?php echo $_GET["id"]; ?>"><?php echo $sn_display; ?> profile</a> /</p>
				<p style="margin-top:20px; font-size:12px;"><?php if(isset($user->gallery_desc)) { echo $user->gallery_desc; } ?></p>				
			
				<img src="/images/spaceball.gif" alt="spacer image" width="160" height="1">			
			</td>			
			<td id="GoodStuff">
			<?php
			$stmt = $conn->prepare("SELECT * FROM photos WHERE uploaded_by=:t0 ORDER by id DESC LIMIT 8");
			$stmt->bindParam(':t0', $_GET['id']);
			$stmt->execute();
			if($stmt->rowCount() > 0) {
				echo "<div>";
				foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $photo) {
					$stmt = $conn->prepare("SELECT * FROM comments WHERE posted_to = :t0"); 
					$stmt->bindParam(':t0', $photo->id);
					$stmt->execute();
					$comment_count = $stmt->rowCount();
					if($comment_count == 1) {
						$comment_display = $comment_count . " Comment";
					} else {
						$comment_display = $comment_count . " Comments";
					}
					echo "				<p class=\"StreamList\">
					<a href=\"/photo.php?id=" . $photo->id . "\"><img src=\"/photos/". $photo->id . ".t.jpg\" alt=\"". $photo->title  . "\" /></a>";
					if(isset($photo->description)) {
						echo "<br \>" . htmlspecialchars($photo->description);
					}
					echo "<br />
					<a href=\"/photo.php?id=". $photo->id . "\">" . $comment_display . "</a>
				</p>";
				}
			}
			?>
			
			</td>
		</tr>
	</table>

<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>