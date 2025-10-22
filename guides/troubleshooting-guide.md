# Troubleshooting Guide

Common issues and solutions for MAS AI Assistant.

---

## Installation Issues

### Module Not Visible in Module Manager

**Solution:**

```bash
# Check file permissions
ls -la /path/to/cmsms/modules/MAS_AIAssistant/
# Should show: drwxr-xr-x root root

# Fix permissions
chown -R root:root MAS_AIAssistant/
chmod -R 755 MAS_AIAssistant/

# Clear CMSMS cache
Extensions → Clear Cache
```

### Installation Fails with Database Error

**Solution:**

```bash
# Check MySQL/MariaDB version
mysql --version
# Should be 5.7+ or MariaDB 10.2+

# Verify user has privileges
GRANT CREATE, ALTER, DROP, INDEX ON cmsms_db.* TO 'cmsms_user'@'localhost';
FLUSH PRIVILEGES;

# Check table doesn't already exist
SHOW TABLES LIKE '%mas_ai%';
# If exists from failed install, drop and retry
```

### Permission Denied Error

**Solution:**

```bash
# User lacks module permission
Extensions → Users & Groups → User Permissions
→ Find "Use MAS_AIAssistant"
→ Check the box for your user/group
→ Save
```

---

## API & Generation Issues

### "Rate Limit Exceeded" Error

**Cause:** Too many requests in short time

**Solutions:**

1. **Wait 60 seconds** and try again
2. **Increase limits** in Settings tab
3. **Check audit log** for unusual activity:
   ```
   Extensions → MAS AI Assistant → Content History
   Filter by last hour
   Look for excessive requests
   ```
4. **Switch provider** temporarily

### "Invalid API Key" Error

**Cause:** API key incorrect, expired, or improperly formatted

**Solutions:**

1. **Verify key format**:

   - OpenAI: starts with `sk-`
   - Anthropic: starts with `sk-ant-`
   - Groq: starts with `gsk_`
   - Gemini: alphanumeric string
   - No extra spaces or quotes
2. **Check provider dashboard**:

   - Is key active?
   - Has it expired?
   - Are there usage limits?
3. **Re-enter key**:

   - Copy from provider dashboard
   - Paste in Settings (avoid double-clicking)
   - Save and test
4. **Check config.php override**:

   ```php
   // If key in config.php, it overrides database
   // Make sure config.php key is correct
   ```

### "Generation Failed" Error

**Causes & Solutions:**

**1. Network/Connectivity:**

```bash
# Test provider connectivity
curl -I https://api.openai.com/v1/
curl -I https://api.anthropic.com/v1/
curl -I https://generativelanguage.googleapis.com/

# Check PHP cURL
php -m | grep curl
# If missing: install php-curl
```

**2. Timeout:**

```ini
# Increase PHP timeout
max_execution_time = 60

# For CyberPanel:
Websites → Manage → PHP → Edit PHP Configs
→ max_execution_time = 60
```

**3. Provider Rate Limit:**

```
Check provider dashboard for:
- Exceeded quota
- Billing issues
- Service outage
```

**4. Content Policy Violation:**

```
Provider rejected prompt
Try different wording
Remove potentially sensitive content
```

### Empty or Incomplete Content

**Solutions:**

1. **Increase max tokens**:

   ```php
   // In provider config (future feature)
   // Or request shorter content
   ```
2. **Try different provider**:

   - Switch from Hugging Face to GPT-3.5
   - Some models produce better results
3. **Rephrase prompt**:

   - Be more specific
   - Add more context
   - Include examples
4. **Check model status**:

   - Hugging Face models may be loading
   - Try again in 30 seconds

---

## Configuration Issues

### Settings Not Saving

**Solutions:**

1. **Check session**:

   ```php
   // Verify sessions work
   <?php
   session_start();
   $_SESSION['test'] = 'working';
   echo $_SESSION['test'];
   ?>
   ```
2. **Clear browser cache**:

   - Ctrl+F5 to hard refresh
   - Clear cookies for site
3. **Check file permissions**:

   ```bash
   # Database connection must work
   # Check CMSMS config.php database settings
   ```

### Default Provider Won't Change

**Solutions:**

1. **Clear module cache**:

   ```
   Extensions → Clear Cache
   ```
2. **Check preferences table**:

   ```sql
   SELECT * FROM cms_module_preferences 
   WHERE module = 'MAS_AIAssistant' 
   AND preference = 'default_provider';

   -- Update manually if needed:
   UPDATE cms_module_preferences 
   SET value = 'chatgpt' 
   WHERE module = 'MAS_AIAssistant' 
   AND preference = 'default_provider';
   ```

### .htaccess Not Working

**For Apache/LiteSpeed:**

