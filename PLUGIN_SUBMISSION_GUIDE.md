# WordPress Plugin Submission Guide

## Prerequisites

Before submitting your plugin, ensure it meets these requirements:

### 1. Plugin Structure
- ✅ Main plugin file with proper header comments
- ✅ Proper folder structure
- ✅ No executable files or dangerous code

### 2. Code Quality
- ✅ Follows WordPress Coding Standards
- ✅ Uses proper WordPress functions and hooks
- ✅ Sanitizes and validates all inputs
- ✅ Escapes all outputs
- ✅ No deprecated functions

### 3. Documentation
- ✅ Complete README.md file
- ✅ Proper plugin header in main file
- ✅ GPL-2.0+ license

## Step-by-Step Submission Process

### Step 1: Create WordPress.org Account
1. Go to [wordpress.org](https://wordpress.org/)
2. Click "Get Involved" → "Developers"
3. Create an account or log in
4. Verify your email

### Step 2: Prepare Your Plugin
1. **Choose a unique plugin slug** (e.g., `aura-preloader`)
2. **Update plugin header** in `aura-preloader.php`:
   ```php
   /**
    * Plugin Name: Aura Preloader
    * Plugin URI: https://wordpress.org/plugins/aura-preloader/
    * Description: A customisable full-screen backdrop-blur preloader with your logo, spinner, and progress bar.
    * Version: 1.2.7
    * Author: Irfan Bhat
    * Author URI: https://irfanbhat.com
    * License: GPL-2.0+
    * License URI: https://www.gnu.org/licenses/gpl-2.0.html
    * Text Domain: aura-preloader
    */
   ```

### Step 3: Test Your Plugin
- Test on multiple WordPress versions (5.5+)
- Test with different themes
- Ensure no conflicts with popular plugins
- Run your GitHub Actions CI workflow

### Step 4: Submit for Review
1. Go to [Plugin Submission Form](https://wordpress.org/plugins/developers/add/)
2. Fill out the form:
   - Plugin name
   - Description
   - Upload your plugin zip file
3. Wait for initial review (usually 1-2 weeks)

### Step 5: SVN Repository Setup
Once approved, you'll get access to SVN repository:
```
https://plugins.svn.wordpress.org/aura-preloader/
```

Upload your files using SVN:
```bash
# Create zip without .git folder
zip -r aura-preloader.zip . -x ".git/*"

# Upload to WordPress.org (after approval)
svn co https://plugins.svn.wordpress.org/aura-preloader/
cd aura-preloader
unzip ../aura-preloader.zip
svn add *
svn ci -m "Initial release"
```

## Important Guidelines

### Plugin Naming
- Must be unique
- Cannot contain "WordPress" in slug
- Should be descriptive but not too long

### Licensing
- Must be GPL-2.0+ compatible
- All code must be GPL licensed
- Third-party assets must be compatible

### Security
- No backdoors or malicious code
- Proper input sanitization
- Secure coding practices

### Support
- Provide support through WordPress.org forums
- Respond to user questions
- Keep plugin updated

## Common Rejection Reasons

- Plugin name conflicts
- Missing GPL license
- Security issues
- Poor code quality
- Incomplete documentation
- Non-unique functionality

## Post-Submission

- Monitor reviews and support requests
- Release updates through SVN
- Maintain compatibility with new WordPress versions
- Fix bugs and security issues promptly

## Resources

- [Plugin Developer Handbook](https://developer.wordpress.org/plugins/)
- [Plugin Review Team Guidelines](https://make.wordpress.org/plugins/handbook/plugin-review-team/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/)