<?php
//Start Session
session_start();

//Reset the session variable for the line graph
unset($_SESSION['leftright']);

//Retrieve the account details;
$usEmail = $_SESSION["useMail"];

//First connect to the database
//variables that define the connection to the database
$servername = "localhost";
$username = "rahim";
$password = "himeez225825";
$dBase = "FinancApp";

// Create connection
$conn = new mysqli($servername, $username, $password, $dBase);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['reset'])) {
    session_start();

    //Retrieve the account details;
    $usEmail = $_SESSION["useMail"];

    //First connect to the database
    //variables that define the connection to the database
    $servername = "localhost";
    $username = "rahim";
    $password = "himeez225825";
    $dBase = "FinancApp";

    // Create connection
    $conn1 = new mysqli($servername, $username, $password, $dBase);

    //Declare query to clear balance and expenses
    //query1
    $qer1 = $conn1->prepare("DELETE FROM Expenses WHERE Email=?");
    $qer1->bind_param("s", $usEmail);
    $qer1->execute();

    //query2
    $qer2 = $conn1->prepare("DELETE FROM Balance WHERE Email=?");
    $qer2->bind_param("s", $usEmail);
    $qer2->execute();
}
?>

<!DOCTYPE html>
<html lang=eng-US>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <!-- api to run pie chart -->
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

    <link rel='stylesheet' type='text/css' media='screen' href='Finance.css'>
    <link rel="stylesheet" href="Finance.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Finance App</title>

    <?php
    // Retrieve details from the database for the pie chart
    //check if the expenses table exists or not
    $expChk = $conn->prepare('SELECT 1 FROM `Expenses` LIMIT 1');

    if ($expChk == TRUE) {

        function catVal($ctGory)
        {
            global $usEmail;
            global $conn;
            //Query to retrieve details for pie chart
            $sQry = $conn->prepare("SELECT * FROM Expenses WHERE Email=? AND Category=?");
            //Get the details for specific expenses
            $sQry->bind_param("ss", $usEmail, $ctGory);
            $sQry->execute();
            $exQry = $sQry->get_result();

            //Initialize the value for amtPay
            $amtPay = 0;

            while ($rev1 = $exQry->fetch_array()) {
                $amtPay += $rev1['Amount'];
            }
            return $amtPay;
        }


        //Get the details for restaurant expenses
        $rstPay = catVal("Restaurant");

        //Get the details for Health expenses
        $hthPay = catVal("Health");

        //Get the details for Groceries expenses
        $grcPay = catVal("Groceries");

        //Get the details for Shopping expenses
        $shpPay = catVal("Shopping");

        //Get the details for Travelling expenses
        $trvPay = catVal("Travelling");

        //Get the details for Education expenses
        $eduPay = catVal("Education");

        //Get the details for Work expenses
        $wrkPay = catVal("Work");

        //Get the details for Bills/Taxes expenses
        $btxPay = catVal("BillsTaxes");

        //Calculate the total spent
        $Total = $eduPay + $shpPay + $grcPay + $btxPay + $wrkPay + $hthPay + $trvPay + $rstPay;

        //calulate the percentage of each spending
        $eduPay = $eduPay / $Total * 100;
        $rstPay = $rstPay / $Total * 100;
        $trvPay = $trvPay / $Total * 100;
        $btxPay = $btxPay / $Total * 100;
        $wrkPay = $wrkPay / $Total * 100;
        $hthPay = $hthPay / $Total * 100;
        $grcPay = $grcPay / $Total * 100;
        $shpPay = $shpPay / $Total * 100;

        $dataPoints = array(
            array("label" => "Restaurant", "symbol" => "Rest.", "y" => $rstPay),
            array("label" => "Health", "symbol" => "Health", "y" => $hthPay),
            array("label" => "Groceries", "symbol" => "Groc.", "y" => $grcPay),
            array("label" => "Shopping", "symbol" => "Shop.", "y" => $shpPay),
            array("label" => "Travelling", "symbol" => "Trvl.", "y" => $trvPay),
            array("label" => "Education", "symbol" => "Edu.", "y" => $eduPay),
            array("label" => "Work", "symbol" => "Work", "y" => $wrkPay),
            array("label" => "Bills/Taxes", "symbol" => "B/T", "y" => $btxPay),

        );
    } else {
        $dataPoints = array(
            array("label" => "Restaurant", "symbol" => "Rest.", "y" => 0),
            array("label" => "Health", "symbol" => "Health", "y" => 0),
            array("label" => "Groceries", "symbol" => "Groc.", "y" => 0),
            array("label" => "Shopping", "symbol" => "Shop.", "y" => 0),
            array("label" => "Travelling", "symbol" => "Trvl.", "y" => 0),
            array("label" => "Education", "symbol" => "Edu.", "y" => 0),
            array("label" => "Work", "symbol" => "Work", "y" => 0),
            array("label" => "Bills/Taxes", "symbol" => "B/T", "y" => 0),

        );
    }



    ?>

    <!-- Scripts to run the pie chart -->
    <script>
        window.onload = function() {

            var chart = new CanvasJS.Chart("chartContainer", {
                theme: "light2",
                animationEnabled: true,
                title: {
                    text: "Overall Spendings"
                },
                data: [{
                    type: "doughnut",
                    indexLabel: "{symbol} - {y}",
                    yValueFormatString: "#,##0.0\"%\"",
                    showInLegend: true,
                    legendText: "{label} : {y}",
                    dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
                }]
            });
            chart.render();

        }
    </script>
