<?php
$title = '«Μαθητές ΣΠΗΥ»';
include('templates/header.inc.php');
check_session();
if ($_SESSION['username'] == "diaxstis") {
require_once('includes/mysqli_connect.php');
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)) {
        $query = "SELECT seires_name FROM seires WHERE id_seires = ?";
        $stmt = my_mysqli_prepare($dbc, $query);
        my_mysqli_stmt_bind_param($stmt, 'i', $id);
        my_mysqli_stmt_execute($stmt);
        my_mysqli_stmt_store_result($stmt);
        if (my_mysqli_stmt_num_rows($stmt) == 1) {
            my_mysqli_stmt_bind_result($stmt, $ekseira);
            my_mysqli_stmt_fetch($stmt);
        }
        $query = "SELECT id_students, vathmoi_abbr, spec_abbr, students_surname, students_name, seires_name FROM (vathmoi NATURAL JOIN students NATURAL JOIN specialization) NATURAL JOIN seires WHERE id_seires = ? AND stud_deleted = 0";
        $stmt = my_mysqli_prepare($dbc, $query);
        my_mysqli_stmt_bind_param($stmt, 'i', $id);
        my_mysqli_stmt_execute($stmt);
        my_mysqli_stmt_store_result($stmt);
        my_mysqli_stmt_bind_result($stmt, $studID, $vathmos, $spec, $surname, $name, $seira);
        print "<h2>Μαθητές $ekseira<sup>ης</sup> Σειράς ΣΠΗΥ</h2>\n<p>Προβάλλετε τους μαθητές της $ekseira<sup>ης</sup> Εκπαιδευτικής Σειράς</p>\n";
        $i = 0;
?>
        <input class="form-control" id="myInput" type="text" placeholder="Αναζήτηση..">
        <br>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Α/Α</th>
                    <th>Βαθμός</th>
                    <th>Ονοματεπώνυμο</th>
                    <th>Σειρά ΣΠΗΥ</th>
                    <th>Επιλογές</th>
                </tr>
            </thead>
            <tbody id="myTable">
                <?php
                while (my_mysqli_stmt_fetch($stmt)) {
                    $i++;
                    print "<tr>\n<td>$i</td>\n<td>$vathmos $spec</td>\n<td>$surname $name</td>\n<td>$seira</td>\n<td><a href='edit_student.php?id=$studID'>Τροποποίηση</a>/<a href='delete_student.php?id=$studID'>Αφαίρεση</a></td>\n</tr>\n";
                }
                ?>
            </tbody>
        </table>
        <a href='add_student.php'>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
            </svg>
            Προσθήκη Μαθητή
        </a>
<?php
    } else {
        print "<p class='alert alert-warning'>Δεν υπάρχει η εκπαιδευτική σειρά που αναζητήσατε!</p>\n";
    }
    mysqli_stmt_close($stmt);
    mysqli_close($dbc);
    include('templates/footer.inc.php');
}
?>
<script>
    $(document).ready(function() {
        $("#myInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#myTable tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
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