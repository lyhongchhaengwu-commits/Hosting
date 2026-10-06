<?php
function calculateShipping($zone, $weight = 1.0)
{
    switch ($zone) {
        case 'Zone1':
            // Base $5 + $1.50 per kg over 1kg
            return 5.00 + max(0, $weight - 1) * 1.50;
        case 'Zone2':
            // Base $10 + $2.50 per kg over 1kg
            return 10.00 + max(0, $weight - 1) * 2.50;
        case 'Zone3':
            // Flat rate
            return 20.00;
        default:
            // Invalid zone
            return -1;
    }
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $zone = $_POST["zone"];
    $weight = (float) $_POST["weight"];
    if ($weight <= 0) {
        $message = "Please enter a valid weight.";
    } else {
        $shippingFee = calculateShipping($zone, $weight);
        if ($shippingFee === -1) {
            $message = "Invalid shipping zone.";
        } else {
            $message = "Shipping Fee: $" . number_format($shippingFee, 2);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Fee Calculator</title>
    <style>
        *{
            box-sizing: border-box;
        }
        body {
            margin-top:100px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            width: 400px;
            background: blue;
            padding: 30px;
            border-radius: 5px;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #444;
        }
        select,input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 12px;
            border: none;
            background: #667eea;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background: #5568d9;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            text-align: center;
            background: #f1f5ff;
            color: #333;
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Shipping Fee Calculator</h2>
        <form method="POST">
            <label for="zone">Select Shipping Zone:</label>
            <select name="zone" id="zone" required>
                <option value="">Select Zone</option>
                <option value="Zone1">Zone 1</option>
                <option value="Zone2">Zone 2</option>
                <option value="Zone3">Zone 3</option>
            </select>
            <label for="weight">Weight (kg):</label>
            <input
                type="number"
                name="weight"
                id="weight"
                step="0.1"
                min="0.1"
                value="1.0"
                required>
            <button type="submit">
                Calculate Shipping
            </button>
        </form>
        <?php if ($message !== ""): ?>
            <div class="result">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>