<?php
session_start();
include('database.php');

$host = "localhost";
$user = "root";
$password = "";
$db = "account";

$data = mysqli_connect($host, $user, $password, $db);
if ($data === false) {
    die("Connection error: " . mysqli_connect_error());
}
// Assuming user email is stored in session after login
$email = 'Guest'; // Default value
$profile_picture = 'https://via.placeholder.com/50'; // Default placeholder image

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $email = $_SESSION['email'];

    // Fetch user's profile picture from the database
    $sql_profile_pic = "SELECT profile_picture FROM useraccount WHERE id = '$user_id'";
    $result_profile_pic = mysqli_query($data, $sql_profile_pic);

    if ($result_profile_pic && mysqli_num_rows($result_profile_pic) > 0) {
        $row_profile_pic = mysqli_fetch_assoc($result_profile_pic);
        $profile_picture = $row_profile_pic['profile_picture'];
        if (empty($profile_picture)) {
            $profile_picture = 'https://via.placeholder.com/50'; // Use placeholder if no picture in DB
        }
    }
}
// Prevent Guest users from reserving a booking
if ($email == 'Guest') {
    echo "<script>alert('You must be logged in to make a booking.'); window.location.href = 'login.php';</script>";
    exit(); // Exit the script if user is a guest
}
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = mysqli_real_escape_string($data, $_POST['name']);
    $age = $_POST['age'];
    $time = $_POST['time'];  // Time comes in 24-hour format from the form
    $date = $_POST['date'];  // Date comes in YYYY-MM-DD format from the form
    $massage = $_POST['massage'];
    $branch = $_POST['branch'];
    $concern = $_POST['concern'];
    $suggestion = $_POST['suggestion'];
    // Get the logged-in user's ID from the session
    $user_id = $_SESSION['user_id'];
    // Create SQL query to insert the reservation
    $sql = "INSERT INTO bookings (name, age, time, date, massage_type, branch, email, user_id, concern, suggestion)
                VALUES ('$name', '$age', '$time', '$date', '$massage', '$branch', '$email', '$user_id', '$concern', '$suggestion')";
    // Execute the SQL query and check for errors
    if (mysqli_query($data, $sql)) {
        // Redirect after the 10-second delay
        echo "<script>
                    setTimeout(function() {
                        window.location.href = 'booking.php'; // Redirect to the same page
                    } ); // 10000 ms = 10 seconds
                 </script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($data) . "');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Massage Reservation</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/booking.css">
    <script src="JS/function.js"></script>
    <style>
    nav {
        border-radius: 5px;
        box-shadow: 0px 2px 10px #888888;
        padding: 10px 30px;
    }
    body {
        font-family: 'Poppins', sans-serif;
    }
    nav li {
        display: inline;
        cursor: pointer;
        font-weight: bold;
        margin-right: 20px;
    }
    /* Change font color of anchor tags in the nav */
    nav li a {
        margin-left: 40px;
        color: black; /* Set the color of the links to black */
        text-decoration: none; /* Remove underline */
        position: relative; /* Necessary for positioning the pseudo-element */
    }
    nav li a::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: -5px;
        width: 0;
        height: 2px;
        background-color: #ff6347; /* Underline color */
        transition: width 0.3s ease; /* Animation timing */
    }
    nav li a:hover::after {
        width: 100%; /* Full width on hover */
    }
    nav li a:hover {
        color: #ff6347;
        text-decoration: none; /* Ensure text isn't underlined when hovered */
    }
    input, select {
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
        transition: border-color 0.3s;
        display: block;
        margin-bottom: 10px;
        padding: 10px;
        width: 600px;
    }
    .container {
        right: 90px; /* Adjust horizontal spacing from the right */
    }
    /* Sweet Notification Styles */
    .notification {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #4CAF50;
        color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        font-size: 16px;
        width: 400px;
        text-align: left;
        display: none;
        z-index: 1000;
        transition: opacity 0.5s;
    }
    .notification.show {
        display: block;
        opacity: 1;
    }
    .notification.hide {
        opacity: 0;
        display: none;
    }
    /* Close button styles */
    .close-btn {
        background: none;
        border: none;
        color: white;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        position: absolute;
        top: 5px;
        right: 10px;
    }
    .close-btn:hover {
        color: #ff3333;
    }
    /* Remove number input arrows in the browser */
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    nav li a:hover {
    color: #ff6347;
    }
    /* Add hover effect to footer links */
    footer a:hover {
        color: black; /* Change color to gray on hover */
    }
    .hamburger-menu {
        width: 35px;
        height: 30px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .line {
        width: 100%;
        height: 5px;
        background-color: lightgreen;
        border-radius: 5px;
        transition: all 0.3s ease;
    }
    .menu {
        display: none;
        background-color: green;
        position: fixed;
        top: 0;
        left: 0;
        height: 500%;
        width: 250px;
        padding-top: 60px;
        box-shadow: 4px 0 6px rgba(0, 0, 0, 0.1);
        z-index: 100;
    }
    /* User Profile Section inside the Menu */
    .user-profile {
        display: flex;
        align-items: center;
        margin-bottom: 20px; /* Space from the top */
        padding: 10px; /* Space around the profile */
        background-color: lightgreen;   /* Slightly different background for the profile section */
        border-radius: 15px;
        margin-left: 10px;
        margin-right: 10px;
    }
    .user-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-right: 10px;
        object-fit: cover; /* Ensure the image covers the circle */
    }
    .username {
        font-size: 15.5px;
        font-weight: bold;
        color: black;
    }
    .menu ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .menu ul li {
        margin: 15px 0;
        text-align: left;
    }
    .menu ul li a {
        margin-bottom: 10px;
        margin-left: 25px;
        color: #fff;
        text-decoration: none;
        font-size: 18px;
        display: block;
        transition: transform 0.5s, box-shadow 0.3s;
        text-align: left;   /* Align the text to the left */
    }
    .menu ul li a:hover {
        color: #ff6347;
        text-decoration: none; /* Ensure no underline on hover */
    }
    /* Hamburger menu transformation */
    .hamburger-menu.active .line:nth-child(1) {
        transform: rotate(45deg);
        position: relative;
        top: 8px;
    }
    .hamburger-menu.active .line:nth-child(2) {
        opacity: 0;
    }
    .hamburger-menu.active .line:nth-child(3) {
        transform: rotate(-45deg);
        position: relative;
        top: -8px;
    }
    /* Close button style */
    .close-btn {
        background-color: transparent;
        color: #fff;
        border: none;
        font-size: 30px;
        font-weight: bold;
        cursor: pointer;
        position: absolute;
        top: 13px;
        right: 15px;
    }
    .close-btn:hover {
        color: #ff6347;
    }
    footer {
        height: 180px;
        margin-top: 10px;
        border: 0px solid green;
        background-color: green;
    }
    .content1 {
        margin-top: 20px;
    }
    .contact {
        flex: 1px;
            text-align: center;
            position: relative;
            right: 210px;
            bottom: 10.5px;
    }
    .about {
        bottom: 1px;
        left: -14px;
    }
    .services {
        right: 160px;
    }
    form {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: space-between;
    }
    .booking {
        flex: 1 1 45%;
        transition: transform 0.5s, box-shadow 0.3s;
    }
    nav a img {
    margin-left: 40px;
    }
    </style>
