<?php
//connect through the database.php file
require_once 'database.php';

// Form validation 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['userName']) && !empty($_POST['userPassword'])) {
        $userName = $_POST['userName'];
        $userPassword = $_POST['userPassword'];

        // Check if the user exists in the database
        $stmt = $conn->prepare("SELECT user_password FROM account WHERE user_name = ?");
        $stmt->bind_param("s", $userName);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($hashedPassword);
            $stmt->fetch();

            // Debugging output
            error_log("Fetched hashed password: $hashedPassword");

            // Verify the password
            if (password_verify($userPassword, $hashedPassword)) {
                session_start();
                $_SESSION['userName'] = $userName;
                header("Location: Booking.php");
                exit(); // Ensure no further code is executed
            } else {
                $errorMessage = "Invalid username or password.";
            }
        } else {
            $errorMessage = "Invalid username or password.";
        }

        $stmt->close();
    } else {
        $errorMessage = "All fields are required.";
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital@1&display=swap" rel="stylesheet">
</head>
<body>
    <div class="logoButton">
        <button><a href="./index.html">RentBicycleTecko</a></button>
        <button class="accessibility" title="Change contrast" onclick="darkTheme()">
            <img src="./Images/brightness.png" alt="contrast"></button>
    </div>
    <div class="registrationForm formHeader">
        <h1 class="formHeader">Log in</h1>
        <?php
        if (!empty($errorMessage)) {
            echo "<p class='error'>$errorMessage</p>";
        }
        if (!empty($successMessage)) {
            echo "<p class='success'>$successMessage</p>";
        }
        ?>
        <form action="login.php" method="POST">
            <input type="text" placeholder="User Name" name="userName" required>
            <input type="password" placeholder="Password" name="userPassword" required>
            <button type="submit">Log in</button>
        </form>
        <p>Don't have an account? <a class="LogLink" href="./Registration.php">Register here</a></p>
    </div>
    <script src="./myscripts.js"></script>
</body>
</html>
