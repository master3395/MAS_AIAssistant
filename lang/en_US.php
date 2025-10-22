<?php
/**
 * English Language File for MAS AI Assistant
 */

// Module info
$lang['friendlyname'] = 'MAS AI Assistant';
$lang['moddescription'] = 'AI-powered content generation, design creation, and SEO optimization for CMSMS. Supports multiple AI providers including free options.';
$lang['postinstall'] = 'MAS AI Assistant has been installed successfully! Configure your AI providers in the Settings tab.';
$lang['postuninstall'] = 'MAS AI Assistant has been uninstalled.';
$lang['really_uninstall'] = 'Are you sure you want to uninstall MAS AI Assistant? All AI generations and settings will be deleted.';
$lang['installed'] = 'Module version %s installed successfully.';
$lang['upgraded'] = 'Module upgraded to version %s.';
$lang['uninstalled'] = 'Module uninstalled.';

// Permissions
$lang['accessdenied'] = 'Access denied. Please check your permissions.';

// Tabs
$lang['tab_dashboard'] = 'Dashboard';
$lang['tab_content'] = 'Content Generator';
$lang['tab_design'] = 'Design Generator';
$lang['tab_settings'] = 'Settings';
$lang['tab_custom_providers'] = 'Custom Providers';
$lang['tab_batch_generation'] = 'Batch Generation';
$lang['tab_content_history'] = 'Content History';
$lang['tab_adminsettings'] = 'Admin Settings';
$lang['tab_donations'] = 'Donations';

// Dashboard
$lang['dashboard_title'] = 'AI Assistant Dashboard';
$lang['total_generations'] = 'Total Generations';
$lang['last_week'] = 'Last 7 Days';
$lang['default_provider'] = 'Default Provider';
$lang['configured_providers'] = 'AI Provider Status';
$lang['configured'] = 'Configured';
$lang['not_configured'] = 'Not Configured';
$lang['quick_actions'] = 'Quick Actions';
$lang['generate_content'] = 'Generate Content';
$lang['generate_design'] = 'Generate Design';
$lang['configure_settings'] = 'Configure Settings';
$lang['getting_started'] = 'Getting Started';
$lang['dashboard_help_text'] = 'Welcome to MAS AI Assistant! Configure your AI providers in Settings, then use the Content or Design tabs to generate amazing content for your website.';

// Content Generation
$lang['content_generation'] = 'AI Content Generation';
$lang['provider'] = 'AI Provider';
$lang['content_type'] = 'Content Type';
$lang['content_type_page'] = 'Page Content';
$lang['content_type_blog'] = 'Blog Post';
$lang['content_type_meta'] = 'Meta Description';
$lang['topic'] = 'Topic / Description';
$lang['topic_help'] = 'Describe what you want to generate. Be specific for better results.';
$lang['word_count'] = 'Word Count';
$lang['word_count_help'] = 'Approximate number of words to generate (100-5000).';
$lang['keywords'] = 'Keywords';
$lang['keywords_help'] = 'Comma-separated keywords to include in the content.';
$lang['generate'] = 'Generate';
$lang['generated_content'] = 'Generated Content';
$lang['copy_to_clipboard'] = 'Copy to Clipboard';
$lang['copied'] = 'Copied to clipboard!';

// Design Generation
$lang['design_generation'] = 'AI Design Generation';
$lang['design_type'] = 'Design Type';
$lang['design_type_layout'] = 'Page Layout';
$lang['design_type_component'] = 'Component';
$lang['design_type_css'] = 'CSS Stylesheet';
$lang['design_type_template'] = 'CMSMS Template';
$lang['description'] = 'Description';
$lang['description_help'] = 'Describe the design you want to create. Include details about style, colors, and functionality.';
$lang['generated_design'] = 'Generated Design';

// Settings
$lang['settings'] = 'Settings';
$lang['general_settings'] = 'General Settings';
$lang['default_provider_help'] = 'Select the default AI provider to use for generations.';
$lang['storage_method'] = 'API Key Storage';
$lang['storage_database'] = 'Encrypted Database';
$lang['storage_config_php'] = 'config.php File';
$lang['storage_both'] = 'Both (config.php preferred)';
$lang['storage_method_help'] = 'Choose where to store API keys. Database is encrypted, config.php is more secure.';
$lang['rate_limit'] = 'Rate Limiting';
$lang['requests_per_minute'] = 'requests per minute';
$lang['rate_limit_help'] = 'Maximum AI requests per minute per user (prevents abuse).';

