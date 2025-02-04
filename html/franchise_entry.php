<?php
    /* -- PAGE INIT ------------------------------------------ */
        include 'functions.php';
        start_page();
        $sql_loc = '../IMPLEMENTATION/';
        $queries_loc = 'queries/';
        $conn = connect();
        session_start();

    /* -- TABLE STUFF ---------------------------------------- */
        $franchise = $_GET['franchise_name'];
        $franchise_id = $_GET['franchise_id'];
        $sel_str = file_get_contents($sql_loc . $queries_loc . 'select_franchise_entry.sql');
        $sel_stmt = $conn->prepare($sel_str);
        $sel_stmt->bind_param('i', $franchise_id);
        // $result = $sel_stmt->store_result();
        // $sel_tbl->execute();
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href = "theme.css">
        <title><?php echo $franchise;?></title>
        <link rel="icon" href="img/icon.svg" type="image/x-icon" />
    </head>
    <body>
        <?php navbar($_SESSION['uid'], $conn); ?>
        <h1 style="text-align:center;"><u>All Entries in the <?php echo $franchise;?> Franchise </u></h1>

         <!-- DISPLAY TABLE -->
         <?php 
            // $sel_tbl->free_result();
            // $sel_tbl->execute();
            $sel_stmt->execute();
            $res = $sel_stmt->get_result();
            result_to_html_table($res); 
            $res->free(); ?>
    </body>
</html>