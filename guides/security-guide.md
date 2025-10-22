# Security Best Practices

Complete security guide for MAS AI Assistant deployment.

---

## API Key Security

### Storage Methods (Most to Least Secure)

1. **config.php with Environment Variables** ⭐ Recommended
2. **config.php with Hardcoded Values**
3. **Encrypted Database Storage**
4. **Plain Text** ❌ Never do this

### config.php Best Practices

```php
// ✅ GOOD: Environment variables
define('MAS_AI_CHATGPT_KEY', getenv('CHATGPT_API_KEY'));

// ✅ ACCEPTABLE: Direct in config (if config.php is secured)
define('MAS_AI_CHATGPT_KEY', 'sk-your-key-here');

// ❌ NEVER: In version control
define('MAS_AI_CHATGPT_KEY', 'sk-actual-key-12345');  // then git commit
```

### Protect config.php

```apache
# In .htaccess or vhost config
<Files "config.php">
    Require all denied
</Files>

# Also block backups
<FilesMatch "config\.php\.(bak|backup|old|~)$">
    Require all denied
</FilesMatch>
```

### Git Ignore Patterns

```gitignore
# .gitignore
config.php
config.php.local
*.key
*.pem
.env
.env.local
logs/*.log
```

---

## Rate Limiting Security

### Why Rate Limiting Matters

1. **Cost Control**: Prevent unexpected API bills
2. **Abuse Prevention**: Stop malicious users
3. **Fair Usage**: Ensure availability for all users
4. **Quota Management**: Stay within provider limits

### Configuration

```php
// High-security production
Requests per Minute: 5
Requests per Hour: 50
Requests per Day: 200

// Balanced production
Requests per Minute: 10
Requests per Hour: 100
Requests per Day: 500

// Development
Requests per Minute: 20
Requests per Hour: 200
Requests per Day: 1000
```

### Monitor Usage

Check audit logs regularly:

1. **Extensions → MAS AI Assistant → Content History**
2. Filter by date range
3. Look for unusual patterns:
   - Same user, many requests
   - Failed generations (potential attacks)
   - Off-hours activity

---

## File System Security

### Directory Permissions

```bash
# Module directory
chmod 755 /path/to/MAS_AIAssistant/
chown newst3922:newst3922 /path/to/MAS_AIAssistant/

# Subdirectories
find MAS_AIAssistant/ -type d -exec chmod 755 {} \;
find MAS_AIAssistant/ -type f -exec chmod 644 {} \;

# Writable directories
chmod 755 MAS_AIAssistant/test/
chmod 755 MAS_AIAssistant/to-do/

# Protected directories (should NOT be writable by web server)
chmod 755 MAS_AIAssistant/lib/
chmod 755 MAS_AIAssistant/providers/
```

### .htaccess Protection

The module includes comprehensive `.htaccess`:

```apache
# Blocks access to:
✅ /lib/ directory (contains classes)
✅ /providers/ directory (API integrations)
✅ /integrations/ directory  
✅ /test/ directory (test files)
✅ /to-do/ directory (documentation)
✅ All PHP files except action.*.php and function.*.php
✅ *.log, *.sql, *.bak, *.backup, *.key, *.pem files
✅ Hidden files (.*) 

# Adds security headers:
✅ X-Content-Type-Options: nosniff
✅ X-Frame-Options: SAMEORIGIN
✅ X-XSS-Protection: 1; mode=block
```

### Test .htaccess Protection

```bash
# Try accessing protected directories (should return 403):
curl -I https://yoursite.com/modules/MAS_AIAssistant/lib/
curl -I https://yoursite.com/modules/MAS_AIAssistant/providers/
curl -I https://yoursite.com/modules/MAS_AIAssistant/.htaccess

# All should return: HTTP/1.1 403 Forbidden
```

### LiteSpeed Users

Restart after `.htaccess` changes:

```bash
# LiteSpeed Enterprise
systemctl restart lsws

# OpenLiteSpeed (automatic)
# Changes apply automatically, but verify by testing
```

---

## Database Security

### Encryption

API keys in database are encrypted with:
- **Algorithm**: AES-256-CBC
- **Key**: 64-character hex (auto-generated)
- **IV**: 32-character hex (auto-generated)
- **Storage**: Base64-encoded ciphertext

