
<!DOCTYPE html>
<html>
<head>
    <title>Registration Form</title>
    <style>
        body {
            font-family: Arial;
            background: linear-gradient(120deg, #89f7fe, #66a6ff);
            padding: 30px;
        }
        .container {
            width: 450px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 15px #555;
        }
        h2 {
            text-align: center;
            color: #0066cc;
        }
        input[type=text], input[type=email] {
            width: 95%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #aaa;
            border-radius: 5px;
        }
        input[type=submit] {
            width: 100%;
            padding: 12px;
            background: #0066cc;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type=submit]:hover {
            background: #004080;
        }
        .error {
            color: red;
            font-size: 13px;
        }
        .success {
            color: green;
            text-align: center;
        }
        label {
            font-weight: bold;
        }
    </style>
</head>
<body>

<?php
$nameErr = $emailErr = $mobileErr = "";
$genderErr = $websiteErr = $agreeErr = "";

$name = $email = $mobile = "";
$gender = $website = "";

function input_data($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Name Validation
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = input_data($_POST["name"]);
        if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
            $nameErr = "Only alphabets and spaces allowed";
        }
    }

    // Email Validation
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = input_data($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    // Mobile Validation
    if (empty($_POST["mobile"])) {
        $mobileErr = "Mobile number is required";
    } else {
        $mobile = input_data($_POST["mobile"]);
        if (!preg_match("/^[0-9]+$/", $mobile)) {
            $mobileErr = "Only numbers are allowed";
        } elseif (strlen($mobile) != 10) {
            $mobileErr = "Mobile number must have 10 digits";
        }
    }

    // Website Validation
    if (!empty($_POST["website"])) {
        $website = input_data($_POST["website"]);
        if (!filter_var($website, FILTER_VALIDATE_URL)) {
            $websiteErr = "Invalid website URL";
        }
    }

    // Gender Validation
    if (empty($_POST["gender"])) {
        $genderErr = "Please select gender";
    } else {
        $gender = input_data($_POST["gender"]);
    }

    // Checkbox Validation
    if (!isset($_POST["agree"])) {
        $agreeErr = "Please accept the terms";
    }

    if ($nameErr == "" && $emailErr == "" &&
        $mobileErr == "" && $genderErr == "" &&
        $websiteErr == "" && $agreeErr == "") {
        $success = true;
    }
}
?>

<div class="container">
    <h2>Registration Form</h2>
    <p style="color:red;">* Required fields</p>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

        <label>Name:</label>
        <input type="text" name="name"
        value="<?php echo $name; ?>">
        <span class="error">* <?php echo $nameErr; ?></span>
        <br><br>

        <label>Email:</label>
        <input type="email" name="email"
        value="<?php echo $email; ?>">
        <span class="error">* <?php echo $emailErr; ?></span>
        <br><br>

        <label>Mobile Number:</label>
        <input type="text" name="mobile"
        value="<?php echo $mobile; ?>">
        <span class="error">* <?php echo $mobileErr; ?></span>
        <br><br>

        <label>Website:</label>
        <input type="text" name="website"
        value="<?php echo $website; ?>">
        <span class="error"><?php echo $websiteErr; ?></span>
        <br><br>

        <label>Gender:</label><br>
        <input type="radio" name="gender" value="Male"> Male
        <input type="radio" name="gender" value="Female"> Female
        <input type="radio" name="gender" value="Other"> Other
        <span class="error">* <?php echo $genderErr; ?></span>
        <br><br>

        <label>
        <input type="checkbox" name="agree" value="Yes">
        I agree to the Terms and Conditions
        </label>
        <span class="error"><?php echo $agreeErr; ?></span>
        <br><br>

        <input type="submit" name="submit" value="Register">
    </form>

    <?php
    if ($success) {
        echo "<h3 class='success'>Successfully Registered!</h3>";
        echo "<h3>Your Details:</h3>";
        echo "Name: " . $name . "<br>";
        echo "Email: " . $email . "<br>";
        echo "Mobile: " . $mobile . "<br>";
        echo "Website: " . $website . "<br>";
        echo "Gender: " . $gender . "<br>";
    }
    ?>
</div>

</body>
</html>

