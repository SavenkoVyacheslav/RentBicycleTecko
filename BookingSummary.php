<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['userName']) || !isset($_POST['bicycleSelect'])) {
    header("Location: Booking.php");
    exit();
}

$bicycleId = $_POST['bicycleSelect'];
$userName = $_SESSION['userName'];

// Fetch customer ID and email
$stmt = $conn->prepare("SELECT c.customer_id, c.email FROM account a JOIN customer c ON customer_id = c.customer_id WHERE a.user_name = ?");
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $userName);
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if ($user) {
    $customerId = $user['customer_id'];
    $userEmail = $user['email'];
} else {
    die("No user found with the given username.");
}
$stmt->close();

// Fetch bicycle details
$stmt = $conn->prepare("
    SELECT
        CASE
            WHEN b.cl_bicycle IS NOT NULL THEN 'Classic'
            WHEN b.el_bicycle IS NOT NULL THEN 'Electrical'
            WHEN b.sp_bicycle IS NOT NULL THEN 'Sport'
        END AS bicycle_type,
        COALESCE(c.class_b_brand, e.el_b_brand, s.sport_b_brand) AS brand,
        COALESCE(c.class_b_frame, e.el_b_frame, s.sport_b_frame) AS frame,
        COALESCE(c.class_b_colour, e.el_b_colour, s.sport_b_colour) AS colour
    FROM Bicycle_list b
    LEFT JOIN Classic_bicycle c ON b.cl_bicycle = c.classic_bicycle_id
    LEFT JOIN Electrical_bicycle e ON b.el_bicycle = e.elect_bicycle_id
    LEFT JOIN Sport_bicycle s ON b.sp_bicycle = s.sport_bicycle_id
    WHERE b.bicycle_idn = ?
");
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $bicycleId);
$stmt->execute();
$result = $stmt->get_result();
$bicycleDetails = $result->fetch_assoc();
$stmt->close();

// Set booking and return dates
$bookingDate = date('Y-m-d');
$returnDate = date('Y-m-d', strtotime('+1 day'));

// Insert booking details (only when "Confirm" is clicked)
if (isset($_POST['confirm'])) {
    $stmt = $conn->prepare("INSERT INTO Booking (made_by, made_on, return_date, bicycle_booked) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("issi", $customerId, $bookingDate, $returnDate, $bicycleId);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Summary</title>
    <link rel="stylesheet" href="./style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital@1&display=swap" rel="stylesheet">
       <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
<script type="text/javascript">
  (function() {
    emailjs.init('GzHRJKCyH8zV-Acpj');
  })();
</script>
<script src="./myscripts.js"></script>
</head>
<body>
    <div class="logoButton">
        <button><a href="./index.html" title="RentBicycleTecko company">RentBicycleTecko</a></button>
        <button class="accessibility" title="Change contrast" onclick="darkTheme()">
            <img src="./Images/brightness.png" alt="contrast">
        </button>
    </div>
    <div class="pageHeader"><h1>Complete your booking</h1></div>
    <div class="itemsContainer">
        <div class="item">
            <div class="itemPhoto">
                <img src="./Images/HappyBicycle.jpg" alt="Bicycle">
            </div>
            <div class="bookingDate">
                <p>Booking <?php echo $bookingDate; ?> Return <?php echo $returnDate; ?></p>
            </div>
            <div class="itemTitle">
                <h2><?php echo htmlspecialchars($bicycleDetails['bicycle_type'] . ' Bicycle'); ?></h2>
                <p>Brand: <?php echo htmlspecialchars($bicycleDetails['brand']); ?></p>
                <p>Frame: <?php echo htmlspecialchars($bicycleDetails['frame']); ?></p>
                <p>Color: <?php echo htmlspecialchars($bicycleDetails['colour']); ?></p>
            </div>
<div class="bookingButtons">
        <form action="BookingSummary.php" method="POST" id="bookingForm">
            <input type="hidden" name="bicycleSelect" value="<?php echo htmlspecialchars($bicycleId); ?>">
            <input type="hidden" name="confirm" value="1"> 
<input type="hidden" id="userName" value="<?php echo htmlspecialchars($userName); ?>">
<input type="hidden" id="userEmail" value="<?php echo htmlspecialchars($userEmail); ?>">
<input type="hidden" id="bicycleType" value="<?php echo htmlspecialchars($bicycleDetails['bicycle_type']); ?>">
<input type="hidden" id="brand" value="<?php echo htmlspecialchars($bicycleDetails['brand']); ?>">
<input type="hidden" id="bookingDate" value="<?php echo htmlspecialchars($bookingDate); ?>">
<input type="hidden" id="returnDate" value="<?php echo htmlspecialchars($returnDate); ?>">
<input type="hidden" id="bicycleId" value="<?php echo htmlspecialchars($bicycleId); ?>">
            <button type="button" onclick="window.confirmBooking()" title="Confirm the booking">Confirm</button>
        </form>
        <button><a href="./Booking.php" title="Cancel the booking">Cancel</a></button>
    </div>
        </div>
    </div>

    <!--Successfull booking popup-->
    <div class="popupContainer" style="display: none;">
    <div class="popup" id="popup">
        <img src="./Images/green tick.jpg" alt="tick">
        <h2>Your booking is successfully completed!</h2>
        <button type="button" title="Return to the Home page">OK</button>
    </div>
</div>
</body>
</html>