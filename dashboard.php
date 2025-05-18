<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

$host = "localhost";
$user = "root";
$password = "";
$db = "account";

// Database connection
$data = mysqli_connect($host, $user, $password, $db);
if ($data === false) {
    die("Connection error");
}

// Retrieve user information from session variables
$email = $_SESSION['email'];
$usertype = $_SESSION['usertype'];

// Fetch total users from the database
$sql_users = "SELECT COUNT(*) AS total_users FROM useraccount";  // Assuming users table exists
$result_users = mysqli_query($data, $sql_users);
$row_users = mysqli_fetch_assoc($result_users);
$total_users = $row_users['total_users'];

// Fetch total bookings from the database
$sql_bookings = "SELECT COUNT(*) AS total_bookings FROM bookings";
$result_bookings = mysqli_query($data, $sql_bookings);
$row_bookings = mysqli_fetch_assoc($result_bookings);
$total_bookings = $row_bookings['total_bookings'];

// Fetch pending bookings
$sql_pending = "SELECT COUNT(*) AS pending_bookings FROM bookings WHERE status = 'pending'";
$result_pending = mysqli_query($data, $sql_pending);
$row_pending = mysqli_fetch_assoc($result_pending);
$pending_bookings = $row_pending['pending_bookings'];

// Fetch accepted bookings
$sql_Accepted = "SELECT COUNT(*) AS accepted_bookings FROM bookings WHERE status = 'Accepted'";
$result_Accepted = mysqli_query($data, $sql_Accepted);
$row_Accepted = mysqli_fetch_assoc($result_Accepted);
$Accepted_bookings = $row_Accepted['accepted_bookings'];

// Fetch total bookings from the database
$sql_concerns = "SELECT COUNT(*) AS total_concerns FROM bookings WHERE concern != '' AND suggestion != '' ";
$result_concerns = mysqli_query($data, $sql_concerns);
$row_concerns = mysqli_fetch_assoc($result_concerns);
$total_concerns = $row_concerns['total_concerns'];

// Query today's bookings
$today = date('Y-m-d');
$sql_today = "SELECT COUNT(*) AS today_bookings FROM bookings WHERE `date` = '$today'";
$result_today = mysqli_query($data, $sql_today);
$row_today = mysqli_fetch_assoc($result_today);
$today_bookings = $row_today['today_bookings'];

// Fetch weekly bookings (Monday to Sunday)
$start_of_week = date('Y-m-d', strtotime('last Monday'));
$end_of_week = date('Y-m-d', strtotime('next Sunday'));
$sql_week = "SELECT COUNT(*) AS weekly_bookings FROM bookings WHERE `date` >= '$start_of_week' AND `date` <= '$end_of_week'";
$result_week = mysqli_query($data, $sql_week);
$row_week = mysqli_fetch_assoc($result_week);
$weekly_bookings = $row_week['weekly_bookings'];

