<?php
$title = '«Αναζήτηση Απουσίας Μαθητή ΣΠΗΥ»';
include('templates/header.inc.php');
check_session();
if ($_SESSION['username'] == "diaxstis") {
require_once('includes/mysqli_connect.php');
?>
<h2>Αναζήτηση Απουσίας Μαθητή ΣΠΗΥ</h2>
<p>Αναζητήστε απουσίες μαθητών ΣΠΗΥ</p>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    print "
    <form action='search_by_day.php' method='post'>\n
        <div class='form-row'>\n
            <div class='form-group col-md-4'>\n
                <label for='day'>Ημέρα της εβδομάδας</label>\n
                <select id='day' name='day' class='form-control'>\n
                    <option value='Monday'>Δευτέρα</option>\n
                    <option value='Tuesday'>Τρίτη</option>\n
                    <option value='Wednesday'>Τετάρτη</option>\n
                    <option value='Thurday'>Πέμπτη</option>\n
                    <option value='Friday'>Παρασκευή</option>\n
                    <option value='Saturday'>Σάββατο</option>\n
                    <option value='Sunday'>Κυριακή</option>\n
                </select>\n
            </div>\n
        </div>\n
        <button type='submit' name='submit' class='btn btn-primary'>Αναζήτηση</button>\n
    </form>
    <br>";

    print "
    <form action='search_by_week.php' method='post'>\n
        <div class='form-row'>\n
            <div class='form-group col-md-4'>\n
                <label for='week'>Eβδομάδα του έτους</label>\n
                <select id='week' name='week' class='form-control'>\n";
    $i = 0;
    while ($i < 53) {
        $i++;
        print "<option value='$i'>$i<sup>η</sup></option>\n";
    }
    print "
                </select>\n
            </div>\n
        </div>\n
        <button type='submit' name='submit' class='btn btn-primary'>Αναζήτηση</button>\n
    </form>
    <br>";

    print "
    <form action='search_by_month.php' method='post'>\n
        <div class='form-row'>\n
            <div class='form-group col-md-4'>\n
                <label for='month'>Μήνας του έτους</label>\n
                <select id='month' name='month' class='form-control'>\n";
    $i = 0;
    while ($i < 12) {
        $i++;
        print "<option value='$i'>$i<sup>ος</sup></option>\n";
    }
    print "
                </select>\n
            </div>\n
        </div>\n
        <button type='submit' name='submit' class='btn btn-primary'>Αναζήτηση</button>\n
    </form>
    <br>";


    print "
    <form action='search_by_seira.php' method='post'>\n
        <div class='form-row'>\n
            <div class='form-group col-md-4'>\n
                <label for='month'>Περίοδο Εκπαιδευτικής Σειράς της ΣΠΗΥ</label>\n
                <select id='seira' name='seira' class='form-control'>\n";
    $query = "SELECT id_seires, seires_name FROM seires WHERE deleted = 0";
    $stmt = my_mysqli_prepare($dbc, $query);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    my_mysqli_stmt_bind_result($stmt, $id_seires, $seires_name);
    while (my_mysqli_stmt_fetch($stmt)) {
        print "<option value='$id_seires'>$seires_name<sup>η</sup></option>\n";
    }
    print "
                </select>\n
            </div>\n
        </div>\n
        <button type='submit' name='submit' class='btn btn-primary'>Αναζήτηση</button>\n
    </form>\n";
    mysqli_stmt_close($stmt);
}
mysqli_close($dbc);
include('templates/footer.inc.php');
?>
</body>

</html>
<?php
ob_end_flush(); // Αποστολή του buffer στον browser και απενεργοποίηση output buffering
} else {
    $title = 'Σφάλμα πρόσβασης';
    print "<p>Δεν έχετε δικαίωμα πρόσβασης σε αυτή τη σελίδα.";
    include('templates/footer.inc.php');
}
?>