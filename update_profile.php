<?php
session_start();
include('database.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $age = mysqli_real_escape_string($conn, $_POST['age']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);

    // Update user information in the database
    $sql = "UPDATE useraccount SET email='$email', name='$name', age='$age', contact='$contact' WHERE id='$user_id'";
    
    if (mysqli_query($conn, $sql)) {
        // Update session variables
        $_SESSION['email'] = $email;
        $_SESSION['name'] = $name;
        $_SESSION['age'] = $age;
        $_SESSION['contact'] = $contact;
    // Optional: Regenerate session ID for security reasons
    session_regenerate_id(true);
        // Redirect back to profile page
        header("Location: profile.php");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>