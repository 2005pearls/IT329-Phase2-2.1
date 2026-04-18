<?php

session_start();
require_once("../config/db.php");

$firstName = $_POST['first_name'];
$lastName  = $_POST['last_name'];
$email     = $_POST['email'];
$password  = $_POST['password'];


//  Check if the email is already used by another user
$sql = "SELECT * FROM user WHERE emailAddress = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    header("Location: ../signup.php?error=emailExists");
    exit();
}


//  Check if the email belongs to a blocked user
$sql = "SELECT * FROM blockeduser WHERE emailAddress = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    header("Location: ../signup.php?error=blocked");
    exit();
}


// Encrypt the password before saving it in the database
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);


//  Handle profile image upload
// If the user uploaded an image, save it with a unique name
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['name'] != "") {

    $photoName = time() . "_" . $_FILES['profile_image']['name'];

    move_uploaded_file(
        $_FILES['profile_image']['tmp_name'],
        "../images/" . $photoName
    );

} else {
    // If no image is uploaded, use a default image
    $photoName = "default-user.jpg";
}


//  Insert the new user into the database
$sql = "INSERT INTO user
(userType, firstName, lastName, emailAddress, password, photoFileName)
VALUES ('user', ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sssss", $firstName, $lastName, $email, $hashedPassword, $photoName);
mysqli_stmt_execute($stmt);


//  Get the ID of the newly created user
$userID = mysqli_insert_id($conn);


//  Store user data in the session (login the user automatically)
$_SESSION['userID'] = $userID;
$_SESSION['userType'] = "user";


// Redirect the user to their dashboard
header("Location: ../user.php");
exit();

?>