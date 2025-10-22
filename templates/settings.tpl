<div class="mas-ai-settings">
    <h2>{$module->Lang('settings')}</h2>
    
    {$settings_form_start}
    {$submit_hidden}
    
    <fieldset style="margin: 20px 0; padding: 15px; border: 1px solid #ddd;">
        <legend><strong>{$module->Lang('general_settings')}</strong></legend>
        
        <div class="pageoverflow" style="margin: 15px 0;">
            <p class="pagetext"><strong>{$module->Lang('default_provider')}:</strong></p>
            <p class="pageinput">
                {$default_provider_dropdown}
                <br/><span class="pagetext">{$module->Lang('default_provider_help')}</span>
            </p>
        </div>
        
        <div class="pageoverflow" style="margin: 15px 0;">
            <p class="pagetext"><strong>{$module->Lang('storage_method')}:</strong></p>
            <p class="pageinput">
                {$storage_method_dropdown}
                <br/><span class="pagetext">{$module->Lang('storage_method_help')}</span>
            </p>
        </div>
        
        <div class="pageoverflow" style="margin: 15px 0;">
            <p class="pagetext"><strong>{$module->Lang('rate_limit')}:</strong></p>
            <p class="pageinput">
                {$rate_limit_input} {$module->Lang('requests_per_minute')}
                <br/><span class="pagetext">{$module->Lang('rate_limit_help')}</span>
            </p>
        </div>
    </fieldset>
    
    <fieldset style="margin: 20px 0; padding: 15px; border: 1px solid #ddd;">
        <legend><strong>{$module->Lang('api_keys')}</strong></legend>
        
        <p class="pageinput" style="margin-bottom: 15px;">
            <em>{$module->Lang('api_keys_help')}</em>
        </p>
        
        {foreach from=$api_keys key=provider item=key_data}
            <div class="pageoverflow" style="margin: 15px 0;">
                <p class="pagetext">
                    <strong>{$provider|capitalize}:</strong>
                    {if $key_data.configured}
                        <span style="color: #46b450;">✓ {$module->Lang('configured')}</span>
                    {/if}
                </p>
                <p class="pageinput">
                    {$key_data.input}
                    <br/><span class="pagetext">{$module->Lang('api_key_placeholder')}</span>
                </p>
            </div>
        {/foreach}
    </fieldset>
    
    <div class="pageoverflow" style="margin: 30px 0;">
        <p class="pagetext">&nbsp;</p>
        <p class="pageinput">{$settings_submit}</p>
    </div>
    
    {$settings_form_end}
    
    <div class="info-box" style="padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107; margin-top: 30px;">
        <h4>{$module->Lang('config_php_option')}</h4>
        <p>{$module->Lang('config_php_help')}</p>
        <pre style="background: #f9f9f9; padding: 10px; margin-top: 10px; overflow-x: auto;">
define('MAS_AI_CHATGPT_KEY', 'your-api-key-here');
define('MAS_AI_CLAUDE_KEY', 'your-api-key-here');
define('MAS_AI_GEMINI_KEY', 'your-api-key-here');
define('MAS_AI_GROQ_KEY', 'your-api-key-here');
        </pre>
    </div>
</div>

