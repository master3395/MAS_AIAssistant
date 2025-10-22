# Configuration Guide

Complete guide to configuring MAS AI Assistant for optimal performance.

---

## Initial Setup

After installation, configure these essential settings:

1. **Default Provider** - Choose your preferred AI service
2. **API Keys** - Add credentials for paid providers
3. **Rate Limiting** - Protect against abuse and control costs
4. **Storage Method** - Choose where to store API keys

---

## API Key Storage

### Option 1: Database Storage (Default)

Keys are encrypted using AES-256-CBC.

**Setup:**

1. Navigate to **Settings** tab
2. Enter API keys in the form
3. Click **Save Settings**
4. Keys are automatically encrypted

**Pros:**

- Easy to manage through UI
- No file editing required
- Keys are encrypted
- Portable across environments

**Cons:**

- Less secure than config.php
- Accessible through database backups
- Requires database access controls

### Option 2: config.php Storage (Recommended for Production)

Store keys in your CMSMS config.php file.

**Setup:**

1. Edit `/config.php` in your CMSMS root
2. Add these lines after existing config:

```php
// MAS AI Assistant API Keys
define('MAS_AI_CHATGPT_KEY', 'sk-your-openai-key-here');
define('MAS_AI_CLAUDE_KEY', 'sk-ant-your-anthropic-key-here');
define('MAS_AI_GEMINI_KEY', 'your-google-gemini-key-here');
define('MAS_AI_GROQ_KEY', 'gsk_your-groq-key-here');
define('MAS_AI_MISTRAL_KEY', 'your-mistral-key-here');
define('MAS_AI_PERPLEXITY_KEY', 'pplx-your-perplexity-key-here');
define('MAS_AI_COHERE_KEY', 'your-cohere-key-here');
```

3. Save the file
4. config.php keys take precedence over database

**Pros:**

- Maximum security
- Keys outside database
- Version control friendly (with .gitignore)
- Environment variable support

**Cons:**

- Requires file system access
- Manual editing needed
- Less portable

### Option 3: Environment Variables (Advanced)

Use environment variables for containerized deployments.

```php
// In config.php
define('MAS_AI_CHATGPT_KEY', getenv('CHATGPT_API_KEY'));
define('MAS_AI_CLAUDE_KEY', getenv('CLAUDE_API_KEY'));
define('MAS_AI_GEMINI_KEY', getenv('GEMINI_API_KEY'));
define('MAS_AI_GROQ_KEY', getenv('GROQ_API_KEY'));
```

Then set environment variables:

```bash
export CHATGPT_API_KEY="sk-your-key-here"
export CLAUDE_API_KEY="sk-ant-your-key-here"
```

---

## Rate Limiting

Protect your API quotas and costs with rate limits.

### Configuration

1. Go to **Settings** tab
2. Find **Rate Limiting** section
3. Set limits:
   - **Requests per Minute**: Default 10
   - **Requests per Hour**: Default 100 (stored in preferences)
   - **Requests per Day**: Default 500 (stored in preferences)

### Recommended Settings

**Development:**

```
Requests per Minute: 20
Requests per Hour: 200
Requests per Day: 1000
```

**Low-traffic Production:**

```
Requests per Minute: 10
Requests per Hour: 100
Requests per Day: 500
```

**High-traffic Production:**

```
Requests per Minute: 5
Requests per Hour: 50
Requests per Day: 200
```

**Enterprise:**

```
Requests per Minute: 30
Requests per Hour: 500
Requests per Day: 2000
```

### How Rate Limiting Works

1. Each generation request is logged
2. System checks request count in time window
3. If limit exceeded, user sees error message
4. User must wait for window to reset
5. Audit log tracks all attempts

---

## Provider Configuration

### Default Provider

Choose which provider to use by default:

1. Go to **Settings** tab
2. Select **Default Provider** dropdown
3. Choose from:
   - Hugging Face (free, no key needed)
   - ChatGPT
   - Claude
   - Gemini
   - Groq
   - Mistral
   - Perplexity
   - Cohere
   - Custom providers

### Provider Settings

Each provider has configurable options:

**Model Selection** (in provider class):

- OpenAI: GPT-4, GPT-3.5-turbo
- Claude: Opus, Sonnet, Haiku
- Gemini: Gemini Pro
- Groq: Mixtral 8x7B, Llama 2 70B

**Parameters**:

- `max_tokens`: Maximum response length (default: 2000)
- `temperature`: Creativity level 0-1 (default: 0.7)
- `model`: Specific model to use

### Custom Provider Configuration

Add custom OpenAI-compatible APIs:

1. Go to **Custom Providers** tab
2. Click **Add Custom Provider**
3. Configure:

   - **Provider Name**: `my_service` (alphanumeric, underscore)
   - **Display Name**: "My AI Service"
   - **Description**: Brief description
   - **API Key**: Authentication key
   - **Endpoint URL**: `https://api.example.com/v1/chat/completions`
   - **Model Name**: `gpt-4` or model identifier
   - **Request Format**: OpenAI, Anthropic, HuggingFace, or Custom
4. For Custom Format:

   - **Custom Request JSON**: Template with placeholders
   - **Response Path**: Dot notation to content (e.g., `choices.0.message.content`)
5. Click **Add Provider**
6. Test connection

