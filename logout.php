<?php
$title = 'Έξοδος';
include('templates/header.inc.php');

if (is_loggedin()) {
    $_SESSION = [];
    setcookie('PHPSESSID', '', time()-3600, '/', '', 0, 0);
    session_destroy();
}
print "<p>Έχετε αποσυνδεθεί επιτυχώς!</p>\n";

include('templates/footer.inc.php');
?>