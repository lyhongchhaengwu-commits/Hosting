<?php session_start(); $message = ""; $search_result = null;
 /* ==== DATABASE CONNECTION ==== */ 
 $host = "localhost"; 
 $username = "root"; 
 $password = ""; 
 $database = "bacll_database2"; 
 $conn = new mysqli( $host, $username, $password, $database ); 
 if ($conn->connect_error) 
 { die("Database connection failed: " . $conn->connect_error); } 
 $conn->set_charset("utf8mb4"); 
 /* ===== CALCULATE GRADE ====== */ 
 function calculateGrade($percentage) 
 { if ($percentage >= 90) { return "A"; } 
 elseif ($percentage >= 80) { return "B"; } 
 elseif ($percentage >= 70) { return "C"; } 
 elseif ($percentage >= 60) { return "D"; } 
 elseif ($percentage >= 50) { return "E"; } 
 else { return "F"; } } 
 /* =============== VALIDATE SCORES =============== */ 
 function validScores( $khmer, $math, $history, $geography, $ES, $english, $morality )
  { return ( $khmer >= 0 && $khmer <= 125 && $math >= 0 && $math <= 75 && $history >= 0 && $history <= 75 && $geography >= 0 && $geography <= 75 && $ES >= 0 && $ES <= 50 && $english >= 0 && $english <= 50 && $morality >= 0 && $morality <= 75 ); } 
  /* ==============ADD STUDENT ============== */ 
  if (isset($_POST['calculate'])) 
  { $name = trim($_POST['name'] ?? ""); 
  $age = (int)($_POST['age'] ?? 0);
   $khmer = (float)($_POST['khmer'] ?? 0); 
   $math = (float)($_POST['math'] ?? 0); 
   $history = (float)($_POST['history'] ?? 0); $geography = (float)($_POST['geography'] ?? 0); 
   $ES = (float)($_POST['ES'] ?? 0); $english = (float)($_POST['english'] ?? 0); 
   $morality = (float)($_POST['morality'] ?? 0); if ($name === "") { $message = "Please enter the student name."; } 
   elseif ($age <= 0) { $message = "Please enter a valid age."; } 
   elseif ( !validScores( $khmer, $math, $history, $geography, $ES, $english, $morality ) ) { $message = "Please enter valid scores."; }
    else { /* Calculate total */ $total = $khmer + $math + $history + $geography + $ES + $english + $morality; 
    /* Calculate percentage */ $percentage = ($total / 525) * 100; 
    /* Calculate grade */ $grade = calculateGrade($percentage); /* Insert student */ $sql = "INSERT INTO social_science ( name, age, khmer, math, history, geography, ES, english, morality, total, percentage, grade ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; 
    $stmt = $conn->prepare($sql); if (!$stmt) { $message = "Database error: " . $conn->error; } 
    else { /* * s = string * i = integer * d = decimal/double * * 12 variables: * * name s * age i * khmer d * math d * history d * geography d * ES d * english d * morality d * total d * percentage d * grade s */ $stmt->bind_param( "siddddddddds", $name, $age, $khmer, $math, $history, $geography, $ES, $english, $morality, $total, $percentage, $grade ); 
    if ($stmt->execute()) { $message = "Student added successfully!"; } else { $message = "Error adding student: " . $stmt->error; } $stmt->close(); } } } /* ========================================================= SEARCH STUDENT ========================================================= */ 
    if (isset($_POST['search'])) { $search_name = trim($_POST['search_name'] ?? ""); 
    $sql = "SELECT * FROM social_science WHERE LOWER(name) = LOWER(?) LIMIT 1"; $stmt = $conn->prepare($sql); if ($stmt) { $stmt->bind_param( "s", $search_name ); $stmt->execute(); $result = $stmt->get_result(); if ($result->num_rows > 0) { $search_result = $result->fetch_assoc(); } 
    else { $message = "Student not found!"; } $stmt->close(); } else { $message = "Database error: " . $conn->error; } } 
    /* ========== UPDATE STUDENT =================== */ 
    if (isset($_POST['update'])) { $update_name = trim( $_POST['update_name'] ?? "" ); $name = trim( $_POST['name'] ?? "" ); $age = (int)( $_POST['age'] ?? 0 ); $khmer = (float)( $_POST['khmer'] ?? 0 ); $math = (float)( $_POST['math'] ?? 0 ); $history = (float)( $_POST['history'] ?? 0 ); $geography = (float)( $_POST['geography'] ?? 0 ); $ES = (float)( $_POST['ES'] ?? 0 ); $english = (float)( $_POST['english'] ?? 0 );
     $morality = (float)( $_POST['morality'] ?? 0 ); if ($update_name === "") { $message = "Please enter the current student name."; } elseif ($name === "") { $message = "Please enter the new name."; } elseif ($age <= 0) { $message = "Please enter a valid age."; } elseif ( !validScores( $khmer, $math, $history, $geography, $ES, $english, $morality ) ) { $message = "Please enter valid scores."; } else { /* Calculate total */ $total = $khmer + $math + $history + $geography + $ES + $english + $morality; /* Calculate percentage */ $percentage = ($total / 525) * 100; /* Calculate grade */ 
     $grade = calculateGrade($percentage); /* Update database */ 
     $sql = "UPDATE social_science SET name = ?, age = ?, khmer = ?, math = ?, history = ?, geography = ?, ES = ?, english = ?, morality = ?, total = ?, percentage = ?, grade = ? WHERE LOWER(name) = LOWER(?) LIMIT 1"; $stmt = $conn->prepare($sql); if (!$stmt) { $message = "Database error: " . $conn->error; } else { /* * 13 variables: * * name * age * khmer * math * history * geography * ES * english * morality * total * percentage * grade * update_name */ 
     $stmt->bind_param( "sidddddddddss", $name, $age, $khmer, $math, $history, $geography, $ES, $english, $morality, $total, $percentage, $grade, $update_name ); if ($stmt->execute()) { if ($stmt->affected_rows > 0) { $message = "Student updated successfully!"; } else { $message = "Student not found or no changes made."; } } else { $message = "Error updating student: " . $stmt->error; } $stmt->close(); } } } /* ========================================================= DELETE STUDENT ========================================================= */ if (isset($_POST['delete'])) 
     { $delete_name = trim( $_POST['delete_name'] ?? "" ); 
     $sql = "DELETE FROM social_science WHERE LOWER(name) = LOWER(?) LIMIT 1"; $stmt = $conn->prepare($sql); if ($stmt) { $stmt->bind_param( "s", $delete_name ); if ($stmt->execute()) { if ($stmt->affected_rows > 0) { $message = "Student deleted successfully!"; } else { $message = "Student not found!"; } } else { $message = "Error deleting student: " . $stmt->error; } $stmt->close(); } else { $message = "Database error: " . $conn->error; } } 
     /* ========= GET ALL STUDENTS ========= */
     $sql = "SELECT * FROM social_science ORDER BY id DESC"; 
     $students_result = $conn->query($sql); ?> 
     <!DOCTYPE html> <html lang="en"> <head>
