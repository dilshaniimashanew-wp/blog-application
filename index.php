<?php
require_once 'config.php';

$query = "SELECT b.*, u.username FROM blogPost b 
          JOIN user u ON b.user_id = u.id 
          ORDER BY b.created_at DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Linking thoughts, one post at a time</title>
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
        <h1 class="page-title">Latest Blog Posts</h1>
        
        <?php if ($result && $result->num_rows > 0): ?>
            <div class="blog-grid">
                <?php while ($blog = $result->fetch_assoc()): ?>
                    <div class="blog-card">
                        <div class="blog-image">
                            <?php if (!empty($blog['image']) && file_exists(UPLOAD_DIR . $blog['image'])): ?>
                                <img src="<?php echo getImageUrl($blog['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($blog['title']); ?>">
                            <?php else: ?>
                                <div class="placeholder">📝</div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="blog-content">
                            <h2 class="blog-title">
                                <a href="view.php?id=<?php echo $blog['id']; ?>">
                                    <?php echo htmlspecialchars($blog['title']); ?>
                                </a>
                            </h2>
                            
                            <div class="blog-meta">
                                <span>👤 <?php echo htmlspecialchars($blog['username']); ?></span>
                                <span>📅 <?php echo formatDate($blog['created_at']); ?></span>
                            </div>
                            
                            <div class="blog-excerpt">
                                <?php 
                                // Clean content for excerpt display
                                $clean_content = str_replace(['\r\n', '\n\r', '\n', '\r', '\\r\\n', '\\n'], ' ', $blog['content']);
                                $clean_content = strip_tags($clean_content);
                                echo htmlspecialchars($clean_content); 
                                ?>
                            </div>
                            
                            <a href="view.php?id=<?php echo $blog['id']; ?>" class="btn btn-link">
                                Read More →
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>📝 No blogs yet. Be the first to share your thoughts!</p>
                <?php if (isLoggedIn()): ?>
                    <a href="create.php" class="btn btn-primary">Create First Blog</a>
                <?php else: ?>
                    <p>Please <a href="login.php" style="color: var(--primary); font-weight: 600;">login</a> to create a blog.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2025 <?php echo SITE_NAME; ?> &mdash; <?php echo SITE_TAGLINE; ?></p>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>