<?php
    /* -- PAGE INIT ------------------------------------------ */
        include 'functions.php';
        start_page();
        $staff = "is_staff";
        $sql_loc = '../IMPLEMENTATION/';
        $queries_loc = 'queries/';
        $conn = connect();
        session_start();

    /* -- FORM STUFF ---------------------------------------- */
        $need_reload = false;
        $sel_tbl = file_get_contents($sql_loc . $queries_loc . 'select_franchises.sql');
        // to filter materials based on type:
        // SELECT (?) FROM (?)
        //                  ^ table name (based on what button is pressed; can store in a variable)
        $result = $conn->query($sel_tbl); // use select statement to get whole table
        $allrows = $result->fetch_all();  // all rows in table
        // (1) set up for deletion
            $del_str = file_get_contents($sql_loc . $queries_loc . 'del_franchise.sql');
            $del_stmt = $conn->prepare($del_str); // prepared delete statement
            $del_stmt->bind_param('i', $id);	//'i' indicates 'int' type for $id

        // (2) check if delete request has been submitted; if so, delete selected records
            for ($i = 0; $i < $result->num_rows; $i++) {
                $id = $allrows[$i][0];		// mat_id
                $key = "checkbox" . $id;
                if (isset($_POST[$key]) || isset($_POST["delall"])) { // if this checkbox was clicked:
                    $need_reload = true;
                    if (!$del_stmt->execute()) {
                        echo("Failed :(( " . $conn->error . "<br>Could not delete record with id# " . $id);
                    }
                }
            }

        // (3) set up for addition
            // $add_str = file_get_contents($sql_loc . $queries_loc . 'add_materials.sql'); // add materials query
            $add_str = file_get_contents($sql_loc . 'add_dummy_franchise.sql');
            $add_stmt = $conn->prepare($add_str); // prepare add statement

        // (4) check if add request has been submitted, and if so, add records
            // add stuff same as manage_instruments
            // file: $sql_loc . 'add_dummy_materials.sql'
            if (array_key_exists('add_records', $_POST)) {
                $add_inst = file_get_contents($sql_loc . 'add_dummy_franchise.sql');
                if (!$conn->query($add_inst)) {
                    echo $conn->error;
                    echo "Oopsie <br>";
                } else {
                    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
                    exit(); // ^ GET current page (reload)
                }
            }


        // (6) reload page if db changed
        if ($need_reload) {
        header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
        exit();
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href = "theme.css">
        <title>Franchises</title>
        <link rel="icon" href="img/icon.svg" type="image/x-icon" />
    </head>
    <body>
        <?php navbar($_SESSION['uid'], $conn); ?>
        <h1 style="text-align:center;"><u>Franchises </u></h1>

         <!-- DISPLAY TABLE -->
            <!-- TODO: support for staff-->
        <?php $result2 = $conn->query($sel_tbl);
        if (isset($_SESSION['uid'])) {
            if (is_staff($_SESSION['uid'], $conn)) {
                result_to_franchise_del_html_table($result2);
            } else {
                result_to_franchise_html_table($result2);
            } 
        } ?>

        <!-- ADD RECORDS BUTTON -->
        
    </body>
</html>