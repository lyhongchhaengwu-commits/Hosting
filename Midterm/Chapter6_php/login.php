<?php
session_start();

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "bacll_database2";

// Connect to database
$conn = new mysqli(
    $servername,
    $db_username,
    $db_password,
    $dbname
);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$message = "";

// Check if form submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if ($username === "" || $password === "") {

        $message = "Please enter username and password.";

    } else {

        // Find user
        $stmt = $conn->prepare(
            "SELECT id, username, password
            FROM users
            WHERE username = ?"
        );


        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            // Check password
            if (password_verify($password, $user["password"])) {

                // Login successful
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];

                header("Location: index.php");
                exit;

            } else {

                $message = "Incorrect password.";
            }

        } else {

            $message = "Username not found.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;

            min-height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;

            background: linear-gradient(
                135deg,
                #667eea,
                #764ba2
            );
        }

        .login-container {
            width: 100%;
            max-width: 400px;

            background: white;

            padding: 35px;

            border-radius: 15px;

            box-shadow:
                0 10px 30px rgba(0, 0, 0, 0.25);
        }

        .login-container h2 {
            text-align: center;

            color: #333;

            margin-bottom: 25px;

            font-size: 28px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;

            margin-bottom: 8px;

            color: #444;

            font-weight: bold;
        }

        .form-group input {
            width: 100%;

            padding: 12px 14px;

            border: 1px solid #ccc;

            border-radius: 8px;

            font-size: 16px;

            transition: 0.3s;
        }

        .form-group input:focus {
            outline: none;

            border-color: #667eea;

            box-shadow:
                0 0 5px rgba(102, 126, 234, 0.4);
        }

        .login-button {
            width: 100%;

            padding: 13px;

            border: none;

            border-radius: 8px;

            background: #667eea;

            color: white;

            font-size: 16px;

            font-weight: bold;

            cursor: pointer;

            transition: 0.3s;
        }

        .login-button:hover {
            background: #5568d9;

            transform: translateY(-1px);
        }

        .message {
            margin-bottom: 20px;

            padding: 12px;

            border-radius: 8px;

            text-align: center;

            background: #ffe5e5;

            color: #d63031;

            border: 1px solid #ffb3b3;

            font-size: 14px;
        }

        .signup {
            text-align: center;

            margin-top: 20px;

            color: #666;

            font-size: 14px;
        }

        .signup a {
            color: #667eea;

            text-decoration: none;

            font-weight: bold;
        }

        .signup a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {

            .login-container {
                width: 90%;

                padding: 25px;
            }

            .login-container h2 {
                font-size: 24px;
            }
        }

    </style>

</head>

<body>

    <div class="login-container">

        <h2>Welcome Back</h2>

        <?php if ($message !== ""): ?>

            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>

        <form method="POST" action="">

            <div class="form-group">

                <label for="username">
                    Username
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Enter username"
                    required
                >

            </div>

            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter password"
                    required
                >

            </div>

            <button
                type="submit"
                class="login-button"
            >
                Login
            </button>

        </form>

        <div class="signup">

            Don't have an account?

            <a href="Signup.php">
                Sign Up
            </a>

        </div>

    </div>

</body>

</html>