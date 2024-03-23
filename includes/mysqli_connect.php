<?php
// Για εκπαιδευτικούς λόγους και λόγους αποστολής ενιαίου αρχείου της εργασίας τοποθετήθηκε σε αυτήν την θέση
$db_host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_database = 'parousiologio';
if ($dbc = @mysqli_connect($db_host, $db_user, $db_password, $db_database)) {
    // print "<p>Έχετε συνδεθεί επιτυχώς με τη βάση δεδομένων!</p>\n";
} else {
    print "<p style='color:red;'>Δεν ήταν δυνατή η σύνδεση με την βάση δεδομένων...</p>\n";
    exit(1);
}