// Fetch monthly bookings
$start_of_month = date('Y-m-01'); // First day of this month
$end_of_month = date('Y-m-t');   // Last day of this month
$sql_month = "SELECT COUNT(*) AS monthly_bookings FROM bookings WHERE `date` >= '$start_of_month' AND `date` <= '$end_of_month'";
$result_month = mysqli_query($data, $sql_month);
$row_month = mysqli_fetch_assoc($result_month);
$monthly_bookings = $row_month['monthly_bookings'];
?>
<!DOCTYPE html>
<html>
<head><title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="JS/function.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
    /* Reset some basic styles */
    body, h1, h2, h3, h4, p {
        margin: 0;
        padding: 0;
        font-family: 'Poppins', sans-serif;
        color: #333;
    }
    /* Body styling */
    body {
        padding-top: 60px;
        line-height: 1.6;
        background-color: #f4f4f4;
    }
    /* Navbar styling */
    nav {
        position: fixed;
        top: 0;
        width: 100%;
        background-color: white;
        border-bottom: 0px solid #444;
        z-index: 1000;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 30px;
        box-shadow: 0px 2px 10px #888888;
    }
    nav .nav-left {
        display: flex;
        align-items: center;
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
        position: relative;
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
    /* Button styling for Register and Login */
    .container {
        display: flex;
        position: absolute; /* Position it in relation to the navbar */
        top: 10px; /* Space from the top */
        right: 30px; /* Adjust horizontal spacing from the right */
    }
    .container .btn {
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1em;
        margin-left: 10px;
        transition: background-color 0.3s;
    }
    .container .btn:hover {
        background-color: #45a049;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }
    th {
        background-color: #f0f0f0;
    }
    td button {
        background-color: #333;
        color: white;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
    }
    td button:hover {
        background-color: #444;
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
        background-color: lightgreen;  /* Slightly different background for the profile section */
        border-radius: 15px;
        margin-left: 10px;
        margin-right: 10px;
    }
    .user-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-right: 10px;
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
        text-align: left;  /* Align the text to the left */
    }
    .menu ul li a:hover {
        color: #ff6347;
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
    /* Content Styling */
    .content {
        max-width: 1200px;
        height: 1000px;
        margin: 20px auto;
        padding: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        gap: 20px;
        margin-top: 30px;
    }
    .content h1 {
        animation: fadeInSlide 3s ease-in-out;
    }
    
    /* Dashboard Stats */
    .dashboard-stats {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 20px;
    }
    .stat-box {
        background-color: #e7f9e7;
        border: 2px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        flex: 1 1 calc(30% - 20px);
        transition: transform 0.3s, background-color 0.3s;
        animation: slideInRight 1.5s ease-out forwards;
    }
    .stat-box:hover {
        background-color: #d4f0d4;
        transform: translateY(-5px);
    }
    .stat-box h3 {
        font-size: 1.5em;
        margin-bottom: 10px;
    }
    .stat-box p {
        font-size: 2em;
        font-weight: bold;
        color: #333;
    }
    /* Chart Container */
    .chart-container {
        height: 500px;
        padding: 20px;
        border: 0px solid lightgreen;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, background-color 0.3s;
    }
    .chart-container h2 {
        font-size: 2em;
        text-align: center;
        margin-top: -20px;
    }
    canvas {
        margin-bottom: 20px;
        max-width: 100%;
        height: auto;
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
                <img src="IMAGES/logo.jpg" alt="User Avatar" class="user-avatar">
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
                    <li><a href="dashboard.php">Admin Dashboard</a></li>
                    <li><a href="adminprofile.php">User</a></li>
                    <li><a href="adminbooking.php">Bookings</a></li>
                    <li><a href="logout.php">Log Out</a></li>
            </div>
        </nav>
        <div class="content">
            <h1 style="text-align:center; margin-bottom: 5px;">Dashboard Overview</h1>
            <div class="dashboard-stats">
                    <div class="stat-box">
                        <h3>Total Users</h3>
                        <p><?php echo $total_users; ?></p>
                    </div>
                    <div class="stat-box">
                        <h3>Total Bookings</h3>
                        <p><?php echo $total_bookings; ?></p>
                    </div>
                    <div class="stat-box">
                        <h3>Pending Bookings</h3>
                        <p><?php echo $total_bookings; ?></p>
                    </div>
                    <div class="stat-box">
                        <h3>Accepted Bookings</h3>
                        <p><?php echo $Accepted_bookings; ?></p>
                    </div>
                    <div class="stat-box">
                        <h3>Concerns and Suggestions</h3>
                        <p><?php echo $total_concerns; ?></p>
                    </div>
                </div>
                <br>
            <div class="chart-container">
                <h2>Bookings</h2>
                    <canvas id="bookingsChart"></canvas>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('hamburgerMenu').addEventListener('click', function () {
          this.classList.toggle('active');
          const menu = document.getElementById('menu');
          menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        });
    // Function to get the current date and generate the dates for this week (Monday to Sunday)
    function getWeekDates() {
      const currentDate = new Date();
      const currentDay = currentDate.getDay();  // 0 (Sunday) to 6 (Saturday)
      const daysInWeek = 7;
      // Days of the week
      const daysOfWeek = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
      // Calculate the difference to the previous Monday (or today if today is Monday)
      const diffToMonday = currentDay === 0 ? -6 : 1 - currentDay; // 1 (Monday)
      const weekDates = [];
      for (let i = 0; i < daysInWeek; i++) {
        const date = new Date(currentDate);
        date.setDate(currentDate.getDate() + diffToMonday + i);
        const dateString = date.toISOString().split('T')[0];  // YYYY-MM-DD format
        const dayName = daysOfWeek[date.getDay()]; // Get the day name (Monday, Tuesday, etc.)
        weekDates.push(`${dateString} (${dayName})`);
      }
      return weekDates;
    }
    // Mock Data for Bookings (Replace this with dynamic data from your database)
    const bookingsData = [
      { bookings: <?php echo $total_bookings; ?> },  // Monday
      { bookings: 0 },  // Tuesday
      { bookings: 0 },  // Wednesday
      { bookings: 0 },  // Thursday
      { bookings: 0 },  // Friday
      { bookings: 0 },  // Saturday
      { bookings: 0 }   // Sunday (No bookings for demo)
    ];
    // Generate the labels (dates and days) for this week
    const weekDates = getWeekDates();
    const bookings = bookingsData.map(item => item.bookings);
    // Create the chart
    const ctx = document.getElementById('bookingsChart').getContext('2d');
    const bookingsChart = new Chart(ctx, {
      type: 'bar',  // Chart type (bar chart)
      data: {
        labels: weekDates,  // X-axis labels (combined date and day)
        datasets: [{
          label: 'Bookings',
          data: bookings,  // Y-axis data (number of bookings)
          backgroundColor: 'rgba(50, 205, 50, 0.5)',
          borderColor: 'rgba(50, 205, 50, 1)',
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true  // Start the y-axis at 0
          }
        },
        responsive: true,
        maintainAspectRatio: false
      }
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