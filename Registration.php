<?php
session_start();
require_once 'database.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration form</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital@1&display=swap" rel="stylesheet">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
</head>
<body>
    <div class="logoButton">
        <button><a href="./index.html">RentBicycleTecko</a></button>
        <button class="accessibility" title="Change contrast" onclick="darkTheme()">
            <img src="./Images/brightness.png" alt="contrast"></button>
    </div>
<div class="registrationForm formHeader">
  <?php
  if (isset($_POST["submit"])) {
    $firstName = $_POST["firstName"];
    $secondName = $_POST["secondName"];
    $userName = $_POST["userName"];
    $phoneNumber = $_POST["phoneNumber"];
    $email = $_POST["email"];
    $userPassword = $_POST["userPassword"];
    $repeatPassword = $_POST["repeatPassword"];

    $passwordHash = password_hash($userPassword, PASSWORD_DEFAULT);

    //VALIDATION
    $errors = array();
    if (empty($firstName) || empty($secondName) || empty($userName) || empty($phoneNumber) || empty($email) || empty($userPassword) || empty($repeatPassword)) {
        array_push($errors, "All fields are required");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($errors, "Email is not valid");
    }
    if (!preg_match('/^[0-9]{10}$/', $phoneNumber)) {
        array_push($errors, "Phone number must contain 8 digits only");
    }
    if ($userPassword !== $repeatPassword) {
        array_push($errors, "Passwords must match");
    }
        //connect through the database.php file
        require_once 'database.php';
        //Check the email 
$sql = "SELECT * FROM customer WHERE email = '$email'";
$result = mysqli_query($conn, $sql);
$rowCount = mysqli_num_rows($result);
if($rowCount>0){
  array_push($errors, "Email already exists!");
}
    if (count($errors) > 0) {
        foreach ($errors as $error) {
            echo "$error<br>";
        }
    } else {

        // Prepare and execute the statement for the 'customer' table
        $sqlCustomer = "INSERT INTO customer (first_name, second_name, phone_number, email) VALUES (?, ?, ?, ?)";
        $stmtCustomer = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmtCustomer, $sqlCustomer)) {
            mysqli_stmt_bind_param($stmtCustomer, "ssss", $firstName, $secondName, $phoneNumber, $email);
            mysqli_stmt_execute($stmtCustomer);
            $customerId = $conn->insert_id; // Get the customer ID after insertion
            mysqli_stmt_close($stmtCustomer);

            // Prepare and execute the statement for the 'account' table
            $sqlAccount = "INSERT INTO account (user_password, user_name, created_by) VALUES (?, ?, ?)";
            $stmtAccount = mysqli_stmt_init($conn);
            if (mysqli_stmt_prepare($stmtAccount, $sqlAccount)) {
                mysqli_stmt_bind_param($stmtAccount, "ssi", $passwordHash, $userName, $customerId);
                mysqli_stmt_execute($stmtAccount);
                mysqli_stmt_close($stmtAccount);

                echo "You are registered successfully<br>";
                header("Location: Booking.php");
                exit();
            } else {
                die("Error with account statement preparation: " . mysqli_error($conn));
            }
        } else {
            die("Error with customer statement preparation: " . mysqli_error($conn));
        }
    }
  }
  ?>
    <h1 class="formHeader">Create new account</h1>
    <p>Personal details</p>
    <form action="registration.php" method="POST">
        <input type="text" placeholder="First Name" name="firstName" required>
        <input type="text" placeholder="Second Name" name="secondName" required>
        <input type="text" placeholder="User Name" name="userName" required>
        <p>Contact details</p>
        <input type="number" placeholder="Mobile Number" name="phoneNumber" required>
        <input type="email" placeholder="Email" name="email" required>
        <p>Password</p>
        <input type="password" placeholder="Password" name="userPassword" required>
        <input type="password" placeholder="Re-enter password" name="repeatPassword" required>
        <button type="submit" name="submit">Submit</button>
    </form>
    <p>Already a member? <a class="LogLink" href="./logIn.php">Log in here</a></p>
</div>
    <script src="./myscripts.js"></script>
</body>
</html>
