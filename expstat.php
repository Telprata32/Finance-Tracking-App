<?php
//Start the session
session_start();



//Carry over the email detail
$usEmail = $_SESSION["useMail"];

//Initiate variable to enter database details
$host = "localhost";
$user = "rahim";
$pass = "himeez225825";
$dtBase = "FinancApp";

//connect to the database 
$conn = new mysqli($host, $user, $pass, $dtBase);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//Prepare the queries to obtain the data of the first expenses
$qri = $conn->prepare("SELECT * FROM Expenses where Email = ? LIMIT 1");
$qri->bind_param("s", $usEmail);
$qri->execute(); // Execute the query
$res1 = $qri->get_result(); // get the result of the query 

//Take today's date and store into $strDate
$strDate = strtotime("today");
//Move the $strDate to the current week's monday's date if $strDate isn't monday
if (date("l",$strDate) != "Monday"){
	$strDate = strtotime("last Monday"); 
}

//--------------------------------------
//Section to move the chart left or right

//Detect if the user pressed next or previous
if (isset($_GET['left'])) {
    $_SESSION['leftright'] -= 1;
} elseif (isset($_GET['right'])) {
    $_SESSION['leftright'] += 1;
}

//Function to implement the left or right
if ($_SESSION['leftright'] > 0) {
    for ($i = 0; $i < $_SESSION['leftright']; $i++) {
        $strDate = strtotime("next Monday", $strDate);
    }
} elseif ($_SESSION['leftright'] < 0) {
    for ($i = 0; $i > $_SESSION['leftright']; $i--) {
        $strDate = strtotime("last Monday", $strDate);
    }
}


//----------------------------------------
/*Section to prapare values for the graph
Initialize an array of 7 elements length  --> Example $dyExp = array(0,0,0,0,0,0,0);*/
$dyExp = array(0, 0, 0, 0, 0, 0, 0);

//calculate up to sunday for the first week with transaction
$endWk = strtotime("next Sunday", $strDate); //get the date of the upcoming sunday
// $dayDiff = ceil(($endWk - $strDate) / 60 / 60 / 24); //get the difference between the two dates in days

//Set the current day
$curDate = date("Y-m-d", $strDate);

//perform a for loop to store the Expenses into their respective days
for ($i = 0; $i < 7; $i++) {
    //prepare query to retrieve the Amount for respective date
    $qr2 = $conn->prepare("SELECT Amount From Expenses WHERE Email=? and pyDate=?");
    $qr2->bind_param("ss", $usEmail, $curDate);
    $qr2->execute();
    $res2 = $qr2->get_result();

    //store the amount for respective day into the array
    while ($curRow=$res2->fetch_array()) {
        $dyExp[$i] += $curRow['Amount'];
    }

    //move to the next date
    $curDate = date("Y-m-d", strtotime("+1 day", strtotime($curDate)));
}

//Store the values from the array into the php function for the line chart
$dataPoints = array(
    array("y" => $dyExp[0], "label" => "Monday"),
    array("y" => $dyExp[1], "label" => "Tuesday"),
    array("y" => $dyExp[2], "label" => "Wednesday"),
    array("y" => $dyExp[3], "label" => "Thursday"),
    array("y" => $dyExp[4], "label" => "Friday"),
    array("y" => $dyExp[5], "label" => "Saturday"),
    array("y" => $dyExp[6], "label" => "Sunday")
);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graphical Statistics</title>

    <!-- CSS Link for the header -->
    <link rel="stylesheet" href="Finance.css">

    <!-- Script to display line graph -->
    <script>
        window.onload = function() {

            var chart = new CanvasJS.Chart("chartContainer", {
                title: {
                    text: "Weekly Expenses"
                },
                axisY: {
                    title: "Spendings(RM)"
                },
                data: [{
                    type: "line",
                    dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
                }]
            });
            chart.render();

        }
    </script> 

    <!-- Api to run the line graph -->
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

    <!-- Links for the next and previous buttons -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

</head>

<body>
    <!-- Website header -->
    <div class="topnav">
        <!-- Home button -->
        <a href="main.php" style="float:left; background-color:black;">Home</a>
        <!-- "Hamburger menu" / "Bar icon" to toggle the navigation links -->
        <a href="addTrn.php" class="active">Weekly Statistics</a>
        <!-- Navigation links (hidden by default) -->
    </div>

    <div class="container">
        <!-- Line graph -->
        <div id="chartContainer" style="height: 370px; width: 100%; border: solid 3.4px #FFFF00; margin-left:0%;"></div>

        <?php
        echo "<div style=\"text-align:center;\">" . date("d M", $strDate) . " - " . date("d M", $endWk) . "</div>";
        ?>

        <ul class="pager">
            <li><a href="expstat.php?left=true">Previous</a></li>
            <li><a href="expstat.php?right=true">Next</a></li>
        </ul>
    </div>

</body>

</html>
