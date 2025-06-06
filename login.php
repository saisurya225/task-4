<?php
require '../config/database.php';
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Server-side validation
    if (empty($username)) {
        $errors[] = "Username is required.";
    } elseif (mb_strlen($username) < 3 || mb_strlen($username) > 32) {
        $errors[] = "Username must be between 3 and 32 characters.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (mb_strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            header('Location: ../posts/index.php');
            exit;
        } else {
            $errors[] = "Invalid credentials.";
        }
    }
}
?>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    min-height: 100vh;
    background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
    background-size: 400% 400%;
    animation: gradientBG 15s ease infinite;
    display: flex;
    align-items: center;
    justify-content: center;
}
@keyframes gradientBG {
    0% {background-position: 0% 50%;}
    50% {background-position: 100% 50%;}
    100% {background-position: 0% 50%;}
}
.form-container {
    background: rgba(255,255,255,0.92);
    padding: 2.5rem 2rem 2rem 2rem;
    border-radius: 1.5rem;
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
    max-width: 400px;
    width: 100%;
}
.form-label {
    color: #23a6d5;
    font-weight: 600;
}
.btn-primary {
    background: linear-gradient(90deg, #e73c7e 0%, #23a6d5 100%);
    border: none;
    font-weight: 600;
    letter-spacing: 1px;
}
.btn-primary:hover {
    background: linear-gradient(90deg, #23a6d5 0%, #e73c7e 100%);
}
</style>

<div class="form-container mx-auto">
    <h3 class="text-center mb-4" style="color:#23a6d5;">Login</h3>
    <form method="POST" id="loginForm" novalidate>
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input
            type="text"
            name="username"
            id="username"
            class="form-control"
            placeholder="Username"
            required
            minlength="3"
            maxlength="32"
        >
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input
            type="password"
            name="password"
            id="password"
            class="form-control"
            placeholder="Password"
            required
            minlength="8"
        >
    </div>
    <button type="submit" class="btn btn-primary w-100">Login</button>
</form>
<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    if (!this.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.add('was-validated');
        alert('Please fill out the form correctly.');
    }
});
</script>
</div>