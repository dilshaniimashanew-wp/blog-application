# 🔗 Blog_Chain

**Linking thoughts, one post at a time**

A modern, feature-rich blogging platform built with PHP, MySQL, and vanilla JavaScript. Perfect for beginners and developers who want a clean, functional blog system.

---

## ✨ Features

### Core Features
- 👤 **User Authentication** - Secure registration and login system
- ✍️ **Create & Edit Blogs** - Rich text editor with image upload support
- 🖼️ **Image Management** - Upload and manage blog featured images (max 5MB)
- 🔒 **User Authorization** - Only blog owners can edit/delete their posts
- 📱 **Responsive Design** - Works perfectly on all devices
- 🎨 **Modern UI** - Beautiful gradient design with smooth animations

### Technical Features
- Environment variable support with `.env` file
- Secure password hashing with `password_hash()`
- SQL injection protection with prepared statements
- XSS protection with `htmlspecialchars()`
- Session management and CSRF protection
- Image validation and secure file uploads
- Clean MVC-inspired architecture

---

## 📋 Requirements

- **PHP** 7.4 or higher
- **MySQL** 5.7 or higher
- **Web Server** (Apache/Nginx)
- **Modern Browser** (Chrome, Firefox, Safari, Edge)

---

## 🚀 Installation Guide

### Step 1: Download the Project

1. Download all project files to your computer
2. Extract the files if they're in a ZIP archive

### Step 2: Prepare Your Files

Create the following folder structure:

```
Blog_Chain/
├── config.php
├── .env
├── index.php
├── login.php
├── register.php
├── create.php
├── edit.php
├── view.php
├── delete.php
├── logout.php
├── database.sql
├── css/
│   └── style.css
├── js/
│   └── main.js
└── uploads/
```

**Important:** Create an empty folder named `uploads` - this is where blog images will be stored.

### Step 3: Configure Environment Variables

1. Open the `.env` file in a text editor
2. Update the database configuration with your details:

```env
DB_HOST=your_database_host
DB_USER=your_database_username
DB_PASS=your_database_password
DB_NAME=your_database_name
```

**For InfinityFree users:**
- Login to your InfinityFree control panel
- Go to MySQL Databases section
- Copy your database host, username, password, and database name
- Paste them into the `.env` file

### Step 4: Set Up the Database

#### Option A: Using phpMyAdmin (Recommended for Beginners)

1. Login to your hosting control panel (cPanel/phpMyAdmin)
2. Click on **phpMyAdmin**
3. Select your database from the left sidebar
4. Click on the **SQL** tab at the top
5. Open the `database.sql` file in a text editor
6. Copy all the SQL code
7. Paste it into the SQL query box in phpMyAdmin
8. Click **Go** button to execute
9. You should see a success message

#### Option B: Using Command Line

```bash
mysql -u your_username -p your_database_name < database.sql
```

### Step 5: Upload Files to Your Web Server

#### For InfinityFree/000webhost:

1. Login to your hosting control panel
2. Open **File Manager** or use an FTP client (FileZilla)
3. Navigate to `htdocs` or `public_html` folder
4. Upload all your files EXCEPT the `.env` file (upload it separately)
5. Make sure the `uploads` folder has write permissions (755 or 777)

#### For Local Development (XAMPP/WAMP):

1. Copy the entire project folder
2. Paste it into `htdocs` (XAMPP) or `www` (WAMP) folder
3. Make sure Apache and MySQL are running

### Step 6: Set Folder Permissions

The `uploads` folder needs write permissions:

