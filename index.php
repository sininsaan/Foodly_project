<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foodly - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa; /* Light grey background */
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        .btn-primary {
            background-color: #ff6b6b; /* Warm orange-red for food apps */
            border: none;
        }
        .btn-primary:hover {
            background-color: #ee5253;
        }
    </style>
</head>
<body>

    <div class="login-card card p-4">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-dark">Foodly</h2>
            <p class="text-muted">Digital Canteen Pre-Ordering</p>
        </div>

        <form action="login_process.php" method="POST">
    <div class="mb-3">
        <label class="form-label">Roll Number</label>
        <input type="text" name="roll_no" class="form-control" placeholder="Enter your Roll No (e.g. BCA001)" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
    </div>
    <button type="submit" class="btn btn-dark w-100">Login</button>
</form>

        <div class="text-center mt-3">
            <small>Don't have an account? <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal">Register here</a></small>
        </div>
    </div>

    <div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content p-3">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Student Registration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="register_process.php" method="POST">
                        <div class="mb-3">
                            <input type="text" name="roll_no" class="form-control" placeholder="Roll No (e.g. 8104)" required>
                        </div>
                        <div class="mb-3">
                            <input type="text" name="phone" class="form-control" placeholder="Phone Number" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control" placeholder="College Email" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Create Password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Create Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>