// API Keys
$lang['api_keys'] = 'API Keys';
$lang['api_keys_help'] = 'Enter API keys for the providers you want to use. Hugging Face works without an API key. Keys are encrypted when stored in database.';
$lang['api_key_placeholder'] = 'Enter API key or leave blank to keep existing';
$lang['config_php_option'] = 'Using config.php for API Keys';
$lang['config_php_help'] = 'For maximum security, add these constants to your config.php file:';

// Messages
$lang['settings_updated'] = 'Settings updated successfully.';
$lang['generation_success'] = 'Content generated successfully!';
$lang['generation_failed'] = 'Generation failed. Please try again.';
$lang['topic_required'] = 'Please enter a topic or description.';
$lang['description_required'] = 'Please enter a description for the design.';
$lang['invalid_provider'] = 'Invalid AI provider selected.';
$lang['rate_limit_exceeded'] = 'Rate limit exceeded. Please wait before generating more content.';

// Admin Settings
$lang['admin_settings'] = 'Admin Settings';
$lang['showdonationstab'] = 'Show donations tab';

// Donations
$lang['donationstab'] = 'Donations';
$lang['donationstext'] = 'A lot of time and effort has been put into creating this AI-powered module. If you find it valuable, please consider supporting its development with a donation (5€ suggested, or whatever you can spare). This helps maintain and improve the module with new AI providers and features. Thank you for your support!';
$lang['sponsors'] = 'Current Sponsors - Thank you!';
$lang['hidedonationssubmit'] = 'Hide donations tab';

// Help
$lang['help_action'] = 'Action to perform (default: generate content)';
$lang['help_provider'] = 'AI provider to use for generation';
$lang['help_content_type'] = 'Type of content to generate';
$lang['help_prompt'] = 'Description or prompt for generation';

// Changelog
$lang['changelog'] = '<ul>
<li><strong>Version 1.0.0, October 2025, Master3395</strong>: Initial release with comprehensive AI features
    <ul>
        <li><strong>Multiple AI Providers:</strong> Support for 6 providers including free Hugging Face default</li>
        <li><strong>Content Generation:</strong> Generate page content, blog posts, meta descriptions, and keywords</li>
        <li><strong>Design Generation:</strong> Create HTML layouts, CSS stylesheets, components, and CMSMS templates</li>
        <li><strong>SEO Optimization:</strong> Content analysis, keyword suggestions, and schema markup generation</li>
        <li><strong>Security:</strong> Encrypted API key storage, rate limiting, input sanitization</li>
        <li><strong>Flexible Configuration:</strong> Store API keys in database or config.php</li>
        <li><strong>Modern UI:</strong> Clean, responsive admin interface with real-time previews</li>
        <li><strong>Comprehensive Documentation:</strong> Installation guides and API provider setup instructions</li>
        <li><strong>PHP 7.4-8.6 Compatible:</strong> Works with all modern PHP versions</li>
        <li><strong>CMSMS 2.2+:</strong> Full integration with CMSMS ecosystem</li>
    </ul>
</li>
</ul>';

// Help documentation
$lang['help'] = '<div class="mas-ai-help">
<h3>MAS AI Assistant Module</h3>

<p><strong>MAS AI Assistant</strong> is a powerful AI-powered content generation and design creation module for CMS Made Simple. It supports multiple AI providers and enables you to generate high-quality content, designs, and templates with ease.</p>

<h4>Features</h4>
<ul>
    <li><strong>Multiple AI Providers:</strong> Hugging Face (free), ChatGPT, Claude, Cursor AI, Google Gemini, and Groq</li>
    <li><strong>Content Generation:</strong> Pages, blog posts, meta descriptions, SEO keywords</li>
    <li><strong>Design Generation:</strong> HTML layouts, CSS stylesheets, components, CMSMS templates</li>
    <li><strong>SEO Optimization:</strong> Content analysis, keyword suggestions, schema markup</li>
    <li><strong>Secure:</strong> Encrypted API key storage, rate limiting, input sanitization</li>
    <li><strong>Flexible:</strong> Store API keys in encrypted database or config.php file</li>
</ul>

<h4>Getting Started</h4>
<ol>
    <li>Go to the <strong>Settings</strong> tab</li>
    <li>Configure your preferred AI provider (Hugging Face works without API key)</li>
    <li>Optionally add API keys for other providers (ChatGPT, Claude, etc.)</li>
    <li>Use the <strong>Content Generator</strong> to create page content, blog posts, or meta descriptions</li>
    <li>Use the <strong>Design Generator</strong> to create HTML layouts, CSS, or CMSMS templates</li>
</ol>

<h4>API Providers</h4>

