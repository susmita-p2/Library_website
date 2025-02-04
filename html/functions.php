<!-- NOT AN ACTUAL PAGE!! THIS IS JUST FOR STORING FUNCTIONS!!! -->
<?php

/* -- GENERAL PAGE FUNCTIONS ------------------------------------------------------------ */
    /* -- Generic page start code (error reporting + cookies) --------- */
    function start_page() {
        // need to put session stuff here?

        /* -- ERROR REPORTING -------------------------------------- */
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        /* -- COOKIES ---------------------------------------------- */
            // ... would go here

    }

    /* -- Create and return connection object ------------------------- */
    function connect() {
        $config = parse_ini_file('/home/mysql_lib.ini');
        $dbhost = 'mysqli.default_host';  // localhost
        $dbuser = 'mysqli.default_user';  // patron_webuser
        $dbpass = 'mysqli.default_pw';
        $dbname = 'library';

        // Connect to database
        if(!$conn = new mysqli($config[$dbhost], $config[$dbuser], $config[$dbpass], $dbname)) {
            echo "Count not connect to " . $dbname . ".";
            exit;
        }

        // MySQL Connection
        if ($conn->connect_errno) {
            echo "Error: Failed to make a MySQL connection, here is why: " . "<br>";
            echo "Errno: " . $conn->connect_errno . "\n";
            echo "Error: " . $conn->connect_error . "\n";
            exit;
        }

        return $conn;
    }

    /* -- Given a user_id (and a connection object), returns whether that user is staff -- */
    function is_staff($uid, $conn) {
        $sel = file_get_contents('../IMPLEMENTATION/' . 'queries/' . 'is_staff.sql');
        $result = $conn->query($sel);

        if ($result != NULL) {
            $allrows = $result->fetch_all();
            for ($i = 0; $i < $result->num_rows; $i++) {
                if ($allrows[$i][0] == $uid) {
                    return 1;
                }
            }
        }
        return 0;
        // WASSHOI !
    }

    function navbar($uid, $conn) {
        ?>
        <meta charset="UTF-8">
        <table style="border-bottom: 1px solid gray; font-size:16px; margin-bottom=20px; margin-left:10px;">
            <tr style="margin-bottom:20px;">
                <td style="border:1px white solid; border-bottom: 1px dotted gray;">&#x1f3e0; <a href="index.php">Home</a></td>
                <td style="border:1px white solid; border-bottom: 1px dotted gray;">&#x1f4da; <a href="catalog.php">Catalog</a></td>
                <td style="border:1px white solid; border-bottom: 1px dotted gray;">❤️ <a href="franchises.php">Franchises</a></td> 
        <?php
        if ($uid != NULL) { ?>
            <?php if (is_staff($uid, $conn)) { ?>
                <td style="border:1px white solid; border-bottom: 1px dotted gray;">&#x1F4D8; <a href="loans.php">Loans</a></td>
                <td style="border:1px white solid; border-bottom: 1px dotted gray;">&#x1f4c7; <a href="checkout.php">Checkout/return</a></td>
            <?php } else { ?>
                <td style="border:1px white solid; border-bottom: 1px dotted gray;">&#x1F4D8; <a href="loans.php">My Loans</a></td>
            <?php }
        }
        ?> </tr>
        </table> <?php
    }

    /* -- Create table with delete button based on given result object */
    function result_to_html_table($result) {
        $qryres = $result->fetch_all();
        $n_rows = $result->num_rows;
        $n_cols = $result->field_count;
        $fields = $result->fetch_fields();
        ?>
        <form method="POST" style="padding-bottom:0px;">
            <table style="text-align:center; margin-left:10px;">
                <thead style="background-color:rgb(184, 226, 184);"> <!-- header -->
                    <tr>
                        <?php for ($i=0; $i<$n_cols; $i++){ ?>
                                <td><b><?php echo $fields[$i]->name; ?></b></td>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody> <!-- Begin body - - - -->
                    <?php for ($i = 0; $i < $n_rows; $i++){
                        $id = $qryres[$i][0]; ?>
                        <tr>
                            <?php for($j = 0; $j < $n_cols; $j++){ ?>
                                <td><?php echo $qryres[$i][$j]; ?></td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>  
        </form>
    <?php }

    /* -- Above, but with delete button -- */
    function result_to_del_html_table($result) {
        $qryres = $result->fetch_all();
        $n_rows = $result->num_rows;
        $n_cols = $result->field_count;
        $fields = $result->fetch_fields();
        ?>
        <form method="POST" style="padding-bottom:0px;">
            <table style="text-align:center; margin-left:10px;">
                <thead style="background-color:rgb(184, 226, 184);"> <!-- header -->
                    <tr>
                        <td><b>Select</b></td>
                            <?php for ($i=0; $i<$n_cols; $i++){ ?>
                                    <td><b><?php echo $fields[$i]->name; ?></b></td>
                            <?php } ?>
                    </tr>
                </thead>
                <tbody> <!-- Begin body - - - -->
                    <?php for ($i = 0; $i < $n_rows; $i++){
                        $id = $qryres[$i][0]; ?>
                        <tr>
                            <td>
                                <input type='checkbox' name=<?php echo("'checkbox".$id."'") ?> value=<?php echo("'".$id."'") ?>
                                <?php // if ($qryres[$i][2]) echo"disabled='disabled'"; ?> />
                                <!-- TODO disable checkbox if item has been checked out -->
                            </td>
                            <?php for($j = 0; $j < $n_cols; $j++){ ?>
                                <td><?php echo $qryres[$i][$j]; ?></td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <!-- Button to delete selected rows -->
            <!-- TODO only show this if logged in as staff!!!! -->
            <p><input type="submit" name="dlbtn" value="Delete selected records" style="margin-left:10px;"/></p>     
        </form>
    <?php }

/* -- CATALOG PAGE -------------------------------------------------------------------- */
    function filter_materials($material_type) {
        $sql_loc = '../IMPLEMENTATION/';
        $queries_loc = 'queries/';
        $file_name = 'select_catalog.sql';
        
        if ($material_type == "eBook") {
            //$sel_tbl = file_get_contents($sql_loc . $queries_loc . 'select_subset.sql'); 
            $file_name = 'select_subset.sql';
        } else if ($material_type == "Book") {
            $file_name = 'select_subset_books.sql';
            //$sel_tbl = file_get_contents($sql_loc . $queries_loc . 'select_subset_books.sql');     
        } else if ($material_type == "Magazine") {
            //$sel_tbl = file_get_contents($sql_loc . $queries_loc . 'select_subset_magazine.sql');
            $file_name = 'select_subset_magazine.sql';     
        } else if ($material_type == "Physical Recording") {
            //$sel_tbl = file_get_contents($sql_loc . $queries_loc . 'select_subset_recordings.sql');  
            $file_name = 'select_subset_recordings.sql';   
        } else if ($material_type == "Audiobook") {
            //$sel_tbl = file_get_contents($sql_loc . $queries_loc . 'select_subset_audiobooks.sql');
            $file_name = 'select_subset_audiobooks.sql';     
        } else if ($material_type == null) {
            //$sel_tbl = file_get_contents($sql_loc . $queries_loc . 'select_catalog.sql');
            $file_name = 'select_catalog.sql';
        }
        $file_path = $sql_loc . $queries_loc . $file_name;
        #echo $file_path;
        #return file_get_contents($sql_loc . $queries_loc . $file_name);
        return $file_path;
    }

    function check_loan_active($loan_id, $conn) {
        $sel = file_get_contents('../IMPLEMENTATION/' . 'queries/' . 'check_loan_active.sql');
        $result = $conn->query($sel);

        if ($result != NULL) {
            $allrows = $result->fetch_all();
            for ($i = 0; $i < $result->num_rows; $i++) {
                if ($allrows[$i][0] == $loan_id) {
                    return 1; // loan is active
                }
            }
        }
        return 0; // loan not in active_loans
    }

    /* -- TODO: Create table with button that redirects to reservations page w/ given material(s) */
    function result_to_reserve_table($result, $conn) {
        $qryres = $result->fetch_all();
        $n_rows = $result->num_rows;
        $n_cols = $result->field_count;
        $fields = $result->fetch_fields();
        ?>
        <form method="POST" style="padding-bottom:0px;">
            <table style="text-align:center; margin-left:10px;">
                <thead style="background-color:rgb(184, 226, 184);">
                    <tr>
                        <td><b>Select</b></td>
                            <?php for ($i=0; $i<$n_cols; $i++){ ?>
                                    <td><b><?php echo $fields[$i]->name; ?></b></td>
                            <?php } ?>
                            <td><b>Reserve</b></td>
                    </tr>
                </thead>
                <tbody> <!-- Begin body - - - -->
                    <?php for ($i = 0; $i < $n_rows; $i++){
                        $id = $qryres[$i][0]; ?>
                        <tr>
                            <td>
                                <input type='checkbox' name=<?php echo("'checkbox".$id."'") ?> value=<?php echo("'".$id."'") ?>
                                <?php // if ($qryres[$i][2]) echo"disabled='disabled'"; ?> />
                                <!-- TODO disable checkbox if item has been checked out -->
                            </td>
                            <?php for($j = 0; $j < $n_cols; $j++){ ?>
                                <td><?php echo $qryres[$i][$j]; ?></td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <!-- Button to delete selected rows -->
            <p><input type="submit" name="reserve_btn" value="Reserve selected materials"/></p>     
        </form>
    <?php }

/* -- LOANS PAGE ------------------------------------------------------------------------ */
    /* -- result_to_del_html_table for loans page -- */
    function result_to_loan_del_html_table($qryres, $n_rows, $n_cols, $fields) {
        ?>
        <form method="POST" style="padding-bottom:0px;">
            <table style="text-align:center; margin-left:10px;">
                <thead style="background-color:rgb(184, 226, 184);"> <!-- header -->
                    <tr>
                        <td><b>Select</b></td>
                            <?php for ($i=0; $i<$n_cols; $i++){ ?>
                                    <td><b><?php echo $fields[$i]->name; ?></b></td>
                            <?php } ?>
                    </tr>
                </thead>
                <tbody> <!-- Begin body - - - -->
                    <?php for ($i = 0; $i < $n_rows; $i++){
                        $id = $qryres[$i][0]; ?>
                        <tr>
                            <td>
                                <input type='checkbox' name=<?php echo("'checkbox".$id."'") ?> value=<?php echo("'".$id."'") ?>
                                <?php // if ($qryres[$i][2]) echo"disabled='disabled'"; ?> />
                                <!-- TODO disable checkbox if item has been checked out -->
                            </td>
                            <?php for($j = 0; $j < $n_cols; $j++){ ?>
                                <td><?php echo $qryres[$i][$j]; ?></td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <!-- Button to delete selected rows -->
            <!-- TODO only show this if logged in as staff!!!! -->
            <p><input type="submit" name="dlbtn" value="Delete selected records"/></p> 
            
            <b>Renew selected loan(s)</b><br>
            New due date: <input type="date" name="expect_return_date" placeholder="Enter text"/>
            <input type="submit" name="update_expect_return_date" value="Submit" style="margin-top:0px;"/>     
        </form>
    <?php }

    /* -- result_to_html_table for loans page -- */
    function result_to_loan_html_table($qryres, $n_rows, $n_cols, $fields) {
        ?>
        <form method="POST" style="padding-bottom:0px;">
            <table style="text-align:center; margin-left:10px;">
                <thead style="background-color:rgb(184, 226, 184);"> <!-- header -->
                    <tr>
                        <!-- <td><b>Select</b></td> -->
                            <?php for ($i=0; $i<$n_cols; $i++){ ?>
                                    <td><b><?php echo $fields[$i]->name; ?></b></td>
                            <?php } ?>
                    </tr>
                </thead>
                <tbody> <!-- Begin body - - - -->
                    <?php for ($i = 0; $i < $n_rows; $i++){
                        $id = $qryres[$i][0]; ?>
                        <tr>
                            <!-- <td> -->
                                <!-- <input type='checkbox' name=<?php echo("'checkbox".$id."'") ?> value=<?php echo("'".$id."'") ?> -->
                                <!-- <?php // if ($qryres[$i][2]) echo"disabled='disabled'"; ?> /> -->
                                <!-- TODO disable checkbox if item has been checked out -->
                            <!-- </td> -->
                            <?php for($j = 0; $j < $n_cols; $j++){ ?>
                                <td><?php echo $qryres[$i][$j]; ?></td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php // expect_return_date: <input type="date" name="expect_return_date" placeholder="Enter text"/> <br>
            // <input type="submit" name="update_expect_return_date" value="update expect_return date" style="margin-top:0px;"/> ?>
        </form>
    <?php }

/*
    function add_and_get_loanid($uid, $checkout, $return, $conn) {
        $add_str = file_get_contents('../IMPLEMENTATION/' . 'queries/' . 'add_loan.sql');
        $add_stmt = $conn->prepare($add_str); // prepare add statement
        $add_stmt->bind_param("iss", $uid, $checkout, $return);
        
        if ($add_stmt->execute()) {
            $get_str = file_get_contents('../IMPLEMENTATION/' . 'queries/' . 'get_loan_id.sql');
            $last_id = $conn->insert_id;
            echo $last_id;
            $get_stmt = $conn->prepare($get_str);
            $get_stmt->bind_param("i", $uid);
            $loan_id = $get_stmt->execute();
            return $loan_id;
        } else {
            echo $conn->error;
            echo "Could not add loan D: <br>";
        }
    }
    */

/* -- FRANCHISES PAGE ------------------------------------------------------------------- */
    /* -- result_to_html_table for franchises --  */
    function result_to_franchise_html_table($result) {
        $qryres = $result->fetch_all();
        $n_rows = $result->num_rows;
        $n_cols = $result->field_count;
        $fields = $result->fetch_fields();
        ?>
        <table style="text-align:center; margin-left:10px;">
            <thead style="background-color:rgb(184, 226, 184);"> <!-- header -->
                <tr>
                        <?php for ($i=0; $i<$n_cols; $i++){ ?>
                                <td><b><?php echo $fields[$i]->name; ?></b></td>
                        <?php } ?>
                </tr>
            </thead>
            <tbody> <!-- Begin body - - - -->
                <?php for ($i = 0; $i < $n_rows; $i++){
                    $id = $qryres[$i][0]; ?>
                    <tr>
                        <?php $franchise_id = $qryres[$i][0];?>
                        <td><?php echo $franchise_id ?></td>
                        <?php $franchise = $qryres[$i][1];?>
                        <td><a href="franchise_entry.php?franchise_name=<?php echo $franchise; ?>&franchise_id=<?php echo $franchise_id;?>"><?php echo $franchise; ?></a>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php }

    /* -- result_to_del_html_table for franchises -- */
    function result_to_franchise_del_html_table($result) {
        $qryres = $result->fetch_all();
        $n_rows = $result->num_rows;
        $n_cols = $result->field_count;
        $fields = $result->fetch_fields();
        ?>
        <form method="POST" style="padding-bottom:0px;">
            <table style="text-align:center; margin-left:10px;">
                <thead style="background-color:rgb(184, 226, 184);"> <!-- header -->
                    <tr>
                        <td><b>Select</b></td>
                            <?php for ($i=0; $i<$n_cols; $i++){ ?>
                                    <td><b><?php echo $fields[$i]->name; ?></b></td>
                            <?php } ?>
                    </tr>
                </thead>
                <tbody> <!-- Begin body - - - -->
                    <?php for ($i = 0; $i < $n_rows; $i++){
                        $id = $qryres[$i][0]; ?>
                        <tr>
                            <td>
                                <input type='checkbox' name=<?php echo("'checkbox".$id."'") ?> value=<?php echo("'".$id."'") ?>
                                <?php // if ($qryres[$i][2]) echo"disabled='disabled'"; ?> />
                                <!-- TODO disable checkbox if item has been checked out -->
                            </td>
                            <?php $franchise_id = $qryres[$i][0];?>
                            <td><?php echo $franchise_id ?></td>
                            <?php $franchise = $qryres[$i][1];?>
                            <td><a href="franchise_entry.php?franchise_name=<?php echo $franchise; ?>&franchise_id=<?php echo $franchise_id;?>"><?php echo $franchise; ?></a>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <!-- Button to delete selected rows -->
            <!-- TODO only show this if logged in as staff!!!! -->
            <p><input type="submit" name="dlbtn" value="Delete selected records"/></p>     
        </form>
    <?php } ?>



