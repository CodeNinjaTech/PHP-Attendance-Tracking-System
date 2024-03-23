<?php
$title = '«Μαθητές ΣΠΗΥ»';
include('templates/header.inc.php');
check_session();
if ($_SESSION['username'] == "diaxstis") {
    $title = 'Σφάλμα πρόσβασης';
    print "<p>Δεν έχετε δικαίωμα πρόσβασης σε αυτή τη σελίδα.";
    include('templates/footer.inc.php');
} else {
    require_once('includes/mysqli_connect.php');
    $seires_name = substr($_SESSION['username'], 7);
    $query = "SELECT id_seires FROM seires WHERE seires_name = ?";
    $stmt = my_mysqli_prepare($dbc, $query);
    my_mysqli_stmt_bind_param($stmt, 's', $seires_name);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    if (my_mysqli_stmt_num_rows($stmt) == 1) {
        my_mysqli_stmt_bind_result($stmt, $id_seires);
        my_mysqli_stmt_fetch($stmt);
    }
    $query = "SELECT id_students, vathmoi_abbr, spec_abbr, students_surname, students_name FROM (vathmoi NATURAL JOIN students NATURAL JOIN specialization) NATURAL JOIN seires WHERE id_seires = ? AND stud_deleted = 0 ORDER BY students_surname, students_name";
    $stmt = my_mysqli_prepare($dbc, $query);
    my_mysqli_stmt_bind_param($stmt, 'i', $id_seires);
    my_mysqli_stmt_execute($stmt);
    my_mysqli_stmt_store_result($stmt);
    my_mysqli_stmt_bind_result($stmt, $studID, $vathmos, $spec, $surname, $name);
    print "<h2>Απουσιολόγιο $seires_name<sup>ης</sup> Σειράς ΣΠΗΥ</h2>\n<p>Απουσιολόγιο της σήμερον της $seires_name<sup>ης</sup> Εκπαιδευτικής Σειράς</p>\n";
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
                <th>Επιλογές</th>
            </tr>
        </thead>
        <tbody id="myTable">
            <?php
            while (my_mysqli_stmt_fetch($stmt)) {
                $i++;
                print "<tr>\n<td>$i</td>\n<td>$vathmos $spec</td>\n<td>$surname $name</td>\n<td><a href='add_temp_apousia.php?id=$studID'>Προσθήκη Απουσίας</a></td>\n</tr>\n";
            }
            ?>
        </tbody>
    </table>
    <a href='view_daily.php'>
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
        </svg>
        Προβολή συγκεντρωτικού ημερήσιου πίνακα απόντων
    </a>
    <?php
    mysqli_stmt_close($stmt);
    mysqli_close($dbc);
    include('templates/footer.inc.php');
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
}
?>