<?php

session_start();

$message = "";
$search_result = null;

/*
|--------------------------------------------------------------------------
| DATABASE CONNECTION
|--------------------------------------------------------------------------
*/

$host = "localhost";
$username = "root";
$password = "";
$database = "bacll_database2";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");


/*
|--------------------------------------------------------------------------
| CALCULATE GRADE
|--------------------------------------------------------------------------
*/

function calculateGrade($percentage)
{
    if ($percentage >= 90) {
        return "A";
    } elseif ($percentage >= 80) {
        return "B";
    } elseif ($percentage >= 70) {
        return "C";
    } elseif ($percentage >= 60) {
        return "D";
    } elseif ($percentage >= 50) {
        return "E";
    } else {
        return "F";
    }
}


/*
|--------------------------------------------------------------------------
| ADD STUDENT
|--------------------------------------------------------------------------
*/

if (isset($_POST['calculate'])) {

    $name = trim($_POST['name']);
    $age = (int) $_POST['age'];

    $math = (float) $_POST['math'];
    $kh = (float) $_POST['kh'];
    $chemistry = (float) $_POST['chemistry'];
    $physics = (float) $_POST['physics'];
    $biology = (float) $_POST['biology'];
    $english = (float) $_POST['english'];
    $history = (float) $_POST['history'];


    /*
    | Validate input
    */

    if ($name == "") {

        $message = "Please enter the student name.";

    } elseif ($age <= 0) {

        $message = "Please enter a valid age.";

    } elseif (
        $math < 0 || $math > 125 ||
        $kh < 0 || $kh > 75 ||
        $chemistry < 0 || $chemistry > 75 ||
        $physics < 0 || $physics > 75 ||
        $biology < 0 || $biology > 75 ||
        $english < 0 || $english > 50 ||
        $history < 0 || $history > 50
    ) {

        $message = "Please enter valid scores.";

    } else {

        /*
        | Calculate total
        */

        $total =
            $math +
            $kh +
            $chemistry +
            $physics +
            $biology +
            $english +
            $history;


        /*
        | Calculate percentage
        */

        $percentage = ($total / 525) * 100;


        /*
        | Calculate grade
        */

        $grade = calculateGrade($percentage);


        /*
        | Insert into database
        */

        $sql = "INSERT INTO Science_class
                (
                    name,
                    age,
                    math,
                    kh,
                    chemistry,
                    physics,
                    biology,
                    english,
                    history,
                    total,
                    average,
                    grade
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "siddddddddds",
            $name,
            $age,
            $math,
            $kh,
            $chemistry,
            $physics,
            $biology,
            $english,
            $history,
            $total,
            $percentage,
            $grade
        );


        if ($stmt->execute()) {

            $message = "Student added successfully!";

        } else {

            $message = "Error adding student: " . $stmt->error;
        }

        $stmt->close();
    }
}


/*
|--------------------------------------------------------------------------
| SEARCH STUDENT
|--------------------------------------------------------------------------
*/

if (isset($_POST['search'])) {

    $search_name = trim($_POST['search_name']);


    $sql = "SELECT *
            FROM Science_class
            WHERE LOWER(name) = LOWER(?)
            LIMIT 1";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $search_name);

    $stmt->execute();

    $result = $stmt->get_result();


    if ($result->num_rows > 0) {

        $search_result = $result->fetch_assoc();

    } else {

        $message = "Student not found.";
    }

    $stmt->close();
}


/*
|--------------------------------------------------------------------------
| UPDATE STUDENT
|--------------------------------------------------------------------------
*/

