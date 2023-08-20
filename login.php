<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . "/incl/header.php"); 
// -------------------------------------------------------------------------------------

if(isset($_SESSION["email"])) {
	header("Location: /");
}

if(isset($_POST["Submit"])) {
	if(empty($_POST["email"])) { error("You did not enter an e-mail."); }
	if(empty($_POST["password"])) { error("No password was entered."); }
	
	$email = $_POST["email"];
	
	$sql = "SELECT * FROM users WHERE email='$email'";
	$result = $conn->query($sql);
	if ($result->rowCount() > 0) {
		while($row = $result->fetch(PDO::FETCH_ASSOC)) { 
			$id = $row["id"]; 
			$password_db = $row["password"]; 
			$isBanned = $row["isBanned"];
			$screen_name = $row["screen_name"];
		}
		
		$password_ok = false;
		
		if (strpos($password_db, 'BCrypt') === 0) {
			if (password_verify($_POST["password"], $password_db)) {
				$password_ok = true;
			} 
		} else {
			if ($password_db == sha1($_POST["password"].$website["sha1_salt"])) {
				$password_ok = true;	
			}
		}
		
		if($password_ok) {
			if($isBanned == 1) {
				error("This account has been suspended.");
			}
			$_SESSION["id"] = $id;
			$_SESSION["email"] = $email;
			$_SESSION["screen_name"] = $screen_name;
			// Update last login date
			$lastLogin = date('Y-m-d H:i:s');
			$stmt = $conn->prepare("UPDATE users SET last_login=:last_login WHERE email=:email");
			$stmt->bindParam(":last_login", $lastLogin);
			$stmt->bindParam(":email", $email);
			$stmt->execute();
			header("Location: /");
		} else {
			error("Username or password is incorrect.");
		}
	}
}
		
	
?>

	<h1>Log In</h1>

	<table>
		<tr>
			<td id="Hint">
				<p>Have you <a href="forgot.gne">forgotten your password</a>?</p>
			<img src="/images/spaceball.gif" alt="spacer image" width="160" height="1">			</td>			<td id="GoodStuff">
  
 
				<form action="login.php" method="post">
				<table>
					<tr>
						<td>Email:</td>
						<td><input type="text" class="input" name="email"  size="40" value="" id="first_field" /></td>
					</tr>
					<tr>
						<td>Password:</td>
						<td><input type="password" class="input" name="password" /></td>
					</tr>
					<!--
					<tr>
						<td>&nbsp;</td>
						<td><input type="checkbox" name="remember_me" value="1" /> Remember me on this computer.</td>
					</tr>
					-->
					<tr>
						<td>&nbsp;</td>
						<td><input name="Submit" type="submit" class="Butt" value="GET IN THERE"></td>
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
document.getElementById('first_field').focus();
//-->
</script>

<?php require($_SERVER["DOCUMENT_ROOT"] . "/incl/footer.php"); ?>
