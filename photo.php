<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php"); 

$view_count = 0;
$add_view = true;
$current_ip = getIP();

if(!isset($_GET["id"])) { die("No photo ID set."); }
$_GET["id"] = (int) $_GET["id"];

// Fetch photo info
$stmt = $conn->prepare("SELECT * FROM photos WHERE id=:t0");
$stmt->bindParam(':t0', $_GET['id']);
$stmt->execute();
foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $photo);
if($photo == NULL) { die("photo is non existent"); }

// Fetch user info
$stmt = $conn->prepare("SELECT * FROM users WHERE id=:t0");
$stmt->bindParam(':t0', $photo->uploaded_by);
$stmt->execute();
foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $user);

// Parse the date
$Now = new DateTime($photo->uploaded_on);

// Handle comments
if(isset($_POST["submit"])) {
	$comment = $_POST["comment"];
	if(mb_strlen($comment, 'utf8') > 200) { die("comment text too long. max length is 200"); }
	if(mb_strlen($comment, 'utf8') < 1) { die("comment text is non existent"); }
	$stmt = $conn->prepare("INSERT INTO comments (posted_to, posted_by, text) VALUES (:posted_to, :posted_by, :text)");
	$stmt->bindParam(":posted_to", $_GET["id"]);
	$stmt->bindParam(":posted_by", $user_id);
	$stmt->bindParam(":text", $comment);
	$stmt->execute();
	header("Location: /photo.php?id=" . $_GET["id"]);
}

// Comment deletion
if(isset($_GET["delcomm"])) {
	$_GET["delcomm"] = intval($_GET['delcomm']);
	// TODO: make this stuff a whole load better
	$stmt = $conn->prepare("SELECT * FROM comments WHERE id=:t0");
	$stmt->bindParam(':t0', $_GET["delcomm"]);
	$stmt->execute();
	foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $comment);
	if($_SESSION["id"] == $comment->posted_by) {
		$stmt = $conn->prepare("DELETE FROM comments WHERE id=:t0");
		$stmt->bindParam(':t0', $_GET["delcomm"]);
		$stmt->execute();
	} else if($_SESSION["id"] == $photo->uploaded_by) {
		$stmt = $conn->prepare("DELETE FROM comments WHERE id=:t0");
		$stmt->bindParam(':t0', $_GET["delcomm"]);
		$stmt->execute();	
	}
}

// Update views and get count
$stmt = $conn->prepare("SELECT * FROM photo_views WHERE id=:t0");
$stmt->bindParam(':t0', $_GET['id']);
$stmt->execute();
foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $photo_view_ip) {
	if ($photo_view_ip->ip == $current_ip) {
		$add_view = false;	
	}
	$view_count++;
}

if ($add_view) {
	$stmt = $conn->prepare("INSERT INTO photo_views (id, ip) VALUES (:id, :ip)");
	$stmt->bindParam(":id", $_GET['id']);
	$stmt->bindParam(":ip", $current_ip);
	$stmt->execute();
	$view_count++;
}

