<!-- END CHANGEABLE CONTENT -->
<link rel="stylesheet" type="text/css" href="css/util.css">
<?php
// Εμφάνιση διαχειριστικών συνδέσμων
// - Αν ο χρήστης είναι συνδεδεμένος (διαχειριστής) και δεν είναι η σελίδα logout.php

if (is_loggedin() && (basename($_SERVER['PHP_SELF'] != 'logout.php'))) {
    if (is_administrator()) {
        print "<hr>
        <h3>Διαχείριση ιστότοπου</h3>
        <p><a href='apousies.php'>Απουσιολόγιο</a> <->
        <a href='students.php'>Μαθητές</a> <->
        <a href='seires.php'>Σειρές</a> <->
        <a href='logout.php'>Αποσύνδεση</a></p>\n";
    } else {
        print "<hr>
        <h3>Διαχείριση ιστότοπου</h3>
        <p><a href='apousiologio.php'>Απουσιολόγιο</a> <->
        <a href='logout.php'>Αποσύνδεση</a></p>\n";
    }
}
?>
</div><!-- container -->
<div id="dropDownSelect1"></div>
<!--===============================================================================================-->
<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
<script src="vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
<script src="vendor/bootstrap/js/popper.js"></script>
<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
<script src="vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
<script src="vendor/daterangepicker/moment.min.js"></script>
<script src="vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
<script src="vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
<script src="js/main.js"></script>