```bash
# Verify AllowOverride is enabled
# Check vhost config file
AllowOverride All

# For LiteSpeed Enterprise, restart
systemctl restart lsws

# Test protection
curl -I https://yoursite.com/modules/MAS_AIAssistant/lib/
# Should return: 403 Forbidden
```

**For Nginx:**

.htaccess doesn't work on Nginx. Convert rules:

```nginx
# In nginx vhost config
location ~* /modules/MAS_AIAssistant/(lib|providers|integrations|test|to-do)/ {
    deny all;
    return 403;
}

location ~* \.(log|sql|bak|backup|key|pem)$ {
    deny all;
    return 403;
}

# Reload nginx
nginx -t && nginx -s reload
```

---

## Content Quality Issues

### Poor Quality Output

**Improve Results:**

1. **Be more specific**:

   ```
   ❌ Bad: "Write about solar energy"
   ✅ Good: "Write a 500-word beginner's guide to residential solar panel installation, covering costs, ROI, and installation process"
   ```
2. **Add keywords**:

   ```
   Keywords: solar panels, installation cost, energy savings, ROI
   ```
3. **Try better provider**:

   - Hugging Face → GPT-3.5
   - GPT-3.5 → GPT-4
   - GPT-4 → Claude Opus
4. **Increase word count**:

   - More context = better quality
   - 500+ words recommended
5. **Regenerate**:

   - AI has randomness
   - Try 2-3 times

### Content Not Formatted Properly

**Solutions:**

1. **Check content type**:

   - Use "Blog Post" for structured content
   - Use "Page Content" for simpler content
2. **Post-process**:

   ```php
   // Module auto-processes HTML
   // If issues persist, manually format
   ```
3. **Try different provider**:

   - Claude better at HTML structure
   - GPT-4 better at complex formatting

### Incorrect or Outdated Information

**Cause:** AI models have training cutoff dates

**Solutions:**

1. **Use Perplexity AI**:

   - Has real-time web search
   - Gets current information
2. **Provide context in prompt**:

   ```
   "Write about solar energy in 2025, considering recent advancements in perovskite cells and current market prices"
   ```
3. **Fact-check important claims**
4. **Add disclaimer** for time-sensitive content

---

## Performance Issues

### Slow Generation Times

**Solutions:**

1. **Use faster providers**:

   - **Groq**: Ultra-fast (recommended)
   - **GPT-3.5 Turbo**: Very fast
   - **Claude Haiku**: Fast and affordable
2. **Reduce word count**:

   - 500 words faster than 2000 words
   - Generate in chunks if needed
3. **Check server load**:

   ```bash
   top
   # Look for high CPU/memory usage
   ```
4. **Verify network speed**:

   ```bash
   ping -c 5 api.openai.com
   curl -o /dev/null -s -w '%{time_total}\n' https://api.openai.com/
   ```

### Timeout Errors

**Solutions:**

1. **Increase PHP timeout**:

   ```ini
   max_execution_time = 90
   ```
2. **Increase cURL timeout** (code-level, future enhancement)
3. **Use simpler prompts**
4. **Try different provider**

---

## Dashboard Issues

### Dashboard Showing Errors

**Solutions:**

1. **Check database tables exist**:

   ```sql
   SHOW TABLES LIKE '%mas_ai%';
   -- Should show:
   -- cms_module_mas_ai_generations
   -- cms_module_mas_ai_usage
   ```
2. **Reinstall module**:

   ```
   Extensions → Module Manager
   → MAS AI Assistant
   → Uninstall (backup data first!)
   → Reinstall
   ```
3. **Check PHP error log**:

   ```bash
   tail -f /var/log/php_errors.log
   # Or check in CyberPanel
   ```

### Stats Not Updating

**Solutions:**

1. **Clear cache**:

   ```
   Extensions → Clear Cache
   ```
2. **Check generation table**:

   ```sql
   SELECT COUNT(*) FROM cms_module_mas_ai_generations;
   -- Should match stats
   ```
3. **Hard refresh browser**: Ctrl+F5

---

## Custom Provider Issues

### Custom Provider Connection Failed

**Solutions:**

1. **Verify endpoint URL**:

   ```bash
   curl -I https://your-api-endpoint.com/v1/chat/completions
   # Should return 200 or 401, not 404
   ```
2. **Check API key format**:

   - Does provider use Bearer token?
   - Different header name?
3. **Test with cURL**:

   ```bash
   curl -X POST https://your-api.com/endpoint \
     -H "Authorization: Bearer YOUR_KEY" \
     -H "Content-Type: application/json" \
     -d '{"model":"test","messages":[{"role":"user","content":"test"}]}'
   ```
