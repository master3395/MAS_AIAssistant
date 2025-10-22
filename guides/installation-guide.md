# Installation Guide

## Requirements

### Server Requirements

- **PHP**: 7.4, 7.5, 8.0, 8.1, 8.2, 8.3, 8.4, 8.5, or 8.6
- **CMSMS**: 2.2.0 or higher
- **Database**: MySQL 5.7+ or MariaDB 10.2+
- **PHP Extensions**:
  - `curl` - API communication
  - `json` - Data processing
  - `openssl` - Encryption
  - `mbstring` - String handling

### Recommended

- **Memory Limit**: 256M+ (for large content generation)
- **Execution Time**: 60+ seconds (for batch operations)
- **HTTPS**: Required for secure API key transmission
- **AlmaLinux**: 8.8, 9.6, or 10 (recommended server OS)
- **CyberPanel**: OpenLiteSpeed or LiteSpeed Enterprise

---

## Installation Methods

### Method 1: XML Module Import (Recommended)

The XML distribution file is the official CMSMS module export format.

1. Download `MAS_AIAssistant-1.0.0.xml` from the [GitHub repository](https://github.com/master3395/MAS_AIAssistant)
2. Log into your CMSMS admin panel
3. Navigate to **Extensions → Module Manager**
4. Click **Install Module**
5. Choose **Import from XML**
6. Browse and select the `MAS_AIAssistant-1.0.0.xml` file
7. Click **Install**
8. Module will auto-create required database tables and folders

### Method 2: ZIP Archive Upload

1. Download the latest release ZIP from [GitHub Releases](https://github.com/master3395/MAS_AIAssistant/releases)
2. Log into your CMSMS admin panel
3. Navigate to **Extensions → Module Manager**
4. Click **Upload Module**
5. Select the downloaded ZIP file
6. Click **Install**
7. Module will auto-create required database tables and folders

### Method 3: Manual Installation

1. Extract the module to your CMSMS modules directory:

   ```bash
   cd /path/to/cmsms/modules/
   unzip MAS_AIAssistant-1.0.0.zip
   ```
2. Set correct permissions:

   ```bash
   # For CyberPanel/LiteSpeed
   chown -R root:root MAS_AIAssistant/
   chmod -R 755 MAS_AIAssistant/
   ```
3. Navigate to **Extensions → Module Manager** in admin
4. Find **MAS AI Assistant** and click **Install**

---

## Post-Installation

After installation:

1. Go to **Extensions → MAS AI Assistant**
2. Navigate to the **Settings** tab
3. Configure your default AI provider (Hugging Face is pre-configured)
4. Optionally add API keys for other providers
5. Adjust rate limiting if needed

---

## Database Tables

The module creates these tables automatically:

**Table: `cms_module_mas_ai_generations`**

```sql
CREATE TABLE cms_module_mas_ai_generations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    type VARCHAR(50),
    provider VARCHAR(50),
    prompt TEXT,
    result TEXT,
    metadata TEXT,
    user_id INT,
    created_date DATETIME,
    INDEX idx_created_date (created_date)
);
```

**Table: `cms_module_mas_ai_usage`**

```sql
CREATE TABLE cms_module_mas_ai_usage (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    provider VARCHAR(50),
    action VARCHAR(100),
    created_timestamp INT,
    INDEX idx_user_timestamp (user_id, created_timestamp)
);
```

---

## Verification

To verify successful installation:

1. Navigate to **Extensions → MAS AI Assistant**
2. Check that the Dashboard tab loads without errors
3. Verify provider status shows correctly
4. Confirm generation stats display (will show 0 initially)
5. Try generating test content with Hugging Face (no API key required)

---

## Troubleshooting Installation

### Module doesn't appear

**Solution:**

```bash
# Check permissions
Extensions → User Management → User Permissions
→ Enable "Use MAS_AIAssistant"

# Clear CMSMS cache
Extensions → Clear Cache
```

### Database errors

**Solution:**

```bash
# Verify MySQL/MariaDB version
mysql --version

# Check user has CREATE TABLE privileges
GRANT CREATE, ALTER, DROP ON cmsms_db.* TO 'cmsms_user'@'localhost';
```

### File permissions issues

**Solution:**

```bash
# For CyberPanel setups
chown -R root:root /path/to/cmsms/modules/MAS_AIAssistant/
chmod -R 755 /path/to/cmsms/modules/MAS_AIAssistant/

# Ensure directories are writable
chmod 755 MAS_AIAssistant/test/
chmod 755 MAS_AIAssistant/to-do/
```

---

## Next Steps

After successful installation:

- Read the [Configuration Guide](configuration-guide.md)
- Set up [API Providers](api-providers-guide.md)
- Check out [Usage Examples](usage-guide.md)
