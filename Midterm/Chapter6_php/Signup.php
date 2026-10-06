<?php

session_start();


/*
|--------------------------------------------------------------------------
| DATABASE CONNECTION
|--------------------------------------------------------------------------
*/

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "bacll_database2";


$conn = new mysqli(
    $servername,
    $db_username,
    $db_password,
    $dbname
);


/*
|--------------------------------------------------------------------------
| CHECK DATABASE CONNECTION
|--------------------------------------------------------------------------
*/

if ($conn->connect_error) {

    die(
        "Database connection failed: "
        . $conn->connect_error
    );
}


$conn->set_charset("utf8mb4");


$message = "";
$success = false;


/*
|--------------------------------------------------------------------------
| SIGN UP
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim(
        $_POST["username"] ?? ""
    );

    $password = $_POST["password"] ?? "";


    /*
    | Check empty fields
    */

    if ($username === "" || $password === "") {

        $message =
            "Please enter a username and password.";

    } elseif (strlen($username) < 3) {

        $message =
            "Username must be at least 3 characters.";

    } elseif (strlen($password) < 4) {

        $message =
            "Password must be at least 4 characters.";

    } else {


        /*
        | Check if username already exists
        */

        $check = $conn->prepare(
            "SELECT *
             FROM users
             WHERE username = ?
             LIMIT 1"
        );


        if (!$check) {

            $message =
                "Database error: " .
                $conn->error;

        } else {

            $check->bind_param(
                "s",
                $username
            );

            $check->execute();

            $result =
                $check->get_result();


            if ($result->num_rows > 0) {

                $message =
                    "Username already exists.";

            } else {


                /*
                | Hash password
                */

                $hashed_password =
                    password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );


                /*
                | Insert user
                */

                $stmt = $conn->prepare(
                    "INSERT INTO users
                    (username, password)
                    VALUES (?, ?)"
                );


                if (!$stmt) {

                    $message =
                        "Database error: " .
                        $conn->error;

                } else {

                    $stmt->bind_param(
                        "ss",
                        $username,
                        $hashed_password
                    );


                    if ($stmt->execute()) {

                        $message =
                            "Account created successfully! You can login now.";

                        $success = true;

                    } else {

                        $message =
                            "Error creating account: " .
                            $stmt->error;
                    }


                    $stmt->close();
                }
            }


            $check->close();
        }
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

    <title>Sign Up</title>


    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }


        body {

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            min-height: 100vh;

            display: flex;

            justify-content: center;

            align-items: center;

            background:
                linear-gradient(
                    135deg,
                    #667eea,
                    #764ba2
                );
        }


        .signup-container {

            width: 100%;

            max-width: 400px;

            background: white;

            padding: 35px;

            border-radius: 15px;

            box-shadow:
                0 10px 30px
                rgba(0, 0, 0, 0.25);
        }


        .signup-container h2 {

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
                0 0 5px
                rgba(102, 126, 234, 0.4);
        }


        .signup-button {

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


        .signup-button:hover {

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


        .success {

            background: #e5f9ed;

            color: #218c54;

            border-color: #a8e6c1;
        }


        .login-link {

            text-align: center;

            margin-top: 20px;

            color: #666;

            font-size: 14px;
        }


        .login-link a {

            color: #667eea;

            text-decoration: none;

            font-weight: bold;
        }


        .login-link a:hover {

            text-decoration: underline;
        }


        @media (max-width: 480px) {

            .signup-container {

                width: 90%;

                padding: 25px;
            }


            .signup-container h2 {

                font-size: 24px;
            }

        }

    </style>

</head>


<body>


<div class="signup-container">


    <h2>Create Account</h2>


    <?php if ($message !== ""): ?>

        <div
            class="message
            <?php
            if ($success) {
                echo "success";
            }
            ?>"
        >

            <?php
            echo htmlspecialchars($message);
            ?>

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
                minlength="3"
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
                minlength="4"
                required
            >

        </div>


        <button
            type="submit"
            class="signup-button"
        >

            Create Account

        </button>


    </form>


    <div class="login-link">

        Already have an account?


        <a href="login.php">
            Login
        </a>

    </div>


</div>


</body>

</html>