</head>
<body>
    <nav>
        <div class="nav-left">
        <div class="hamburger-menu" id="hamburgerMenu">
        <div class="line"></div>
        <div class="line"></div>
        <div class="line"></div>
    </div>
    <div id="menu" class="menu">
    <button id="closeMenu" class="close-btn">X</button>
      <div class="user-profile">
                <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="User Avatar" class="user-avatar">
                <span class="username"><?php echo htmlspecialchars($email); ?></span>
            </div>
        <ul>
            <li><a href="homepage.php">Home</a></li><br>
            <li><a href="aboutus.php">About Us</a></li><br>
            <li><a href="services.php">Our Services</a></li><br>
            <li><a href="booking.php">Book Now</a></li><br>
            <li><a href="logout.php">Log Out</a></li>
        </ul>
    </div>
            <a href="#" onclick="location.reload()"><img src="IMAGES/logo.jpg" alt="logo1" style="height: 40px;"></a>
            <li><a href="homepage.php">Home</a></li>
            <li><a href="aboutus.php">About Us</a></li>
            <li><a href="services.php">Our Services</a></li>
            <li><a href="booking.php">Book Now</a></li>
        </div>
        <div class="container">
        <?php if (!isset($_SESSION['user_id'])): ?> <button class="btn" onclick="showLogin()">Login</button>
                    <button class="btn" onclick="showRegister()">Register</button>
                <?php else: ?> <?php endif; ?>
        </div>
    </nav>
    <div class="content1">
        <h2>Booking Form</h2>
        <form action="" id="booking-form" method="POST" autocomplete="off">
            <div class="booking">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" placeholder="Your name" required>
            </div>
            <div class="booking">
                <label for="age">Age</label>
                <input type="number" name="age" id="age" placeholder="Your age" required>
            </div>
            <div class="booking">
                <label for="time">Time</label>
                <input type="time" name="time" id="time" required>
            </div>
            <div class="booking">
                <label for="date">Date</label>
                <input type="date" name="date" id="date" required>
            </div>
            <div class="booking2">
                <label for="massage">Massage</label>
                <input type="massage" name="massage" id="massage" placeholder="What healot do you need?" required>
            </div>
            <div class="booking2">
                <label>Anything we should know of about your concern?</label>
                <input type="concern" name="concern" id="concern" placeholder="Any concern" required>
            </div>
            <div class="booking2">
                <label for="branch">Which branch</label>
                <select name="branch" id="branch" required>
                    <option value="" disabled selected>Select branch</option>
                    <option value="Alabang">Alabang</option>
                    <option value="Caloocan">Caloocan</option>
                    <option value="Cavite">Cavite</option>
                    <option value="Pampanga">Pampanga</option>
                    <option value="Quezon City">Quezon City</option>
                    <option value="Taguig">Taguig</option>
                </select>
            </div>
            <div class="booking2">
                <label>Any suggestions you want to give?</label>
                <input type="suggestion" name="suggestion" id="suggestion" placeholder="Any suggestions" required>
            </div>
            <div class="booking">
                <button type="submit" value="Book Now">Submit</button>
            </div>
        </form>
    </div>
    <div class="notification" id="notification">
        <button class="close-btn" id="close-btn">&times;</button>
        <strong>Success!</strong> Your reservation has been made successfully.
        <br><strong>Name:</strong> <span id="notif-name"></span>
        <br><strong>Time:</strong> <span id="notif-time"></span>
        <br><strong>Date:</strong> <span id="notif-date"></span>
        <br><strong>Massage Type:</strong> <span id="notif-massage"></span>
        <br><strong>Branch:</strong> <span id="notif-branch"></span>
    </div>
    <footer>
        <div class="services">
            <p><a href="services.php">Our Services:</a></p>
            <p>Vertigo</p>
            <p>Neck Pain</p>
            <p>Migraine</p>
            <p>TMJ</p>
            <p><a href="services.php">Check More:</a></p>
        </div>
        <div class="contact">
            <div class="copyright">
                <p>© 2023 All Rights Reserved. Healot Biomekaniks</p>
            </div>
            <p style="text-decoration: underline;"><a href="aboutus.php">Contact Us:</a></p>
            <p>📞Ms. Lhey: 09258543916 📞Ms. Archyl: 09770076818</p>
            <p>📞Ms. Dencie: 09255510644 📞Mr. Denver: 09271423805</p>
            <p>📞Ms. Reyna: 09774174556 📞Mr. Vaughn: 09772361189</p>
            <p>📞Mr. Julius: 09772361185</p>
        </div>
        <div class="about">
            <p><a href="aboutus.php">About Us:</a></p>
            <p>Email us at: biomekaniksph@gmail.com</p>
            <p>Facebook: Maning Gomez (Healot Biomekaniks)</p>
            <p>Youtube: HEALotBIOMEKANIKSOfficial</p>
            <p>Tiktok: Maning Gomez</p>
            <p>Instagram: Maning Gomez</p>
        </div>
    </footer>
