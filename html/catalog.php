<?php
    /* -- PAGE INIT ------------------------------------------ */
        include 'functions.php';
        start_page();
        $sql_loc = '../IMPLEMENTATION/';
        $queries_loc = 'queries/';
        $conn = connect();
        session_start();
    //
    /* -- FORM STUFF ---------------------------------------- */
        $need_reload = false;
        $sel_tbl = file_get_contents($sql_loc . $queries_loc . 'select_catalog.sql');
        $result = $conn->query($sel_tbl); // use select statement to get whole table
        $allrows = $result->fetch_all();  // all rows in table
        /* -- DELETE MATERIALS ------------------------------ */
            $del_str = file_get_contents($sql_loc . $queries_loc . 'del_material.sql');
            $del_stmt = $conn->prepare($del_str);   // prepared delete statement
            $del_stmt->bind_param('i', $id);	    //'i' indicates 'int' type for $id

            // check if delete request has been submitted; if so, delete selected records
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
            
        /* -- ADD MATERIALS --------------------------------- */
            if (array_key_exists('add_mat', $_POST)) {
                #$add_inst = file_get_contents($sql_loc . 'add_dummy_materials.sql');
              
                $mat_title = $_POST['title'];
                $mat_year = $_POST['year'];
                $mat_publisher = $_POST['publisher'];
                $mat_description =$_POST['desc'];
                $mat_decimal = $_POST['decm'];
                $mat_lang = $_POST['lang'];
                $mat_subset = $_POST['subset'];
                $add_str = file_get_contents($sql_loc . $queries_loc . 'add_materials.sql');
                $add_stmt = $conn->prepare($add_str); // prepare add statement
                $add_stmt->bind_param("sissss", $mat_title, $mat_year, $mat_publisher, $mat_description, $mat_decimal,$mat_lang);

                if ($add_stmt->execute()) {
                    $last_id = $conn->insert_id;
                    echo $last_id;
                    if ($mat_subset=="Book") {
                        $add_str1 = file_get_contents($sql_loc . $queries_loc . 'add_material_books.sql');
                    } else if ($mat_subset=="Magazine") {
                        $add_str1 = file_get_contents($sql_loc . $queries_loc . 'add_material_magazines.sql');
                    } else if ($mat_subset=="eBook") {
                        $add_str_b = file_get_contents($sql_loc . $queries_loc . 'add_material_books.sql');
                        $add_stmt_b = $conn->prepare($add_str_b); // prepare add statement
                        $add_stmt_b->bind_param("i", $last_id);
                        $add_stmt_b->execute();
                        $add_str1 = file_get_contents($sql_loc . $queries_loc . 'add_material_ebooks.sql');   
                    } else if ($mat_subset=="Audiobook") {
                        $add_str_b = file_get_contents($sql_loc . $queries_loc . 'add_material_books.sql');
                        $add_stmt_b = $conn->prepare($add_str_b); // prepare add statement
                        $add_stmt_b->bind_param("i", $last_id);
                        $add_stmt_b->execute();
                        $add_str1 = file_get_contents($sql_loc . $queries_loc . 'add_material_audiobooks.sql');   
                    } else if ($mat_subset=="Physical Recording") {
                        $add_str_b = file_get_contents($sql_loc . $queries_loc . 'add_material_books.sql');
                        $add_stmt_b = $conn->prepare($add_str_b); // prepare add statement
                        $add_stmt_b->bind_param("i", $last_id);
                        $add_stmt_b->execute();
                        $add_str1 = file_get_contents($sql_loc . $queries_loc . 'add_material_physical_recordings.sql');   
                    }
                  
                    $add_stmt1 = $conn->prepare($add_str1); // prepare add statement
                    $add_stmt1->bind_param("i", $last_id);
                    if ($add_stmt1->execute()) {
                        echo "Material book added successfully with ID: " . $last_id;
                    } else {
                        echo "Error adding material book: " . $add_stmt1->error;
                    }
                    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
                    exit();                 
                } else {
                    echo $conn->error;
                    echo "Oopsie <br>";
                }
            }
          
        /* -- RELOAD IF CHANGED ----------------------------- */
            if ($need_reload) {
                header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
                exit();
            }
    //
    /* -- SET UP TABLE -------------------------------------- */
        $sel_tbl = file_get_contents($sql_loc . $queries_loc . 'select_catalog.sql'); // get table info using sql select query
        $result = $conn->query($sel_tbl);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href = "theme.css">
        <title>TCPL Catalog</title>
        <link rel="icon" href="img/icon.svg" type="image/x-icon" />
    </head>

    <body style="font-family:arial">
        <?php navbar($_SESSION['uid'], $conn); ?>
        <h1 style="text-align:center;"><u>Materials Catalog</u></h1>
        <p>
            <!-- FILTER DROPDOWN ---------- -->
            <form method="GET">
                <select name= "material_type" style="margin-top:0px">
                    <option value="" disabled selected>Select type of material</option>
                    <?php
                    $mat_types= file_get_contents($sql_loc . $queries_loc . 'select_material_types.sql'); 
                    $result3 = $conn->query($mat_types);
                    $data = $result3->fetch_all();
                    //var_dump($all_data);
                    foreach ($data as $row) {
                        $val = $row[0];?>
                        <option value="<?=$val?>"><?=$val ?></option>
                    <?php } ?>
                </select>
                <input type= "submit" name="filter" value= "filter"/>
		    </form>
           
            <!-- DISPLAY TABLE ------------ -->
            <?php if (isset($_GET["filter"])) {
                $material_type = $_GET["material_type"] ?? null;
                #var_dump($_GET);
                $sel_tbl = file_get_contents(filter_materials($material_type)); 
            } 

            $result = $conn->query($sel_tbl);

            if (isset($_SESSION['uid'])) {
                if (is_staff($_SESSION['uid'], $conn)) {
                    result_to_del_html_table($result);
                } else {
                    result_to_html_table($result); 
                }
            } else {
                result_to_html_table($result); // display mats for all web users
            }

            // ADD MATERIALS FORM
            if (isset($_SESSION['uid'])) {
                if (is_staff($_SESSION['uid'], $conn)) { ?>
                <form method = "POST">
                <table>
                    <tr style="background-color:rgb(184, 226, 184);"><td style="text-align:left;" colspan="3"><b>Add Materials</b></td></tr>
                    <tr style="vertical-align:top; text-align:left;">
                        <td> <!-- info -->
                            <table style="margin-left:0px;      margin-top:0px; text-align:left;">
                                <tr><td style="border:1px solid white; padding:0px; text-align:left;"><u>Title</u>:</td>
                                    <td style="border:1px solid white; padding:0px; text-align:left;"><input type="text" name="title" placeholder="Enter text"/></td></tr>
                                <tr><td style="border:1px solid white; padding:0px; padding-right:5px; text-align:left;"><u>Year published</u>:</td>
                                    <td style="border:1px solid white; padding:0px; text-align:left;"><input type="text" name="year" placeholder="Enter text"/></td></tr>
                                <tr><td style="border:1px solid white; padding:0px; text-align:left;"><u>Publisher</u>:</td>
                                    <td style="border:1px solid white; padding:0px; text-align:left;"><input type="text" name="publisher" placeholder="Enter text"/></td></tr>
                                <tr><td style="border:1px solid white; padding:0px; text-align:left;"><u>Description</u>:</td>
                                    <td style="border:1px solid white; padding:0px; text-align:left;"><input type="text" name="desc" placeholder="Enter text"/></td></tr>
                                <tr><td style="border:1px solid white; padding:0px; text-align:left;"><u>Decimal</u>:</td>
                                    <td style="border:1px solid white; padding:0px; text-align:left;"><input type="text" name="decm" placeholder="Enter text"/></td></tr>
                            </table>
                        </td><td style="text-align:left;"> <!-- Language -->
                            <p style="margin-bottom:5px;"><u>Primary language</u>:</p>               
                            <?php
                                $lang = file_get_contents($sql_loc . $queries_loc . 'select_languages.sql'); 
                                $result2 = $conn->query($lang);
                                $all_data = $result2->fetch_all();
                                //var_dump($all_data);
                                $val="";
                                foreach ($all_data as $row) {
                                    if(isset($row[0])) {
                                        $val=$row[0];?>
                                        <input type= "radio" id="<?=$val?>" name="lang" value="<?=$val?>">
                                        <label for="<?=$val?>"><?=$val ?></label><br>
                                    <?php } else {
                                        "Error";
                                    }
                                } ?>
                        </td> <td style="text-align:left;"> <!-- Material_type  -->
                        <p style="margin-bottom:5px;"><u>Type</u>:</p>  
                            <?php
                                $mat_types= file_get_contents($sql_loc . $queries_loc . 'select_material_types.sql'); 
                                $result3 = $conn->query($mat_types);
                                $data = $result3->fetch_all();
                                $val="";
                                foreach ($data as $row) {
                                    if(isset($row[0])) {
                                        $val = $row[0];?>
                                        <input type= "radio" id="<?=$val?>" name="subset" value="<?=$val?>">
                                        <label for="<?=$val?>"><?=$val ?></label><br>
                                    <?php } else {
                                        "Error";
                                    }
                                } ?>
                        </td>
                    </tr>
                </table>
                <input type="submit" name="add_mat" value="Submit" style="margin-left:10px;"/></form>
                <?php } 
                } ?>
            </p>
    </body>

</html>