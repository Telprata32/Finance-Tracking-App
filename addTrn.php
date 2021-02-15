<!DOCTYPE html>
<html lang="en-US">

<head>
  <meta charset='utf-8'>
  <meta http-equiv='X-UA-Compatible' content='IE=edge'>
  <link rel="stylesheet" href="Finance.css">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <title>Finance App</title>
</head>

<body>
  <div class="topnav">
    <!-- Home button -->
    <a href="main.php" style="float:left; background-color:black;">Home</a>
    <!-- "Hamburger menu" / "Bar icon" to toggle the navigation links -->
    <a href="addTrn.php" class="active">Expenses</a>
    <!-- Navigation links (hidden by default) -->
  </div>





  <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" name="addExpense" class="mainForm">
    <div class="keyin">
      Amount
    </div>
    <input type="text" onfocus="this.value=''" value="0" step="0.01" id="amount" name="amount">

    <div style="position: fixed;" id="Cat">
      <select name="catGory" id="catSelect">
        <option hidden disabled selected value=''>Select a Category</option>
        <option value="Restaurant">Restaurant</option>
        <option value="Health">Health</option>
        <option value="Groceries">Groceries</option>
        <option value="Shopping">Shopping</option>
        <option value="Travelling">Travelling</option>
        <option value="Education">Education</option>
        <option value="Work">Work</option>
        <option value="BillsTaxes">Bills/Taxes</option>
      </select>
    </div>
    <input type="submit" value="Add Expense" class="subBtn">



  </form>


</body>

</html>


<?php
//Start Session
session_start();

//Retrieve the account details;
$usEmail = $_SESSION["useMail"];

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

//Define the query to check if the Balance table exists or not
$val3 = $conn->prepare('SELECT 1 FROM `Balance` LIMIT 1');

//Definet the query to check if the Accounts' record of balance exists or not
$val2 = $conn->prepare("SELECT accBal FROM Balance WHERE Email=?");
$val2->bind_param("s", $usEmail);
$val2->execute();
$eVal2 = $val2->get_result();

//Check if the Expense table exists or no
$val = $conn->prepare('select 1 from `Expenses` LIMIT 1');

//Create Table query first 
$sQuer1 = "CREATE TABLE Expenses(
    Amount DECIMAL(9,2),
    Category CHAR(10),
    pyDate Date ,
    dyWeek CHAR(10) , 
    Email CHAR(50)
  )";

if ($val === FALSE) //if the Table exists
{
  $conn->query($sQuer1);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  if (!isset($_POST['catGory'])) {
    echo "<script> alert(\"Please select a category\");</script>";
  }

  //Declare the variables that will receive the payment infos
  if (isset($_POST['amount']) && isset($_POST['catGory'])) {

    $inAmount = $_POST['amount'];
    $trnCat = $_POST['catGory'];

    if (preg_match("/[a-z]/i", $inAmount)) {
      echo "<script> alert(\"The value entered is a string, only integers are excepted\")</script>";
    } 
    else 
    {
      if (($val3 === FALSE) || ($eVal2->num_rows < 1))  //When there're no money in balance
      {
        echo "<script> alert(\"Not enough money in your balance, please top up your account!\");</script>";
      } else {
        //Retrieve balance from account
        //Obtain balance from account
        $recv = $eVal2->fetch_array();
        $Bal = $recv['accBal'];


        if ($inAmount > $Bal) {
          echo "<script> alert(\"Not enough money in your balance, please top up your account!\");</script>";
        } else {
          //Deduct the amount from account balance
          $Bal -= $inAmount;

          //Update the account's balance
          //Prepare the parameter to update the value of the balance
          $Qr2 = $conn->prepare("UPDATE Balance SET accBal=? WHERE Email=?");
          $Qr2->bind_param("ds", $Bal, $usEmail);
          $Qr2->execute();

          //Execute addition of record into Table
          //Prepare statement and parameters to be executed into the query
          $statmnt = $conn->prepare("INSERT INTO Expenses (Amount,Category,pyDate,dyWeek,Email) VALUES (?,?,CURDATE(),DAYNAME(CURDATE()),?)"); //prepare SQL statement/query for execution
          $statmnt->bind_param("dss", $inAmount, $trnCat, $usEmail); //bind the selected parameters to the SQL statement   

          //Execute the query statement
          $statmnt->execute();
        }
      }
    }
  }
}
$dt = date("l d-m"); // To be used for later, for storing date
$conn->close();

?>