if (isset($_POST['update'])) {

    $update_name = trim($_POST['update_name']);

    $name = trim($_POST['name']);
    $age = (int) $_POST['age'];

    $math = (float) $_POST['math'];
    $kh = (float) $_POST['kh'];
    $chemistry = (float) $_POST['chemistry'];
    $physics = (float) $_POST['physics'];
    $biology = (float) $_POST['biology'];
    $english = (float) $_POST['english'];
    $history = (float) $_POST['history'];


    /*
    | Validate input
    */

    if ($name == "") {

        $message = "Please enter a new name.";

    } elseif ($age <= 0) {

        $message = "Please enter a valid age.";

    } elseif (
        $math < 0 || $math > 125 ||
        $kh < 0 || $kh > 75 ||
        $chemistry < 0 || $chemistry > 75 ||
        $physics < 0 || $physics > 75 ||
        $biology < 0 || $biology > 75 ||
        $english < 0 || $english > 50 ||
        $history < 0 || $history > 50
    ) {

        $message = "Please enter valid scores.";

    } else {

        /*
        | Calculate total
        */

        $total =
            $math +
            $kh +
            $chemistry +
            $physics +
            $biology +
            $english +
            $history;


        /*
        | Calculate percentage
        */

        $percentage = ($total / 525) * 100;


        /*
        | Calculate grade
        */

        $grade = calculateGrade($percentage);


        /*
        | Update database
        */

        $sql = "UPDATE Science_class
                SET
                    name = ?,
                    age = ?,
                    math = ?,
                    kh = ?,
                    chemistry = ?,
                    physics = ?,
                    biology = ?,
                    english = ?,
                    history = ?,
                    total = ?,
                    average = ?,
                    grade = ?
                WHERE LOWER(name) = LOWER(?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "sidddddddddss",
            $name,
            $age,
            $math,
            $kh,
            $chemistry,
            $physics,
            $biology,
            $english,
            $history,
            $total,
            $percentage,
            $grade,
            $update_name
        );


        if ($stmt->execute()) {

            if ($stmt->affected_rows > 0) {

                $message = "Student updated successfully!";

            } else {

                $message = "Student not found!";
            }

        } else {

            $message = "Error updating student: " . $stmt->error;
        }

        $stmt->close();
    }
}


/*
|--------------------------------------------------------------------------
| DELETE STUDENT
|--------------------------------------------------------------------------
*/

if (isset($_POST['delete'])) {

    $delete_name = trim($_POST['delete_name']);


    $sql = "DELETE FROM Science_class
            WHERE LOWER(name) = LOWER(?)
            LIMIT 1";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $delete_name);


    if ($stmt->execute()) {

        if ($stmt->affected_rows > 0) {

            $message = "Student deleted successfully!";

        } else {

            $message = "Student not found!";
        }

    } else {

        $message = "Error deleting student: " . $stmt->error;
    }

    $stmt->close();
}


/*
|--------------------------------------------------------------------------
| GET ALL STUDENTS
|--------------------------------------------------------------------------
*/

$sql = "SELECT *
        FROM Science_class
        ORDER BY id DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Science Class</title>


    <style>

        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 30px;
        }


        form {
            background-color: white;
            width: 450px;
            margin: auto;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }


        h1 {
            color: #2c3e50;
            text-align: center;
        }


        input {
            width: 90%;
            padding: 10px;
            margin: 6px 0 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }


        label {
            display: inline-block;
            width: 120px;
            font-weight: bold;
        }


        button {
            background-color: #e80004;
            color: white;
            border: none;
            padding: 12px 18px;
            margin: 5px 2px;
            border-radius: 5px;
            cursor: pointer;
        }


        button:hover {
            background-color: #b00003;
        }


        .result {
            background-color: white;
            width: 450px;
            margin: 20px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
            text-align: center;
        }


        .search-box {
            background-color: white;
            width: 450px;
            margin: 20px auto;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
            box-sizing: border-box;
        }


        .search-box form {
            width: 100%;
            padding: 0;
            box-shadow: none;
        }


        .student-list {
            background-color: white;
            width: 90%;
            margin: 20px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
            overflow-x: auto;
        }


        table {
            width: 100%;
            border-collapse: collapse;
        }


        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }


        th {
            background-color: #eee;
        }

    </style>

</head>


<body>


<!-- =========================================================
     ADD STUDENT
========================================================= -->

<form method="post">

    <h1>Science Class</h1>


    <input
        type="text"
        name="name"
        placeholder="Enter your name"
        required>


    <input
        type="number"
        name="age"
        placeholder="Enter your age"
        min="1"
        required>


    <label>Math (125):</label>

    <input
        type="number"
        name="math"
        min="0"
        max="125"
        required>


    <label>Khmer (75):</label>

    <input
        type="number"
        name="kh"
        min="0"
        max="75"
        required>


    <label>Chemistry (75):</label>

    <input
        type="number"
        name="chemistry"
        min="0"
        max="75"
        required>


    <label>Physics (75):</label>

    <input
        type="number"
        name="physics"
        min="0"
        max="75"
        required>


    <label>Biology (75):</label>

    <input
        type="number"
        name="biology"
        min="0"
        max="75"
        required>


    <label>English (50):</label>

    <input
        type="number"
        name="english"
        min="0"
        max="50"
        required>


    <label>History (50):</label>

    <input
        type="number"
        name="history"
        min="0"
        max="50"
        required>


    <button
        type="submit"
        name="calculate">

        Calculate

    </button>


    <button
        type="button"
        onclick="window.location.href='Index.php'">

        Back

    </button>

