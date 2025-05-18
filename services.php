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
<html>
  <head><title>Services</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="JS/function.js"></script>
  <style>
  body {
    font-family: 'Poppins', sans-serif;
  }
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
    padding: 10px 30px;
    box-shadow: 0px 2px 10px #888888;
  }
  nav .nav-left {
    display: flex;
    align-items: center;
  }
  .nav-left {
    color: black;
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
  nav a img {
    margin-left: 40px;
  }
  /* Button styling for Register and Login */
  .container {
    display: flex;
    position: absolute; /* Position it in relation to the navbar */
    top: 15px; /* Space from the top */
    right: 250px; /* Adjust horizontal spacing from the right */
    transition: transform 0.5s, box-shadow 0.3s;
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
    transition: transform 0.5s, box-shadow 0.3s;
  }
  .container .btn:hover {
    background-color: #45a049; 
  }
  button:hover {
    background-color: #ccc;
  }
  /* Body styling */
  body {
    font-family: 'Poppins', sans-serif;
    line-height: 1.6;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
  }
  /* Content styling */
  .content {
    width: 1752px;
    background: white;
    margin-top: 100px;
    margin-left: 78px;
    margin-bottom: 50px;
  }
  .content h1 {
    animation: fadeInSlide 3s ease-in-out ;
    margin-top: 60px;
    text-align: center;
    text-decoration: none;
    color: black;
    font-family: 'Lucida Sans';
    font-size: 50px;
    font-weight: 1000px;
    margin-top: 15px;
  }
  .content h3 {
    width: 100%;
  }
  .Info {
    border: 3px solid #eee;
    border-radius: 15px;
    padding: 20px;
    margin: 10px 0;
  }
  .Info h3 {
    font-family: Arial, Helvetica, sans-serif;
    margin-bottom: 10px;
    text-align: center;
  }
  footer {
    border: 0px solid black;
    height: 300px; /* Adjust height as needed */
    background-color: rgb(14, 199, 38);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 20px;
  }
  .copyright {
    text-align: center;
    margin-bottom: 10px;
    font-size: 0.9em;
  }
  .services {
    flex: 1px;
    text-align: center;
    position: relative;
    right: 160px;
  }
  .contact {
    flex: 1px;
            text-align: center;
            position: relative;
            right: 210px;
            bottom: 10.5px;
  }
  .about {
    text-align: center;
    position: relative;
    right: 70px
  }
  footer p, footer a {
    margin: 5px;
    padding: 0.5px;
    color: white;
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
  .Info h3, a {
    color: black;
  }
  .conditions {
    display: grid;
    grid-template-columns: repeat(4, 1fr); /* 4 equal columns */
    gap: 15px;
    margin-bottom: 50px;
    margin-left: 40px;
    font-family: Arial, Helvetica, sans-serif
  }
  .conditions h3 {
    margin: 0;
    font-size: 20px;
  }
  footer {
    height: 180px;
    background-color: green;
  }
  .contact {
    bottom: 12px;
  }
  .copyright {
    text-align: center;
    margin-bottom: 10px;
    font-size: 0.9em;
  }
  .about {
    bottom: 1px;
    left: -15px;
  }
    </style>
  </head>
<body>
<!-- Navigation Bar --> 
    <nav>
      <div class="nav-left">
      <div class="hamburger-menu" id="hamburgerMenu">
      <div class="line"></div>
      <div class="line"></div>
      <div class="line"></div>
    </div>
  <div id="menu" class="menu">
    <button id="closeMenu" class="close-btn">X</button>
     <!-- User Profile in the Menu -->
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
          <!-- Login and Register -->        
        <div class="container">
        <?php if (!isset($_SESSION['user_id'])): ?> <!-- Check if user is not logged in -->
                <button class="btn" onclick="showLogin()">Login</button>
                <button class="btn" onclick="showRegister()">Register</button>
            <?php else: ?> <!-- If logged in, hide the buttons -->
                
            <?php endif; ?>
        </div>
    </nav>
      <!-- Info about the company and the benefits -->           
        <div class="content">
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
              </div>
            
            </div>
          </div>
        <!-- References & Copyrights -->
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
          <p>📞Ms. Lhey     : 09258543916 📞Ms. Archyl   : 09770076818</p>
          <p>📞Ms. Dencie   : 09255510644 📞Mr. Denver   : 09271423805</p>
          <p>📞Ms. Reyna    : 09774174556 📞Mr. Vaughn   : 09772361189</p>
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