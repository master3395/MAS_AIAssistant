/**
 * MAS AI Assistant - Admin JavaScript
 */

(function($) {
    'use strict';
    
    // Initialize on document ready
    $(document).ready(function() {
        MASAIAssistant.init();
    });
    
    // Main module object
    var MASAIAssistant = {
        
        /**
         * Initialize the module
         */
        init: function() {
            this.setupFormHandlers();
            this.setupCopyButtons();
            this.setupProviderInfo();
            this.setupValidation();
        },
        
        /**
         * Setup form submission handlers
         */
        setupFormHandlers: function() {
            // Content generation form
            $('form[action*="generate_content"]').on('submit', function(e) {
                var topic = $('input[name*="topic"]').val();
                if (!topic || topic.trim() === '') {
                    e.preventDefault();
                    alert('Please enter a topic or description.');
                    return false;
                }
                
                // Show loading indicator
                var $submitBtn = $(this).find('input[type="submit"]');
                $submitBtn.prop('disabled', true).val('Generating...');
            });
            
            // Design generation form
            $('form[action*="generate_design"]').on('submit', function(e) {
                var description = $('textarea[name*="description"]').val();
                if (!description || description.trim() === '') {
                    e.preventDefault();
                    alert('Please enter a description for the design.');
                    return false;
                }
                
                // Show loading indicator
                var $submitBtn = $(this).find('input[type="submit"]');
                $submitBtn.prop('disabled', true).val('Generating...');
            });
        },
        
        /**
         * Setup copy to clipboard buttons
         */
        setupCopyButtons: function() {
            // Already handled in templates with inline functions
            // This provides fallback for older browsers
            if (!navigator.clipboard) {
                console.warn('Clipboard API not available');
            }
        },
        
        /**
         * Setup provider information tooltips
         */
        setupProviderInfo: function() {
            // Add tooltips to provider dropdowns
            $('select[name*="provider"]').each(function() {
                $(this).on('change', function() {
                    var selected = $(this).val();
                    var info = MASAIAssistant.getProviderInfo(selected);
                    
                    if (info) {
                        // Show info message
                        var $infoBox = $(this).siblings('.provider-info');
                        if ($infoBox.length === 0) {
                            $infoBox = $('<div class="provider-info" style="margin-top: 10px; padding: 10px; background: #e7f3ff; border-left: 3px solid #0073aa; font-size: 12px;"></div>');
                            $(this).parent().append($infoBox);
                        }
                        $infoBox.html('<strong>' + info.name + ':</strong> ' + info.description);
                    }
                });
            });
        },
        
        /**
         * Get provider information
         */
        getProviderInfo: function(provider) {
            var providers = {
                'huggingface': {
                    name: 'Hugging Face',
                    description: 'Free, open-source AI. No API key required. Great for getting started!'
                },
                'chatgpt': {
                    name: 'ChatGPT (OpenAI)',
                    description: 'High-quality content generation. Requires API key.'
                },
                'claude': {
                    name: 'Claude (Anthropic)',
                    description: 'Advanced AI with excellent detailed content. Requires API key.'
                },
                'gemini': {
                    name: 'Google Gemini',
                    description: 'Google\'s AI with free tier available. Requires API key.'
                },
                'groq': {
                    name: 'Groq',
                    description: 'Ultra-fast inference with free tier. Requires API key.'
                }
            };
            
            return providers[provider] || null;
        },
        
        /**
         * Setup form validation
         */
        setupValidation: function() {
            // Word count validation
            $('input[name*="word_count"]').on('input', function() {
                var value = parseInt($(this).val());
                if (isNaN(value) || value < 100) {
                    $(this).val(100);
                } else if (value > 5000) {
                    $(this).val(5000);
                }
            });
            
            // Rate limit validation
            $('input[name*="rate_limit"]').on('input', function() {
                var value = parseInt($(this).val());
                if (isNaN(value) || value < 1) {
                    $(this).val(1);
                } else if (value > 100) {
                    $(this).val(100);
                }
            });
        },
        
        /**
         * Show loading indicator
         */
        showLoading: function($element) {
            $element.prop('disabled', true);
            $element.after('<span class="mas-ai-loading" style="margin-left: 10px;"></span>');
        },
        
        /**
         * Hide loading indicator
         */
        hideLoading: function($element) {
            $element.prop('disabled', false);
            $element.siblings('.mas-ai-loading').remove();
        }
    };
    
    // Export to global scope
    window.MASAIAssistant = MASAIAssistant;
    
})(jQuery);

