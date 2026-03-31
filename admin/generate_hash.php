<!DOCTYPE html>
<html>
<head>
    <title>Generate Hashed Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 50px;
            background-color: #f4f4f4;
        }
        .container {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            max-width: 500px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input[type="password"], input[type="submit"] {
            padding: 10px;
            width: 100%;
            margin-top: 10px;
        }
        .output {
            margin-top: 20px;
            word-wrap: break-word;
            background-color: #e7f3fe;
            padding: 10px;
            border-left: 5px solid #2196F3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Generate Hashed Password</h2>
    <form method="POST">
        <label>Enter Password:</label>
        <input type="password" name="password" required>
        <input type="submit" value="Generate Hash">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $password = $_POST['password'];
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        echo "<div class='output'><strong>Hashed Password:</strong><br>$hashed</div>";
    }
    ?>
</div>

</body>
</html>
