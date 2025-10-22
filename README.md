# MAS AI Assistant for CMS Made Simple

[![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://github.com/master3395/MAS_AIAssistant)
[![License](https://img.shields.io/badge/license-GPL%20v3-green.svg)](LICENSE)
[![CMSMS](https://img.shields.io/badge/CMSMS-2.2%2B-orange.svg)](https://www.cmsmadesimple.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4--8.6-purple.svg)](https://www.php.net/)
[![Discord](https://img.shields.io/badge/Discord-Join%20Server-7289da.svg)](https://discord.gg/nx9Kzrk)

**AI-powered content generation, design creation, and SEO optimization for CMS Made Simple**

Transform your content creation workflow with cutting-edge AI technology. MAS AI Assistant brings multiple AI providers directly into your CMSMS admin panel.

---

## ✨ Features

- **🤖 9 AI Providers** - Hugging Face (free), ChatGPT, Claude, Gemini, Groq, Mistral, Perplexity, Cohere, plus custom providers
- **✍️ Content Generation** - Pages, blog posts, meta descriptions, SEO keywords, content rewriting
- **🎨 Design Generation** - Layouts, CSS, UI components, CMSMS templates
- **📦 Batch Processing** - Generate up to 20 items at once
- **📊 Content History** - Track, filter, restore, and export all generations
- **🔒 Security First** - AES-256 encryption, rate limiting, CSRF protection, audit logging
- **🌐 PHP 7.4-8.6** - Compatible with all modern PHP versions

---

## 🚀 Quick Start

### Installation

**Method 1: XML Module Import (Recommended)**

1. Download `MAS_AIAssistant-1.0.0.xml` from repository
2. CMSMS Admin → **Extensions → Module Manager**
3. Click **Install Module** → **Import from XML**
4. Select the XML file and click **Install**

**Method 2: ZIP Upload**

1. Download ZIP from [Releases](https://github.com/master3395/MAS_AIAssistant/releases)
2. CMSMS Admin → **Extensions → Module Manager**
3. **Upload Module** → Select ZIP → **Install**

📖 **Full instructions**: [Installation Guide](guides/installation-guide.md)

> **Note**: The `MAS_AIAssistant-1.0.0.xml` file is the official CMSMS module export format - a complete, portable package containing all files, templates, and metadata. This is the recommended installation method for CMSMS modules.

### Basic Setup

1. Go to **Extensions → MAS AI Assistant**
2. Navigate to **Settings** tab
3. Default provider (Hugging Face) works without API key
4. Optionally add API keys for other providers
5. Start generating content!

📖 **Detailed setup**: [Configuration Guide](guides/configuration-guide.md)

---

## 📚 Documentation

### Comprehensive Guides

- **[Installation Guide](guides/installation-guide.md)** - Installation methods, requirements, database setup
- **[Configuration Guide](guides/configuration-guide.md)** - Settings, rate limiting, security options
- **[API Providers Guide](guides/api-providers-guide.md)** - Setup all 9 providers with pricing and best practices
- **[Usage Guide](guides/usage-guide.md)** - Examples, workflows, and tips
- **[Security Guide](guides/security-guide.md)** - Best practices, compliance, audit logging
- **[Troubleshooting Guide](guides/troubleshooting-guide.md)** - Common issues and solutions

---

## 🎯 Usage Example

### Generate SEO Blog Post

```
1. Go to Content Generator tab
2. Provider: ChatGPT (or Hugging Face for free)
3. Content Type: Blog Post
4. Topic: "10 Benefits of Solar Energy for Homeowners"
5. Word Count: 800
6. Keywords: solar panels, renewable energy, cost savings
7. Click Generate

Result: Complete blog post with headings, paragraphs, HTML formatting
```

📖 **More examples**: [Usage Guide](guides/usage-guide.md)

---

## 🔌 Supported AI Providers

| Provider | Cost | API Key Required | Best For |
|----------|------|------------------|----------|
| **Hugging Face** | Free | No | Getting started, budget projects |
| **ChatGPT** | $0.50-$30/1M tokens | Yes | Professional content |
| **Claude** | $0.25-$75/1M tokens | Yes | Detailed analysis |
| **Gemini** | Free tier + paid | Yes | Frequent use |
| **Groq** | Free tier + paid | Yes | Speed |
| **Mistral** | $0.25-$24/1M tokens | Yes | EU compliance |
| **Perplexity** | $5/1M tokens | Yes | Current events |
| **Cohere** | $0.15-$15/1M tokens | Yes | Multilingual |
| **Custom** | Varies | Yes | Your own API |

📖 **Setup instructions**: [API Providers Guide](guides/api-providers-guide.md)

---

## 🔒 Security Features

- **AES-256-CBC Encryption** for API keys in database
- **config.php Support** for maximum security
- **Rate Limiting** with configurable per-minute/hour/day limits
- **Input Sanitization** (XSS/SQL injection protection)
- **CSRF Protection** on all forms
- **.htaccess Protection** blocks sensitive directories
- **Audit Logging** tracks all operations
- **HTTPS Support** for secure transmission

📖 **Full security guide**: [Security Guide](guides/security-guide.md)

---

## 🏗️ Architecture

```
MAS_AIAssistant/
├── action.*.php          # Admin actions
├── assets/               # CSS/JS
├── guides/               # Documentation
├── integrations/         # Module integrations
├── lang/                 # Language files
├── lib/                  # Core classes
├── providers/            # AI provider integrations
├── templates/            # Smarty templates
├── test/                 # Test files
├── .htaccess            # Security rules
└── MAS_AIAssistant-1.0.0.xml  # Module distribution
```

**Key Features:**
- Modular design (max 500 lines per file)
- PSR coding standards
- No external dependencies
- Pure PHP implementation

---

## 💻 Requirements

- **PHP**: 7.4, 7.5, 8.0, 8.1, 8.2, 8.3, 8.4, 8.5, or 8.6
- **CMSMS**: 2.2.0 or higher
- **Database**: MySQL 5.7+ or MariaDB 10.2+
- **Extensions**: curl, json, openssl, mbstring
- **Recommended**: AlmaLinux 8.8/9.6/10, CyberPanel

📖 **Full requirements**: [Installation Guide](guides/installation-guide.md)

---

## 🤝 Contributing

We welcome contributions! 

1. Fork the repository
2. Create feature branch
3. Follow coding standards (max 500 lines/file, PSR-12)
4. Add tests if applicable
5. Submit pull request

**Coding Standards:**
- Security-first approach
- Comprehensive error handling
- PHP 7.4-8.6 compatible
- Full inline documentation

---

## 📄 License

Licensed under **GNU General Public License v3.0** (GPL-3.0).

Free to use, modify, and distribute. See [LICENSE](LICENSE) for full terms.

---

## 💬 Support & Community

### Get Help

- **Discord**: [Join our server](https://discord.gg/nx9Kzrk) for real-time support
- **Issues**: [GitHub Issues](https://github.com/master3395/MAS_AIAssistant/issues)
- **Email**: info [at] newstargeted [dot] com

### Resources

- **Documentation**: Check the [guides/](guides/) folder
- **Source Code**: Browse the repository
- **Changelog**: See below for version history

### Commercial Support

For enterprise support and custom development:
- **Website**: [News Targeted](https://newstargeted.com/contact/)
- **Email**: info [at] newstargeted [dot] com

---

## 🙏 Credits

**Author**: master3395  
**Website**: [News Targeted](https://newstargeted.com/)  
**License**: GPL v3

### Sponsors

Thank you to our sponsors:
- [News Targeted](https://newstargeted.com/)

---

## 📊 Changelog

### [1.0.0] - 2025-10-22

**Initial Release** 🎉

#### Features
- ✨ 9 AI provider integrations (Hugging Face, ChatGPT, Claude, Gemini, Groq, Mistral, Perplexity, Cohere, Custom)
- ✨ Content generation (page, blog, meta, keywords, rewriting)
- ✨ Design generation (layouts, CSS, components, CMSMS templates)
- ✨ Batch generation (up to 20 items)
- ✨ Content history with filtering and search
- ✨ Custom provider support for any OpenAI-compatible API
- ✨ SEO optimization tools and schema markup
- ✨ News module integration
- ✨ Real-time streaming support (SSE)
- ✨ Responsive admin UI

#### Security
- 🔒 AES-256-CBC API key encryption
- 🔒 Rate limiting system (per-minute/hour/day)
- 🔒 Input sanitization and validation
- 🔒 CSRF protection
- 🔒 .htaccess security rules
- 🔒 Audit logging for all operations

#### Compatibility
- ✅ PHP 7.4, 7.5, 8.0, 8.1, 8.2, 8.3, 8.4, 8.5, 8.6
- ✅ CMSMS 2.2.0+
- ✅ MySQL 5.7+ / MariaDB 10.2+
- ✅ AlmaLinux 8.8, 9.6, 10
- ✅ CyberPanel (OpenLiteSpeed & LiteSpeed Enterprise)

---

## 🗺️ Roadmap

### Version 1.1 (Q2 2025)
- Real-time streaming for all providers
- Multi-language content generation
- Advanced SEO dashboard
- More CMSMS module integrations

### Version 1.2 (Q3 2025)
- AI image generation (DALL-E, Midjourney)
- Content scheduling
- Team collaboration features
- REST API

### Version 2.0 (Q4 2025)
- Local AI model support (Ollama, LLaMA.cpp)
- Fine-tuning support
- Advanced workflow automation
- Multi-site network support

---

<div align="center">

**Made with ❤️ by [master3395](https://github.com/master3395)**

[⭐ Star this repo](https://github.com/master3395/MAS_AIAssistant) | [🐛 Report Bug](https://github.com/master3395/MAS_AIAssistant/issues) | [💡 Request Feature](https://github.com/master3395/MAS_AIAssistant/issues) | [💬 Discord](https://discord.gg/nx9Kzrk)

</div>
