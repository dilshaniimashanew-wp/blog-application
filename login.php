<?php
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'All fields are required';
    } else {
        $stmt = $conn->prepare("SELECT id, username, password FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                session_regenerate_id(true);
                header('Location: index.php');
                exit();
            } else {
                $error = 'Invalid username or password';
            }
        } else {
            $error = 'Invalid username or password';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-box">
            <!-- Branding -->
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="font-size: 3.5rem; animation: swing 2s ease-in-out infinite;">🔗</div>
                <h1 style="margin-top: 0.5rem;"><?php echo SITE_NAME; ?></h1>
                <p style="color: var(--text-muted); font-size: 1rem; margin-top: -0.5rem;">
                    <?php echo SITE_TAGLINE; ?>
                </p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username *</label>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           placeholder="Enter your username"
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                           required 
                           autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           placeholder="Enter your password"
                           required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; font-size: 1.05rem; padding: 1rem;">
                    🔓 Login to Your Account
                </button>
            </form>
            
            <p class="auth-link">
                Don't have an account? 
                <a href="register.php">Create one here</a>
            </p>
            
            <!-- Test Account -->
            <div class="test-account-info">
                <strong>🧪 Test Account Available</strong>
                <p>Username: <code>testuser</code></p>
                <p>Password: <code>password123</code></p>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 <?php echo SITE_NAME; ?> &mdash; <?php echo SITE_TAGLINE; ?></p>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>