**Via File Manager:**
1. Right-click on the `uploads` folder
2. Select "Change Permissions" or "Permissions"
3. Set to `755` or `777` (if 755 doesn't work)

**Via FTP:**
1. Right-click on `uploads` folder
2. File Attributes/Permissions
3. Set to `755`

### Step 7: Test Your Installation

1. Open your browser
2. Navigate to: `https://myblogapp.infinityfreeapp.com/` or `http://localhost/Blog_Chain`
3. You should see the Blog_Chain homepage

#### Test Login:

Use the default test account:
- **Username:** `testuser`
- **Password:** `password123`

### Step 8: Create Your Own Account

1. Click **Register** button
2. Fill in the registration form
3. Create your account
4. Login with your new credentials
5. Start creating blogs!

---

## 📁 File Structure Explained

### Core PHP Files

| File | Purpose |
|------|---------|
| `config.php` | Database connection & helper functions |
| `index.php` | Homepage - displays all blog posts |
| `login.php` | User login page |
| `register.php` | User registration page |
| `create.php` | Create new blog post |
| `edit.php` | Edit existing blog post |
| `view.php` | View single blog post |
| `delete.php` | Delete blog post |
| `logout.php` | User logout |

### Configuration Files

| File | Purpose |
|------|---------|
| `.env` | Environment variables (database credentials) |
| `database.sql` | Database schema and sample data |

### Frontend Files

| File | Purpose |
|------|---------|
| `css/style.css` | All styling and responsive design |
| `js/main.js` | Interactive features and validation |

### Folders

| Folder | Purpose |
|--------|---------|
| `uploads/` | Stores uploaded blog images |

---

## 🎯 Usage Guide

### Creating a Blog Post

1. **Login** to your account
2. Click **"✏️ Create Blog"** button in navigation
3. Fill in the form:
   - **Title:** Give your blog a catchy title (minimum 5 characters)
   - **Image:** Upload a featured image (optional, max 5MB)
   - **Content:** Write your blog content (minimum 20 characters)
4. Click **"📤 Publish Blog"** button
5. Your blog is now live!

### Editing a Blog Post

1. Navigate to your blog post
2. Click **"✏️ Edit"** button (only visible if you're the owner)
3. Make your changes
4. You can:
   - Update the title
   - Change the content
   - Replace the image
   - Remove the current image
5. Click **"💾 Update Blog"** to save changes

### Deleting a Blog Post

1. Navigate to your blog post
2. Click **"🗑️ Delete"** button (only visible if you're the owner)
3. Confirm the deletion
4. The blog and its image will be permanently removed

---

## 🔒 Security Features

### Password Security
- Passwords are hashed using PHP's `password_hash()` function
- Uses bcrypt algorithm (industry standard)
- Passwords are never stored in plain text

### SQL Injection Protection
- All database queries use prepared statements
- User input is sanitized before use
- Prevents malicious SQL code execution

### XSS Protection
- All user output is escaped with `htmlspecialchars()`
- Prevents JavaScript injection attacks
- Sanitizes file upload names

### File Upload Security
- Validates file types (only images allowed)
- Checks file size (max 5MB)
- Uses `getimagesize()` to verify real image files
- Generates unique filenames to prevent overwrites
- Stores files outside accessible code

### Session Security
- Uses `session_regenerate_id()` after login
- Proper session destruction on logout
- Session data validation

---

## ⚙️ Configuration Options

### Changing Site Name

Edit `.env` file:
```env
SITE_NAME=Your Blog Name
SITE_TAGLINE=Your custom tagline
```

### Adjusting Upload Limits

Edit `.env` file:
```env
MAX_FILE_SIZE=10485760
# This is 10MB in bytes (10 * 1024 * 1024)
```

### Changing Timezone

Edit `.env` file:
```env
TIMEZONE=America/New_York
# Use PHP timezone identifiers
```

### Production Mode

When going live, set in `.env`:
```env
ENVIRONMENT=production
DISPLAY_ERRORS=0
ERROR_REPORTING=0
```

---

## 🐛 Troubleshooting

### Issue: Can't Upload Images

**Solution:**
1. Check `uploads` folder exists
2. Set folder permissions to `755` or `777`
3. Check `MAX_FILE_SIZE` in `.env`
4. Verify image is under 5MB

### Issue: Database Connection Failed

**Solution:**
1. Check `.env` file has correct credentials
2. Verify database exists
3. Run `database.sql` to create tables
4. Test database connection from hosting panel

### Issue: Blank Page or Errors

**Solution:**
1. Check PHP error logs
2. Set `DISPLAY_ERRORS=1` in `.env`
3. Verify all files uploaded correctly
4. Check file permissions

### Issue: Images Not Displaying

**Solution:**
1. Check `uploads` folder permissions
2. Verify image files exist in `uploads` folder
3. Check browser console for errors
4. Clear browser cache

### Issue: Session/Login Problems

**Solution:**
1. Clear browser cookies
2. Check PHP session directory is writable
3. Verify `session_start()` is being called
4. Check for session conflicts

---

## 🎨 Customization

### Changing Colors

Edit `css/style.css` - look for the `:root` section:

```css
:root {
    --primary: #FF6B35;        /* Main orange color */
    --secondary: #004E89;      /* Blue color */
    --accent: #F7B32B;         /* Yellow accent */
    /* Change these hex codes to your preferred colors */
}
```

### Adding Features

The codebase is well-commented and structured for easy modification:
- **config.php** - Add helper functions here
- **style.css** - Add new styles here
- **main.js** - Add new JavaScript features here

---

## 📝 Database Schema

### User Table
```sql
- id (Primary Key)
- username (Unique)
- email (Unique)
- password (Hashed)
- role (default: 'user')
- created_at (Timestamp)
```

### BlogPost Table
```sql
- id (Primary Key)
- user_id (Foreign Key → user.id)
- title
- content
- image (filename)
- created_at (Timestamp)
- updated_at (Timestamp)
```

---

## 🚀 Going Live Checklist

Before deploying to production:

- [ ] Update `.env` with production database credentials
- [ ] Set `ENVIRONMENT=production` in `.env`
- [ ] Set `DISPLAY_ERRORS=0` in `.env`
- [ ] Remove test accounts from database
- [ ] Test all features thoroughly
- [ ] Set up regular database backups
- [ ] Configure SSL certificate (HTTPS)
- [ ] Set secure folder permissions
- [ ] Remove `database.sql` from web directory
- [ ] Add `.env` to `.gitignore` (if using Git)

---

## 📞 Support

### Common Questions

**Q: Can I use this commercially?**
A: Yes! This is open-source and free to use.

**Q: How do I add categories?**
A: You'll need to create a categories table and modify the blog creation form.

**Q: Can I add comments?**
A: Yes! You'll need to create a comments table and add comment forms.

**Q: How do I backup my data?**
A: Use phpMyAdmin to export your database, and download the `uploads` folder.

---

## 🤝 Contributing

Want to improve Blog_Chain? Here's how:

1. Fork the repository
2. Make your changes
3. Test thoroughly
4. Submit a pull request

---

## 📄 License

This project is free and open-source. Feel free to use it for personal or commercial projects.

---

## 🙏 Credits

- **Design Inspiration:** Modern web design trends
- **Fonts:** Google Fonts (Poppins & Playfair Display)
- **Icons:** Unicode emojis

---

## 📧 Contact

For questions or support:
- Open an issue on GitHub
- Email: your-email@example.com

---

## 🎉 Thank You!

Thank you for using Blog_Chain! If you find it useful, please star the repository and share it with others.

**Happy Blogging! 🔗✨**

---


*Last Updated: November 2025*