</form>


<!-- =========================================================
     MESSAGE
========================================================= -->

<?php if ($message != ""): ?>

    <div class="result">

        <h3>
            <?php echo htmlspecialchars($message); ?>
        </h3>

    </div>

<?php endif; ?>


<!-- =========================================================
     SEARCH STUDENT
========================================================= -->

<div class="search-box">

    <h2>Search Student</h2>


    <form method="post">

        <input
            type="text"
            name="search_name"
            placeholder="Enter student name"
            required>


        <button
            type="submit"
            name="search">

            Search

        </button>

    </form>

</div>


<!-- =========================================================
     SEARCH RESULT
========================================================= -->

<?php if ($search_result != null): ?>

    <div class="result">

        <h2>Student Found</h2>


        <h3>
            Name:
            <?php echo htmlspecialchars($search_result['name']); ?>
        </h3>


        <h3>
            Age:
            <?php echo htmlspecialchars($search_result['age']); ?>
        </h3>


        <h3>
            Total:
            <?php echo $search_result['total']; ?> / 525
        </h3>


        <h2>
            Percentage:
            <?php echo number_format($search_result['average'], 2); ?>%
        </h2>


        <h2>
            Grade:
            <?php echo htmlspecialchars($search_result['grade']); ?>
        </h2>

    </div>

<?php endif; ?>


<!-- =========================================================
     UPDATE STUDENT
========================================================= -->

<div class="search-box">

    <h2>Update Student</h2>


    <form method="post">


        <input
            type="text"
            name="update_name"
            placeholder="Name to update"
            required>


        <input
            type="text"
            name="name"
            placeholder="New name"
            required>


        <input
            type="number"
            name="age"
            placeholder="New age"
            min="1"
            required>


        <input
            type="number"
            name="math"
            placeholder="Math (0-125)"
            min="0"
            max="125"
            required>


        <input
            type="number"
            name="kh"
            placeholder="Khmer (0-75)"
            min="0"
            max="75"
            required>


        <input
            type="number"
            name="chemistry"
            placeholder="Chemistry (0-75)"
            min="0"
            max="75"
            required>


        <input
            type="number"
            name="physics"
            placeholder="Physics (0-75)"
            min="0"
            max="75"
            required>


        <input
            type="number"
            name="biology"
            placeholder="Biology (0-75)"
            min="0"
            max="75"
            required>


        <input
            type="number"
            name="english"
            placeholder="English (0-50)"
            min="0"
            max="50"
            required>


        <input
            type="number"
            name="history"
            placeholder="History (0-50)"
            min="0"
            max="50"
            required>


        <button
            type="submit"
            name="update">

            Update Student

        </button>

    </form>

</div>


<!-- =========================================================
     DELETE STUDENT
========================================================= -->

<div class="search-box">

    <h2>Delete Student</h2>


    <form method="post">


        <input
            type="text"
            name="delete_name"
            placeholder="Enter name to delete"
            required>


        <button
            type="submit"
            name="delete">

            Delete Student

        </button>

    </form>

</div>


<!-- =========================================================
     ALL STUDENTS
========================================================= -->

<?php if ($result && $result->num_rows > 0): ?>

    <div class="student-list">

        <h2>All Students</h2>


        <table>

            <tr>

                <th>Name</th>

                <th>Age</th>

                <th>Total</th>

                <th>Percentage</th>

                <th>Grade</th>

            </tr>


            <?php while ($student = $result->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?php
                        echo htmlspecialchars($student['name']);
                        ?>
                    </td>


                    <td>
                        <?php
                        echo htmlspecialchars($student['age']);
                        ?>
                    </td>


                    <td>
                        <?php
                        echo $student['total'];
                        ?>
                        / 525
                    </td>


                    <td>
                        <?php
                        echo number_format(
                            $student['average'],
                            2
                        );
                        ?>%
                    </td>


                    <td>
                        <?php
                        echo htmlspecialchars($student['grade']);
                        ?>
                    </td>

                </tr>

            <?php endwhile; ?>

        </table>

    </div>

<?php endif; ?>


</body>

</html>

<?php

$conn->close();

?>
