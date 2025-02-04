<?php
    /* -- PAGE INIT ----------------------------------------- */
        include 'functions.php';
        start_page();

        $sql_loc = '../IMPLEMENTATION/';
        $queries_loc = 'queries/';
        $conn = connect();
        session_start();
    //
    /* -- FORM STUFF ---------------------------------------- */
        $need_reload = 0;
        $loan_added = 0;
        $mat_returned = 0;

        // DEBUG
            $sel_tbl = file_get_contents($sql_loc . $queries_loc . 'sel_recent_mat_loans.sql'); // get table info using sql select query
            $result = $conn->query($sel_tbl);

        // $sel_tbl = file_get_contents($sql_loc . $queries_loc . 'select_loans.sql');
        // $result = $conn->query($sel_tbl); // use select statement to get whole table

        /* Honestly, even though a lot of the information overlaps, I'm starting to think we should have just made
        separate material_loans and equipment_loans tables instead of making them subset tables... I think that
        would actually make more sense in terms of practical design */

        // Add new material loan
            if (array_key_exists('add_mat_loan', $_POST)) {
                $user_id = $_POST['user_id'];
                $loan_checkout_date = $_POST['loan_checkout_date'];
                $type = $_POST['type'];
                $mat_id = $_POST['mat_id'];

                if (empty($user_id) || empty($loan_checkout_date) || empty($mat_id)) {
                    die("all the fields need to be filled");
                }

                // add to loan table
                $add_str = file_get_contents($sql_loc . $queries_loc . 'add_loan.sql');
                $add_stmt = $conn->prepare($add_str); // prepare add statement
                $add_stmt->bind_param("sss", $user_id, $loan_checkout_date, $type);
                
                if ($add_stmt->execute()) {
                    $last_id = $conn->insert_id;
                    echo $last_id;
                    echo $type;
                    if ($type == 'Book') {
                        $add_str1 = file_get_contents($sql_loc . $queries_loc . 'add_material_loan.sql');
                    } else if ($type == 'Multimedia') {
                        $add_str1 = file_get_contents($sql_loc . $queries_loc . 'add_material_loan.sql');
                    }
                    echo $add_str1;
                    $add_stmt1 = $conn->prepare($add_str1); // prepare add statement
                    $add_stmt1->bind_param("ii", $last_id, $mat_id);
                    $add_stmt1->execute();
                    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
                    $loan_added = true;
                    echo("LOAN ADDED");
                    exit();
                } else {
                    echo $conn->error;
                    echo "Oopsie <br>";
                }
            
                // TODO: ADD TO MATERIALS LOAN TABLE
                // $loan_id = add_and_get_loanid($user_id, $loan_checkout_date, $loan_expected_return, $type, $conn);

                // $add_str2 = file_get_contents($sql_loc . $queries_loc . 'add_material_loan.sql');
                // $add_stmt2 = $conn->prepare($add_str); // prepare add statement
                // $add_stmt2->bind_param("ii", $loan_id, $mat_id);

                // if ($add_stmt2->execute()) {
                //     header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
                //     exit();                 
                // } else {
                //     echo $conn->error;
                //     echo "Oopsie <br>";
                // }
            }

            if (array_key_exists('return',$_POST)) {
                $lid = $_POST['loan_id2'];
                $retdate = $_POST['loan_actual_return'];
                $ret_str = file_get_contents($sql_loc . $queries_loc . 'update_loan_actual_return.sql');
                $ret_stmt = $conn->prepare($ret_str);
                $ret_stmt->bind_param("ss", $retdate, $lid);
                if ($ret_stmt->execute()) {
                    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
                    $mat_returned = 1;
                    exit();
                }
            }
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href = "theme.css">
        <meta charset="UTF-8">
        <title>Materials checkout/returns</title>
        <link rel="icon" href="img/icon.svg" type="image/x-icon" />
    </head>

    <body>
        <?php navbar($_SESSION['uid'], $conn); ?>
        <?php if (isset($_SESSION['uid'])) {
            if (is_staff($_SESSION['uid'], $conn)) { ?>
                <h1 style="text-align:center;"><u>Materials Checkout & Returns </u></h1>
                <table style="margin-left:10px; margin-top:20px; padding-left:5px;"><tr>
                    <thead style="background-color:rgb(184, 226, 184);"><tr><td>Materials Checkout</td>  <td>Materials Return</td></tr></thead>
                    <td style="vertical-align:top; text-align:left;">
                        <!-- Add new loan record --> 
                        <form method = "POST"> 
                            User ID #: &emsp;&ensp;&ensp;&nbsp; <input type="text" name="user_id" placeholder="Enter text"/> <br>
                            Material ID #: &nbsp;&ensp;         <input type="text" name="mat_id" placeholder="Enter text"/> <br>
                            Checkout Date:&nbsp;                <input type="date" name="loan_checkout_date"/> <br>
                            Type: <input type= "radio" id="book" name="type" value="Book">
                                    <label for="book">Book</label><br>
                                    &emsp;&emsp;&ensp;&nbsp;<input type= "radio" id="multimedia" name="type" value="Multimedia">
                                    <label for="multimedia">Multimedia</label><br>
                            <input type="submit" name="add_mat_loan" value="Checkout" style="margin-top:10px;"/> 
                        </form>
                        <!-- <?php echo $loan_added ? "Loan added!" : "" ?> -->
                        </td><td style="vertical-align:top; text-align:left;">
                        <!-- Update loan record -->
                        <form method = "POST">
                            Loan ID: <input type="text" name="loan_id2" /> <br>
                            Return date: <input type="date" name="loan_actual_return"/> <br>
                            <input type="submit" name="return" value="Submit" />
                            <!-- <?php echo $mat_returned ? "Returned!" : "" ?> -->
                        </form>
                    </td>
                </tr></table>

                <!-- DEBUG -->
                 <br>
                <?php result_to_html_table($result); ?>

            <?php } else {
                header("Location: " . "http://34.66.235.244/team_JACS/index.php", true, 303); // redirect to home page
            }
        } else {
            ?> <img src="img/skel.png" alt="the skeleton appears" style="width:100%"/>
        <?php } ?>
        
    </body>
</html>