
<!DOCTYPE html>
<html>
<head>
<title>KSEB Electricity Bill</title>
<style>
body { font-family: Arial; background: #e8f0f8; padding: 20px; }
.container {
    max-width: 650px; margin: auto; background: white;
    padding: 25px; border-radius: 10px; box-shadow: 0 3px 12px #aaa;
}
h2 { text-align: center; color: #07549b; }
input, select {
    width: 95%; padding: 9px; margin: 7px 0;
}
button {
    width: 100%; padding: 12px; background: #07549b;
    color: white; border: 0; cursor: pointer;
}
table { width: 100%; border-collapse: collapse; }
td, th { padding: 9px; border: 1px solid #ccc; }
.error { color: red; }
.total { color: green; font-size: 22px; }
@media print {
    .form, .printbtn { display: none; }
    body { background: white; }
}
</style>
</head>
<body>
<div class="container">
<?php
// Store validation and bill details.
$error = "";
$bill = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST["name"]));
    $consumer = htmlspecialchars(trim($_POST["consumer"]));
    $previous = $_POST["previous"];
    $current = $_POST["current"];
    $months = (int)$_POST["months"];
    $phase = $_POST["phase"];
    $date = $_POST["date"];
    $arrears = (float)$_POST["arrears"];
    $other = (float)$_POST["other"];
    if ($name == "" || $consumer == "" || $date == "" ||
        !is_numeric($previous) || !is_numeric($current) ||
        $previous < 0 || $current < $previous ||
        !in_array($months, [1, 2]) ||
        !in_array($phase, ["single", "three"]) ||
        $arrears < 0 || $other < 0) {
        $error = "Please enter valid details.";
    } else {
        // Calculate units consumed from meter readings.
        $units = $current - $previous;
        $monthly = $units / $months;
        if ($monthly <= 250) {
            $energy = min($monthly,50)*3.35;
            $energy += max(0,min($monthly-50,50))*4.25;
            $energy += max(0,min($monthly-100,50))*5.35;
            $energy += max(0,min($monthly-150,50))*7.20;
            $energy += max(0,min($monthly-200,50))*8.50;
        } else {
            if ($monthly <= 300) $rate = 6.75;
            elseif ($monthly <= 350) $rate = 7.60;
            elseif ($monthly <= 400) $rate = 7.95;
            elseif ($monthly <= 500) $rate = 8.25;
            else $rate = 9.20;
            $energy = $monthly * $rate;
        }
        if ($monthly <= 50) $slab = 0;
        elseif ($monthly <= 100) $slab = 1;
        elseif ($monthly <= 150) $slab = 2;
        elseif ($monthly <= 200) $slab = 3;
        elseif ($monthly <= 250) $slab = 4;
        elseif ($monthly <= 300) $slab = 5;
        elseif ($monthly <= 350) $slab = 6;
        elseif ($monthly <= 400) $slab = 7;
        elseif ($monthly <= 500) $slab = 8;
        else $slab = 9;
        $single = [50,85,105,140,160,220,240,260,285,310];
        $three = [130,175,205,215,235,240,250,260,285,310];
        // Select fixed charge according to connection type.
        $fixed = ($phase == "single" ? $single[$slab] :
                  $three[$slab]) * $months;
        $energy *= $months;
        $total = $energy + $fixed + $arrears + $other;
        $due = date("d-m-Y", strtotime($date . " +15 days"));
        $bill = true;
    }
}
?>
<div class="form">
<h2>KSEB ELECTRICITY BILL</h2>
<p align="center">Kerala State Electricity Board Limited</p>
<hr>
<h3>Enter Consumer Details</h3>
<form method="post">
<label>Consumer Name</label><input type="text" name="name" required>
<label>Consumer Number</label><input type="text" name="consumer" required>
<label>Previous Meter Reading</label><input type="number" name="previous" min="0" required>
<label>Current Meter Reading</label><input type="number" name="current" min="0" required>
<label>Billing Period</label>
<select name="months">
<option value="1">1 Month</option><option value="2">2 Months</option>
</select>
<label>Connection Type</label>
<select name="phase">
<option value="single">Single Phase</option><option value="three">Three Phase</option>
</select>
<label>Bill Date</label><input type="date" name="date" required>
<label>Previous Arrears (Rs.)</label><input type="number" name="arrears" min="0" value="0">
<label>Other Charges (Rs.)</label><input type="number" name="other" min="0" value="0">
<button type="submit">Generate Electricity Bill</button>
</form>
</div>
<?php if ($error != "") echo "<p class='error'>$error</p>"; ?>
<?php if ($bill) { ?>
<h2>KSEB ELECTRICITY BILL</h2>
<p align="center">Kerala State Electricity Board Limited</p>
<hr>
<table>
<tr><td>Consumer Name</td><td><?php echo $name; ?></td></tr>
<tr><td>Consumer Number</td><td><?php echo $consumer; ?></td></tr>
<tr><td>Bill Number</td><td>KSEB-<?php echo date("Ymd"); ?>-<?php echo $consumer; ?></td></tr>
<tr><td>Bill Date</td><td><?php echo $date; ?></td></tr>
<tr><td>Due Date</td><td><?php echo $due; ?></td></tr>
<tr><td>Connection</td><td><?php echo ucfirst($phase); ?> Phase</td></tr>
</table>
<h3>Meter Reading Details</h3>
<table>
<tr><td>Previous Reading</td><td><?php echo $previous; ?></td></tr>
<tr><td>Current Reading</td><td><?php echo $current; ?></td></tr>
<tr><td>Units Consumed</td><td><?php echo $units; ?> Units</td></tr>
</table>
<h3>Bill Calculation</h3>
<table>
<tr><td>Energy Charge</td><td>Rs. <?php echo number_format($energy,2); ?></td></tr>
<tr><td>Fixed Charge</td><td>Rs. <?php echo number_format($fixed,2); ?></td></tr>
<tr><td>Previous Arrears</td><td>Rs. <?php echo number_format($arrears,2); ?></td></tr>
<tr><td>Other Charges</td><td>Rs. <?php echo number_format($other,2); ?></td></tr>
<tr><th>Total Amount</th><th class="total">Rs. <?php echo number_format($total,2); ?></th></tr>
</table>
<p><b>Payment Status:</b> Unpaid</p>
<p><b>Pay Before:</b> <?php echo $due; ?></p>
<p align="center">Thank you for using KSEB electricity!</p>
<button class="printbtn" onclick="window.print()">Print Bill</button>
<?php } ?>
</div>
</body>
</html>

