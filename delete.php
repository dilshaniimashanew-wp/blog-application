<?php
/**
 * BlogChain - Delete Blog
 * Deletes blog post and associated image
 */

require_once 'config.php';
requireLogin();

// Get blog ID
$blog_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// First, get the blog to retrieve image filename
$stmt = $conn->prepare("SELECT image FROM blogPost WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $blog_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $blog = $result->fetch_assoc();
    
    // Delete the blog post from database
    $deleteStmt = $conn->prepare("DELETE FROM blogPost WHERE id = ? AND user_id = ?");
    $deleteStmt->bind_param("ii", $blog_id, $_SESSION['user_id']);
    
    if ($deleteStmt->execute() && $deleteStmt->affected_rows > 0) {
        // Blog deleted successfully
        
        // Also delete the associated image file if exists
        if (!empty($blog['image'])) {
            deleteImage($blog['image']);
        }
        
        // Set success message
        $_SESSION['success_message'] = 'Blog post deleted successfully!';
    } else {
        // Failed to delete
        $_SESSION['error_message'] = 'Failed to delete blog post.';
    }
    
    $deleteStmt->close();
} else {
    // Blog not found or user doesn't own it
    $_SESSION['error_message'] = 'Blog not found or you do not have permission to delete it.';
}

$stmt->close();

// Redirect to home page
header('Location: index.php');
exit();
?>