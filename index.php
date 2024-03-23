<?php
$title = '«Παρουσιολόγιο ΣΠΗΥ» | Σύνδεση';
include('templates/header.inc.php');
print '<link rel="stylesheet" type="text/css" href="css/main.css">';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$errors = [];
	if (!($username = filter_input(INPUT_POST, 'username'))) {
		$username = NULL;
		$errors[] = 'Παρακαλώ εισάγετε όνομα χρήστη';
	}
	if (!($password = filter_input(INPUT_POST, 'password'))) {
		$password = NULL;
		$errors[] = 'Παρακαλώ εισάγετε κωδικό';
	}
	if (!empty($errors)) {
		print_error_messages($errors);
	} else {
		$success = false;
		require_once('includes/mysqli_connect.php');
		$q = "SELECT password FROM roles WHERE username=?";
		$stmt = my_mysqli_prepare($dbc, $q);
		my_mysqli_stmt_bind_param($stmt, 's', $username);
		my_mysqli_stmt_execute($stmt);
		my_mysqli_stmt_store_result($stmt);
		if (my_mysqli_stmt_num_rows($stmt) == 1) {
			my_mysqli_stmt_bind_result($stmt, $password_hashdb);
			my_mysqli_stmt_fetch($stmt);
			if (password_verify($password, $password_hashdb)) {
				$success = true;
			}
		}
		if ($success) {
			$_SESSION['username'] = $username;
			$_SESSION['loggedin'] = time();
			$_SESSION['agent'] = sha1($_SERVER['HTTP_USER_AGENT']);
		} else {
			print "<p class='alert alert-warning'>Το ονομα και ο κωδικός χρήστη δεν αντιστοιχούν σε υφιστάμενο χρήστη.</p>\n";
		}
	}
}
if (is_loggedin()) {
	print '<link rel="stylesheet" type="text/css" href="css/util.css">';
	print "<p>Είστε συνδεδεμένος!</p>\n";
	if (is_administrator()) {
		header("Location: apousies.php");
	} else {
		header("Location: apousiologio.php");
	}
	exit();
} else {
?>
	<div class="container-login100" style="background-image: url('images/aithoysa_a.jpg');">
		<div class="wrap-login100 p-t-30 p-b-50">
			<span class="login100-form-title p-b-41">
				ΕΙΣΟΔΟΣ ΣΤΗΝ ΕΦΑΡΜΟΓΗ<br>«ΠΑΡΟΥΣΙΟΛΟΓΙΟ ΣΠΗΥ»
			</span>
			<form class="login100-form validate-form p-b-33 p-t-5" action="" method="post">

				<div class="wrap-input100 validate-input" data-validate="Enter username">
					<input class="input100" type="text" name="username" placeholder="Όνομα Χρήστη">
					<span class="focus-input100" data-placeholder="&#xe82a;"></span>
				</div>

				<div class="wrap-input100 validate-input" data-validate="Enter password">
					<input class="input100" type="password" name="password" placeholder="Κωδικός">
					<span class="focus-input100" data-placeholder="&#xe80f;"></span>
				</div>

				<div class="container-login100-form-btn m-t-32">
					<button class="login100-form-btn">
						ΕΙΣΟΔΟΣ
					</button>
				</div>

			</form>
		</div>
	</div>
<?php
}
include('templates/footer.inc.php');
?>
</body>

</html>
<?php
ob_end_flush(); // Αποστολή του buffer στον browser και απενεργοποίηση output buffering
?>