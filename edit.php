<?php
require_once 'config.php';
requireLogin();

$blog_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = '';

$stmt = $conn->prepare("SELECT * FROM blogPost WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $blog_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: index.php');
    exit();
}

$blog = $result->fetch_assoc();

// Clean up content for display in textarea
$content_display = str_replace(['\r\n', '\\r\\n', '\\n'], "\n", $blog['content']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $content = sanitize($_POST['content']);
    $remove_image = isset($_POST['remove_image']);
    
    if (empty($title) || empty($content)) {
        $error = 'All fields are required';
    } elseif (strlen($title) < 5) {
        $error = 'Title must be at least 5 characters';
    } elseif (strlen($content) < 20) {
        $error = 'Content must be at least 20 characters';
    } else {
        $imageName = $blog['image'];
        
        if ($remove_image && !empty($blog['image'])) {
            deleteImage($blog['image']);
            $imageName = null;
        }
        
        if (isset($_FILES['blog_image']) && $_FILES['blog_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $newImage = handleImageUpload($_FILES['blog_image']);
            if ($newImage === false) {
                $error = 'Failed to upload image. Check size (max 5MB) and format';
            } else {
                if (!empty($blog['image'])) deleteImage($blog['image']);
                $imageName = $newImage;
            }
        }
        
        if (empty($error)) {
            $stmt = $conn->prepare("UPDATE blogPost SET title = ?, content = ?, image = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("sssii", $title, $content, $imageName, $blog_id, $_SESSION['user_id']);
            
            if ($stmt->execute()) {
                header('Location: view.php?id=' . $blog_id);
                exit();
            } else {
                $error = 'Failed to update blog';
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="index.php"><?php echo SITE_NAME; ?></a>
            </div>
            <div class="nav-menu">
                <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                <a href="index.php" class="btn btn-secondary">🏠 Home</a>
                <a href="logout.php" class="btn btn-secondary">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="editor-box">
            <h1>✏️ Edit Blog Post</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" enctype="multipart/form-data">
                <!-- Title -->
                <div class="form-group">
                    <label for="title">Blog Title *</label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="<?php echo htmlspecialchars($blog['title']); ?>" 
                           required 
                           maxlength="255"
                           placeholder="Enter blog title">
                    <small>Maximum 255 characters</small>
                </div>
                
                <!-- Current Image -->
                <?php if (!empty($blog['image']) && file_exists(UPLOAD_DIR . $blog['image'])): ?>
                    <div class="form-group">
                        <label>Current Featured Image</label>
                        <div class="current-image-preview">
                            <img src="<?php echo getImageUrl($blog['image']); ?>" 
                                 alt="Current blog image">
                        </div>
                        <div class="checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="remove_image" value="1">
                                <span>🗑️ Remove current image</span>
                            </label>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- New Image Upload -->
                <div class="form-group">
                    <label for="blog_image" style="font-size: 1.05rem; color: var(--primary);">
                        📸 <?php echo !empty($blog['image']) ? 'Replace Image' : 'Add Image'; ?> (Optional)
                    </label>
                    <input type="file" 
                           id="blog_image" 
                           name="blog_image" 
                           accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                           class="file-input">
                    <small>JPG, PNG, GIF, WebP - Max 5MB</small>
                </div>
                
                <!-- Content -->
                <div class="form-group">
                    <label for="content">Blog Content *</label>
                    <textarea id="content" 
                              name="content" 
                              rows="15" 
                              required 
                              placeholder="Write your blog content here..."><?php echo htmlspecialchars($content_display); ?></textarea>
                    <small>Minimum 20 characters. Press Enter twice for paragraph breaks.</small>
                </div>
                
                <!-- Blog Info -->
                <div class="blog-info-box">
                    <p><strong>📅 Created:</strong> <?php echo date('F d, Y \a\t H:i', strtotime($blog['created_at'])); ?></p>
                    <p><strong>🔄 Last Updated:</strong> <?php echo date('F d, Y \a\t H:i', strtotime($blog['updated_at'])); ?></p>
                </div>
                
                <!-- Buttons -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        💾 Update Blog
                    </button>
                    <a href="view.php?id=<?php echo $blog_id; ?>" class="btn btn-secondary">
                        ❌ Cancel
                    </a>
                </div>
            </form>
            
            <!-- Warning -->
            <div class="warning-message">
                <p><strong>⚠️ Note:</strong> Changes will be saved immediately. Make sure to review before updating.</p>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 <?php echo SITE_NAME; ?> &mdash; <?php echo SITE_TAGLINE; ?></p>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>