<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Social Science Class</title>


<style>

    * {
        box-sizing: border-box;
    }


    body {
        font-family: Arial, sans-serif;

        background-color: #f2f2f2;

        margin: 0;

        padding: 20px;
    }


    h1 {
        color: #2c3e50;

        text-align: center;
    }


    h2 {
        color: #2c3e50;
    }


    form {
        background-color: white;

        width: 450px;

        margin: 15px auto;

        padding: 25px;

        border-radius: 10px;

        box-shadow: 0 0 10px #ccc;
    }


    input {
        width: 100%;

        padding: 10px;

        margin: 6px 0 12px;

        border: 1px solid #ccc;

        border-radius: 5px;

        font-size: 15px;
    }


    label {
        display: block;

        margin-top: 5px;

        font-weight: bold;

        color: #333;
    }


    button {
        background-color: #e80004;

        color: white;

        border: none;

        padding: 11px 18px;

        margin: 5px 2px;

        border-radius: 5px;

        cursor: pointer;

        font-size: 14px;
    }


    button:hover {
        background-color: #b00003;
    }


    .back-button {
        background-color: #34495e;
    }


    .back-button:hover {
        background-color: #2c3e50;
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

        padding: 20px;

        border-radius: 10px;

        box-shadow: 0 0 10px #ccc;
    }


    .search-box form {
        width: 100%;

        margin: 0;

        padding: 0;

        box-shadow: none;
    }


    .student-list {
        background-color: white;

        width: 95%;

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

        padding: 10px;

        text-align: center;
    }


    th {
        background-color: #2c3e50;

        color: white;
    }


    tr:nth-child(even) {
        background-color: #f8f8f8;
    }


    .grade-A {
        color: green;

        font-weight: bold;
    }


    .grade-B {
        color: #27ae60;

        font-weight: bold;
    }


    .grade-C {
        color: #2980b9;

        font-weight: bold;
    }


    .grade-D {
        color: #f39c12;

        font-weight: bold;
    }


    .grade-E {
        color: #e67e22;

        font-weight: bold;
    }


    .grade-F {
        color: red;

        font-weight: bold;
    }


    @media (max-width: 600px) {

        body {
            padding: 10px;
        }


        form,
        .result,
        .search-box {
            width: 100%;
        }


        .student-list {
            width: 100%;
        }

    }

