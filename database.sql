-- ============================================
-- BlogChain Database Schema
-- Linking thoughts, one post at a time
-- ============================================

-- ============================================
-- USER TABLE
-- Stores user authentication information
-- ============================================
CREATE TABLE IF NOT EXISTS user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- BLOG POST TABLE
-- Stores all blog posts with image support
-- ============================================
CREATE TABLE IF NOT EXISTS blogPost (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- SAMPLE DATA
-- Test user and blog posts
-- ============================================

-- Insert test user (only if not already existing)
INSERT IGNORE INTO user (id, username, email, password, role) VALUES 
(1, 'testuser', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Insert sample blog posts (linked to test user)
INSERT IGNORE INTO blogPost (id, user_id, title, content, image) VALUES 
(1, 1, 'Welcome to Blog_Chain', 
'Welcome to Blog_Chain - where thoughts connect and ideas flourish! This is your space to share stories, insights, and experiences. We believe that every blog post is a link in the chain of human knowledge and creativity.

In this platform, you can:
- Share your unique perspective
- Connect with like-minded readers
- Express yourself through words and images
- Build a community around your ideas

Start your journey today by creating your first blog post. Add images to make your content more engaging, and watch as your thoughts link with others to form a beautiful chain of ideas!', 
NULL),

(2, 1, 'The Power of Blogging', 
'Blogging has become one of the most powerful forms of self-expression in the digital age. It allows you to share your voice with the world, connect with others who share your interests, and build a lasting digital presence.

Whether you are sharing personal experiences, professional insights, or creative works, blogging gives you a platform to be heard. The beauty of Blog_Chain is that each post contributes to a larger narrative - a chain of interconnected thoughts and ideas.

Remember: Your story matters. Your perspective is unique. Your voice deserves to be heard. Start blogging today and become a link in our ever-growing chain!', 
NULL);

-- ============================================
-- INDEXES FOR PERFORMANCE
-- Speed up common queries
-- ============================================
CREATE INDEX IF NOT EXISTS idx_user_username ON user(username);
CREATE INDEX IF NOT EXISTS idx_user_email ON user(email);
CREATE INDEX IF NOT EXISTS idx_blog_user_id ON blogPost(user_id);
CREATE INDEX IF NOT EXISTS idx_blog_created_at ON blogPost(created_at);

-- ============================================
-- SUCCESS MESSAGE
-- ============================================
SELECT 'BlogChain database created successfully!' AS message;
