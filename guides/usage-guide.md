# Usage Guide

Complete guide to using MAS AI Assistant for content and design generation.

---

## Quick Start

### Your First Content Generation

1. Navigate to **Extensions → MAS AI Assistant**
2. Go to the **Content Generator** tab
3. Select provider: **Hugging Face** (works without API key)
4. Choose content type: **Page Content**
5. Enter topic: *"Benefits of renewable energy"*
6. Set word count: `500`
7. Add keywords: *"solar, wind, sustainable"*
8. Click **Generate**
9. Copy the generated content

---

## Content Generation

### Page Content

Generate SEO-optimized pages:

```
Provider: ChatGPT (or any provider)
Content Type: Page Content
Topic: "How to optimize website performance"
Word Count: 800
Keywords: "speed, caching, optimization"

Result:
- H2/H3 headings
- Proper paragraphs
- Keywords integrated
- HTML formatted
```

### Blog Posts

Create engaging blog content:

```
Provider: Claude
Content Type: Blog Post
Topic: "10 Tips for Remote Work Productivity"
Word Count: 1000
Keywords: "remote work, productivity, tips"

Result:
- Introduction paragraph
- Numbered list with details
- Subheadings for sections
- Conclusion with call-to-action
```

### Meta Descriptions

SEO-friendly meta descriptions:

```
Provider: Hugging Face
Content Type: Meta Description
Topic: "Renewable energy benefits for homeowners"

Result:
- Under 160 characters
- Includes main keywords
- Compelling call-to-action
- Search engine optimized
```

---

## Design Generation

### Page Layouts

Create complete responsive layouts:

```
Design Type: Page Layout
Description: "Modern landing page with hero section, features grid, testimonials, and contact form"

Result:
- Complete HTML structure
- Semantic HTML5 tags
- Bootstrap 5 compatible
- Fully responsive
- Accessibility labels
```

### Components

Generate reusable UI components:

```
Design Type: Component
Description: "Pricing table with 3 tiers, hover effects, and popular badge"

Result:
- Clean HTML/CSS
- Interactive effects
- Mobile responsive
- Copy-paste ready
```

### CSS Stylesheets

Modern CSS generation:

```
Design Type: CSS Stylesheet
Description: "Modern blog theme with dark mode toggle, gradient headers"

Result:
- CSS variables
- Dark mode support
- Responsive breakpoints
- Smooth transitions
- Well-organized code
```

### CMSMS Templates

Generate Smarty templates:

```
Design Type: CMSMS Template
Description: "Blog post template with sidebar, author box, and related posts"

Result:
- Smarty syntax
- CMSMS tags ({$content}, {menu}, etc.)
- Proper structure
- Comments included
```

---

## Batch Generation

Generate multiple pieces of content at once.

### Setup

1. Go to **Batch Generation** tab
2. Select provider
3. Choose content type
4. Enter topics (one per line, max 20)
5. Set word count and keywords
6. Click **Generate Batch**

### Example

```
Topics (one per line):
Introduction to solar energy
Wind power advantages
Geothermal energy basics
Hydroelectric power systems
Biomass energy overview

Provider: Groq (for speed)
Content Type: Page Content
Word Count: 400

Result:
- 5 separate articles generated
- Statistics shown (success/errors)
- Export as JSON
- Download as individual files
```

### Tips

- **Keep topics related** for consistency
- **Test with 2-3 topics** first
- **Use Groq or Gemini** for faster batch processing
- **Monitor API costs** with large batches
- **Export results** for backup

---

## Content History

Track and manage all your generations.

### Features

- **View all past generations** with details
- **Filter by provider, type, date**
- **Search content** by keywords
- **Restore previous content** with one click
- **Delete old versions** to clean up
- **Export history** as JSON

### Using History

1. Go to **Content History** tab
2. Browse recent generations
3. Click **View** to see full content
4. Click **Restore** to reuse
5. Click **Delete** to remove

### Filtering

```
Filter by:
- Provider: All, ChatGPT, Claude, etc.
- Type: Page, Blog, Meta, Design
- Date Range: From/To dates
- Search: Keywords in prompt or content

Results: Paginated (20 per page)
```

