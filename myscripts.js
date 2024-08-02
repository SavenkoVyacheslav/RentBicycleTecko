document.addEventListener('DOMContentLoaded', function() {
    // Change the contrast
    function darkTheme() { 
        document.body.classList.toggle("dark-theme");
    }
    let darkThemeButton = document.querySelector(".accessibility");
    if (darkThemeButton) { darkThemeButton.onclick = darkTheme; }

    // Index html nav menu
    let smallMenu = document.querySelector(".hambMenu"); 
    if (smallMenu) {
        smallMenu.onclick = function() {
            let bigMenu = document.querySelector(".navigationMenu"); 
            if (bigMenu) {
                bigMenu.classList.toggle("active");
            }
        }
    }

    function openPopup() {
        document.querySelector('.popupContainer').style.display = 'flex';
        document.getElementById('popup').style.display = 'block';
    } 

    function closePopup() {
        document.querySelector('.popupContainer').style.display = 'none';
        document.getElementById('popup').style.display = 'none';
    }

    function sendConfirmationEmail(userName, userEmail, bicycleType, brand, bookingDate, returnDate, bicycle_id) {
    console.log("Sending email with data:", {userName, userEmail, bicycleType, brand, bookingDate, returnDate, bicycle_id});
    return emailjs.send('service_6ejq08g', 'template_itunn2o', {
        to_name: userName,
        to_email: userEmail,
        bicycle_type: bicycleType,
        bicycle_brand: brand,
        booking_date: bookingDate,
        return_date: returnDate,
        bicycle_id: bicycle_id
    });
}

    window.confirmBooking = function() {
        // Show popup immediately
        openPopup();
    };

    // Attach actions to the OK button
    let okButton = document.querySelector("#popup button");
    if (okButton) {
        okButton.onclick = function() {
    const userName = document.getElementById('userName').value;
    const userEmail = document.getElementById('userEmail').value;
    const bicycleType = document.getElementById('bicycleType').value;
    const brand = document.getElementById('brand').value;
    const bookingDate = document.getElementById('bookingDate').value;
    const returnDate = document.getElementById('returnDate').value;
    const bicycle_id = document.querySelector('input[name="bicycleSelect"]').value;

    console.log("Data being sent:", {userName, userEmail, bicycleType, brand, bookingDate, returnDate, bicycle_id});

    sendConfirmationEmail(userName, userEmail, bicycleType, brand, bookingDate, returnDate, bicycle_id)
        .then((response) => {
            console.log('EMAIL SENT SUCCESSFULLY', response.status, response.text);
            return document.getElementById('bookingForm').submit();
        })
        .catch((error) => {
            console.error('FAILED TO SEND EMAIL', error);
            alert('Failed to send confirmation email, but your booking will still be processed.');
            return document.getElementById('bookingForm').submit();
        })
        .finally(() => {
            closePopup();
            window.location.href = './index.html';
        });
};
    }
});