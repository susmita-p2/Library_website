<!-- Library homepage -->

<?php
    /* -- ERROR REPORTING + INITIALIZE COOKIES ----------------- */
        include 'functions.php';
        start_page(); // set up error reporting, initialize cookies
    //
    /* -- SESSION INIT ----------------------------------------- */
        session_start();
        $_SESSION['staff'] = 0;

		// store uid
		if (isset($_POST['uid'])) {
			$_SESSION['uid'] = $_POST['uid'];   // store uid in $_SESSION array
			header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
			exit();
		}

		// log out
		if (isset($_POST['logout'])) {
			session_unset();
			header("Location: {$_SERVER['REQUEST_URI']}",true, 303);
			exit();
		}
    //
    /* -- CONNECT TO DB ----------------------------------------- */
        $sql_loc = '../IMPLEMENTATION/';
        $queries_loc = 'queries/';
        $conn = connect(); // actually not sure if we will need to access the DB from here but just in case
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href = "theme.css"> <!-- Keeps site style consistent -->
        <meta charset="UTF-8"> <!-- Enables use of emojis :3 -->
        <title>TCPL Home</title>
        <link rel="icon" href="img/icon.svg" type="image/x-icon" />
    </head>

    <body style="font-family:arial">
        <h1 style="text-align:center;"><u>Therpston County Public Library</u></h1>
        <table style="border:white solid 1px; text-align:left;"> <!-- Why no workie :-( -->
            <tr>
            <!-- NAV BAR --------------- -->
            <td style="vertical-align:top; border:white solid 1px;"><table style="width:240px; margin-left:0px; margin-top:0px; padding-left:5px; text-align:left;">
                <tr style="padding-top:5px; padding-bottom:5px; vertical-align:top; height:75px; text-align:left;">
                    <!-- LOG IN ------------ -->
                    <td style=" padding-top:5px; padding-left:10px; text-align:left;">
                        <h3 style="margin-top:5px; margin-bottom:10px;"><u>Log In</u> <span style="font-size:20px;">&#128272;</span></h3>
                        <?php if (isset($_SESSION['uid'])) { ?>
                            <p style="margin-bottom:0px;">Welcome, <b><?php echo $_SESSION['uid']; ?></b></span>.</p>
                            <form action="index.php" method=POST style="margin-top:0px; margin-bottom:10px;">
                                <input type=submit name="logout" value="Log out" />
                            </form>
                        <?php } else { ?>
                            <p style="margin-bottom:0px;">
                                <form method=POST style="margin-bottom:0px;">
                                    <input type=text name="uid" placeholder="User ID#" />
                                    <input type=submit value="Submit" style="margin-bottom:0px;"/>
                                </form>
                            </p>
                        <?php } ?>
                    </td>
                </tr>
                <tr style="padding-top:5px; padding-bottom:5px; vertical-align:top; height:75px;">
                    <!-- NAVIGATION ------- -->
                    <td style="width:240px; padding-left:10px; text-align:left;">
                        <h3 style="margin-bottom:0px; margin-top:5px;"><u>Navigation</u> <span style="font-size:20px;">&#128269;</span></h3>
                            <ul style="margin-top:0px; margin-left:5px; list-style-type:'> '">
                                <li><a href="catalog.php">Materials catalog</a></li>
                                <li><a href="franchises.php">Franchises</a></li>
                                <!-- <?php if (isset($_SESSION['uid'])) { ?> <li><a href="equipment.php">Equipment</a> (WIP)</li> <?php } ?> -->
                                <?php if (isset($_SESSION['uid'])) { ?>
                                    <li><a href="loans.php">Loans</a></li>
                                <?php } ?>
                            </ul>
                    </td>
                </tr>
                <!-- STAFF MENU ------- -->
                <?php if (isset($_SESSION['uid'])) { ?>
                    <?php if (is_staff($_SESSION['uid'], $conn)) { ?>
                        <tr style="padding-top:5px; padding-bottom:5px; vertical-align:top; height:75px;">
                            <td style="width:250px; padding-left:10px; text-align:left; padding-right:5px;">
                                <h3 style="margin-bottom:0px; margin-top:5px;"><u>Staff Menu</u> <span style="font-size:20px;">&#128221;</span></h3>
                                <ul style="margin-top:0px; margin-left:5px; list-style-type:'> '">
                                    <li><a href="checkout.php">Materials checkout & returns</a></li>
                                </ul>
                        </tr>
                    <?php } 
                }?>
            </table></td>
            <td style="border:white solid 1px;"> <img src="img/shelves.jpg" alt="bookshelves" style="width:420px; padding-left=10px;"/> </td>
            <td style="border:white solid 1px;"> <img src="img/garf.png" alt="jarfield" style="height:420px;"></td>
            <td style="vertical-align:top; border:white solid 1px; width:100px;"><p> <br><br><br>&lt;&nbsp;Reading is cool! </p></td>
            </tr>
        </table>
    </body>
</html>