---

## Advanced Features

### Custom Providers

Add your own AI API:

1. Go to **Custom Providers** tab
2. Click **Add Custom Provider**
3. Fill in configuration:
   - Name: `my_provider`
   - Display Name: "My AI Service"
   - API Key: Your key
   - Endpoint: API URL
   - Model: Model name
   - Format: OpenAI Compatible
4. Click **Add Provider**
5. Test connection
6. Use in Content/Design generators

### Real-time Streaming

Watch content generate live (where supported):

- Uses Server-Sent Events (SSE)
- Shows progress in real-time
- Available for compatible providers
- Faster perceived performance

### News Module Integration

Generate content directly in News articles:

1. Edit or create News article
2. Find **AI Content Generation** section
3. Enter article topic
4. Select provider
5. Click **Generate Content** or **Stream Content (Live)**
6. Content appears in editor
7. Edit and publish

---

## Best Practices

### Prompt Writing

**Be Specific**
```
❌ Bad: "Write about cars"
✅ Good: "Write a 500-word guide on choosing your first electric car, covering cost, range, and charging infrastructure"
```

**Provide Context**
```
❌ Bad: "Create a homepage"
✅ Good: "Create a modern SaaS homepage with hero section highlighting AI automation, 3 key features, customer testimonials, and pricing CTA"
```

**Use Keywords**
```
❌ Bad: No keywords provided
✅ Good: Keywords: "electric vehicles, EV charging, battery range, sustainability"
```

### Content Quality

1. **Review all generated content** before publishing
2. **Add personal insights** and examples
3. **Check facts** especially with free models
4. **Maintain brand voice** by editing generated content
5. **Combine AI + human** for best results

### Cost Optimization

1. **Start with free tiers** (Hugging Face, Groq, Gemini)
2. **Use GPT-3.5 instead of GPT-4** for drafts
3. **Batch similar requests** to save time
4. **Set word count limits** appropriately
5. **Monitor usage** in provider dashboards

### Performance Tips

| Task | Best Provider | Why |
|------|--------------|-----|
| Quick drafts | Groq | Ultra-fast inference |
| High quality | GPT-4 or Claude Opus | Superior output |
| Bulk generation | Gemini Free Tier | No cost |
| Technical content | Claude | Better reasoning |
| Creative writing | GPT-4 | More creative |
| Budget projects | Hugging Face | Completely free |

---

## Workflow Examples

### Blog Writing Workflow

1. **Generate outline** with GPT-4
2. **Expand sections** with Claude
3. **Generate meta** with any provider
4. **Create images descriptions** with GPT-4
5. **Edit and publish**

### Website Content Workflow

1. **Generate pages** in batch (5-10 pages)
2. **Review and edit** each page
3. **Generate meta descriptions** for all
4. **Create design components** as needed
5. **Publish** staged content

### E-commerce Product Workflow

1. **Batch generate** product descriptions
2. **Use templates** for consistency
3. **Add specifications** manually
4. **Generate SEO meta** for each
5. **Export and import** to shop

---

## Keyboard Shortcuts

When in admin panel:

- **Tab 1-8**: Switch between tabs
- **Ctrl+Enter**: Submit form (in text areas)
- **Ctrl+C**: Copy generated content

---

## Troubleshooting

### Generation Failed

**Check:**
- API key is correct
- Provider has sufficient credits
- Internet connection is stable
- Content doesn't violate provider policies

### Rate Limit Exceeded

**Solutions:**
- Wait 60 seconds for reset
- Increase rate limits in Settings
- Switch to different provider
- Upgrade provider plan

### Poor Quality Output

**Improve by:**
- Being more specific in prompts
- Adding more keywords
- Trying different providers
- Increasing word count
- Adding examples in prompt

### Empty or Incomplete Content

**Fix:**
- Check provider status
- Increase max tokens in provider settings
- Try again (sometimes random)
- Switch to more reliable provider

---

## Next Steps

- Learn about [Security Best Practices](security-guide.md)
- Check [Troubleshooting Guide](troubleshooting-guide.md)
- Review [Configuration Options](configuration-guide.md)

