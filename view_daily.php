<?php
$title = '«Προβολή ημερήσιου πίνακα απόντων»';
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
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $query = "SELECT idtemp_apousies, id_students, vathmoi_abbr, spec_abbr, students_surname, students_name, id_aitia, aitia, tekmiriosi, id_roles, roles, xronosfragida 
    FROM temp_apousies NATURAL JOIN students NATURAL JOIN seires NATURAL JOIN vathmoi NATURAL JOIN specialization NATURAL JOIN aitia NATURAL JOIN roles WHERE imerominia = date(now()) AND seires_name = ? AND stud_deleted = 0 AND deleted = 0 ORDER BY imerominia, xronosfragida, students_surname, students_name";
        $stmt = my_mysqli_prepare($dbc, $query);
        my_mysqli_stmt_bind_param($stmt, 's', $seires_name);
        my_mysqli_stmt_execute($stmt);
        my_mysqli_stmt_store_result($stmt);
        my_mysqli_stmt_bind_result($stmt, $idtemp_apousies, $id_students, $vathmoi_abbr, $spec_abbr, $students_surname, $students_name, $id_aitia, $aitia, $tekmiriosi, $id_roles, $roles, $xronosfragida);
        $i = 0;
        print "<h2>Προβολή ημερήσιου πίνακα απόντων της $seires_name<sup>ης</sup> Εκπαιδευτικής Σειράς</h2>\n";
?>
        <p>Προβάλλετε τον ημερήσιο πίνακα απόντων πριν την καταχώρηση</p>
        <input class="form-control" id="myInput" type="text" placeholder="Αναζήτηση..">
        <br>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Α/Α</th>
                    <th>Βαθμός</th>
                    <th>Ονοματεπώνυμο</th>
                    <th>Αιτία</th>
                    <th>Έγγραφο τεκμηρίωσης<br>(προαιρετικά)</th>
                    <th>Καταχωρητής απουσίας</th>
                    <th>Ημ/νια καταχώρησης απουσίας</th>
                </tr>
            </thead>
            <tbody id="myTable">
                <?php
                while (my_mysqli_stmt_fetch($stmt)) {
                    $i++;
                    $sub = substr($tekmiriosi, 8);
                    print "<tr>\n<td>$i</td>\n<td value='$id_students'>$vathmoi_abbr $spec_abbr</td>\n<td>$students_surname $students_name</td>\n
            <td value='$id_aitia'>$aitia</td>\n<td><a href='$tekmiriosi'>$sub</a></td>\n<td value='$id_roles'>$roles</td>\n<td>$xronosfragida</td>\n</tr>\n";
                }
                ?>
            </tbody>
        </table>
        <form action='' method='post'>
            <button type='submit' name='submit' class='btn btn-primary'>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" />
                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z" />
                </svg>
                Καταχώρηση ημερήσιου πίνακα απουσιών
            </button>
        </form>
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
    <?php
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $query = "SELECT id_students, id_aitia, tekmiriosi, id_roles, xronosfragida FROM temp_apousies NATURAL JOIN students NATURAL JOIN seires NATURAL JOIN vathmoi NATURAL JOIN specialization NATURAL JOIN aitia NATURAL JOIN roles 
    WHERE imerominia = date(now()) AND seires_name = ? AND stud_deleted = 0 AND deleted = 0 ORDER BY imerominia, xronosfragida, students_surname, students_name";
        $stmt = my_mysqli_prepare($dbc, $query);
        my_mysqli_stmt_bind_param($stmt, 's', $seires_name);
        my_mysqli_stmt_execute($stmt);
        my_mysqli_stmt_store_result($stmt);
        my_mysqli_stmt_bind_result($stmt, $id_students, $id_aitia, $tekmiriosi, $id_roles, $xronosfragida);
        $array = [];
        $j = 0;
        while (my_mysqli_stmt_fetch($stmt)) {
            $array[$j] = array($id_students, $id_aitia, $tekmiriosi, $id_roles, $xronosfragida);
            $j++;
        }
        mysqli_stmt_close($stmt);
        for ($i = 0; $i < count($array); $i++) {
            if ($tekmiriosi == NULL) {
                $query = "INSERT INTO apousies (imerominia, id_students, id_aitia, id_roles, xronosfragida, apousia) VALUES (date(now()), ?, ?, ?, ?, 1)";
                $stmt = my_mysqli_prepare($dbc, $query);
                my_mysqli_stmt_bind_param($stmt, 'iiis', $array[$i][0], $array[$i][1], $array[$i][3], $array[$i][4]);
                my_mysqli_stmt_execute($stmt);
            } else {
                $query = "INSERT INTO apousies (imerominia, id_students, id_aitia, tekmiriosi, id_roles, xronosfragida, apousia) VALUES (date(now()), ?, ?, ?, ?, ?, 1)";
                $stmt = my_mysqli_prepare($dbc, $query);
                my_mysqli_stmt_bind_param($stmt, 'iisis', $array[$i][0], $array[$i][1], $array[$i][2], $array[$i][3], $array[$i][4]);
                my_mysqli_stmt_execute($stmt);
            }
        }
        if (my_mysqli_stmt_affected_rows($stmt) >= 1) {
            print "<p class='alert alert-success'>Οι απουσίες προστέθηκαν <strong>επιτυχώς!</strong></p>\n";
        } else {
            print "<p class='alert alert-warning'Οι απουσίες <strong>ΔΕΝ</strong> προστέθηκαν!</p>\n";
            print_system_error();
        }
        // mysqli_stmt_close($stmt1);
        mysqli_stmt_close($stmt);
        mysqli_close($dbc);

        include('templates/footer.inc.php');
    ?>
        </body>

        </html>
        <?php
        ob_end_flush(); // Αποστολή του buffer στον browser και απενεργοποίηση output buffering
        ?>
<?php
    }
}