<p><strong>Hugging Face (Default, Free):</strong><br/>
No API key required for basic usage. Uses open-source models like Mistral-7B. Great for getting started!<br/>
<a href="https://huggingface.co/" target="_blank">Get Optional API Key</a></p>

<p><strong>ChatGPT (OpenAI):</strong><br/>
High-quality content generation with GPT-4 and GPT-3.5-turbo models. Requires API key.<br/>
<a href="https://platform.openai.com/api-keys" target="_blank">Get API Key</a></p>

<p><strong>Claude (Anthropic):</strong><br/>
Advanced AI with Claude 3 Opus, Sonnet, and Haiku models. Excellent for detailed content.<br/>
<a href="https://console.anthropic.com/" target="_blank">Get API Key</a></p>

<p><strong>Google Gemini:</strong><br/>
Google\'s multimodal AI with free tier available. Good balance of quality and cost.<br/>
<a href="https://makersuite.google.com/app/apikey" target="_blank">Get API Key</a></p>

<p><strong>Groq:</strong><br/>
Ultra-fast inference with free tier. Uses Mixtral and Llama models.<br/>
<a href="https://console.groq.com/" target="_blank">Get API Key</a></p>

<h4>Security Best Practices</h4>
<ul>
    <li>Store API keys in config.php for maximum security</li>
    <li>Use rate limiting to prevent abuse</li>
    <li>Regularly rotate API keys</li>
    <li>Monitor usage in provider dashboards</li>
</ul>

