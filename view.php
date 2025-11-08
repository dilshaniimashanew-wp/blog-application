<?php
require_once 'config.php';

$blog_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("SELECT b.*, u.username FROM blogPost b 
                        JOIN user u ON b.user_id = u.id 
                        WHERE b.id = ?");
$stmt->bind_param("i", $blog_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: index.php');
    exit();
}

$blog = $result->fetch_assoc();
$is_owner = isLoggedIn() && $_SESSION['user_id'] == $blog['user_id'];

// Clean up line breaks in content
$content = $blog['content'];
// Replace various line break formats with actual line breaks
$content = str_replace(['\r\n', '\n\r', '\n', '\r', '\\r\\n', '\\n'], "\n", $content);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($blog['title']); ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="index.php"><?php echo SITE_NAME; ?></a>
            </div>
            <div class="nav-menu">
                <?php if (isLoggedIn()): ?>
                    <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                    <a href="create.php" class="btn btn-primary">✍️ Create Blog</a>
                    <a href="logout.php" class="btn btn-secondary">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary">Login</a>
                    <a href="register.php" class="btn btn-secondary">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="blog-view">
            <!-- Featured Image with Fixed Size -->
            <?php if (!empty($blog['image']) && file_exists(UPLOAD_DIR . $blog['image'])): ?>
                <div class="blog-featured-image">
                    <img src="<?php echo getImageUrl($blog['image']); ?>" 
                         alt="<?php echo htmlspecialchars($blog['title']); ?>">
                </div>
            <?php endif; ?>
            
            <div class="blog-view-wrapper">
                <h1 class="blog-view-title">
                    <?php echo htmlspecialchars($blog['title']); ?>
                </h1>
                
                <div class="blog-view-meta">
                    <span>👤 By <strong><?php echo htmlspecialchars($blog['username']); ?></strong></span>
                    <span>📅 <?php echo formatDate($blog['created_at']); ?></span>
                    <?php if ($blog['created_at'] != $blog['updated_at']): ?>
                        <span>✏️ Updated <?php echo formatDate($blog['updated_at']); ?></span>
                    <?php endif; ?>
                </div>
                
                <?php if ($is_owner): ?>
                    <div class="blog-actions">
                        <a href="edit.php?id=<?php echo $blog['id']; ?>" class="btn btn-primary">✏️ Edit</a>
                        <a href="delete.php?id=<?php echo $blog['id']; ?>" 
                           class="btn btn-danger" 
                           onclick="return confirm('Are you sure you want to delete this blog?')">🗑️ Delete</a>
                    </div>
                <?php endif; ?>
                
                <!-- Blog Content with Proper Line Breaks -->
                <div class="blog-view-content">
                    <?php 
                    // Display content with line breaks preserved
                    // htmlspecialchars prevents XSS while keeping formatting
                    echo htmlspecialchars($content); 
                    ?>
                </div>
                
                <div class="back-link">
                    <a href="index.php" class="btn btn-secondary">← Back to All Blogs</a>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 <?php echo SITE_NAME; ?> &mdash; <?php echo SITE_TAGLINE; ?></p>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>