</style>

</head> <body> <!-- ===================================================== ADD STUDENT ===================================================== --> <form method="post">
<h1>Social Science Class</h1>


<input
    type="text"
    name="name"
    placeholder="Enter student name"
    required
>


<input
    type="number"
    name="age"
    placeholder="Enter student age"
    min="1"
    required
>


<label>
    Khmer (125)
</label>

<input
    type="number"
    name="khmer"
    min="0"
    max="125"
    step="0.01"
    required
>


<label>
    Math (75)
</label>

<input
    type="number"
    name="math"
    min="0"
    max="75"
    step="0.01"
    required
>


<label>
    History (75)
</label>

<input
    type="number"
    name="history"
    min="0"
    max="75"
    step="0.01"
    required
>


<label>
    Geography (75)
</label>

<input
    type="number"
    name="geography"
    min="0"
    max="75"
    step="0.01"
    required
>


<label>
    Earth Science (50)
</label>

<input
    type="number"
    name="ES"
    min="0"
    max="50"
    step="0.01"
    required
>


<label>
    English (50)
</label>

<input
    type="number"
    name="english"
    min="0"
    max="50"
    step="0.01"
    required
>


<label>
    Morality (75)
</label>

<input
    type="number"
    name="morality"
    min="0"
    max="75"
    step="0.01"
    required
>


<button
    type="submit"
    name="calculate"
>
    Add Student
</button>


<button
    type="button"
    class="back-button"
    onclick="window.location.href='Index.php'"
>
    Back
</button>

</form> <!-- ===================================================== MESSAGE ===================================================== --> <?php if ($message !== ""): ?>
<div class="result">

    <h3>
        <?php
        echo htmlspecialchars($message);
        ?>
    </h3>

</div>

<?php endif; ?> <!-- ===================================================== SEARCH STUDENT ===================================================== --> <div class="search-box">
<h2>
    Search Student
</h2>


<form method="post">

    <input
        type="text"
        name="search_name"
        placeholder="Enter student name"
        required
    >


    <button
        type="submit"
        name="search"
    >
        Search
    </button>

</form>

</div> <!-- ===================================================== SEARCH RESULT ===================================================== --> <?php if ($search_result !== null): ?>
<div class="result">

    <h2>
        Student Found
    </h2>


    <p>
        <strong>Name:</strong>

        <?php
        echo htmlspecialchars(
            $search_result['name']
        );
        ?>
    </p>


    <p>
        <strong>Age:</strong>

        <?php
        echo htmlspecialchars(
            $search_result['age']
        );
        ?>
    </p>
    <h3>
        Total:

        <?php
        echo number_format(
            $search_result['total'],
            2
        );
        ?>

        / 525
    </h3>


    <h2>
        Percentage:

        <?php
        echo number_format(
            $search_result['percentage'],
            2
        );
        ?>%
    </h2>


    <h2>
        Grade:

        <?php
        echo htmlspecialchars(
            $search_result['grade']
        );
        ?>
    </h2>

</div>

<?php endif; ?> <!-- ===================================================== UPDATE STUDENT ===================================================== --> <div class="search-box">
<h2>
    Update Student
</h2>