4. **Check response format**:

   - Verify response path in settings
   - Common: `choices.0.message.content`
   - Or: `content.0.text` for Claude-like

### Custom Provider Returns Empty

**Solutions:**

1. **Check response path**:

   ```
   If response is: {"data": {"result": "text here"}}
   Response path should be: data.result
   ```
2. **Enable request logging** (future feature)
3. **Use provider documentation** to verify format

---

## Batch Generation Issues

### Batch Fails Partway Through

**Cause:** Rate limit or timeout

**Solutions:**

1. **Reduce batch size**: Try 5-10 instead of 20
2. **Increase delays** between requests
3. **Use faster provider** (Groq)
4. **Check provider rate limits**

### Some Items Fail, Others Succeed

**Normal behavior** - check individual errors:

1. View batch results
2. Read error messages for failed items
3. Address specific issues
4. Re-run failed items individually

---

## Database Issues

### Table Not Found Error

**Solution:**

```sql
-- Manually create tables if installer failed
-- Copy SQL from installation-guide.md
-- Run in MySQL/phpMyAdmin
```

### Slow Query Performance

**Solution:**

```sql
-- Check indexes exist
SHOW INDEX FROM cms_module_mas_ai_generations;

-- Add missing indexes
CREATE INDEX idx_created_date ON cms_module_mas_ai_generations(created_date);
CREATE INDEX idx_user_timestamp ON cms_module_mas_ai_usage(user_id, created_timestamp);

-- Optimize tables
OPTIMIZE TABLE cms_module_mas_ai_generations;
OPTIMIZE TABLE cms_module_mas_ai_usage;
```

---

## Error Messages Reference

### Common Errors

| Error Message         | Cause               | Solution                       |
| --------------------- | ------------------- | ------------------------------ |
| "Access denied"       | No permission       | Grant user permission in CMSMS |
| "Rate limit exceeded" | Too many requests   | Wait or increase limits        |
| "Invalid provider"    | Wrong provider name | Check provider list            |
| "API key required"    | Missing key         | Add key in Settings            |
| "Generation failed"   | Various             | Check specific error message   |
| "Topic required"      | Empty input         | Enter topic/description        |
| "Connection timeout"  | Network/slow API    | Increase timeout, retry        |

---

## Getting Help

If issues persist:

1. **Check module logs**:

   ```
   Content History → Filter by recent
   Look for error patterns
   ```
2. **Enable debug mode** (if available)
3. **Contact support**:

   - Discord: [Join server](https://discord.gg/nx9Kzrk)
   - Email: info [at] newstargeted [dot] com
   - GitHub: [Open issue](https://github.com/master3395/MAS_AIAssistant/issues)
4. **Provide details**:

   - PHP version
   - CMSMS version
   - Module version
   - Provider used
   - Error message
   - Steps to reproduce

---

## Advanced Troubleshooting

### Enable PHP Error Display

**Temporarily**, for debugging:

```php
// At top of action file
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

**Remember to disable** after debugging!

### Check cURL Configuration

```bash
# Test cURL
php -r "echo (function_exists('curl_version') ? 'cURL enabled' : 'cURL disabled');"

# Check cURL version
php -r "print_r(curl_version());"

# Test SSL
curl -I https://api.openai.com/v1/models
```

### Database Connection Test

```php
// In CMSMS admin
$db = cmsms()->GetDb();
$result = $db->Execute("SELECT 1");
var_dump($result);
```

### Module Files Integrity

```bash
# Verify all files present
ls -la MAS_AIAssistant/
# Should show:
# - lib/ directory
# - providers/ directory
# - templates/ directory
# - action.*.php files
# - MAS_AIAssistant.module.php
```

---

## Known Issues

### Issue: Cursor AI Provider

**Status:** Placeholder implementation
**Workaround:** Uses Claude as fallback
**Future:** Will integrate when Cursor releases public API

### Issue: Very Long Content May Timeout

**Workaround:**

- Reduce word count
- Generate in chunks
- Increase PHP timeout
- Use faster provider

---

## Diagnostic Checklist

Run through this checklist when troubleshooting:

- [ ] Module installed and activated
- [ ] User has "Use MAS_AIAssistant" permission
- [ ] Database tables exist
- [ ] API key configured (if not using Hugging Face)
- [ ] Internet connection working
- [ ] PHP extensions (curl, json, openssl) installed
- [ ] .htaccess not blocking required files
- [ ] Rate limits not exceeded
- [ ] PHP error log checked
- [ ] CMSMS cache cleared
- [ ] Provider service status checked

---

## Next Steps

- Review [Security Guide](security-guide.md)
- Check [Configuration Guide](configuration-guide.md)
- Read [API Providers Guide](api-providers-guide.md)
