# API Providers Setup Guide

Complete guide to setting up all supported AI providers.

---

## Table of Contents

- [Hugging Face (Free)](#hugging-face-free)
- [ChatGPT (OpenAI)](#chatgpt-openai)
- [Claude (Anthropic)](#claude-anthropic)
- [Google Gemini](#google-gemini)
- [Groq](#groq)
- [Mistral AI](#mistral-ai)
- [Perplexity AI](#perplexity-ai)
- [Cohere AI](#cohere-ai)
- [Custom Providers](#custom-providers)

---

## Hugging Face (Free)

**Works out of the box!** No configuration needed.

### Features
- **Cost**: Free
- **Model**: Mistral-7B-Instruct-v0.2
- **Best for**: Getting started, testing, budget projects

### Optional API Key

For better rate limits:
1. Sign up at [Hugging Face](https://huggingface.co/)
2. Navigate to [Settings → Access Tokens](https://huggingface.co/settings/tokens)
3. Click **Create new token**
4. Copy the token
5. Add to MAS AI Assistant Settings

---

## ChatGPT (OpenAI)

High-quality content generation with GPT models.

### Setup Steps

1. Sign up at [OpenAI Platform](https://platform.openai.com/)
2. Navigate to [API Keys](https://platform.openai.com/api-keys)
3. Click **Create new secret key**
4. Copy key starting with `sk-...`
5. Add to MAS AI Assistant Settings

### Models Available
- **GPT-4 Turbo** - Most capable
- **GPT-4** - High quality
- **GPT-3.5 Turbo** - Recommended (cost-effective)
- **GPT-3.5 Turbo 16K** - Longer context

### Pricing
- **GPT-3.5 Turbo**: $0.50 - $1.50 per 1M tokens
- **GPT-4**: $10 - $30 per 1M tokens
- **GPT-4 Turbo**: $10 - $30 per 1M tokens

### Best For
- Professional content
- Technical writing
- Creative content
- Complex instructions

---

## Claude (Anthropic)

Advanced AI with excellent reasoning capabilities.

### Setup Steps

1. Sign up at [Anthropic Console](https://console.anthropic.com/)
2. Navigate to **API Keys**
3. Click **Create Key**
4. Copy key starting with `sk-ant-...`
5. Add to MAS AI Assistant Settings

### Models Available
- **Claude 3 Opus** - Most capable
- **Claude 3 Sonnet** - Balanced (recommended)
- **Claude 3 Haiku** - Fast and affordable
- **Claude 2.1** - Previous generation

### Pricing
- **Haiku**: $0.25 - $1.25 per 1M tokens
- **Sonnet**: $3 - $15 per 1M tokens
- **Opus**: $15 - $75 per 1M tokens

### Best For
- Detailed analysis
- Long-form content
- Research-heavy writing
- Code generation

---

## Google Gemini

Google's multimodal AI with free tier.

### Setup Steps

1. Get API key from [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Click **Get API Key** or **Create API Key**
3. Select or create a Google Cloud project
4. Copy the generated API key
5. Add to MAS AI Assistant Settings

### Models Available
- **Gemini Pro** - Recommended
- **Gemini Pro Vision** - Multimodal (images + text)

### Pricing
- **Free Tier**: 60 requests per minute
- **Paid**: $0.25 - $1.25 per 1M tokens

### Best For
- General content
- Cost-effective solution
- Quick prototyping
- Frequent use (free tier)

---

## Groq

Ultra-fast inference with free tier.

### Setup Steps

1. Sign up at [Groq Console](https://console.groq.com/)
2. Navigate to **API Keys**
3. Create new key starting with `gsk_...`
4. Copy the key
5. Add to Settings

### Models Available
- **Mixtral 8x7B** - Recommended
- **Llama 2 70B** - High quality
- **Gemma 7B** - Fast

### Pricing
- **Free Tier**: Available with limits
- **Paid**: Pay-as-you-go

### Best For
- Fast responses
- Real-time applications
- High-volume generation
- Cost-effective quality

---

## Mistral AI

High-performance European AI models.

### Setup Steps

1. Create account at [Mistral AI](https://console.mistral.ai/)
2. Generate API key
3. Copy the key
4. Add to Settings

### Models Available
- **Mistral Large** - Latest and most capable
- **Mistral Small** - Balanced
- **Mistral Tiny** - Fast and affordable
- **Open Mistral 7B** - Open source
- **Open Mixtral 8x7B** - Open source, high quality

### Pricing
- **Tiny**: $0.25 per 1M tokens
- **Small**: $2 per 1M tokens (input), $6 per 1M (output)
- **Large**: $8 per 1M tokens (input), $24 per 1M (output)

### Best For
- European GDPR compliance
- Multi-language support
- Cost-effective quality
- Open-source options

---

## Perplexity AI

Real-time web search integrated AI.

### Setup Steps

1. Sign up at [Perplexity AI](https://www.perplexity.ai/)
2. Get API key from console
3. Copy the key (starts with `pplx-...`)
4. Add to Settings

### Models Available
- **Llama 3.1 Sonar Small (Online)** - Web search enabled
- **Llama 3.1 Sonar Large (Online)** - Better quality
- **Llama 3.1 Sonar (Chat)** - No web search

### Pricing
- **All models**: $5 per 1M tokens

### Best For
- Current events
- Research with citations
- Fact-checking
- Up-to-date information

### Special Features
- Real-time web search
- Citation support
- Domain filtering
- Recency filtering

---

## Cohere AI

Enterprise-grade multilingual AI.

### Setup Steps

1. Register at [Cohere Dashboard](https://dashboard.cohere.com/)
2. Create API key
3. Copy the key
4. Add to Settings

### Models Available
- **Command R+** - Latest and best
- **Command R** - Balanced
- **Command Light** - Fast
- **Command** - Standard

### Pricing
- **Light**: $0.15 - $0.15 per 1M tokens
- **Command**: $1 - $1 per 1M tokens
- **R**: $3 - $15 per 1M tokens
- **R+**: $3 - $15 per 1M tokens

### Best For
- Multilingual content
- Enterprise deployments
- RAG applications
- Tool use/function calling

---

## Custom Providers

Add any OpenAI-compatible API.

### Setup Steps

1. Go to **Custom Providers** tab in module admin
2. Click **Add Custom Provider**
3. Fill in details:
   - **Provider Name**: Internal identifier (alphanumeric)
   - **Display Name**: Friendly name
   - **API Key**: Your authentication key
   - **Endpoint**: Full API URL
   - **Model**: Model identifier
   - **Format**: Choose API format

### Supported Formats

**OpenAI Compatible** (most common)
```
Endpoint: https://api.openai.com/v1/chat/completions
Works with: OpenAI, Azure OpenAI, OpenRouter, many others
```

**Anthropic Claude**
```
Endpoint: https://api.anthropic.com/v1/messages
Works with: Anthropic Claude API
```

**Hugging Face**
```
Endpoint: https://api-inference.huggingface.co/models/MODEL_NAME
Works with: Hugging Face Inference API
```

**Custom JSON**
```
Define your own request/response format
Useful for proprietary APIs
```

### Example Endpoints

- **OpenRouter**: `https://openrouter.ai/api/v1/chat/completions`
- **Together AI**: `https://api.together.xyz/v1/chat/completions`
- **Anyscale**: `https://api.endpoints.anyscale.com/v1/chat/completions`
- **Fireworks AI**: `https://api.fireworks.ai/inference/v1/chat/completions`

### Testing

After adding a custom provider:
1. Click **Test** button
2. Verify connection successful
3. Try generating content
4. Monitor for errors

---

## Configuration Storage

### Option 1: Database (Default)

API keys are encrypted using AES-256-CBC:
1. Go to **Settings** tab
2. Enter API keys
3. Keys are automatically encrypted on save

### Option 2: config.php (Recommended for Production)

For maximum security, add to `/config.php`:

```php
// Add after existing config
define('MAS_AI_CHATGPT_KEY', 'sk-your-openai-key-here');
define('MAS_AI_CLAUDE_KEY', 'sk-ant-your-anthropic-key-here');
define('MAS_AI_GEMINI_KEY', 'your-google-gemini-key-here');
define('MAS_AI_GROQ_KEY', 'gsk_your-groq-key-here');
define('MAS_AI_MISTRAL_KEY', 'your-mistral-key-here');
define('MAS_AI_PERPLEXITY_KEY', 'pplx-your-perplexity-key-here');
define('MAS_AI_COHERE_KEY', 'your-cohere-key-here');
```

**Note**: config.php keys take precedence over database storage.

---

## Best Practices

### Cost Management
1. Start with free tiers (Hugging Face, Groq, Gemini free)
2. Monitor usage in provider dashboards
3. Set up billing alerts
4. Use rate limiting in module settings

### Security
1. Use config.php for production
2. Never commit API keys to Git
3. Rotate keys regularly
4. Use environment variables where possible

### Performance
1. Use Groq for speed
2. Use GPT-3.5 Turbo for balance
3. Use Claude Haiku for fast, cheap quality
4. Cache repeated prompts

### Quality vs Cost
- **Highest Quality**: GPT-4, Claude Opus
- **Best Balance**: GPT-3.5 Turbo, Claude Sonnet
- **Most Affordable**: Hugging Face, Mixtral, Gemini

---

## Next Steps

- Read [Configuration Guide](configuration-guide.md)
- Check out [Usage Examples](usage-guide.md)
- Learn about [Security Best Practices](security-guide.md)

