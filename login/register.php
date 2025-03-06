<?php  
session_start();
include 'connect.php';

// SIGN-UP HANDLER
if(isset($_POST['signUp'])){
    $firstName = $_POST['fName'];
    $lastName = $_POST['lName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hashedPassword = md5($password); // Hash the password

    // Check if email already exists
    $checkEmail = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkEmail);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        echo "<script>alert('Email Address Already Exists!'); window.location='index.php';</script>";
        exit();
    }

    // Insert user into database
    $insertQuery = "INSERT INTO users (firstname, lastname, email, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssss", $firstName, $lastName, $email, $hashedPassword);

    if($stmt->execute()){
        echo "<script>alert('Registration Successful! Please login.'); window.location='index.php';</script>";
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}

// SIGN-IN HANDLER
if(isset($_POST['signIn'])){
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    // Use prepared statements to prevent SQL injection
    $sql = "SELECT * FROM users WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

   
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['email'] = $row['email'];
        $_SESSION['firstname'] = $row['firstName']; // Store first name in session
        header("Location: homepage.php");
        exit();
    } else {
        header("Location: error.php?message=Invalid Email or Password. Please sign up first.");
        exit();
    }
}
?>
