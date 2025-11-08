<?php
require_once 'config.php';
requireLogin();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $content = sanitize($_POST['content']);
    $user_id = $_SESSION['user_id'] ?? null;

    // 🔒 Extra safety: check user session
    if (!$user_id) {
        $error = 'Session expired. Please log in again.';
    } elseif (empty($title) || empty($content)) {
        $error = 'Title and content are required.';
    } elseif (strlen($title) < 5) {
        $error = 'Title must be at least 5 characters.';
    } elseif (strlen($content) < 20) {
        $error = 'Content must be at least 20 characters.';
    } else {
        // 🖼 Handle image upload (optional)
        $imageName = null;
        if (isset($_FILES['blog_image']) && $_FILES['blog_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $imageName = handleImageUpload($_FILES['blog_image']);
            if ($imageName === false) {
                $error = 'Failed to upload image. Check file size (max 5MB) and format (JPG, PNG, GIF).';
            }
        }

        if (empty($error)) {
            // ✅ Ensure user still exists in database before inserting
            $check = $conn->prepare("SELECT id FROM user WHERE id = ?");
            $check->bind_param("i", $user_id);
            $check->execute();
            $check->store_result();

            if ($check->num_rows === 0) {
                $error = 'User not found in database. Please log in again.';
            } else {
                // ✅ Insert blog post safely
                $stmt = $conn->prepare("INSERT INTO blogPost (user_id, title, content, image) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isss", $user_id, $title, $content, $imageName);

                if ($stmt->execute()) {
                    $blog_id = $conn->insert_id;
                    header('Location: view.php?id=' . $blog_id);
                    exit();
                } else {
                    $error = 'Failed to create blog. Please try again.';
                    if ($imageName) deleteImage($imageName);
                }
                $stmt->close();
            }
            $check->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Blog - <?php echo SITE_NAME; ?></title>
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
                <a href="index.php" class="btn btn-secondary">Home</a>
                <a href="logout.php" class="btn btn-secondary">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="editor-box">
            <h1>✍️ Create New Blog Post</h1>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data" id="blogForm">
                <!-- Title -->
                <div class="form-group">
                    <label for="title">Blog Title *</label>
                    <input type="text" id="title" name="title"
                        placeholder="Enter an engaging title"
                        value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>"
                        required maxlength="255">
                    <small>Maximum 255 characters</small>
                </div>

                <!-- Image Upload -->
                <div class="form-group">
                    <label for="blog_image" style="font-size: 1.1rem; color: var(--primary);">
                        📸 Add Featured Image (Optional but Recommended)
                    </label>
                    <input type="file" id="blog_image" name="blog_image"
                        accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                        style="font-size: 1rem; padding: 1rem;">
                    <small style="color: var(--primary); font-weight: 500;">
                        ✨ Upload an image to make your blog stand out! (JPG, PNG, GIF - Max 5MB)
                    </small>
                </div>

                <!-- Content -->
                <div class="form-group">
                    <label for="content">Blog Content *</label>
                    <textarea id="content" name="content" rows="15"
                        placeholder="Write your blog content here... Share your thoughts and ideas!"
                        required><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                    <small>Minimum 20 characters</small>
                </div>

                <!-- Buttons -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">📤 Publish Blog</button>
                    <a href="index.php" class="btn btn-secondary">❌ Cancel</a>
                </div>
            </form>

            <!-- Tips Section -->
            <div style="margin-top: 2rem; padding: 1.5rem; background: rgba(255,107,53,0.05); border-left: 4px solid var(--primary); border-radius: 0.5rem;">
                <h3 style="color: var(--primary); margin-bottom: 1rem;">💡 Blogging Tips:</h3>
                <ul style="margin-left: 1.5rem; color: var(--text-light);">
                    <li><strong>Add an image</strong> - Posts with images get 2x more engagement!</li>
                    <li><strong>Catchy title</strong> - Make readers want to click.</li>
                    <li><strong>Clear content</strong> - Use short paragraphs.</li>
                    <li>Be authentic and share your unique perspective.</li>
                </ul>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 <?php echo SITE_NAME; ?> &mdash; <?php echo SITE_TAGLINE; ?></p>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>