<form method="post">


    <input
        type="text"
        name="update_name"
        placeholder="Current student name"
        required
    >


    <input
        type="text"
        name="name"
        placeholder="New name"
        required
    >


    <input
        type="number"
        name="age"
        placeholder="New age"
        min="1"
        required
    >


    <input
        type="number"
        name="khmer"
        placeholder="Khmer (0-125)"
        min="0"
        max="125"
        step="0.01"
        required
    >


    <input
        type="number"
        name="math"
        placeholder="Math (0-75)"
        min="0"
        max="75"
        step="0.01"
        required
    >


    <input
        type="number"
        name="history"
        placeholder="History (0-75)"
        min="0"
        max="75"
        step="0.01"
        required
    >


    <input
        type="number"
        name="geography"
        placeholder="Geography (0-75)"
        min="0"
        max="75"
        step="0.01"
        required
    >


    <input
        type="number"
        name="ES"
        placeholder="Earth Science (0-50)"
        min="0"
        max="50"
        step="0.01"
        required
    >


    <input
        type="number"
        name="english"
        placeholder="English (0-50)"
        min="0"
        max="50"
        step="0.01"
        required
    >


    <input
        type="number"
        name="morality"
        placeholder="Morality (0-75)"
        min="0"
        max="75"
        step="0.01"
        required
    >


    <button
        type="submit"
        name="update"
    >
        Update Student
    </button>

</form>

</div> <!-- ===================================================== DELETE STUDENT ===================================================== --> <div class="search-box">
<h2>
    Delete Student
</h2>


<form method="post">

    <input
        type="text"
        name="delete_name"
        placeholder="Enter student name"
        required
    >


    <button
        type="submit"
        name="delete"
    >
        Delete Student
    </button>

</form>

</div> <!-- ===================================================== ALL STUDENTS ===================================================== --> <div class="student-list">
<h2>
    All Students
</h2>


<?php if (
    $students_result &&
    $students_result->num_rows > 0
): ?>


    <table>

        <tr>

            <th>
                Name
            </th>

            <th>
                Age
            </th>

            <th>
                Khmer
            </th>

            <th>
                Math
            </th>

            <th>
                History
            </th>

            <th>
                Geography
            </th>

            <th>
                ES
            </th>

            <th>
                English
            </th>

            <th>
                Morality
            </th>

            <th>
                Total
            </th>

            <th>
                Percentage
            </th>

            <th>
                Grade
            </th>

        </tr>


        <?php while (
            $student =
            $students_result->fetch_assoc()
        ): ?>


            <tr>

                <td>
                    <?php
                    echo htmlspecialchars(
                        $student['name']
                    );
                    ?>
                </td>


                <td>
                    <?php
                    echo htmlspecialchars(
                        $student['age']
                    );
                    ?>
                </td>


                <td>
                    <?php
                    echo htmlspecialchars(
                        $student['khmer']
                    );
                    ?>
                </td>


                <td>
                    <?php
                    echo htmlspecialchars(
                        $student['math']
                    );
                    ?>
                </td>


                <td>
                    <?php
                    echo htmlspecialchars(
                        $student['history']
                    );
                    ?>
                </td>


                <td>
                    <?php
                    echo htmlspecialchars(
                        $student['geography']
                    );
                    ?>
                </td>


                <td>
                    <?php
                    echo htmlspecialchars(
                        $student['ES']
                    );
                    ?>
                </td>


                <td>
                    <?php
                    echo htmlspecialchars(
                        $student['english']
                    );
                    ?>
                </td>


                <td>
                    <?php
                    echo htmlspecialchars(
                        $student['morality']
                    );
                    ?>
                </td>


                <td>
                    <?php
                    echo number_format(
                        $student['total'],
                        2
                    );
                    ?>
                    / 525
                </td>


                <td>
                    <?php
                    echo number_format(
                        $student['percentage'],
                        2
                    );
                    ?>%
                </td>


                <td
                    class="grade-<?php
                    echo htmlspecialchars(
                        $student['grade']
                    );
                    ?>"
                >
                    <?php
                    echo htmlspecialchars(
                        $student['grade']
                    );
                    ?>
                </td>

            </tr>


        <?php endwhile; ?>


    </table>


<?php else: ?>


    <p style="text-align:center;">
        No students found.
    </p>


<?php endif; ?>