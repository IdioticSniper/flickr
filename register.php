<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php"); 

if($_SERVER['REQUEST_METHOD'] == "POST") {
	$email = $_POST["reg_email"];
	$username = preg_replace("/<[^>]*>/", "", $_POST["username"]);
	$username = str_replace(['"',"'"], "", $username);
	$password = $_POST["password"];
	// now, check the length.
	if(strlen($email) > 60) {$error = true;}
	if(strlen($username) > 20) { $error = true;}
	if(strlen($password) > 43) { $error = true;}
	if(strlen($username) < 2) { $error = true; }
	// ----------------------
	if(empty($email)) { die(header("Location: /register.php?err=2")); }
	if(empty($password)) { die(header("Location: /register.php?err=2")); }
	// ----------------------
	$password = password_hash($password, PASSWORD_BCRYPT);
	$password = "BCrypt".$password;
	// ----------------------
	// And now, the actual signup
  $result = $conn->query("SELECT email FROM users WHERE email = '$username'");
	if($result->rowCount() == 0) {
		$login_ok = true;
	} else die();
	
	$result = $conn->query("SELECT screen_name FROM users WHERE screen_name = '$username'");
	if($result->rowCount() == 0) {
		$login_ok = true;
	} else die();
	
	if($login_ok) {
		$stmt = $conn->prepare("INSERT INTO users (screen_name, password, email) VALUES (:screen_name, :password, :email)");
		$stmt->bindParam(':screen_name', $username);
		$stmt->bindParam(':password', $password);
		$stmt->bindParam(':email', $email);
		$stmt->execute();
		// You may now login
		$_SESSION["id"] = getNextID("SELECT * FROM users ORDER BY id DESC", "id");
		$_SESSION["id"] = $_SESSION["id"] - 1;
		$_SESSION["email"] = $email;
		$_SESSION["screen_name"] = $username;
		header("Location: /login.php");
	} else {
		die();
	}
}

?>
<script language="Javascript">
<!--

function set_username(username){
	document.getElementById('username_field').value = username;
}

//-->
</script>


	<h1>Register for Flickr here.</h1>

	<table>
		<tr>
			<td id="Hint">
				<p>Our hatred for spam is difficult to articulate. We promise unreservedly never to share your email address with anyone without your explicit permission.</p>
				<img src="/images/spaceball.gif" alt="spacer image" width="160" height="1">
			</td>
			<td id="GoodStuff">

				<?php if(strlen($email) > 60) { error("The email was too long!");
	if(strlen($username) > 20) { error("Your screenname is way too long.");}
	if(strlen($password) > 43) { error("Keep it easy with that password, will 'ya?");}
	if(strlen($username) < 2) { error("Your screenname is way too short.");} ?>
				<form action="register.php" method="post">
				<table>
					<tr>
						<td colspan="2"><p class="Focus">Your screen name is like a nickname. You can have spaces in it if you like. For example, <strong>Johnnie Rotten</strong> or <strong>My Little Pony</strong> are both fine.</p></td>
					</tr>
					<tr>
						<td class="Label">Screen&nbsp;Name:</td>
						<td valign="top" class="DateTime">
						<input type="text" name="username" value="" size="30" id="username_field"><br />
						<b>Note:</b> You can change your screen name later, as many times as you like.
						</td>
					</tr>
					<tr>
						<td class="Label">Email:</td>
						<td valign="top" class="DateTime"><input type="text" name="reg_email" value="" size="30" id="email_field"><br />
						<!-- <b>Note:</b> You'll need to confirm your email address to complete your registration.</td>-->
					</tr>
					<tr>
						<td class="Label">Password:</td>
						<td><input type="password" name="password" value=""></td>
					</tr>
					<!--
					<tr>
						<td>&nbsp;</td>
						<td><input type="checkbox" name="terms" value="1"> I am over 13 years old, and have read the fascinating <a href="/terms.php" onClick="window.open('/terms.php','TOC','status=yes,scrollbars=yes,resizable=yes,width=600,height=480'); return false;">Terms of Use</a>.</td>
					</tr>
					-->
					<tr>
						<td>&nbsp;</td>
						<td><input name="Submit" type="submit" class="Butt" value="SIGN UP"></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>Or, <a href="./">return to the home page</a>.</td>
					</tr>
				</table>
				</form>
				
			</td>
		</tr>
	</table>

<script language="Javascript">
<!--
document.getElementById('username_field').focus();
//-->
</script>

<?php require($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>