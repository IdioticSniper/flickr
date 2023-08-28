<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/logincheck.php");

// NOTE FROM SNIPER: THIS PAGE SERIOUSLY HAS TO BE REMADE DUE TO THE GODAWFUL SOLUTIONS I CAME UP WITH.
// THIS ISNT FINAL - SOMEONE JUST HELP REWRITE. THANKS.

if(isset($_GET["delete"])) {
	$_GET["delete"] = intval($_GET['delete']);
	$stmt = $conn->prepare("SELECT * FROM testimonials WHERE id=:t0");
	$stmt->bindParam(':t0', $_GET["delete"]);
	$stmt->execute();
	foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $testimonial);
	if($_SESSION["id"] == $testimonial->sent_to || $testimonial->sent_by) {
		$stmt = $conn->prepare("DELETE FROM testimonials WHERE id=:t0");
		$stmt->bindParam(':t0', $_GET["delete"]);
		$stmt->execute();
		header("Location: /testimonials_manage.php");
	}
}

if(isset($_GET["approve"])) {
	$_GET["approve"] = intval($_GET['approve']);
	$stmt = $conn->prepare("SELECT * FROM testimonials WHERE id=:t0");
	$stmt->bindParam(':t0', $_GET["approve"]);
	$stmt->execute();
	foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $testimonial);
	if($_SESSION["id"] == $testimonial->sent_to) {
		$stmt = $conn->prepare("UPDATE testimonials set approved=1 WHERE id=:t0");
		$stmt->bindParam(':t0', $_GET["approve"]);
		$stmt->execute();
		header("Location: /testimonials_manage.php");
	}
}

?>