### Verify Encryption

```sql
-- Check that keys are encrypted (should look like random base64)
SELECT preference, value 
FROM cms_module_preferences 
WHERE module = 'MAS_AIAssistant' 
  AND preference LIKE 'api_key_%';

-- Example encrypted value:
-- value: 'SGVsbG8gV29ybGQhIFRoaXMgaXMgZW5jcnlwdGVkIQ=='
```

### Database Backups

When backing up:

```bash
# ✅ GOOD: Encrypt backup
mysqldump -u user -p cmsms_db | gpg -c > backup.sql.gpg

# ❌ BAD: Plain text backup with keys
mysqldump -u user -p cmsms_db > backup.sql  # Contains encrypted keys

# ⚠️  CAUTION: Backup encryption keys separately
mysqldump -u user -p cmsms_db cms_module_preferences \
  --where="preference IN ('encryption_key', 'encryption_iv')" \
  > encryption_keys_backup.sql  # Store securely offline!
```

### Database User Permissions

Restrict database user:

```sql
-- Create limited user for CMSMS
CREATE USER 'cmsms_app'@'localhost' IDENTIFIED BY 'strong_password';

-- Grant only necessary permissions
GRANT SELECT, INSERT, UPDATE, DELETE 
  ON cmsms_db.* 
  TO 'cmsms_app'@'localhost';

-- Do NOT grant:
-- DROP, CREATE, ALTER (unless needed for upgrades)
-- FILE (dangerous - can read/write files)
-- SUPER (unnecessary)
```

---

## Input Validation

### Automatic Sanitization

All inputs are automatically sanitized:

**Content Generation:**
```php
// Prompt sanitization (automatic)
- Strip HTML tags
- Remove excessive whitespace
- Limit length to 10,000 characters
- Escape special characters
```

**Provider Validation:**
```php
// Only allowed providers accepted
Valid: huggingface, chatgpt, claude, gemini, groq, mistral, perplexity, cohere
Invalid: Rejected with error
```

**CSRF Protection:**
```php
// All forms include CSRF tokens
// Validated on submission
```

### Manual Validation

For custom integrations:

```php
// Always validate user input
$this->LoadClass('SecurityHelper');
$security = new SecurityHelper($this);

$safe_input = $security->SanitizeInput($user_input, 'string');
$safe_html = $security->SanitizeInput($user_content, 'html');
```

---

## Network Security

### HTTPS Only

**Always use HTTPS** for:
- Admin panel access
- API key transmission
- Generated content viewing

```apache
# Force HTTPS in .htaccess
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}/$1 [R=301,L]
```

### API Communication

All provider communication uses:
- **HTTPS/TLS**: Encrypted transport
- **SSL Verification**: Enabled by default
- **Timeouts**: 60 seconds max
- **Error Handling**: No sensitive data in errors

---

## Access Control

### CMSMS Permissions

Restrict module access:

1. **Extensions → Users & Groups → User Permissions**
2. Find **Use MAS_AIAssistant**
3. Assign only to trusted users/groups

### Multi-User Security

For multi-user sites:

```php
// Each user's generations are tracked
// User ID stored in generations table
// Audit log shows who did what

// View user activity:
SELECT user_id, COUNT(*) as total 
FROM cms_module_mas_ai_generations 
GROUP BY user_id;
```

---

## Audit Logging

### What Gets Logged

- ✅ All content generations
- ✅ Design generations
- ✅ Batch operations
- ✅ Settings changes
- ✅ Provider configuration
- ✅ Failed attempts
- ✅ Rate limit violations

### View Audit Log

1. **Extensions → System Admin**
2. **Audit Log**
3. Filter by: "MAS AI Assistant"

### Log Monitoring

Regular checks:

```bash
# Weekly: Check for anomalies
# Look for:
- Unusual activity times (2-5 AM)
- High failure rates
- Same IP many requests
- Rapid succession requests
```

---

## Provider-Specific Security

### API Key Permissions

Limit what keys can do:

**OpenAI:**
1. Dashboard → API Keys
2. Set permissions: "Restricted"
3. Enable only: Chat, Completions
4. Disable: Fine-tuning, Files, etc.

**Anthropic:**
1. Console → API Keys
2. Set rate limits on key itself
3. Monitor usage dashboard