?>
	<h1 style="margin-bottom: 10px;"><?php echo htmlspecialchars($photo->title); ?></h1>
	<table>
		<tr>			
			<td id="GoodStuff">
							<table cellspacing="0" cellpadding="0" style="margin-top:0px;">
								<tr>
									<td valign="top" style="padding:0px;">
										<div id="photoImgDiv<?php echo $_GET["id"]; ?>"><img src="/photos/<?php echo $_GET["id"]; ?>.jpg"></div>							
									</td>
									
									<td valign="top" style="padding-left: 20px; padding-top: 0px;">
									
										<table cellspacing="0" cellpadding="0" style="margin-top: 0px;">
											<tr>
												<td width="50" valign="top" style="padding-left: 0px; padding-top: 0px;"><a href="/profile_photos.php?id=<?php echo $photo->uploaded_by; ?>"><img src="<?php echo $user->display_picture; ?>" alt="view profile" width="48" height="48" style="border: solid 1px #000; margin: 0px;" /></a></td>
												<td valign="top" style="padding-bottom: 0px; padding-top: 0px; text-align: center;">
												<a href="/photo.php?id=<?php echo $_GET["id"]; ?>" class="ShowUsYerDate">
												<p style="margin-top: 0px; margin-bottom: 0px; font-size: 13px; font-weight: bold; text-transform: uppercase"><?php echo $Now->format('M'); ?></p>												
												<p style="line-height: 21px; margin-bottom: 0px; margin-top: 0px; font-size: 21px; font-weight: bold;"><?php echo $Now->format('d'); ?></p>
												<p style="line-height: 12px; margin-top: 0px; font-size: 12px; font-weight: normal; margin-bottom: 0px;"><?php echo $Now->format('Y');; ?></p>
												</a>
												</td>
											</tr>																					
										</table>

										<p class="DateTime" style="margin-top: 3px; margin-bottom: 3px;">From <a href="/profile_photos.php?id=<?php echo $photo->uploaded_by; ?>" title="Link to <?php echo $user->screen_name; ?>'s photos"><?php echo $user->screen_name; ?></a> at <a href="/photo.php?id=<?php echo $_GET["id"]; ?>" class="pale"><?php echo $Now->format('h') . "." . $Now->format('i') . "<span style=\"text-transform: lowercase\">" . $Now->format('A') . "</span>"; ?></a></p>
										<p style="margin-top: 0px;">
										<span class="DateTime">
	<img src="/images/icon_public.gif" style="vertical-align:middle; margin-right: 4px; margin-bottom: 4px; float:left; border:none;" alt="This photo is public" width="15" height="15" />This photo is public.
										</span>
										</p>

										<?php
										if($photo->tags !== "") {
										echo "<div class=\"TagList\">
											<h4>TAGS</h4>
											<div id=\"thetags\">";	
											$thetags = [];
											$thetags = array_merge($thetags, explode(" ", $photo->tags));
											$thetags = array_unique($thetags);
											foreach($thetags as $tag) {
												echo "<div id=\"tagdiv\"><a href=\"#\" class=\"pale\">" . htmlspecialchars($tag) . "</a></div>";
											}
										}
											?>
											
											</div>
											<br />
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="2" style="padding-top: 10px; padding-left: 65px;">
										<p style="width: 450px;"><?php echo nl2br(htmlspecialchars($photo->description)); ?></p>
										<?php if(isset($photo->camera)) {
											if($photo->camera !== "Unavailable") {
											echo "<p style=\"font-style: italic; color: #666; width: 450px;\">Taken with a <a href=\"photo_exif.php?id=" . $_GET["id"] . "\" style=\"color: #4B8FE3;\">" . htmlspecialchars($photo->camera) . "</a>.</p>";
											}
										}
										?>
									</td>
								</tr>
							</table>
			</td>
		</tr>
	</table>
	<table style="margin-top:0px;">
		<tr>
			<td id="Hint">
				<p style="margin-top: 20px;">This page has been viewed <b><?php echo $view_count; ?></b> times - as of <?php echo $Now->format('F') . " " . $Now->format('d'); ?>.</p>
				<img src="/images/spaceball.gif" alt="spacer image" width="180" height="1" style="border: none;">
			</td>
			<td valign="top" width="450"> 
					<?php if(!isset($_SESSION["id"])) {
						echo "				<table cellspacing=\"0\" cellpadding=\"0\" class=\"Tease\">
					<tr>
						<td><span style=\"font-weight: bold; color:#FF6699;\">PSST!</span> Would you like to comment?</td>
						<td>Choose a screen name...</td>
						</tr>
						<tr>
							<td class=\"Or\">(Already a member? <a href=\"/login.php\" title=\"Log in to " . $website["instance_name"] . "\">Log in</a>.)</td>
							<td align=\"center\"><input name=\"username\" type=\"text\" size=\"20\">&nbsp;<a href=\"/register.php\"><input type=\"image\" src=\"/images/button_go_up_small.gif\" style=\"border:none;\" alt=\"Get your screen name!\" align=\"absmiddle\"></a></td>
						</tr>
				</table>";
					} else {
						echo "<table cellspacing=\"0\" cellpadding=\"0\">
					<tr>
						<form method=\"post\">
							<td><h3>Add your comment</h3></td>
						</tr>
						<tr>
							<td><textarea style=\"width: 400px;height:100px\" name=\"comment\"></textarea></td>
						</tr>
						<tr>
							<td><input name=\"submit\" type=\"submit\" class=\"Butt\" value=\"POST COMMENT\">
						</tr>
						</form>
					</table>";
					}
					
					$stmt = $conn->prepare("SELECT * FROM comments WHERE posted_to=:t0");
					$stmt->bindParam(':t0', $_GET['id']);
					$stmt->execute();
					if($stmt->rowCount() > 0) {
						echo "<h3>Comments</h3><table>";
						foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $comment) {
							$Now = new DateTime($comment->posted_on);
							// Fetch user info
							$stmt = $conn->prepare("SELECT * FROM users WHERE id=:t0");
							$stmt->bindParam(':t0', $comment->posted_by);
							$stmt->execute();
							foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $commenter);
							//HACKY SHIT: Delete comment
							if($_SESSION["id"] == $comment->posted_by) {
								$options = "| <a href=\"/photo.php?id=" . $_GET["id"] . "&delcomm=" . $comment->id . "\" class=\"PostLinks\">Delete</a>";
							} else if($_SESSION["id"] == $photo->uploaded_by) {
								$options = "| <a href=\"/photo.php?id=" . $_GET["id"] . "&delcomm=" . $comment->id . "\" class=\"PostLinks\">Delete</a>";
							} else {
								$options = NULL;
							}
							// Comment html itself
							echo "<tr>
						<td valign=\"top\"><a href=\"/profile.php?id=". $comment->posted_by . "\" name=\"comment" . $comment->id . "\"><img src=\"". $commenter->display_picture . "\" alt=\"view profile\" width=\"48\" height=\"48\" align=\"left\" hspace=\"5\" /></a></td>
						<td>
							<h4><a href=\"profile.php?id=". $comment->posted_by . "\">". $commenter->screen_name . "</a> says:</h4>
							<p>". htmlspecialchars($comment->text) . "<br />
								<span class=\"PostDateTime\">
									Posted at ". $Now->format('d') . " " . $Now->format('M') . " '" . $Now->format('y') . ", ". $Now->format('h') . "." . $Now->format('i') . strtolower($Now->format('A')) . "
									|
									<a href=\"/photo.php?id=" . $_GET["id"] . "#comment" . $comment->id . "\" class=\"PostLinks\">Permalink</a> " . $options . "
								</span>
							</p>
						</td>
					</tr>";
						}
					echo "				</table>
				<br />
			
			</td>
		</tr>
	</table>";
					}
					?>
				<br />
			
			</td>
		</tr>
	</table>	

<?php require($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>
