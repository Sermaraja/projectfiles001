<?php
session_start();
include("connect.php");

if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please Login First!'); window.location='index.php';</script>";
    exit();
}

$email = $_SESSION['email'];

// Fetch user's first name securely
$stmt = $conn->prepare("SELECT firstname FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$firstname = "Guest";

if ($row = $result->fetch_assoc()) {
    $firstname = htmlspecialchars($row['firstname']);
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Smart Paper</title>
   <link rel="stylesheet" href="./src/home.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<!-- Navigation Bar -->
<nav>
    <div class="navbar">
        <div class="logo"><a href="#">SmartGrade</a></div>
        <ul class="menu">
            <li><a href="#Home">Home</a></li>
            <li><a href="#About">About</a></li>
            <li><a href="#Category">Features</a></li>
            <li><a href="http://localhost:5000" target="_blank">Evaluation</a></li> <!-- Updated Link -->
            <li><a href="#Feedback">Feedback</a></li>
            <li><a href="#" style="font-weight:bold; color:green;"><?php echo $firstname; ?></a></li>
            <li><a href="logout.php" style="color:red;">Logout</a></li>
        </ul>
    </div>
</nav>

<!-- Page Sections -->
<section id="Home">Home Section</section>
<section id="About">About Section</section>
<section id="Category">Features Section</section>
<section id="Contact">Evaluation Section</section>
<section id="Feedback">Feedback Section</section>

</body>
</html>