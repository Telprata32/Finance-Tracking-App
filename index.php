<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="logregForm.css">
</head>

<body>


  <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div class="container">
      <h1>Sign In</h1>
      <p>Please fill in this form to access your account</p>
      <hr>

      <label for="email"><b>Email</b></label>
      <input type="text" placeholder="Enter Email" name="email" id="email" required>

      <label for="psw"><b>Password</b></label>
      <input type="password" placeholder="Enter Password" name="psw" id="psw" required>

      <hr>
      <?php
      //Start session to transfer account details to other pages
      session_start();

      //Connect to database
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

      //Define the variables to receive user login input
      $email = $_POST['email'];
      $pass = $_POST['psw'];

      //Define the query to check if the Accounts table exists or not
      $val = $conn->prepare('SELECT 1 FROM `Accounts` LIMIT 1');

      //Create Table query first just in case table doesn't exist
      $sQuer1 = "CREATE TABLE Accounts(
        Email CHAR(50),
        Pass CHAR(50)
     )";

      //Check if the table Accounts exist
      if ($val === FALSE) //if the Table exists
      {
        //Create the table because table doesn't exist
        $conn->query($sQuer1);  // query() function performs a query against a database
      }

      //Define the query to check if specific account exists or not 
      $query = "SELECT `Email` FROM `Accounts` WHERE Email=?";

      //Define the query to check if the password is correct
      $query2 = "SELECT `Email`,`Pass` FROM `Accounts` WHERE Email=? and Pass=?";

      //If the post method is detected then initiated then retreive the values into the variables and call the functions
      if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if ($stmt = $conn->prepare($query)) {

          $stmt->bind_param("s", $email);

          if ($stmt->execute()) {
            $stmt->store_result();

            $email_check = "";
            $stmt->bind_result($email_check);
            $stmt->fetch();

            //check if the Password is correct
            if ($stmt->num_rows >= 1) {
              //Execute only if the fields are filled in
              if (isset($_POST['email']) && isset($_POST['psw'])) {

                if ($stmt2 = $conn->prepare($query2)) {

                  $stmt2->bind_param("ss", $email, $pass);

                  if ($stmt2->execute()) {
                    $stmt2->store_result();

                    $email_check = "";
                    $stmt2->bind_result($email_check);
                    $stmt2->fetch();

                    if ($stmt2->num_rows >= 1) //If the password matches the respective password of the email
                    {
                      //Carry over account details using session variables
                      $_SESSION["useMail"] = $email;

                      //Redirect to main page
                      header('Location: main.php');
                    } else {
                      echo "<p1 style=\"color: red;\"> The password is incorrect, try again </p1>";
                    }
                  }
                }
              }
            } else {
              echo "<p1 style=\"color: red;\"> Account doesn't exist, please register first </p1>";
            }
          }
        }
      }

      ?>

      <button type="submit" class="registerbtn">Sign In</button>

      <div class="container signin">
        <p>Do not have an account yet? <a href="regacc.php">Register</a>.</p>
      </div>
    </div>


  </form>

</body>

</html>

<?php $conn->close(); ?>