---

## Security Configuration

### .htaccess Protection

The module includes `.htaccess` protection. Verify it's working:

```apache
# Test by trying to access:
https://yoursite.com/modules/MAS_AIAssistant/lib/
# Should return 403 Forbidden

# Protected directories:
- /lib/
- /providers/
- /integrations/
- /test/
- /to-do/

# Protected files:
- *.log, *.sql, *.bak, *.backup, *.key, *.pem
- .* (hidden files)
```

### For LiteSpeed Users

After modifying `.htaccess`, restart:

```bash
# LiteSpeed Enterprise
systemctl restart lsws

# OpenLiteSpeed (automatic, but verify)
systemctl status lsws
```

### Permissions

Set correct file permissions:

```bash
# For CyberPanel/LiteSpeed
chown -R root:root /path/to/MAS_AIAssistant/
chmod -R 755 /path/to/MAS_AIAssistant/

# Writable directories
chmod 755 /path/to/MAS_AIAssistant/test/
chmod 755 /path/to/MAS_AIAssistant/to-do/
```

---

## Performance Optimization

### Caching

Enable content caching (if implemented in future versions):

```php
// In preferences
$this->SetPreference('enable_caching', 1);
$this->SetPreference('cache_duration', 3600); // 1 hour
```

### Database Optimization

Index the generations table:

```sql
-- Already created by installer, but verify:
SHOW INDEX FROM cms_module_mas_ai_generations;

-- Should show indexes on:
- id (PRIMARY KEY)
- created_date (idx_created_date)
```

### PHP Configuration

Optimize PHP settings for AI generation:

```ini
; In php.ini or .user.ini
memory_limit = 256M
max_execution_time = 60
max_input_time = 60
post_max_size = 32M
upload_max_filesize = 32M
```

For CyberPanel, edit PHP settings:

1. **Websites → List Websites**
2. Select your website
3. **Manage → PHP → Edit PHP Configs**
4. Adjust settings above

---

## Module Preferences

All module preferences are stored in CMSMS preferences table:

| Preference Key            | Description                   | Default         |
| ------------------------- | ----------------------------- | --------------- |
| `default_provider`      | Default AI provider           | `huggingface` |
| `storage_method`        | API key storage method        | `database`    |
| `rate_limit_per_minute` | Max requests per minute       | `10`          |
| `rate_limit_per_hour`   | Max requests per hour         | `100`         |
| `rate_limit_per_day`    | Max requests per day          | `500`         |
| `encryption_key`        | AES encryption key            | Auto-generated  |
| `encryption_iv`         | AES initialization vector     | Auto-generated  |
| `api_key_*`             | Encrypted API keys            | User-provided   |
| `custom_providers_list` | JSON list of custom providers | `[]`          |
| `custom_provider_*`     | Custom provider configs       | User-provided   |
| `hidedonationstab`      | Hide donations tab            | Module version  |

---

## Audit Logging

All AI operations are logged in CMSMS audit log:

**View logs:**

1. **Extensions → System Admin**
2. **Audit Log** or **User Operations**
3. Filter by module: "MAS AI Assistant"

**Logged operations:**

- Content generation
- Design generation
- Batch generation
- Settings changes
- Provider configuration
- Custom provider actions

---

## Backup Configuration

### Backup API Keys

If using database storage:

```bash
# Backup module preferences
mysqldump -u user -p cmsms_db cms_module_preferences \
  --where="module='MAS_AIAssistant'" > mas_ai_backup.sql
```

If using config.php:

- Include `config.php` in your regular backup routine

### Backup History

Backup generation history:

```bash
# Backup generations table
mysqldump -u user -p cmsms_db \
  cms_module_mas_ai_generations \
  cms_module_mas_ai_usage \
  > mas_ai_history_backup.sql
```

---

## Multi-Environment Setup

### Development

```php
// config.php (development)
define('MAS_AI_CHATGPT_KEY', getenv('DEV_CHATGPT_KEY'));
define('MAS_AI_RATE_LIMIT_MINUTE', 20); // More permissive
```

### Staging

```php
// config.php (staging)
define('MAS_AI_CHATGPT_KEY', getenv('STAGING_CHATGPT_KEY'));
define('MAS_AI_RATE_LIMIT_MINUTE', 15);
```

### Production

```php
// config.php (production)
define('MAS_AI_CHATGPT_KEY', getenv('PROD_CHATGPT_KEY'));
define('MAS_AI_RATE_LIMIT_MINUTE', 10); // More restrictive
```

---

## Troubleshooting Configuration

### API Keys Not Working

1. Check key format matches provider
2. Verify no extra spaces
3. Confirm key has permissions
4. Test key in provider dashboard
5. Check if config.php overrides database

### Rate Limit Too Restrictive

1. Increase limits in Settings
2. Check audit log for unusual activity
3. Consider per-user limits
4. Monitor actual usage patterns

### Permission Errors

```bash
# Fix ownership
chown -R root:root MAS_AIAssistant/

# Fix permissions
chmod -R 755 MAS_AIAssistant/

# Verify
ls -la modules/MAS_AIAssistant/
```

---

## Next Steps

- Set up [API Providers](api-providers-guide.md)
- Learn [Security Best Practices](security-guide.md)
- Explore [Usage Examples](usage-guide.md)
