
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
        background: linear-gradient(135deg, #74ebd5, #ACB6E5);
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-card {
        background-color: #ffffff;
        padding: 40px 30px;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 400px;
    }

    .login-card h2 {
        font-weight: 600;
        margin-bottom: 25px;
        text-align: center;
        color: #333;
    }

    .form-control {
        height: 45px;
        border-radius: 10px;
    }

    .btn-primary {
        height: 45px;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 500;
    }

    .alert {
        margin-top: 10px;
    }
  </style>

  <script>
    function validateLoginForm() {
        let email = document.getElementById("email").value.trim();
        let password = document.getElementById("password").value.trim();

        if (email === "" || password === "") {
            alert("Please fill in both fields.");
            return false;
        }

        let emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
        if (!emailPattern.test(email)) {
            alert("Please enter a valid email address.");
            return false;
        }

        return true;
    }
  </script>
</head>
<body>

<div class="login-card">
    <h2>Login</h2>

    <?php
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger">'. $_SESSION['error'] .'</div>';
        unset($_SESSION['error']);
    }
    ?>

<form action="Login-validate.php" method="POST" onsubmit="return validateLoginForm()">
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Enter password">
        </div>

        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
</div>

</body>
</html>
