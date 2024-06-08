<?php
// Establishing connection to the database
$servername = "localhost"; // Change this to your database server name
$username = "root"; // Change this to your database username
$password = ""; // Change this to your database password
$dbname = "pharma"; // Change this to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to clean input data
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to validate email
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Variable to store validation errors
$errors = array();

// Checking if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Cleaning and validating input data
    $email = clean_input($_POST["email"]);
    $Fname = clean_input($_POST["Fname"]);
    $Lname = clean_input($_POST["Lname"]);
    $address = clean_input($_POST["address"]);
    $passwd = clean_input($_POST["passwd"]);

    // Validate email
    if (!validate_email($email)) {
        $errors[] = "Invalid email address";
    }

    // Check if any of the fields are empty
    if (empty($email) || empty($Fname) || empty($Lname) || empty($address) || empty($passwd)) {
        $errors[] = "All fields are required";
    }

    // If there are validation errors, display them in an alert
    if (!empty($errors)) {
        $error_message = implode("\\n", $errors);
        echo "<script>alert('$error_message');</script>";
    } else {
        // Inserting data into the database without hashing the password
        $sql = "INSERT INTO user (user_Lname, email, user_password, user_fname, user_address) VALUES ('$Lname', '$email', '$passwd', '$Fname', '$address')";

        if ($conn->query($sql) === TRUE) {
            // If data insertion is successful
            echo "<script>alert('New record created successfully');</script>";
        } else {
            // If there is an error in data insertion
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    }
}

// Closing the database connection
$conn->close();
?>



<head>
    <title>
        Lifecare
    </title>
    <link rel="icon" href="images/logo.png" type="image/icon type">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
</head>
<!------ Include the above in your HEAD tag ---------->
<div class="container">
    <div id="signupbox" style=" margin-top:50px" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">


        <div class="panel panel-info">
            <div class="panel-heading">
                <div class="panel-title">Sign Up</div>
            </div>  
            
            <div class="panel-body">
                <form id="signupform" class="form-horizontal" role="form" method="post" action="signUp.php">
                    <div class="form-group">
                        <label for="email" class="col-md-3 control-label">Email</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="email" placeholder="Email Address">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="firstname" class="col-md-3 control-label">First Name</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="Fname" placeholder="First Name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="lastname" class="col-md-3 control-label">Last Name</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="Lname" placeholder="Last Name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address" class="col-md-3 control-label">Address</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="address" placeholder="Address">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password" class="col-md-3 control-label">Password</label>
                        <div class="col-md-9">
                            <input type="password" class="form-control" name="passwd" placeholder="Password">
                        </div>
                    </div>
                    <div style=" margin-left: 39px;">
                        <b> Password must contain the following:</b>
                        <ul>
                            <li>at least 1 number and 1 letter</li>
                            <li>Must be 8-30 characters</li>
                        </ul>
                    </div>


                    <div class="form-group">
                        <!-- Button -->
                        <div class="col-sm-12 controls">
                            <input id="btn-login" class="btn btn-success" type="submit" value="Sign Up" name="singUp" />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12 control">
                            <div style="border-top: 1px solid#888; padding-top:15px; font-size:85%">
                                You are already have an account?!
                                <a href="login.php">
                                    Sign In Here
                                </a>
                            </div>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>