<h1><a href="#" style="text-decoration: none">Your profile</a> / Testimonials</h1>

	<table>
		<tr>
			<td id="Hint"> 
				<img src="/images/spaceball.gif" alt="spacer image" width="20" height="1"> 
			</td>
			
			<td id="GoodStuff" valign="top">
				<?php
				if(isset($_GET["written"])) {
					echo "<div class=\"Confirm\">This testimonial has been saved and is now awaiting approval.</div>";
				}
				
				$stmt = $conn->prepare("SELECT * from testimonials WHERE sent_to=:t0 AND approved=0");
				$stmt->bindParam(":t0", $_SESSION["id"]);
				$stmt->execute();
				if($stmt->rowCount() > 0) {
					echo "<table><h3>Testimonials for you to approve</h3></table>";
					foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $testimonial) {
							$stmt = $conn->prepare("SELECT * FROM users WHERE id=:t0");
							$stmt->bindParam(':t0', $testimonial->sent_by);
							$stmt->execute();
							foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $tst_user);
							echo "<table><tr>
							<td valign=\"top\"><img src=\"". $tst_user->display_picture . "\" alt=\"view profile\" width=\"48\" height=\"48\" align=\"left\" hspace=\"5\" /></td>
							<td>
							<h4><a href=\"profile.php?id=". $testimonial->sent_by . "\">". $tst_user->screen_name . "</a> says:</h4>
							<p>\"". nl2br(htmlspecialchars($testimonial->text)) . "\"</p><a href=\"?approve=" . $testimonial->id . "\"><input type=\"submit\" class=\"SmallButt\" value=\"APPROVE\" style=\"padding: 3px\"></a> OR <a href=\"?delete=" . $testimonial->id . "\"><input type=\"submit\" class=\"SmallDeleteButt\" value=\"DELETE\" style=\"padding: 3px\"></a>
							</td>
						</tr></table>
						";
					}
				}		
				
				?>
				<table><h3>Testimonials written for you by other people</h3></table>
				<?php
				$stmt = $conn->prepare("SELECT * from testimonials WHERE sent_to=:t0 AND approved=1");
				$stmt->bindParam(":t0", $_SESSION["id"]);
				$stmt->execute();
				if($stmt->rowCount() > 0) {
					foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $testimonial) {
							$stmt = $conn->prepare("SELECT * FROM users WHERE id=:t0");
							$stmt->bindParam(':t0', $testimonial->sent_by);
							$stmt->execute();
							foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $tst_user);
							echo "<table><tr>
							<td valign=\"top\"><img src=\"". $tst_user->display_picture . "\" alt=\"view profile\" width=\"48\" height=\"48\" align=\"left\" hspace=\"5\" /></td>
							<td>
							<h4><a href=\"profile.php?id=". $testimonial->sent_by . "\">". $tst_user->screen_name . "</a> says:</h4>
							<p>\"". nl2br(htmlspecialchars($testimonial->text)) . "\"</p><a href=\"?delete=" . $testimonial->id . "\"><input type=\"submit\" class=\"SmallDeleteButt\" value=\"DELETE\" style=\"padding: 3px\"></a>
							</td>
						</tr></table>
						";
					}
				} else {
					echo "<p>Nobody has written a testimonial for you yet. Why not ask your friends to write one about you?</p>";
				}
				?>
				<table><h3>Testimonials you've written for other people</h3></table>
				<?php
				$stmt = $conn->prepare("SELECT * from testimonials WHERE sent_by=:t0");
				$stmt->bindParam(":t0", $_SESSION["id"]);
				$stmt->execute();
				foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $testimonial);
				if($stmt->rowCount() > 0) {
					if($testimonial->approved == 0) { 
						echo "<table><h4>Awaiting approval</h4></table><table>";
						$stmt = $conn->prepare("SELECT * from testimonials WHERE sent_by=:t0 AND approved=0");
						$stmt->bindParam(":t0", $_SESSION["id"]);
						$stmt->execute();
						foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $testimonial);
						$stmt = $conn->prepare("SELECT * FROM users WHERE id=:t0");
						$stmt->bindParam(':t0', $testimonial->sent_to);
						$stmt->execute();
						foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $tst_user);
						echo "<table><tr>
						<td valign=\"top\"><img src=\"". $tst_user->display_picture . "\" alt=\"view profile\" width=\"48\" height=\"48\" align=\"left\" hspace=\"5\" /></td>
						<td>
						<h4>You said this about <a href=\"profile.php?id=". $testimonial->sent_to . "\">". $tst_user->screen_name . "</a>:</h4>
						<p>\"". nl2br(htmlspecialchars($testimonial->text)) . "\"</p><a href=\"?delete=" . $testimonial->id . "\"><input type=\"submit\" class=\"SmallDeleteButt\" value=\"DELETE\" style=\"padding: 3px\"></a>
						</td>
					</tr></table>
					";
					} else if($testimonial->approved == 1) {
						echo "<table><h4>Approved</h4></table>";
						$stmt = $conn->prepare("SELECT * from testimonials WHERE sent_by=:t0 AND approved=1");
						$stmt->bindParam(":t0", $_SESSION["id"]);
						$stmt->execute();
						foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $testimonial);
						$stmt = $conn->prepare("SELECT * FROM users WHERE id=:t0");
						$stmt->bindParam(':t0', $testimonial->sent_to);
						$stmt->execute();
						foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $tst_user);
						echo "<table><tr>
						<td valign=\"top\"><img src=\"". $tst_user->display_picture . "\" alt=\"view profile\" width=\"48\" height=\"48\" align=\"left\" hspace=\"5\" /></td>
						<td>
						<h4>You said this about <a href=\"profile.php?id=". $testimonial->sent_to . "\">". $tst_user->screen_name . "</a>:</h4>
						<p>\"". nl2br(htmlspecialchars($testimonial->text)) . "\"</p><a href=\"?delete=" . $testimonial->id . "\"><input type=\"submit\" class=\"SmallDeleteButt\" value=\"DELETE\" style=\"padding: 3px\"></a>
						</td>
					</tr></table>
					";
					}
				}
				?>
				</table>
				
			</td>
		</tr>
	</table>


<?php require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>