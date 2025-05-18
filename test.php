<?php
$host="localhost";
$user="root";
$password="";
$db="account";

$data=mysqli_connect($host,$user,$password,$db);
if($data===false) {
    die("Connection error");
}

session_start();
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HEALot Biomekaniks</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="JS/function.js"></script>
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            line-height: 1.6; /* Improve readability */
        }
        /* Navbar styling */
        nav {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: white;
            border-bottom: 0px solid #444;
            border-radius: 5px;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px; /* Adjust horizontal padding */
            box-shadow: 0px 2px 10px #888888;
        }
        nav .nav-left {
            display: flex;
            align-items: center;
        }
        .nav-left li {
            display: none; /* Hide for smaller screens initially */
            cursor: pointer;
            font-weight: bold;
            margin-right: 15px; /* Adjust spacing */
        }
        .nav-left li:first-child {
            display: inline-block; /* Show the logo/home link */
            margin-right: 20px; /* Adjust spacing */
        }
        .nav-left li a{
            color: black;
            text-decoration: none;
            position: relative;
            margin-left: 0; /* Reset margin for better mobile layout */
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
        nav a img {
            height: 35px; /* Adjust logo size */
        }
        /* Button styling for Register and Login */
        .container {
            display: flex;
            transition: transform 0.5s, box-shadow 0.3s;
        }
        .container .btn {
            background-color: #4CAF50;
            color: white;
            padding: 8px 15px; /* Adjust button padding */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em; /* Adjust font size */
            margin-left: 5px; /* Adjust spacing */
            transition: transform 0.5s, box-shadow 0.3s;
        }
        .container .btn:hover {
            background-color: #45a049;
        }
        /* Hamburger Menu */
        .hamburger-menu {
            width: 30px;
            height: 25px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 15px;
        }
        .line {
            width: 100%;
            height: 3px;
            background-color: lightgreen;
            border-radius: 3px;
            transition: all 0.3s ease;
        }
        .menu {
            display: none;
            background-color: green;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%; /* Cover the whole screen */
            width: 200px; /* Adjust menu width */
            padding-top: 60px;
            box-shadow: 4px 0 6px rgba(0, 0, 0, 0.1);
            z-index: 100;
        }
        /* User Profile Section inside the Menu */
        .user-profile {
            display: flex;
            align-items: center;
            margin-bottom: 15px; /* Space from the top */
            padding: 10px; /* Space around the profile */
            background-color: lightgreen;   /* Slightly different background for the profile section */
            border-radius: 10px;
            margin-left: 10px;
            margin-right: 10px;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover; /* Ensure the image covers the circle */
        }
        .username {
            font-size: 14px;
            font-weight: bold;
            color: black;
        }
        .menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .menu ul li {
            margin: 10px 0;
            text-align: left;
        }
        .menu ul li a {
            margin-bottom: 0;
            margin-left: 20px;
            color: #fff;
            text-decoration: none;
            font-size: 16px;
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
            transform: rotate(45deg) translate(5px, 6px);
        }
        .hamburger-menu.active .line:nth-child(2) {
            opacity: 0;
        }
        .hamburger-menu.active .line:nth-child(3) {
            transform: rotate(-45deg) translate(5px, -6px);
        }
        /* Close button style */
        .close-btn {
            background-color: transparent;
            color: #fff;
            border: none;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .close-btn:hover {
            color: #ff6347;
        }
        /* Content styling */
        .content {
            border: 1px solid white;
            border-radius: 5px;
            padding: 15px; /* Adjust padding */
            margin: 70px auto 20px; /* Adjust top margin to accommodate fixed navbar */
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 960px; /* Limit content width for better readability on larger screens */
        }
        .content h1 {
            text-align: center;
            text-decoration: none;
            color: black;
            font-size: 2em; /* Adjust heading size */
            font-weight: 700; /* Adjust font weight */
            margin-top: 10px;
            margin-bottom: 15px; /* Add some bottom margin */
            transition: transform 0.5s, box-shadow 0.3s;
        }
        .content h1:hover {
            transform: translateX(0); /* Remove hover animation for better mobile experience */
        }
        .content p {
            font-size: 1em; /* Adjust paragraph size */
            margin-bottom: 15px; /* Add spacing between paragraphs */
            transition: transform 0.5s, box-shadow 0.3s;
        }
        .content p:hover {
            transform: translateX(0); /* Remove hover animation for better mobile experience */
        }
        .animated-background {
            border-radius: 10px;
            margin-top: 20px;
            height: 300px; /* Adjust background height */
            background-size: cover; /* Ensure the image covers the entire background */
            background-position: center; /* Center the image */
            animation: BgAnimation 12s linear infinite;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        @keyframes BgAnimation {
            0% {
                background-image: url(IMAGES/testing1.jpg);
            }
            50% {
                background-image: url(IMAGES/testing2.jpg);
            }
            100% {
                background-image: url(IMAGES/testing3.jpg);
            }
        }
        .animated-background:hover {
            transform: translateY(0); /* Remove hover animation */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow on hover */
        }
        .Info, .Benefits, .Book {
            border: 1px solid #eee; /* Lighter border */
            border-radius: 10px; /* Adjust border radius */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05); /* Subtle shadow */
            padding: 15px; /* Adjust padding */
            margin: 15px 0; /* Adjust vertical margin */
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .Info h1 {
            font-size: 1.5em; /* Adjust heading size */
            margin-bottom: 10px;
        }
        .Info h3, .Info a {
            color: black;
            font-size: 1em; /* Adjust font size */
        }
        .Info:hover {
            transform: translateY(0); /* Remove hover animation */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow on hover */
        }
        .conditions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); /* Responsive columns */
            gap: 10px; /* Adjust gap */
            margin-bottom: 30px;
            margin-left: 10px; /* Adjust left margin */
            margin-right: 10px; /* Adjust right margin */
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .conditions h3 {
            margin: 5px 0; /* Adjust vertical margin */
            font-size: 1em; /* Adjust font size */
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .conditions h3 a:hover {
            color: green;
            transform: translateX(0); /* Remove hover animation */
        }
        .Book {
            text-align: center;
            margin: 20px 0;
            font-size: 1em; /* Adjust font size */
        }
        .Book h4 {
            font-size: 1.2em; /* Adjust heading size */
            margin-bottom: 5px;
        }
        .Book button {
            background-color: green;
            color: white;
            border-radius: 7px;
            padding: 8px 15px; /* Adjust button padding */
            border: none;
            cursor: pointer;
            font-size: 1em; /* Adjust font size */
            display: inline-block; /* Make button inline for better centering */
            margin: 10px auto; /* Adjust margins for centering */
            transition: background-color 0.3s;
        }
        .Book button:hover {
            background-color: #45a049;
        }
        .Benefits h2 {
            margin-bottom: 10px;
            text-align: center;
            font-size: 1.3em; /* Adjust heading size */
        }
        .Benefits p {
            font-size: 1em; /* Adjust paragraph size */
            margin-bottom: 10px;
        }
        footer {
            border: 0px solid black;
            padding: 15px 20px; /* Adjust padding */
            background-color: green;
            color: white;
            display: flex;
            flex-direction: column; /* Stack elements on smaller screens */
            align-items: center; /* Center items on smaller screens */
            text-align: center; /* Center text on smaller screens */
        }
        .copyright {
            text-align: center;
            margin-bottom: 10px;
            font-size: 0.8em; /* Adjust font size */
        }
        .services, .contact, .about {
            margin-bottom: 15px; /* Add spacing between sections */
        }
        .services p, .contact p, .about p, footer a {
            margin: 3px 0; /* Adjust vertical margin */
            padding: 0;
            color: white;
            font-size: 0.9em; /* Adjust font size */
            text-decoration: none;
        }
        footer a:hover {
            color: black; /* Change color to gray on hover */
        }

        /* Media query for larger screens (e.g., tablets and desktops) */
        @media (min-width: 768px) {
            nav {
                padding: 15px 40px;
            }
            .nav-left li {
                display: inline;
                margin-right: 25px;
            }
            .hamburger-menu {
                display: none; /* Hide hamburger menu on larger screens */
            }
            .menu {
                display: none !important; /* Ensure menu is hidden */
            }
            .container {
                position: static; /* Adjust container positioning */
                margin-left: auto; /* Push buttons to the right */
            }
            .content {
                padding: 20px;
                margin-top: 80px;
            }
            .content h1 {
                font-size: 3em;
            }
            .content p {
                font-size: 1.2em;
            }
            .animated-background {
                height: 400px;
                margin-top: 30px;
            }
            .conditions {
                grid-template-columns: repeat(4, 1fr); /* Back to 4 columns */
                margin-left: 90px;
            }
            .Book {
                font-size: 1.2em;
            }
            .Book button {
                display: inline-block;
                margin: 20px auto;
                padding: 12px 25px;
                font-size: 1.1em
            }
            footer {
                flex-direction: row; /* Arrange footer items horizontally */
                justify-content: space-between;
                align-items: flex-start;
                text-align: left;
                padding: 20px 40px;
                height: auto; /* Adjust height based on content */
            }
            .copyright {
                margin-bottom: 0;
            }
            .services, .contact, .about {
                margin-bottom: 0;
            }
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
                <li><a href="homepage.php">Home</a></li>
                <li><a href="aboutus.php">About Us</a></li>
                <li><a href="services.php">Our Services</a></li>
                <li><a href="booking.php">Book Now</a></li>
                <li><a href="profile.php">Settings</a></li>
                <li><a href="logout.php">Log Out</a></li>
            </ul>
        </div>
            <a href="#" onclick="location.reload()"><img src="IMAGES/logo.jpg" alt="logo1"></a>
            <li><a href="homepage.php">Home</a></li>
            <li><a href="aboutus.php">About Us</a></li>
            <li><a href="services.php">Our Services</a></li>
            <li><a href="booking.php">Book Now</a></li>
        </div>
        <div class="container">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <button class="btn" onclick="showLogin()">Login</button>
            <button class="btn" onclick="showRegister()">Register</button>
        <?php endif; ?>
        </div>
    </nav>
        <div id="notification" class="notification" style="display: none;"></div>
          <div class="content">
        <div class="animated-background"></div>
        <h1>Healot Biomekaniks</h1>
        <p>Hello! This is HEALot Biomekaniks, a massage company that can help your body to relax and relieve stress. HEALot Biomekaniks is a 100% Filipino technology best relief for Vertigo, Migraine, Deafness, Neck pain, Stiff neck, Shoulder pain, Frozen shoulder, Chest pain, Asthma, Difficulty in Breathing, Back pain, Sciatica, Leg pain, Knee pain, and a lot more!</p>
        <div class="Info">
        <h1>What we can treat:</h1>
        <div class="conditions">
          <h3>Vertigo</h3>
          <h3>Migraine</h3>
          <h3>TMJ</h3>
          <h3>Bulging Eyes</h3>
          <h3>Deafness</h3>
          <h3>Neck Pain</h3>
          <h3>Stiff Neck</h3>
          <h3>Whiplash</h3>
          <h3>Trigeminal Neuralgia</h3>
          <h3>Frozen Shoulder</h3>
          <h3>Shoulder Pain</h3>
          <h3>Carpal Tunnel</h3>
          <h3>Chest Pain</h3>
          <h3>Asthma</h3>
          <h3>Difficulty in Breathing</h3>
          <h3>Back Pain</h3>
          <h3>Sciatica</h3>
          <h3>Polycystic Ovary Syndrome</h3>
          <h3>Hernia</h3>
          <h3>Slip Disc</h3>
          <h3>Joint Disease</h3>
          <h3>Leg Pain</h3>
          <h3>Leg Cramps</h3>
          <h3>Knee Pain</h3>
          <h3>Sprain</h3>
          <h3>Fibromyalgia</h3>
          <h3>Pliformis Syndrome</h3>
          <h3>Rheumatoid</h3>
          <h3>Osteoarthritis</h3>
          <h3><a href="services.php">and a lot more!</a></h3>
        </div>
      </div>
        <div class="Benefits">
          <h2>Benefits of Massages in your body:</h2>
          <p><strong>Pain relief:</strong> Massage can alleviate pain from conditions like chronic back pain, muscle soreness, arthritis, and tension headaches. It can also help reduce the severity and frequency of migraines.</p>
          <p><strong>Muscle relaxation and Tension release:</strong> Massage helps reduce muscle tension, relax tight muscles, and promote flexibility. It can be especially beneficial for people with muscle stiffness from overuse or injury.</p>
          <p><strong>Improved Circulation:</strong> The pressure applied during a massage stimulates blood flow, which can help deliver oxygen and nutrients to tissues and remove waste products from the body. This improved circulation can enhance overall tissue health and promote faster healing.</p>
        </div>
        <div class="Book">
          <h4>Got Interested?</h4>
          <p>Click the button below to schedule your appointment today!</p>
          <a href="booking.php">
            <button>BOOK NOW!</button>
          </a>
        </div>
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
            <p>📞Ms. Lhey     : 09258543916</p>
            <p>📞Ms. Archyl   : 09770076818</p>
            <p>📞Ms. Dencie   : 09255510644</p>
            <p>📞Mr. Denver   : 09271423805</p>
            <p>📞Ms. Reyna     : 09774174556</p>
            <p>📞Mr. Vaughn   : 09772361189</p>
            <p>📞Mr. Julius   : 09772361185</p>
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