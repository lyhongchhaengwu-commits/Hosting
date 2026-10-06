<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION["username"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BACILL Calculator</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            text-align: center;
            margin: 0;
            padding: 30px;
        }

        h1 {
            margin-bottom: 10px;
        }

        h2 {
            margin-bottom: 10px;
        }

        .logout {
            display: inline-block;
            margin: 30px 0;
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .logout:hover {
            background-color: #bb2d3b;
        }

        h3 {
            margin-bottom: 20px;
        }

        .card-header {
            background-color: white;
            width: 300px;
            margin: 20px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            border: none;
            cursor: pointer;
            display: block;
            font-size: 16px;
        }

        .card-header:hover {
            background-color: #e8f0ff;
        }

        .card-header p {
            margin: 0;
        }
    </style>
</head>

<body>
    <h2>
        Hello, <?php echo htmlspecialchars($username); ?>!
    </h2>

    <p>You are successfully logged in.</p>

    <h3>BACILL Calculator</h3>

    <button
        class="card-header"
        onclick="window.location.href='Science_Class.php'"
    >
        <p>Science Class</p>
    </button>

    <button
        class="card-header"
        onclick="window.location.href='Social_science.php'"
    >
        <p>Social Science Class</p>
    </button>

    <a class="logout" href="logout.php">
        Logout
    </a>

</body>
</html>
