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
  <head><title>About Us</title>
    <link rel="stylesheet" href="CSS/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="JS/function.js"></script>
  <style>
  body {
    font-family: 'Poppins', sans-serif;
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
  .content p, h1 {
    transition: transform 0.5s, box-shadow 0.3s;
  }
  .content p:hover {
    transform: translateX(20px);
  }
  .content h1:hover {
    transform: translateX(50px);
  }
  .content img {
    float: left;
    margin-left: 10px;
    border: 0px solid black;
    border-radius: 20px;
    width: 400px;
    margin-right: 10px;
    margin-top: 50px;
    height: 300px;  /* Allow box height to adjust based on content */
    overflow: hidden;  /* Hide overflow to keep the image within the box */
  }
  .content img:hover {
    transform: translateY(20px);
  }
  .content a {
    color: black;
  }
  .content a:hover {
    color: green;
  }
  .text-container {
    margin-left: 20px;
  }
  .text-container2 {
    margin-right: 20px;
  }
  .boxtext {
    border: 1px solid white;
    border-radius: 10px;
    margin: 30px 0;
    box-shadow: 0 4px 80px rgba(0, 0, 0, 0.1);
  }
  .box-text img {
    border-radius: 10px;
  }
  footer {
    height: 180px;
    background-color: green;
  }
  /* Add hover effect to footer links */
  footer a:hover {
    color: black; /* Change color to gray on hover */
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
    bottom: 1px;
    left: -15px;
  }
  /* Button styling for Register and Login */
  .container {
    display: flex;
    position: absolute; /* Position it in relation to the navbar */
    top: 15px; /* Space from the top */
    left: 1696px; /* Adjust horizontal spacing from the right */
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
        <div class="container">
        <?php if (!isset($_SESSION['user_id'])): ?> <!-- Check if user is not logged in -->
                <button class="btn" onclick="showLogin()">Login</button>
                <button class="btn" onclick="showRegister()">Register</button>
            <?php else: ?> <!-- If logged in, hide the buttons -->
                
            <?php endif; ?>
        </div>
    </nav>      
          <div class="content">
            <hr>
            <div class="boxtext">
            <a style="text-decoration: none;"  href="https://www.facebook.com/biomekaniks"><h1>What is HEALot Biomekaniks?</h1></a>
            <p>HEALot Biomekaniks is a 100% Filipino technology best relief for Vertigo, Migraine, Deafness, Neck pain, Stiff neck, Shoulder pain, Frozen shoulder, Chest pain, Asthma, Difficulty in Breathing, Back pain, Sciatica, Leg pain, Knee pain and lot more! HEALot BIOMEKANIKS is a complementary healthcare provider recognized by Philippine Institute of Traditional and Alternative Health Care (PITAHC) which is attached to the Department of Health (DOH). HEALot BIOMEKANIKS practitioners are the first and currently the only ones certified by the said agency for this modality. </p><br><br>
            </div>
            <div class="boxtext">
            <h1>The Technology</h1><br>
            <p>Biomechanics is the science of movement of a living body, including how muscles, bones, tendons, and ligaments work together to produce movement. <strong>HEALot BIOMEKANIKS</strong> is a 100% Filipino technology developed by an Engineer which took about 45 years of R&D and experience. The technology is based on the study of human anatomy with the application of engineering principles, common sense and logic.</p><br><br><br>
            </div>
            <div class="boxtext">
            <h1>The Practice</h1><br>
            <p class="text-container">HEALot BIOMEKANIKS is the safest method of realigning the skeletal and muscular system of the human body without resorting to interventions. Manipulations by hand at the proper angle and natural positioning of the body is the key to a safe realignment. Dynamics of movement is greatly emphasized as a natural reference point at which limits and range of movement are observed. </p><br><br><br>
            </div>
            <br><br><br><br>
            <div class="boxtext">
            <img src="IMAGES/logo.jpg">
            <h1>Our Logo</h1><br>
            <p class="text-container2">Upong BIOMEKANIKS (Asian Squat) since time immemorial, has been practiced by our ancestors as a way to relax without a chair, and without them knowing, it is the natural way of strengthening lower body muscles, developing lower body mobility, relieving lower back and knee joint pains, and improves overall posture. The position protects the knees and the spine which are the pillars and foundation of our body.</p><br><br><br><br><br><br>
            </div>
            <div class="boxtext">
            <h1>For more inquiries:</h1><br>
            <p class="text-container">Visit our facebook page at: <strong><a href="https://www.facebook.com/biomekaniks">Maning Gomez (Healot Biomekaniks)</a></strong>, an Alternative & Holistic Health Service. HEALot Biomekaniks is a 100% Filipino technology best relief for Vertigo, Migraine, Deafness, Neck pain, Stiff neck, Shoulder pain, Frozen shoulder, Chest pain, Asthma, Difficulty in Breathing, Back pain, Sciatica, Leg pain, Knee pain and lot more!</p><br><br><br><br><br><br>
            </div>
            <div class="boxtext">
            <h1>For more inquiries:</h1><br>
            <p class="text-container">Watch our youtube channel at: <strong><a href="https://www.youtube.com/@HEALotBIOMEKANIKSOfficial/featured">Maning Gomez (HEALotBIOMEKANIKSOfficial)</a></strong>, a Licensed Healot Practitioner. HEALot BIOMEKANIKS is under (DOH) Department of Health and (PITAHC) Philippine Institute of Traditional and Alternative Health Care -100% Filipino technolgy best relief for:
            • Migraine • Vertigo • Deafness • Asthma • Back Pain • Bulging Eyes • Stiff Neck • Shoulder Pain • Chest Pain • Scoliosis • Slip Disc • Leg Pain • Leg Cramps • Knee Pain • Sprain • Psoriasis • Herniated Disc • Headaches • Sciatica • Whiplash • Fibromyalgia • TMJ • Carpal Tunnel • Radiculopathy • Osteoarthritis •  Golfer's / Tennis Elbow • Sinusitis • Piriformis Syndrome • Difficulty in Breathing • Degenerative Joint Disease • Dislocated Shoulder • Rheumatoid Arthritis • Degenerative Disc Disease • Spondylolisthesis </p><br><br><br><br><br>
            </div>
            <div class="boxtext">
            <h1>For more inquiries:</h1><br>
            <p class="text-container">Email us for further consultation or any concerns: <strong><a href="https://mail.google.com/mail/u/0/#inbox?compose=new">biomekaniksph@gmail.com</a></strong>.</p><br><br><br><br><br><br><br><br><br><br><br>
            </div>

            <h1> Where to find us? (Venue and Scheduling)</h1>
            <div class="boxtext">
            <p class="text-container"><strong>APALIT:</strong></p>
            <p class="text-container">Address: 38B Toronto St. Highview Hills Subdivision, Sampaloc, Apalit, Pampanga</p>
            <p class="text-container">Ms. Lhey Gomez: 📞 +639258543916</p>
            <p class="text-container">Dencie Soriano: 📞 +639255510644</p>
            <p class="text-container">Ms. Archyl: 📞 +639770076818</p>
            <p class="text-container">Every Sunday to Tuesday - 8:00am to 5:00pm</p><br>
            </div>
            <div class="boxtext">
            <p class="text-container"><strong>MONUMENTO:</strong></p>
            <p class="text-container">Address: 2F #20 OK Plaza Bldg. 284 Samson Road, Victory Liner Compound, Caloocan City</p>
            <p class="text-container">Ms. Archyl: 📞 +639770076818</p>
            <p class="text-container">Mr. Denver: 📞 +639271423805</p>
            <p class="text-container">Ms. Reyna Lademora: 📞 +639774174556</p>
            <p class="text-container">Mr. Julius: 📞 +639772361185</p>
            <p class="text-container">Every Monday to Sunday - 10:00am to 5:00pm</p><br>
            </div>
            <div class="boxtext">
            <p class="text-container"><strong>SILANG CAVITE:</strong></p>
            <p class="text-container">Address: Pneumahaus Tagaytay Farmhills Phase 3 Brgy. Ulat, Silang Cavite</p>
            <p class="text-container">Mr. Denver: 📞 +639271423805</p>
            <p class="text-container">Ms. Reyna Lademora: 📞 +639774174556</p>
            <p class="text-container">Sir Vaughn Santos: 📞 09772361189</p>
            <p class="text-container">Every Other Sunday</p><br>
            </div>
            <div class="boxtext">
            <p class="text-container"><strong>MARKET MARKET:</strong></p>
            <p class="text-container">Address: 3rd Floor Gift Market, Ayala Malls BGC Taguig</p>
            <p class="text-container">Mr. Vaughn: 📞 +09772361189</p>
            <p class="text-container">Ms. Dencie: 📞 +639258543916</p>
            <p class="text-container">Ms. Reyna: 📞 +639774174556</p>
            <p class="text-container">Sunday to Friday - 1:00am to 7:00pm</p>
            <p class="text-container">Every Saturday - 10:00am to 2:00pm</p><br>
            </div>
            <div class="boxtext">
            <p class="text-container"><strong>QUEZON CITY:</strong></p>
            <p class="text-container">PITACH Building, Matapang St. East Avenue, Diliman, Quezon City (Infront of SSS)</p>
            <p class="text-container">Mr. Denver: 📞 +639271423805</p>
            <p class="text-container">Ms. Reyna: 📞 +639174174556</p>
            <p class="text-container">Mr. Julius: 📞 +639772361185</p>
            <p class="text-container">Monday to Sunday - 8:00am to 5:00pm</p><br>
            </div>
            <div class="boxtext">
            <p class="text-container"><strong>ALABANG:</strong></p>
            <p class="text-container">Address: 3rd floor Ayala Malls South Park, South Park District  National Road, Alabang, Muntinlupa City 1770</p>
            <p class="text-container">Mr. Denver: 📞 +639271423805</p>
            <p class="text-container">Ms. Reyna: 📞 +639174174556</p>
            <p class="text-container">Mr. Julius: 📞 +639772361185</p>
            <p class="text-container">Monday to Sunday - 10:00am to 5:00pm</p><br>
            </div>
            <div class="boxtext">
            <p class="text-container"><strong>FOR PROVINCIAL SCHEDULE:</strong></p>
            <p class="text-container">Ms. Archyl: 📞 +639770076818</p>
            <p class="text-container">Call our coordinator for HEALot session  schedule</p>
            </div>
            <h1>"5 squats a day keeps the doctor away!"</h1> 
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
      <p>📞Ms. Lhey   : 09258543916 📞Ms. Archyl   : 09770076818</p>
      <p>📞Ms. Dencie : 09255510644 📞Mr. Denver : 09271423805</p>
      <p>📞Ms. Reyna  : 09774174556 📞Mr. Vaughn  : 09772361189</p>
      <p>📞Mr. Julius : 09772361185</p>
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