<?php
// Start the session and check if the user name is valuable
session_start();
if (!isset($_SESSION['userName'])) {
    header("Location: login.php");
    exit();
}
//connect through the database.php file
require_once 'database.php';

// options from table
function fetchOptions($conn, $table, $idColumn, $typeColumn, $bicycleType) {
    $options = "";
    $sql = "SELECT $idColumn, $typeColumn FROM $table";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $options .= "<option value='" . $row[$idColumn] . "' data-type='" . $bicycleType . "'>" . $row[$idColumn] . " - " . $row[$typeColumn] . "</option>";
        }
    }

    return $options;
}

// Bicycle types
$classicOptions = fetchOptions($conn, 'classic_bicycle', 'classic_bicycle_id', 'class_b_type', 'Classic');
$electricalOptions = fetchOptions($conn, 'electrical_bicycle', 'elect_bicycle_id', 'el_b_type', 'Electrical');
$sportOptions = fetchOptions($conn, 'sport_bicycle', 'sport_bicycle_id', 'sport_b_type', 'Sport');

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>  
    <title>Booking</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital@1&display=swap" rel="stylesheet">
</head>

<body>
<div class="logoButton">
    <button><a href="./index.html" title="RentBicycleTecko company">RentBicycleTecko</a></button>
    <button class="accessibility" title="Change contrast" onclick="darkTheme()">
        <img src="./Images/brightness.png" alt="contrast"></button>
</div>
<div class="pageHeader">
    <h1>Choose your bicycle</h1>
</div>
<div class="itemsContainer" style="height: 60vh;">
    <div class="item">
        <div class="itemPhoto">
            <img src="./Images/classicBicycle.jpg" alt="Classic bicycle">
        </div>
        <div class="itemTitle">
            <h2>Classic bicycle</h2>
        </div>
        <div class="itemDescription">
            <p>Looking for a bicycle without any unnecessary features. That is a good choice for those who cycles to work or around the town. Bicycle comes with steel or aluminium frames. Suitable for city or countryside rides.</p>
        </div>
        <form action="BookingSummary.php" method="POST"><!--added-->
            <div class="itemDroplist">
                <select name="bicycleSelect" id="ClBicycleSelect" title="Select the bicycle" required>
                    <option value="" selected disabled>Available bicycles</option>
                    <?php echo $classicOptions; ?>
                </select>
            </div>
            <button type="submit" title="Book the bicycle">Book</button>
        </form>
    </div>
    <div class="item">
        <div class="itemPhoto">
            <img src="./Images/electricalBicycle.jpg" alt="Electrical bicycle">
        </div>
        <div class="itemTitle">
            <h2>Electrical bicycle</h2>
        </div>
        <div class="itemDescription">
            <p>The future is just around the corner. Have a great experience of riding an electrically powered cycle. Aluminium, carbon, or titanium frames are available. Bicycles can run up to 27 km on the electrical engine.</p>
        </div>

    <form action="BookingSummary.php" method="POST">
            <div class="itemDroplist">
                <select name="bicycleSelect" id="ElBicycleSelect" title="Select the bicycle" required>
                    <option value="" selected disabled>Available bicycles</option>
                    <?php echo $electricalOptions; ?>
                </select>
            </div>
            <button type="submit" title="Book the bicycle">Book</button>
        </form>
    </div>
    <div class="item">

        <div class="itemPhoto">
            <img src="./Images/sportBicycle.jpg" alt="Sport bicycle">
        </div>
        <div class="itemTitle">
            <h2>Sport bicycle</h2>
        </div>
        <div class="itemDescription">
            <p>Get fitter and enjoy the ride. Sport bicycles are for those who like speed. Still good for city rides as well as for cycling for long distances. The bicycles have up to 20-speed bikes.</p>
        </div>

        <form action="BookingSummary.php" method="POST">
            <div class="itemDroplist">
                <select name="bicycleSelect" id="SpBicycleSelect" title="Select the bicycle" required>
                    <option value="" selected disabled>Available bicycles</option>
                    <?php echo $sportOptions; ?>
                </select>
            </div>
            <button type="submit" title="Book the bicycle">Book</button>
        </form>
    </div>
</div>

<div class="logoButton logout">
    <button><a href="./index.html" title="RentBicycleTecko company">Logout</a></button>
</div>
<script src="./myscripts.js"></script>
</body>
</html>