<script>
        // Handle form submission
        document.getElementById("booking-form").addEventListener("submit", function(event) {
            // Get values from the form
            var name = document.getElementById("name").value;
            var time = document.getElementById("time").value;
            var date = document.getElementById("date").value;
            var massage = document.getElementById("massage").value;
            var branch = document.getElementById("branch").value;
            // Populate notification
            document.getElementById("notif-name").innerText = name;
            document.getElementById("notif-time").innerText = time;
            document.getElementById("notif-date").innerText = date;
            document.getElementById("notif-massage").innerText = massage;
            document.getElementById("notif-branch").innerText = branch;
            // Show success notification
            document.getElementById("notification").classList.add("show");
        });
        // Close notification
        document.getElementById("close-btn").addEventListener("click", function() {
            document.getElementById("notification").classList.remove("show");
        });
        // Validate the name field (no numbers allowed)
        document.getElementById("name").addEventListener("input", function() {
            var name = document.getElementById("name").value;
            var notification = document.getElementById("notification");
            // Check for numbers in the name
            if (/\d/.test(name)) {
                notification.innerHTML = "<strong>Error:</strong> Please enter a valid name (no numbers allowed).";
                notification.classList.add("show");
                setTimeout(function() {
                    notification.classList.remove("show");
                }, 1000); // Hide after 1 second
            }
        });
        // Validate the age field (allow only numbers)
        function validateAge() {
            var ageField = document.getElementById("age");
            var age = ageField.value;
            if (isNaN(age)) {
                ageField.setCustomValidity("Please enter a valid age (numbers only).");
            } else {
                ageField.setCustomValidity("");
            }
        }
        document.getElementById('hamburgerMenu').addEventListener('click', function () {
            this.classList.toggle('active');
            const menu = document.getElementById('menu');
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        });
            // Close the menu when the close button is clicked
            document.getElementById('closeMenu').addEventListener('click', function () {
                const menu = document.getElementById('menu');
                menu.style.display = 'none';
                document.getElementById('hamburgerMenu').classList.remove('active'); // Reset the hamburger icon
        });
            // Add click event to the username to redirect
            document.querySelector('.username').addEventListener('click', function() {
                window.location.href = 'profile.php'; // Change this URL to the actual profile page
        });
    </script>
</body>
</html>