<!DOCTYPE html>
<html lang="en-US">

<?php
//Start the session
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
$conn = new mysqli($servername, $username, $password, $dBase);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

//Create the table if it doesn't exist for the user
//Define the query to check if the Accounts table exists or not
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

//Check if the account has a record in the table
$sQry = $conn->prepare("SELECT accBal FROM Balance WHERE Email=?");
$sQry->bind_param("s", $usEmail);
$sQry->execute();
$exQry = $sQry->get_result();

?>

<head>
  <meta charset='utf-8'>
  <meta http-equiv='X-UA-Compatible' content='IE=edge'>
  <link rel="stylesheet" href="Finance.css">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <title>Finance App</title>
  <style>
    .income {
      margin-top: 25%;
      margin-left: 15%;
      font-size: 40px;
    }

    .key1 {
      border-style: solid;
      border-width: 1.4px;
      border-color: black;
      color: #b27720;
      float: left;
      padding: 4.7px;
      text-align: center;
      width: 40%;
    }

    #amount1 {
      font-size: 40px;
      border-style: solid;
      border-width: 1.4px;
      border-color: black;
      width: 40%;
      text-align: center;
    }

    .subBtn {
      background-color: #0563af;
      color: white;
      padding: 12px;
      width: 225px;
      margin-left: 23.3cm;
      border: none;
      border-radius: 13px;
      font-size: 20px;
      -webkit-appearance: button;
      appearance: button;
      outline: none;
    }
  </style>
</head>

<body>
  <div class="topnav">
    <!-- Home button -->
    <a href="main.php" style="float:left; background-color:black;"> Home </a>
    <!-- "Hamburger menu" / "Bar icon" to toggle the navigation links -->
    <a href="income.php" class="active">Income</a>
    <!-- Navigation links (hidden by default) -->
  </div>


  <form action="" method="POST" class="mainForm">
    <div>
      <div class="keyin">
        Income
      </div>


      <input type="text" onfocus="this.value=''" value="0" step="0.01" id="amount" name="amount">
    </div>
    <input type="submit" value="Add Income" class="subBtn">

  </form>

  <?php

  // Execute function to calculate balance of account
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (preg_match("/[a-z]/i", $_POST['amount'])) {
      echo "<script> alert(\"The value entered is a string, only integers are accepted\")</script>";
    } else {
      //If the record for the specific account doesn't exist create one
      if ($exQry->num_rows >= 1) {
        //Retrieve respective Balance value from database
        $recv = $exQry->fetch_array();
        $Bal = $recv['accBal'];

        //Obtain the income value from the form
        $upBal = $_POST['amount'];

        //Calculate the updated balance
        $Bal += $upBal;

        //Prepare the parameter to update the value of the balance
        $Qr2 = $conn->prepare("UPDATE Balance SET accBal=? WHERE Email=?");
        $Qr2->bind_param("ds", $Bal, $usEmail);
        $Qr2->execute();
      } else {
        //Obtain the balance from the form
        $Bal = $_POST['amount'];

        //Prepare the parameter to create the record of the balance for the account
        $Qr2 = $conn->prepare("INSERT INTO Balance (Email,accBal) VALUES (?,?)");
        $Qr2->bind_param("sd", $usEmail, $Bal);
        $Qr2->execute();
      }
    }
  }
  ?>

</body>



</html>