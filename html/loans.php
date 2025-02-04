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
        $user_id = $_SESSION['uid'];
        // TODO: IF ISSET
        if (is_staff($user_id , $conn)) {
            $all_loans = file_get_contents($sql_loc . $queries_loc . 'select_mat_loans.sql');
            $result2 = $conn->query($all_loans);
            $qryres = $result2->fetch_all();
            $n_rows = $result2->num_rows;
            $n_cols = $result2->field_count;
            $fields = $result2->fetch_fields();

            $del_str = file_get_contents($sql_loc . $queries_loc . 'del_loan.sql');
            $del_stmt = $conn->prepare($del_str);   // prepared delete statement
            $del_stmt->bind_param('i', $id);	    //'i' indicates 'int' type for $id

            // check if delete request has been submitted; if so, delete selected records
            for ($i = 0; $i < $result2->num_rows; $i++) {
                $id = $qryres[$i][0];	// mat_id
                $key = "checkbox" . $id;
                //echo "New date: " . $new_loan_expected_return . "<br>";
                //echo "Loan ID: " . $id . "<br>";

                if (isset($_POST[$key]) && isset($_POST['dlbtn']) ) { // if this checkbox was clicked:
                    $need_reload = true;
                    if (!$del_stmt->execute()) {
                        echo("Failed :(( " . $conn->error . "<br>Could not delete record with id# " . $id);
                    }
                }
            }
                // Renew loan
            if (isset($_POST['update_expect_return_date'])) {
                //$new_loan_expected_return = $_POST['loan_expected_return'];
                $new_loan_expected_return = $_POST['expect_return_date'];

                //var_dump($_POST);
                $updateexd_str = file_get_contents($sql_loc . $queries_loc . 'update_loan_exp_return.sql');

                $updateexd_stmt = $conn->prepare($updateexd_str); // prepare update exp date statement

                $updateexd_stmt->bind_param("si", $new_loan_expected_return , $id);
                //var_dump($_POST);
                for($i=0; $i<$result2->num_rows; $i++){
                    $id = $qryres[$i][0];
                    // Check for a checkbox being clicked, and if it is, update the associated record.
                    $key = "checkbox" . $id;
                    if (isset($_POST[$key]) ){
                        // Check for a checkbox being clicked, and if it is, update the associated record.
                        $key = "checkbox" . $id;
                        if ($updateexd_stmt->execute()) {
                            $need_reload = true;
                            //header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
                            //exit();                 
                        } else {
                            echo $conn->error;
                            echo "Oopsie <br>";
                        }
                        /*
                        //$needs_reload = true;
                        if(!$updateexd_stmt->execute()){
                            ?>
                            <b>Failed:</b> <?php echo $conn->error; ?><br>
                            <p><b>Could not delete instrument with id=<?php echo $id; ?></b></p><br>
                            <?php
                        } */
                    }
                }
            }
        } else {
            $sel_tbl = file_get_contents($sql_loc . $queries_loc . 'select_user_mat_loans.sql');
            $sel_stmt = $conn->prepare($sel_tbl);
            $sel_stmt->bind_param("i", $user_id );
            $sel_stmt->execute();
            $result = $sel_stmt->get_result();
            
            $qryres = $result->fetch_all();
            
            $n_rows = $result->num_rows;
            $n_cols = $result->field_count;
            $fields = $result->fetch_fields();
                    // Renew loan
        if (isset($_POST['update_expect_return_date'])) {
            //$new_loan_expected_return = $_POST['loan_expected_return'];
            $new_loan_expected_return = $_POST['expect_return_date'];

            var_dump($_POST);
            $updateexd_str = file_get_contents($sql_loc . $queries_loc . 'update_loan_exp_return.sql');
            echo $updateexd_str;
            $updateexd_stmt = $conn->prepare($updateexd_str); // prepare update exp date statement

            $updateexd_stmt->bind_param("si", $new_loan_expected_return , $id);
            //var_dump($_POST);
            for($i=0; $i<$result->num_rows; $i++){
                $id = $qryres[$i][0];

                // Check for a checkbox being clicked, and if it is, update the associated record.
                $key = "checkbox" . $id;
                if (isset($_POST[$key]) ){
                    if ($updateexd_stmt->execute()) {
                        $need_reload = true;
                        //header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
                        //exit();                 
                    } else {
                        echo $conn->error;
                        echo "Oopsie <br>";
                    }
                    /*
                    //$needs_reload = true;
                    if(!$updateexd_stmt->execute()){
                        ?>
                        <b>Failed:</b> <?php echo $conn->error; ?><br>
                        <p><b>Could not delete instrument with id=<?php echo $id; ?></b></p><br>
                        <?php
                    } */
                }
            }
        }
    }
/*
        //return materials
        if (array_key_exists('actual_return_date', $_POST)) {
            $new_loan_expected_return = $_POST['actual_return_date'];
            $id = $qryres[$i][0];
            $updateact_str = file_get_contents($sql_loc . $queries_loc . 'update_loan_actual_return.sql');
            $updateact_stmt = $conn->prepare($updateact_str); // prepare add statement
            $updateact_stmt->bind_param("si", $new_loan_expected_return , $id);
            
            if ($updateact_stmt->execute()) {
                header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
                exit();                 
            } else {
                echo $conn->error;
                echo "Oopsie <br>";
            }
        }

        //delete
        $del_str = file_get_contents($sql_loc . $queries_loc . 'del_loan.sql');
        // Prepare a statement to delete individual rows.
        $del_stmt = $conn->prepare($del_str);
        $del_stmt->bind_param('i', $id);
        
        $needs_reload = false;
        // Check if the data was selected for deletion last time the script was run:
        for($i=0; $i<$result->num_rows; $i++){
            $id = $qryres[$i][0];
            
            // Check for a checkbox being clicked, and if it is, delete the associated record.
            $key = "checkbox" . $id;
            if (isset($_POST[$key]) ){
                $needs_reload = true;
                if(!$del_stmt->execute()){
                    ?>
                    <b>Failed:</b> <?php echo $conn->error; ?><br>
                    <p><b>Could not delete instrument with id=<?php echo $id; ?></b></p><br>
                    <?php
                } 
            }
        }

*/
     // Reload if changed
     if ($need_reload) {
        header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
        exit();
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href = "theme.css">
        <title> Loans </title>
        <link rel="icon" href="img/icon.svg" type="image/x-icon" />
    </head>

    <body style="font-family:arial">
        <?php navbar($_SESSION['uid'], $conn); ?>
        <h1 style="text-align:center;"><u>Material loans</u></h1>
        <p>
            <!-- DISPLAY TABLE -->
            <?php
                // TODO add IF statement here for staff
                if (isset($_SESSION['uid'])) {
                    if (is_staff($_SESSION['uid'], $conn)) {
                        //result_to_update_html_table($result2);
                        result_to_loan_del_html_table($qryres, $n_rows, $n_cols, $fields);
                    }else {
                        //result_to_loan_html_table($qryres, $n_rows, $n_cols, $fields)
                        result_to_loan_html_table($qryres, $n_rows, $n_cols, $fields);// change to RENEW ONLY
                    }
                } else {
                    echo("<p>You are not logged in</p>");
                }
            /*
                <!-- update actural date BUTTON -->
                <form method="POST">
                    actual_return_date: <input type="text" name="actual_return_date" placeholder="Enter text"/> <br>
                    <input type="submit" name="actual_return_date" value="update actual_return date" style="margin-top:0px;"/>
                </form> */
            ?>

            <!-- update expect date BUTTON -->
        </p>
    </body>


</html>