<h4>config.php Setup</h4>
<p>For maximum security, add API keys to your config.php file:</p>
<pre>
define(\'MAS_AI_CHATGPT_KEY\', \'sk-your-api-key-here\');
define(\'MAS_AI_CLAUDE_KEY\', \'sk-ant-your-api-key-here\');
define(\'MAS_AI_GEMINI_KEY\', \'your-api-key-here\');
define(\'MAS_AI_GROQ_KEY\', \'gsk_your-api-key-here\');
</pre>

<h4>Support</h4>
<p><strong>Author:</strong> master3395<br/>
<strong>Email:</strong> info [at] newstargeted [dot] com<br/>
<strong>Website:</strong> <a href="https://newstargeted.com/contact/" target="_blank">https://newstargeted.com/contact/</a></p>

<h4>License</h4>
<p>This module is licensed under GPL v3. Free to use for personal and commercial projects.</p>
</div>';

// Custom Providers
$lang['custom_providers'] = 'Custom AI Providers';
$lang['add_custom_provider'] = 'Add Custom Provider';
$lang['provider_name'] = 'Provider Name';
$lang['provider_name_help'] = 'Internal name (alphanumeric and underscore only)';
$lang['display_name'] = 'Display Name';
$lang['display_name_help'] = 'Friendly name shown in dropdowns';
$lang['description'] = 'Description';
$lang['description_help'] = 'Brief description of this provider';
$lang['api_key'] = 'API Key';
$lang['api_key_help'] = 'Your API key for this provider';
$lang['endpoint_url'] = 'Endpoint URL';
$lang['endpoint_help'] = 'Full API endpoint URL (e.g., https://api.openai.com/v1/chat/completions)';
$lang['model_name'] = 'Model Name';
$lang['model_help'] = 'Model identifier (e.g., gpt-4, claude-3-sonnet)';
$lang['request_format'] = 'Request Format';
$lang['request_format_help'] = 'Choose the API format this provider uses';
$lang['custom_format_settings'] = 'Custom Format Settings';
$lang['custom_request_format'] = 'Custom Request Format';
$lang['custom_request_help'] = 'JSON template for request body. Use {prompt}, {max_tokens}, {temperature} as placeholders';
$lang['response_path'] = 'Response Path';
$lang['response_path_help'] = 'Dot notation path to content in response (e.g., choices.0.message.content)';
$lang['add_provider'] = 'Add Provider';
$lang['existing_providers'] = 'Existing Custom Providers';
$lang['no_custom_providers'] = 'No custom providers configured yet.';
$lang['name'] = 'Name';
$lang['endpoint'] = 'Endpoint';
$lang['model'] = 'Model';
$lang['format'] = 'Format';
$lang['created'] = 'Created';
$lang['actions'] = 'Actions';
$lang['test'] = 'Test';
$lang['delete'] = 'Delete';
$lang['confirm_delete'] = 'Are you sure you want to delete this custom provider?';
$lang['provider_added'] = 'Custom provider added successfully!';
$lang['provider_deleted'] = 'Custom provider deleted successfully!';
$lang['test_success'] = 'Connection test successful!';
$lang['test_failed'] = 'Connection test failed!';
$lang['missing_required_fields'] = 'Please fill in all required fields.';
$lang['invalid_provider_name'] = 'Invalid provider name. Use only letters, numbers, and underscores.';
$lang['custom_providers_help_title'] = 'Custom AI Providers Help';
$lang['custom_providers_help_text'] = 'Add your own AI providers by configuring their API endpoints and request formats. This allows you to use any AI service that provides a REST API.';
$lang['supported_formats'] = 'Supported Request Formats';
$lang['example_endpoints'] = 'Example Endpoints';

// Additional AI Providers
$lang['provider_mistral'] = 'Mistral AI';
$lang['provider_perplexity'] = 'Perplexity AI';
$lang['provider_cohere'] = 'Cohere AI';

// Batch Generation
$lang['batch_content_generation'] = 'Batch Content Generation';
$lang['generate_batch'] = 'Generate Batch';
$lang['topics'] = 'Topics';
$lang['topics_help'] = 'Enter one topic per line (max 20 topics)';
$lang['batch_results'] = 'Batch Results';
$lang['batch_summary'] = 'Batch Summary';
$lang['total_items'] = 'Total Items';
$lang['successful'] = 'Successful';
$lang['errors'] = 'Errors';
$lang['batch_complete'] = 'Batch generation completed successfully!';
$lang['no_topics'] = 'Please enter at least one topic.';
$lang['too_many_topics'] = 'Maximum 20 topics allowed per batch.';
$lang['export_results'] = 'Export Results';
$lang['download_as_files'] = 'Download as Files';
$lang['batch_generation_help_title'] = 'Batch Generation Help';
$lang['batch_generation_help_text'] = 'Generate multiple pieces of content at once. Enter one topic per line in the textarea. The system will process each topic sequentially with a small delay between requests.';
$lang['batch_tips'] = 'Batch Generation Tips';
$lang['batch_tip_1'] = 'Use specific, clear topics for better results';
$lang['batch_tip_2'] = 'Keep topics related to each other for consistency';
$lang['batch_tip_3'] = 'Monitor your API usage and costs';
$lang['batch_tip_4'] = 'Test with a small batch first';
$lang['example_topics'] = 'Example Topics';

// Content History
$lang['content_history'] = 'Content History';
$lang['generation_history'] = 'Generation History';
$lang['total_generations'] = 'Total Generations';
$lang['providers_used'] = 'Providers Used';
$lang['avg_content_length'] = 'Average Content Length';
$lang['last_generation'] = 'Last Generation';
$lang['characters'] = 'characters';
$lang['provider_usage'] = 'Provider Usage';
$lang['generations'] = 'generations';
$lang['filter_history'] = 'Filter History';
$lang['all_providers'] = 'All Providers';
$lang['all_types'] = 'All Types';
$lang['date_from'] = 'Date From';
$lang['date_to'] = 'Date To';
$lang['search'] = 'Search';
$lang['search_prompt_content'] = 'Search prompt or content...';
$lang['filter'] = 'Filter';
$lang['clear_filters'] = 'Clear Filters';
$lang['items'] = 'items';
$lang['no_history_found'] = 'No history found matching your criteria.';
$lang['view'] = 'View';
$lang['restore'] = 'Restore';
$lang['delete'] = 'Delete';
$lang['page'] = 'Page';
$lang['of'] = 'of';
$lang['previous'] = 'Previous';
$lang['next'] = 'Next';
$lang['confirm_restore'] = 'Are you sure you want to restore this content?';
$lang['confirm_delete_version'] = 'Are you sure you want to delete this version?';
$lang['content_restored'] = 'Content restored successfully!';
$lang['version_deleted'] = 'Version deleted successfully!';
$lang['restored_content'] = 'Restored Content';
$lang['original_prompt'] = 'Original Prompt';

// Streaming
$lang['stream_content'] = 'Stream Content (Live)';
$lang['streaming_generation'] = 'Streaming Generation';
$lang['connected_to_provider'] = 'Connected to AI provider, generating content...';
$lang['generating'] = 'Generating...';
$lang['streaming_complete'] = 'Streaming complete!';

// News Integration
$lang['ai_content_generation'] = 'AI Content Generation';
$lang['article_topic'] = 'Article Topic';
$lang['generate_content'] = 'Generate Content';
$lang['stream_content_live'] = 'Stream Content (Live)';
$lang['ai_generation_status'] = 'AI Generation Status';
$lang['ai_generated_content'] = 'AI Generated Content';

$lang['save'] = 'Save';
$lang['submit'] = 'Submit';
?>

