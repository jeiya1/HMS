<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hotel Home</title>
</head>
<body>

<h1>Welcome to Our Hotel</h1>

<?php if (isset($_SESSION['logged_in_user_id'])) {
    echo "<p>Logged in as UserID: " . $_SESSION['logged_in_user_id'] . "</p>";
} else {
    echo "<p>Not logged in</p>";
}
?>

<?php if (!$_SESSION['logged_in_user_id']) {
    echo "<a href='/login'>Login</a> | <a href='/signup'>Sign Up</a>";
} else {
    echo "<a href='/logout'>Logout</a>";
}
?>

<br>
<a href="/reservation">Reserve Now</a>

</body>
</html>