</head>

<body>
    <?php

    //Define the query to check if the Balance table exists or not
    $val = $conn->prepare('SELECT 1 FROM `Balance` LIMIT 1');

    //Create Table query first just in case table doesn't exist
    //define the query
    $sQuer1 = "CREATE TABLE Balance(
    Email CHAR(50),
    accBal DECIMAL(9,2)
  )";

    //Check if the Table exist
    if ($val === FALSE) {
        $conn->query($sQuer1); // Execute the query
    }

    //Definet the query to check if the Accounts' record of balnce exists or not
    $val2 = $conn->prepare("SELECT accBal FROM Balance WHERE Email=?");
    $val2->bind_param("s", $usEmail);
    $val2->execute();
    $eVal2 = $val2->get_result();

    ?>

    <div class="topnav">
        <!--Logout button-->
        <a style="float:left; background-color:black;" href="index.php"> Logout</a>


        <!-- "Hamburger menu" / "Bar icon" to toggle the navigation links -->
        <a href="#home" class="active">Financio</a>
        <!-- Navigation links (hidden by default) -->

    </div>

    <div style="border: solid 6px #FFFF00; margin: 2cm;">
        <div id="monBal" style="font-size: 37px;text-align: center; margin-top: 4.7cm;">
            Balance: RM

            <?php
            if ($eVal2->num_rows < 1)  //When there're no money in balance
            {
                echo "0"; // just print 0
            } else {
                //Obtain balance from account
                $recv = $eVal2->fetch_array();
                $Bal = $recv['accBal'];

                //print the balance
                echo "$Bal";
            }
            ?>
        </div>


        <table id="mainDat">
            <div style="height:50px; overflow: auto;">
                <tr style="border-bottom: 4.2px solid #FFFF00;">
                    <th>Category</th>
                    <th>Amount</th>
                    <th>Date</th>

                </tr>




                <?php

                //check if the expenses table exists or not
                $expChk = $conn->prepare('SELECT 1 FROM `Expenses` LIMIT 1');


                if ($expChk == TRUE) {
                    // Prepare query to enter transactions for account logged in
                    $sQry = $conn->prepare("SELECT * FROM Expenses WHERE Email=?");
                    $sQry->bind_param("s", $usEmail);
                    $sQry->execute();
                    $exQry = $sQry->get_result();

                    // Fill the table elements with html elements
                    while ($tblRow = $exQry->fetch_array()) {
                        echo "<tr>" . "<th>" . ($tblRow['Category']) . "</th>" . "<th>" . "RM " . ($tblRow['Amount']) . "</th>" . "<th>" . ($tblRow['pyDate']) . "</th>" . "</tr>";
                    }
                }

                ?>
            </div>

        </table>

        <!-- Division to display the pie chart -->
        <?php
        //check if the specific account have any expenses
        //Definet the query to check if the Accounts' record of balnce exists or not
        $expChk2 = $conn->prepare("SELECT accBal FROM Balance WHERE Email=?");
        $expChk2->bind_param("s", $usEmail);
        $expChk2->execute();
        $expRet = $expChk2->get_result();

        //If else to print the div or not
        if ($expRet->num_rows >= 1) {
            echo "<div id=\"chartContainer\" style=\"height: 370px; width: 100%;\"></div>";
        }

        $conn->close();
        ?>

    </div>





    <div style="position:fixed; right: 170px; bottom: 168px">
        <div class="dropdown">

            <div id="menHid" class="dropdown-content">
                <a href="addTrn.php"> Add Expenses </a>
                <a href="income.php">Add Income</a>
                <a href="expstat.php">View Statistics</a>
                <a href='main.php?reset=true'>Clear History</a>
            </div>
            <button class="buttonPlus btn btn-primary dropdown-toggle" type="button" onclick="shwFunc()" data-toggle="dropdown"><i style="font-size:24px" class="fa">&#xf067;</i>

        </div>

    </div>



    <script>
        /* When the user clicks on the button, 
        toggle between hiding and showing the dropdown content */
        function shwFunc() {
            document.getElementById("menHid").classList.toggle("show");
        }

        // Close the dropdown if the user clicks outside of it
        window.onclick = function(event) {
            if (!event.target.matches('.buttonPlus')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                var i;
                for (i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>

</html>