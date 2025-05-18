<?php
session_start();
include('database.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data, including profile picture
$sql_user = "SELECT email, name, age, contact, usertype, profile_picture FROM useraccount WHERE id = '$user_id'";
$result_user = mysqli_query($conn, $sql_user);

if ($result_user && mysqli_num_rows($result_user) > 0) {
    $user = mysqli_fetch_assoc($result_user);
    $email = $user['email'];
    $name = $user['name'];
    $age = $user['age'];
    $contact = $user['contact'];
    $usertype = $user['usertype'];
    $profile_picture = $user['profile_picture'];

    // Set a default profile picture if none is found in the database
    if (empty($profile_picture)) {
        $profile_picture = 'IMAGES/logo.jpg'; // Or any default image path you prefer
    }
} else {
    $email = $name = $age = $contact = $usertype = '';
    $profile_picture = 'IMAGES/logo.jpg'; // Default for user not found as well
}

$sql = "SELECT * FROM bookings WHERE user_id = '$user_id' ORDER BY date DESC, time DESC";
$result = mysqli_query($conn, $sql);
$bookings = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile View</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Reset some basic styles */
        body, h1, h2, h3, h4, p {
            margin: 0;
        }

        /* Content styling */
        .content {
            border: 1px solid white;
            border-radius: 5px;
            padding: 20px;
            margin: 20px auto;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
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
            color: black;
            text-decoration: none;
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

        /* Body styling */
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
            padding-top: 60px; /* To prevent content from being hidden behind navbar */
        }

        /* Profile styling */
        .profile-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
        }

        .profile-container h1 {
            font-size: 28px;
            margin-bottom: 20px;
        }

        .profile-picture {
             width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid #ff6347;
            margin-bottom: 50px;
            cursor: pointer;
            transition: transform 0.3s ease;
            object-fit: cover; /* Ensure the image covers the circle */
        }

        .profile-picture:hover {
            transform: scale(1.1);
        }

        .profile-details {
            display: grid;
            grid-template-columns: 1fr 2fr; /* Two columns: label (1fr) and value (2fr) */
            gap: 10px; /* Adds space between the columns */
            margin-bottom: 20px;
            margin-left: 65px;
        }

        .profile-details span {
            display: block;
            font-size: 18px;
            margin: 10px 0;
            color: #666;
        }

        .profile-details label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }

        .profile-details input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            transition: border-color 0.3s ease;
        }

        .profile-details input:focus {
            border-color: #ff6347;
            outline: none;
        }

        .button-container {
            text-align: center;
        }

        .button-container button {
            background-color: #ff6347;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .button-container a:hover {
            background-color: #e0533f;
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

        /* Table styling for bookings */
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 3px;
            border: 1px solid #ddd;
            text-align: center;
        }

        table th {
            background-color: #f2f2f2;
        }

        table td {
            background-color: #f9f9f9;
        }

        table tr:nth-child(even) td {
            background-color: #fafafa;
        }

        nav a img {
            margin-left: 40px;
        }

         /* Edit Profile Form Styling */
        #editProfileForm {
            display: none; /* Initially hidden */
            position: fixed; /* Absolute positioning to avoid shifting text */
            top: 100px; /* Adjust based on where you want the form to appear */
            left: 50%; /* Center the form */
            transform: translateX(-50%); /* Centering fix */
            padding: 30px;
            width: 400px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            z-index: 1000; /* Ensure it stays on top of other content */
        }

        /* Styling for the profile details container */
        #editProfileForm .profile-details1 {
            display: flex;
            flex-direction: column;
            gap: 15px; /* Adds space between form fields */
            margin: 10px 0;
        }

        /* Styling for each form field (input + label) */
        #editProfileForm .detail1 {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-right: 20px;
        }

        /* Label styling */
        #editProfileForm .detail1 label {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
            font-size: 14px;
        }

        /* Input field styling */
        #editProfileForm .detail1 input {
            width: 100%;
            padding: 12px; /* Increased padding for better readability */
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            margin-bottom: 15px; /* Adds margin to the bottom of inputs */
            margin-right: 10px;
        }

        /* Focus state for input fields */
        #editProfileForm .detail1 input:focus {
            border-color: #ff6347;
            outline: none;
        }

        /* Button styling for Save and Cancel */
        #editProfileForm .button-container button {
            background-color: #ff6347;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        #editProfileForm .button-container button:hover {
            background-color: #e0533f;
        }

        /* Cancel button specific styling */
        #editProfileForm .button-container button#cancelEditBtn {
            background-color: #ccc;
            margin-top: 10px;
        }

        #editProfileForm .button-container button#cancelEditBtn:hover {
            background-color: #999;
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
            <li><a href="dashboard.php">Admin Dashboard</a></li>
            <li><a href="adminprofile.php">User</a></li>
            <li><a href="adminbooking.php">Bookings</a></li>
            <li><a href="logout.php">Log Out</a></li>
        </div>
    </nav>
    <div class="content">
        <div class="profile-container">
            <h1>Welcome Admin!</h1>
            <div class="profile-details">
                <div class="detail">
                    <label for="editEmail">Email</label>
                    <span id="email"><?php echo htmlspecialchars($email); ?></span>
                </div>
                <div class="detail">
                    <label for="editName">Name</label>
                    <span id="name"><?php echo htmlspecialchars($name); ?></span>
                </div>
                <div class="detail">
                    <label for="editAge">Age</label>
                    <span id="age"><?php echo htmlspecialchars($age); ?></span>
                </div>
                <div class="detail">
                <label for="editContact">Contact Information</label>
                    <span><?php echo htmlspecialchars($contact); ?></span>
                </div>
                <div class="button-container">
        <button id="editProfileBtn">Edit Profile</button>
      </div><br><br>
      <div id="editProfileForm" style="display: none;">
        <h2>Edit Profile</h2>
        <form id="profileEdit" method="POST" action="update_profile.php">
          <div class="profile-details1">
            <div class="detail1">
              <label for="editEmail">Email</label>
              <input type="email" id="editEmail" name="email" value="<?php echo htmlspecialchars($email); ?>" required autocomplete="email">
            </div>
            <div class="detail1">
              <label for="editName">Name</label>
              <input type="text" id="editName" name="name" value="<?php echo htmlspecialchars($name); ?>" required autocomplete="name">
            </div>
            <div class="detail1">
              <label for="editAge">Age</label>
              <input type="number" id="editAge" name="age" value="<?php echo htmlspecialchars($age); ?>" required autocomplete="age">
            </div>
            <div class="detail1">
              <label for="editContact">Contact Information</label>
              <input type="text" id="editContact" name="contact" value="<?php echo htmlspecialchars($contact); ?>" required autocomplete="tel">
            </div>
          </div>
          <div class="button-container">
            <button type="submit">Save Changes</button>
            <button type="button" id="cancelEditBtn">Cancel</button>
          </div>
        </form>
      </div>
            </div>
            <br><br>
            <h2>Your Bookings</h2>
            <?php if (empty($bookings)): ?>
                <p>You have no bookings yet.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Time</th>
                            <th>Date</th>
                            <th>Massage</th>
                            <th>Branch</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['age']); ?></td>
                            <td><?php echo htmlspecialchars($booking['time']); ?></td>
                            <td><?php echo htmlspecialchars($booking['date']); ?></td>
                            <td><?php echo htmlspecialchars($booking['massage_type']); ?></td>
                            <td><?php echo htmlspecialchars($booking['branch']); ?></td>
                            <td><?php echo htmlspecialchars($booking['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                </table>
            <?php endif; ?>
        </div><br>
    <script>
        document.getElementById('editProfileBtn').addEventListener('click', function() {
            document.getElementById('editProfileForm').style.display = 'block';
        });

        document.getElementById('cancelEditBtn').addEventListener('click', function() {
            document.getElementById('editProfileForm').style.display = 'none';
        });
        // Restrict input for the "Age" field to numbers only
        document.getElementById('editAge').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, ''); // Allow only numbers
        });
        // Restrict input for the "Contact" field to numbers only
        document.getElementById('editContact').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, ''); // Allow only numbers
        });
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
            window.location.href = 'adminprofile.php'; // Change this URL to the actual profile page
        });
        // Function to preview the uploaded image
        function previewImage(event) {
            const file = event.target.files[0];
            const reader = new FileReader();
            reader.onload = function() {
            const image = document.getElementById('profile-picture');
            image.src = reader.result;
            }
            if (file) {
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>