<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$db = "account";

$data = mysqli_connect($host, $user, $password, $db);
if ($data === false) {
    die("Connection error");
}

// Retrieve user information from session variables
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$password = isset($_SESSION['password']) ? $_SESSION['password'] : '';
$usertype = isset($_SESSION['usertype']) ? $_SESSION['usertype'] : '';

if (isset($_POST['action']) && $_POST['action'] == 'accept') {
    if (isset($_POST['booking_id'])) {
        $booking_id = intval($_POST['booking_id']);
        $sql = "UPDATE bookings SET status = 'Accepted' WHERE id = $booking_id";
        if (mysqli_query($data, $sql)) {
            echo "<script>
                    document.getElementById('accept-btn-' + " . $booking_id . ").innerHTML = '✔';
                    document.getElementById('accept-btn-' + " . $booking_id . ").disabled = true;
                  </script>";
        } else {
            echo "<script>alert('Error accepting booking');</script>";
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    if (isset($_GET['id'])) {
        $booking_id = intval($_GET['id']);
        $sql = "DELETE FROM bookings WHERE id = $booking_id";
        if (mysqli_query($data, $sql)) {
            echo 'success'; // Return success message to indicate deletion is done
        } else {
            echo 'error'; // Return error message if deletion fails
        }
    }
    exit();
}

// Get the branch parameter from the URL (default to 'All' if not set)
$branch = isset($_GET['branch']) ? $_GET['branch'] : 'All';

// Fetch bookings based on the selected branch (or all bookings if 'All' is selected)
if ($branch == 'All') {
    $sql = "SELECT * FROM bookings";
} else {
    $sql = "SELECT * FROM bookings WHERE branch = '$branch'";
}
$result = mysqli_query($data, $sql);

// Fetch concerns and suggestions for the second table
$concernSql = "SELECT * FROM bookings";
$concernResult = mysqli_query($data, $concernSql);
?>
<!DOCTYPE html>
  <html>
    <head><title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="JS/function.js"></script>
    <style>
  /* Reset some basic styles */
  body, h1, h2, h3, h4, p {
    margin: 0;
    padding: 0;
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
  /* General styles for the dropdown */
  nav li .dropdown-menu {
    display: none;  /* Start with the menu hidden */
    position: absolute;
    background-color: #fff;
    box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
    z-index: 1;
    list-style: none;
    margin: 0;
    padding: 0;
    width: 200px; /* Set a fixed width */
    border-radius: 5px;
  }
  nav li .dropdown-menu li {
    padding: 10px;
  }
  nav li .dropdown-menu li a {
    color: black;
    text-decoration: none;
    display: block;
  }
  .dropdown-menu {
    margin-bottom: 10px;
    margin-left: 25px;
    color: #fff;
    text-decoration: none;
    font-size: 18px;
    display: block;
    text-align: left;  /* Align the text to the left */
    transition: transform 0.5s, box-shadow 0.3s;
  }
  .dropdown-menu li a {
    transition: transform 0.5s, box-shadow 0.3s;
  }
  nav li .dropdown-menu li a:hover {
    border-radius: 10px;
    transform: translateX(5px);
    width: 105px;
    text-decoration: underline;
  }
  /* Ensure the dropdown menu aligns properly */
  nav li .dropdown {
    cursor: pointer;
  }
  /* Show the dropdown when it is toggled */
  .show {
    display: block !important;
  }
  /* Position the dropdown properly */
  nav li {
    position: relative;
  }
  .content {
    max-width: 900px;
    height: 100%;
    margin: 20px auto;
    padding: 20px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  }
  .content h2 {
    animation: fadeInSlide 3s ease-in-out;
  }
  /* Body styling */
  body {
    padding-top: 60px;
    font-family: 'Poppins', sans-serif;
    line-height: 1.6;
    background-color: #f4f4f4;
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
  /* Table Styling */
  table {
    margin-top: 20px;
    width: 100%;
    border-collapse: collapse;
  }
  th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
    font-size: 15px;
  }
  th {
    background-color: #4CAF50;
    color: white;
    font-size: 16px;
  }
  td button {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 8px 15px;
    cursor: pointer;
    font-size: 14px;
    border-radius: 5px;
    transition: transform 0.3s, background-color 0.3s;
  }
  td button:hover {
    background-color: #45a049;
    transform: translateX(10px);
  }
  nav a img {
    margin-left: 40px;
  }
  .reservation-table {
    border: 0px white solid;
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
      <li><a href="#" class="dropdown" id="bookingDropdown">Bookings</a>
        <ul class="dropdown-menu" id="dropdownMenu">
      <li><a href="adminbooking.php?branch=All">All</a></li>
      <li><a href="adminbooking.php?branch=Quezon City">Quezon City</a></li>
      <li><a href="adminbooking.php?branch=Alabang">Alabang</a></li>
      <li><a href="adminbooking.php?branch=Taguig">Taguig</a></li>
      <li><a href="adminbooking.php?branch=Caloocan">Caloocan</a></li>
      <li><a href="adminbooking.php?branch=Pampanga">Pampanga</a></li>
      <li><a href="adminbooking.php?branch=Cavite">Cavite</a></li>
        </ul>
      </li>
      <li><a href="logout.php">Log Out</a></li>
    </div>
  </nav>
    <div class="content">
      <h2 style="text-align:center; margin-bottom: 5px;">Admin Dashboard - Bookings</h2>
        <table id="reservation-table">
          <thead>
            <tr>
              <th>User</th>
              <th>Age</th>
              <th>Time</th>
              <th>Date</th>
              <th>Massage</th>
              <th>Branch</th>
              <th>Pendings</th>
              <th>Pendings</th>
            </tr>
          </thead>
        <tbody>
          <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr id='row-" . $row['id'] . "'>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['age']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['time']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['massage_type']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['branch']) . "</td>";
                    if ($row['status'] == 'Accepted') {
                        echo "<td><button class='accept-btn' id='accept-btn-" . $row['id'] . "' disabled>✔</button></td>";
                    } else {
                        echo "<td><form method='POST' style='display:inline;'>
                                <input type='hidden' name='booking_id' value='" . $row['id'] . "'>
                                <input type='hidden' name='action' value='accept'>
                                <button type='submit' class='accept-btn' id='accept-btn-" . $row['id'] . "'>Accept</button>
                              </form></td>";
                    }
                    echo "<td><button class='delete-btn' onclick='deleteBooking(" . $row['id'] . ")'>Delete</button></td>";
                    echo "</tr>";
                  }
                } else {
                    echo "<tr><td colspan='8'>No bookings found</td></tr>";
                }
                ?>
              </tbody>
            </table>
            <h2 style="text-align:center; margin-top: 30px;">Admin Dashboard - Consultation</h2>
            <table id="consultation-table">
            <thead>
                <tr>
                  <th>User</th>
                  <th>Time</th>
                  <th>Date</th>
                  <th>Concerns</th>
                  <th>Suggestions</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  if (mysqli_num_rows($concernResult) > 0) {
                    while ($row = mysqli_fetch_assoc($concernResult)) {
                      echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['time']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['concern']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['suggestion']) . "</td>";
                    echo "</tr>";
                    }
                  } else {
                    echo "<tr><td colspan='5'>No consultations found</td></tr>";
                  }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      <script>
        // Accept booking and show SweetAlert
          function acceptBooking(booking_id) {
            Swal.fire({
            title: 'Are you sure?',
            text: "You want to accept this booking?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Accept!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
          if (result.isConfirmed) {
            // Reload the page after accepting (you might want to do a more efficient update)
            window.location.href = 'adminbooking.php?action=accept&booking_id=' + booking_id;
          }
        });
      }
      function deleteBooking(booking_id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this booking?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Delete!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // AJAX request to delete the booking
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'adminbooking.php?action=delete&id=' + booking_id, true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        if (xhr.responseText == 'success') {
                            // Remove the row from the table
                            var row = document.getElementById('row-' + booking_id);
                            if (row) {
                                row.remove();
                            }
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Booking deleted successfully!',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Error deleting booking!',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    }
                };
                xhr.send();
            }
        });
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
    
        // JavaScript to toggle the dropdown on click
    document.getElementById('bookingDropdown').addEventListener('click', function(event) {
      var dropdownMenu = document.getElementById('dropdownMenu');
    
      // Toggle the 'show' class to display the dropdown
      dropdownMenu.classList.toggle('show');
    
      // Prevent the link from navigating (if necessary)
      event.preventDefault();
    });
    
    // Optional: Close the dropdown if clicking outside of it
    window.addEventListener('click', function(event) {
      var dropdownMenu = document.getElementById('dropdownMenu');
      var bookingDropdown = document.getElementById('bookingDropdown');
    
      // Close the dropdown if clicked outside the dropdown
      if (!bookingDropdown.contains(event.target) && !dropdownMenu.contains(event.target)) {
        dropdownMenu.classList.remove('show');
      }
    });
      </script>
    </body>
    </html>