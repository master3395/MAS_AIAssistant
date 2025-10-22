<div class="mas-ai-custom-providers">
    <h2>{$module->Lang('custom_providers')}</h2>
    
    {if isset($message)}
        <div class="page{($message_type == 'success') ? 'success' : 'error'}box" style="margin: 20px 0;">
            {$message}
            {if isset($test_message)}
                <br/><small>{$test_message}</small>
            {/if}
        </div>
    {/if}
    
    <div class="custom-providers-content">
        <!-- Add New Provider Form -->
        <div class="add-provider-section" style="margin-bottom: 40px; padding: 20px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">
            <h3>{$module->Lang('add_custom_provider')}</h3>
            
            {$form_start}
            {$add_provider_hidden}
            
            <div class="pageoverflow" style="margin: 15px 0;">
                <p class="pagetext"><strong>{$module->Lang('provider_name')}:</strong> *</p>
                <p class="pageinput">
                    {$provider_name_input}
                    <br/><span class="pagetext">{$module->Lang('provider_name_help')}</span>
                </p>
            </div>
            
            <div class="pageoverflow" style="margin: 15px 0;">
                <p class="pagetext"><strong>{$module->Lang('display_name')}:</strong></p>
                <p class="pageinput">
                    {$display_name_input}
                    <br/><span class="pagetext">{$module->Lang('display_name_help')}</span>
                </p>
            </div>
            
            <div class="pageoverflow" style="margin: 15px 0;">
                <p class="pagetext"><strong>{$module->Lang('description')}:</strong></p>
                <p class="pageinput">
                    {$description_input}
                    <br/><span class="pagetext">{$module->Lang('description_help')}</span>
                </p>
            </div>
            
            <div class="pageoverflow" style="margin: 15px 0;">
                <p class="pagetext"><strong>{$module->Lang('api_key')}:</strong> *</p>
                <p class="pageinput">
                    {$api_key_input}
                    <br/><span class="pagetext">{$module->Lang('api_key_help')}</span>
                </p>
            </div>
            
            <div class="pageoverflow" style="margin: 15px 0;">
                <p class="pagetext"><strong>{$module->Lang('endpoint_url')}:</strong> *</p>
                <p class="pageinput">
                    {$endpoint_input}
                    <br/><span class="pagetext">{$module->Lang('endpoint_help')}</span>
                </p>
            </div>
            
            <div class="pageoverflow" style="margin: 15px 0;">
                <p class="pagetext"><strong>{$module->Lang('model_name')}:</strong></p>
                <p class="pageinput">
                    {$model_input}
                    <br/><span class="pagetext">{$module->Lang('model_help')}</span>
                </p>
            </div>
            
            <div class="pageoverflow" style="margin: 15px 0;">
                <p class="pagetext"><strong>{$module->Lang('request_format')}:</strong></p>
                <p class="pageinput">
                    {$request_format_dropdown}
                    <br/><span class="pagetext">{$module->Lang('request_format_help')}</span>
                </p>
            </div>
            
            <div class="custom-format-section" style="margin: 20px 0; padding: 15px; background: #fff; border: 1px solid #ccc; border-radius: 3px; display: none;">
                <h4>{$module->Lang('custom_format_settings')}</h4>
                
                <div class="pageoverflow" style="margin: 15px 0;">
                    <p class="pagetext"><strong>{$module->Lang('custom_request_format')}:</strong></p>
                    <p class="pageinput">
                        {$custom_request_textarea}
                        <br/><span class="pagetext">{$module->Lang('custom_request_help')}</span>
                    </p>
                </div>
                
                <div class="pageoverflow" style="margin: 15px 0;">
                    <p class="pagetext"><strong>{$module->Lang('response_path')}:</strong></p>
                    <p class="pageinput">
                        {$response_path_input}
                        <br/><span class="pagetext">{$module->Lang('response_path_help')}</span>
                    </p>
                </div>
            </div>
            
            <div class="pageoverflow" style="margin: 30px 0;">
                <p class="pagetext">&nbsp;</p>
                <p class="pageinput">{$add_provider_button}</p>
            </div>
            
            {$form_end}
        </div>
        
        <!-- Existing Providers List -->
        <div class="providers-list-section">
            <h3>{$module->Lang('existing_providers')}</h3>
            
            {if empty($providers_data)}
                <p class="pagetext">{$module->Lang('no_custom_providers')}</p>
            {else}
                <div class="providers-table" style="margin-top: 20px;">
                    <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
                        <thead>
                            <tr style="background: #f5f5f5;">
                                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">{$module->Lang('name')}</th>
                                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">{$module->Lang('endpoint')}</th>
                                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">{$module->Lang('model')}</th>
                                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">{$module->Lang('format')}</th>
                                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">{$module->Lang('created')}</th>
                                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">{$module->Lang('actions')}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$providers_data item=provider}
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #ddd;">
                                        <strong>{$provider.display_name}</strong>
                                        {if $provider.description}
                                            <br/><small style="color: #666;">{$provider.description}</small>
                                        {/if}
                                    </td>
                                    <td style="padding: 10px; border: 1px solid #ddd;">
                                        <code style="font-size: 11px;">{$provider.endpoint}</code>
                                    </td>
                                    <td style="padding: 10px; border: 1px solid #ddd;">{$provider.model}</td>
                                    <td style="padding: 10px; border: 1px solid #ddd;">{$provider.format}</td>
                                    <td style="padding: 10px; border: 1px solid #ddd;">{$provider.created}</td>
                                    <td style="padding: 10px; border: 1px solid #ddd;">
                                        <a href="?module=MAS_AIAssistant&action=custom_providers&test_provider={$provider.name}" 
                                           class="pagebutton" style="padding: 5px 10px; font-size: 12px; margin-right: 5px;">
                                            {$module->Lang('test')}
                                        </a>
                                        <a href="?module=MAS_AIAssistant&action=custom_providers&delete_provider={$provider.name}" 
                                           class="pagebutton" style="padding: 5px 10px; font-size: 12px; background: #dc3232;"
                                           onclick="return confirm('{$module->Lang('confirm_delete')|escape:"javascript"}')">
                                            {$module->Lang('delete')}
                                        </a>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            {/if}
        </div>
    </div>
    
    <!-- Help Section -->
    <div class="help-section" style="margin-top: 40px; padding: 20px; background: #e7f3ff; border-left: 4px solid #0073aa;">
        <h4>{$module->Lang('custom_providers_help_title')}</h4>
        <p>{$module->Lang('custom_providers_help_text')}</p>
        
        <h5>{$module->Lang('supported_formats')}</h5>
        <ul>
            <li><strong>OpenAI Compatible:</strong> Works with most OpenAI-compatible APIs</li>
            <li><strong>Anthropic Claude:</strong> For Claude-compatible APIs</li>
            <li><strong>Hugging Face:</strong> For Hugging Face Inference API</li>
            <li><strong>Custom Format:</strong> Define your own request/response format</li>
        </ul>
        
        <h5>{$module->Lang('example_endpoints')}</h5>
        <ul>
            <li><code>https://api.openai.com/v1/chat/completions</code></li>
            <li><code>https://api.anthropic.com/v1/messages</code></li>
            <li><code>https://api-inference.huggingface.co/models/mistralai/Mistral-7B-Instruct-v0.2</code></li>
            <li><code>https://your-custom-api.com/v1/generate</code></li>
        </ul>
    </div>
</div>

<script>
// Show/hide custom format section based on selection
document.addEventListener('DOMContentLoaded', function() {
    const formatSelect = document.querySelector('select[name="request_format"]');
    const customSection = document.querySelector('.custom-format-section');
    
    function toggleCustomSection() {
        if (formatSelect.value === 'custom') {
            customSection.style.display = 'block';
        } else {
            customSection.style.display = 'none';
        }
    }
    
    formatSelect.addEventListener('change', toggleCustomSection);
    toggleCustomSection(); // Initial check
});
</script>
