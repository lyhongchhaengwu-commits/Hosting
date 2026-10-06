<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body{
            margin: 30px;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
        }
       
        .flex-container{
            display: flex;
            justify-content:center;
        }
        
        form {
            border-radius: 10px;
            background-color: #ffebeb;
            padding: 20px;
            padding-alight:center;
            margin: 100px auto;
            justify-content:center;
            width: 500px;
            max-width: 90%;
        }

        h2 {
            text-align: center;
        }

        input ,select{
            width: 200px;
            padding: 12px;
            margin: 8px ;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        label {
            display: inline-block;
            width: 180px;
            vertical-align: middle;
        }

        button{
            width: 100px;
            padding: 10px;
            margin: 10px auto;
            border: 1px solid #ccc;
            border-radius: 10px;
            display: block;
        }

        button:hover{
            background-color: red;
            color: white;
        }
        h2{
            text-alight:center;
        }
        table {
            border-collapse: collapse;
            width: 90%;
            margin: 30px auto;
            background-color: white;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ccc;
        }

        th {
            background-color: #ffebeb;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <form method="POST">
            <h2>Wellcome To Student Management</h2>
            <label>Enter Student ID </label>
            <input type="number" name="id"></input>
            <br><br>
            <label>Enter Student Name </label>
            <input type="name" name="name"></input>
            <br><br>
            <label>Enter Cisco Score</label>
            <input type="number" name="score1" min="0" max="100"></input>
            <br><br>
            <label>Enter PHP Score</label>
            <input type="number" name="score2" min="0" max="100"></input>
            <br><br>
            <label>Enter Database Score</label>
            <input type="number" name="score3" min="0" max="100"></input>
            <br><br>
            <label>Enter PB Score</label>
            <input type="number" name="score4" min="0" max="100"></input>
            <br><br>
            <label>Enter C# Score</label>
            <input type="number" name="score5" min="0" max="100"></input>
            <br><br>
            <button type="submit">Calculate</button>
        </form>
    </div>
<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get student name and student id
    $studentId = $_POST["id"];
    $studentName = $_POST["name"];
    //store the scores in an array
    $scores = [
        $_POST["score1"],
        $_POST["score2"],
        $_POST["score3"],
        $_POST["score4"],
        $_POST["score5"]
    ];
    // Uses loop  to caculate the total
    $total = 0;
    for ($i=0; $i < count($scores); $i++){
        $total = $total + $scores[$i];
    }

    // Calculate the everage
    $average = $total / count($scores);

    // Find the highest score

    $highest =  max($scores);

    // Find the lowest score
    $lowest = min($scores);

    // Determines the grade
    if ($average >= 90){
        $grade = "A";
    }
    elseif ($average >= 80){
        $grade = "B";
    }
    elseif ($average >= 70){
        $grade = "C";
    }
    elseif ($average >=60){
        $grade = "D";
    }
    elseif ($average >=50){
        $grade = "E";
    }
    else { $grade = "F";}

    // Displays all results in an HTML tbale
    echo "<h2>Student Results</h2>";
    echo "<table>";

    echo "<tr>";
    echo "<th>Student ID</th>";
    echo "<th>Student Name</th>";
    echo "<th>Total</th>";
    echo "<th>Average</th>";
    echo "<th>Highest</th>";
    echo "<th>Lowest</th>";
    echo "<th>Grade</th>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>" . $studentId . "</td>";
    echo "<td>" . $studentName . "</td>";
    echo "<td>" . $total . "</td>";
    echo "<td>" . $average . "</td>";
    echo "<td>" . $highest . "</td>";
    echo "<td>" . $lowest . "</td>";
    echo "<td>" . $grade . "</td>";
    echo "</tr>";

    echo "</table>";

    }
?>
</body>
</html>