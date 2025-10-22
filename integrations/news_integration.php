<?php
/**
 * News Module Integration
 * Adds "Generate with AI" button to News module
 */

if (!function_exists('cmsms')) exit;

// Check if News module is available
$news_mod = cms_utils::get_module('News');
if (!$news_mod) {
    return; // News module not installed
}

// Check if MAS AI Assistant is available
$ai_mod = cms_utils::get_module('MAS_AIAssistant');
if (!$ai_mod) {
    return; // AI Assistant not installed
}

// Add AI generation button to News article forms
function addAIGenerationButton($params) {
    global $ai_mod;
    
    if (!isset($params['form'])) {
        return;
    }
    
    $form = $params['form'];
    
    // Add AI generation section
    $ai_section = '
    <div class="mas-ai-news-integration" style="margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">
        <h4>AI Content Generation</h4>
        <p>Generate article content using AI:</p>
        
        <div style="margin: 10px 0;">
            <label for="ai_topic">Article Topic:</label><br/>
            <input type="text" id="ai_topic" name="ai_topic" style="width: 100%; max-width: 500px; padding: 8px; margin: 5px 0;" placeholder="Enter the main topic for your article..." />
        </div>
        
        <div style="margin: 10px 0;">
            <label for="ai_provider">AI Provider:</label><br/>
            <select id="ai_provider" name="ai_provider" style="padding: 8px; margin: 5px 0;">
                <option value="huggingface">Hugging Face (Free)</option>
                <option value="chatgpt">ChatGPT</option>
                <option value="claude">Claude</option>
                <option value="gemini">Google Gemini</option>
                <option value="groq">Groq</option>
            </select>
        </div>
        
        <div style="margin: 10px 0;">
            <label for="ai_word_count">Word Count:</label><br/>
            <input type="number" id="ai_word_count" name="ai_word_count" value="500" min="100" max="2000" style="padding: 8px; margin: 5px 0;" />
        </div>
        
        <div style="margin: 10px 0;">
            <button type="button" id="generate_ai_content" class="pagebutton" style="padding: 10px 20px; margin-right: 10px;">
                Generate Content
            </button>
            <button type="button" id="stream_ai_content" class="pagebutton" style="padding: 10px 20px; background: #46b450;">
                Stream Content (Live)
            </button>
        </div>
        
        <div id="ai_generation_status" style="margin: 10px 0; padding: 10px; display: none; border-radius: 3px;"></div>
        <div id="ai_generated_content" style="margin: 10px 0; padding: 15px; background: white; border: 1px solid #ccc; border-radius: 3px; display: none; max-height: 400px; overflow-y: auto;"></div>
    </div>
    
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const generateBtn = document.getElementById("generate_ai_content");
        const streamBtn = document.getElementById("stream_ai_content");
        const statusDiv = document.getElementById("ai_generation_status");
        const contentDiv = document.getElementById("ai_generated_content");
        const topicInput = document.getElementById("ai_topic");
        const providerSelect = document.getElementById("ai_provider");
        const wordCountInput = document.getElementById("ai_word_count");
        
        function showStatus(message, type = "info") {
            statusDiv.style.display = "block";
            statusDiv.className = "ai-status-" + type;
            statusDiv.innerHTML = message;
            
            if (type === "success") {
                statusDiv.style.background = "#d4edda";
                statusDiv.style.color = "#155724";
                statusDiv.style.border = "1px solid #c3e6cb";
            } else if (type === "error") {
                statusDiv.style.background = "#f8d7da";
                statusDiv.style.color = "#721c24";
                statusDiv.style.border = "1px solid #f5c6cb";
            } else {
                statusDiv.style.background = "#d1ecf1";
                statusDiv.style.color = "#0c5460";
                statusDiv.style.border = "1px solid #bee5eb";
            }
        }
        
        function generateContent(streaming = false) {
            const topic = topicInput.value.trim();
            if (!topic) {
                showStatus("Please enter an article topic.", "error");
                return;
            }
            
            const provider = providerSelect.value;
            const wordCount = wordCountInput.value;
            
            showStatus("Generating content" + (streaming ? " (streaming)..." : "..."), "info");
            contentDiv.style.display = "none";
            
            if (streaming) {
                // Use Server-Sent Events for streaming
                const eventSource = new EventSource(
                    "?module=MAS_AIAssistant&action=streaming&provider=" + encodeURIComponent(provider) + 
                    "&prompt=" + encodeURIComponent(topic) + "&type=content&word_count=" + wordCount
                );
                
                let fullContent = "";
                
                eventSource.onmessage = function(event) {
                    const data = JSON.parse(event.data);
                    
                    if (data.status === "connected") {
                        showStatus("Connected to AI provider, generating content...", "info");
                    } else if (data.status === "chunk") {
                        fullContent += data.chunk;
                        contentDiv.innerHTML = fullContent;
                        contentDiv.style.display = "block";
                        showStatus("Generating... " + data.progress + "%", "info");
                    } else if (data.status === "complete") {
                        showStatus("Content generated successfully! (" + data.word_count + " words)", "success");
                        eventSource.close();
                    } else if (data.status === "error") {
                        showStatus("Error: " + data.error, "error");
                        eventSource.close();
                    }
                };
                
                eventSource.onerror = function() {
                    showStatus("Connection error occurred.", "error");
                    eventSource.close();
                };
                
            } else {
                // Use regular AJAX for non-streaming
                fetch("?module=MAS_AIAssistant&action=generate_content", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: "provider=" + encodeURIComponent(provider) + 
                          "&content_type=blog&topic=" + encodeURIComponent(topic) + 
                          "&word_count=" + wordCount
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        contentDiv.innerHTML = data.content;
                        contentDiv.style.display = "block";
                        showStatus("Content generated successfully!", "success");
                    } else {
                        showStatus("Error: " + data.error, "error");
                    }
                })
                .catch(error => {
                    showStatus("Error: " + error.message, "error");
                });
            }
        }
        
        generateBtn.addEventListener("click", () => generateContent(false));
        streamBtn.addEventListener("click", () => generateContent(true));
        
        // Auto-populate topic from article title if available
        const titleInput = document.querySelector("input[name=\'title\']");
        if (titleInput && !topicInput.value) {
            titleInput.addEventListener("blur", function() {
                if (!topicInput.value && this.value) {
                    topicInput.value = this.value;
                }
            });
        }
    });
    </script>';
    
    // Insert AI section before the content textarea
    $content_textarea_pos = strpos($form, '<textarea');
    if ($content_textarea_pos !== false) {
        $form = substr_replace($form, $ai_section, $content_textarea_pos, 0);
    }
    
    $params['form'] = $form;
}

// Hook into News module
if (function_exists('cms_utils::get_module')) {
    // Add hook for News article forms
    $news_mod->AddEventHandler('News', 'ArticleForm', 'addAIGenerationButton');
}
?>