**Google Gemini:**
1. Cloud Console → Credentials
2. Restrict key to specific IPs (if possible)
3. Set quotas

### Monitor Provider Usage

Check provider dashboards weekly:

- **OpenAI**: https://platform.openai.com/usage
- **Anthropic**: https://console.anthropic.com/settings/usage
- **Google**: https://console.cloud.google.com/apis/dashboard

### Set Billing Alerts

**In each provider:**
1. Set monthly budget limit
2. Enable email alerts at 50%, 75%, 90%
3. Consider hard limits

---

## Incident Response

### If API Key Is Compromised

1. **Immediately revoke** the key in provider dashboard
2. **Generate new key**
3. **Update config.php** or database
4. **Check audit logs** for unauthorized usage
5. **Review bills** in provider dashboard
6. **Change database passwords** if breach was database-related

### If Database Is Compromised

1. **Rotate encryption keys**:
   ```php
   // In CMSMS admin
   $this->RemovePreference('encryption_key');
   $this->RemovePreference('encryption_iv');
   // Module will auto-generate new keys
   ```

2. **Re-encrypt all API keys**:
   - Remove old keys from preferences
   - Re-enter keys in Settings

3. **Change database passwords**

4. **Review user permissions**

### Emergency Disable

Quickly disable module:

```bash
# Method 1: Rename module directory
mv MAS_AIAssistant MAS_AIAssistant.disabled

# Method 2: Remove from database
# DELETE FROM cms_modules WHERE name = 'MAS_AIAssistant';
# (Not recommended - can break things)

# Method 3: Revoke all API keys
# Just delete them from config.php or database
```

---

## Compliance & Privacy

### GDPR Considerations

**Data Stored:**
- User ID (CMSMS user)
- Generated content
- Prompts/inputs
- Timestamps
- Provider used

**User Rights:**
- Right to access: Content History shows user's data
- Right to erasure: Delete from Content History
- Right to portability: Export as JSON

### Provider Data Handling

**Be aware:**
- Prompts sent to AI providers
- Providers may use data for training (check ToS)
- Consider anonymizing sensitive data
- Review each provider's privacy policy

### Recommendations

1. **Don't send PII** in prompts
2. **Review provider privacy policies**
3. **Use EU-based providers** for GDPR (Mistral AI)
4. **Implement data retention policy**
5. **Document data flows**

---

## Regular Security Maintenance

### Weekly

- [ ] Check audit logs for anomalies
- [ ] Review rate limit violations
- [ ] Monitor provider dashboards

### Monthly

- [ ] Review API key usage and costs
- [ ] Check for module updates
- [ ] Verify .htaccess still working
- [ ] Test backup and restore

### Quarterly

- [ ] Rotate API keys
- [ ] Review user permissions
- [ ] Audit security configurations
- [ ] Update documentation

### Annually

- [ ] Full security audit
- [ ] Penetration testing (if applicable)
- [ ] Review compliance requirements
- [ ] Update incident response procedures

---

## Security Checklist

### Initial Setup
- [ ] API keys stored in config.php (not database)
- [ ] config.php protected by .htaccess
- [ ] HTTPS enabled sitewide
- [ ] Rate limiting configured
- [ ] File permissions set correctly
- [ ] .htaccess protection verified
- [ ] Only necessary users have access
- [ ] Audit logging enabled

### Ongoing
- [ ] Monitor usage weekly
- [ ] Review logs monthly
- [ ] Rotate keys quarterly
- [ ] Test backups quarterly
- [ ] Update module when available
- [ ] Check provider security bulletins

---

## Resources

### Security Tools

- **OWASP ZAP**: Web application scanner
- **Sucuri**: Security scanning and hardening
- **Wordfence**: WordPress security (similar principles apply)
- **Let's Encrypt**: Free HTTPS certificates

### Provider Security Docs

- [OpenAI Security Best Practices](https://platform.openai.com/docs/guides/safety-best-practices)
- [Anthropic Security](https://www.anthropic.com/security)
- [Google Cloud Security](https://cloud.google.com/security)

---

## Next Steps

- Review [Configuration Guide](configuration-guide.md)
- Check [Troubleshooting Guide](troubleshooting-guide.md)
- Set up [API Providers](api-providers-guide.md)

