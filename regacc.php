<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="logregForm.css">
    <title>Register Account</title>
</head>

<body>


    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="container">
            <h1>Register</h1>
            <p>Please fill in this form to create an account.</p>
            <hr>

            <label for="email"><b>Email</b></label>
            <!-- Print the email validation error message -->
            <?php
            if (isset($_GET['mailErr'])) {
                echo "<label style=\"color:red;margin-left:15px; \"> Invalid Email Format</label>";
            }
            ?>
            <input type="text" placeholder="Enter Email" name="email" id="email" required>


            <label for="psw"><b>Password</b></label>
            <?php
            if (isset($_GET['passLth'])) {
                echo "<label style=\"color:red;margin-left:15px; \"> Password is not long enough </label>";
            }
            ?>
            <input type="password" placeholder="Enter Password" name="psw" id="psw" required>

            <label for="psw-repeat"><b>Repeat Password</b></label>
            <?php
            if (isset($_GET['passLth'])) {
                echo "<label style=\"color:red;margin-left:15px; \"> Password is not long enough </label>";
            }
            ?>
            <input type="password" placeholder="Repeat Password" name="psw-repeat" id="psw-repeat" required>
            <hr>
            <?php
            //Start Session
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

            //Define the variables to register user account
            $email = $_POST['email'];
            $pass = $_POST['psw'];
            $pasRpt = $_POST['psw-repeat'];

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


            //Prepare query for checking if an account exists  
            $query = "SELECT `Email` FROM `Accounts` WHERE Email=?";

            //If the post method is detected then initiated then retreive the values into the variables and call the functions
            if ($_SERVER["REQUEST_METHOD"] == "POST") {

                if ($stmt = $conn->prepare($query)) {

                    $stmt->bind_param("s", $email);

                    if ($stmt->execute()) {
                        $stmt->store_result();

                        $email_check = "";
                        $stmt->bind_result($email_check);
                        $stmt->fetch();

                        if ($stmt->num_rows >= 1) {

                            echo "<p1 style=\"color: red;\"> Account already exists </p1>";
                        } else {
                            //Execute only if the fields are filled in
                            if (isset($_POST['email']) && isset($_POST['psw']) && isset($_POST['psw-repeat'])) {

                                //Check if Reentered password matches password
                                if (!filter_var($email, FILTER_VALIDATE_EMAIL))  // if the email is not a valid email format
                                { 
                                    header("Location: regacc.php?mailErr=true");
                                }
                                elseif (($pass == $pasRpt) && (strlen($pass)<6)) //if the password is not long enough
                                {
                                    // Bring to the same page and display error
                                    header("Location: regacc.php?passLth=true");
                                }  
                                elseif($pass!=$pasRpt)
                                { 
                                    echo "<p1 style=\"color: red;\">The passwords do not match, reenter them </p1>";
                                }
                                else 
                                {
                                    //Prepare statement and parameters to be executed into the query
                                    $statmnt = $conn->prepare("INSERT INTO Accounts (Email,Pass) VALUES (?,?)"); //prepare SQL statement/query for execution
                                    $statmnt->bind_param("ss", $email, $pass); //bind the selected parameters to the SQL statement   

                                    //Execute the query statement
                                    $statmnt->execute();

                                    //Bring over the account details to main page
                                    $_SESSION["useMail"] = $email;

                                    //Redirect to main page
                                    header('Location: main.php');
                                }
                            }
                        }
                    }
                }
            }

            ?>

            <button type="submit" class="registerbtn">Register</button>

            <div class="container signin">
                <p>Already have an account? <a href="index.php">Sign in</a>.</p>
            </div>
        </div>


    </form>

</body>

</html>

<?php $conn->close(); ?>