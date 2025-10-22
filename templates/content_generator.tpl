<div class="mas-ai-content-generator">
    <h2>{$module->Lang('content_generation')}</h2>
    
    {if isset($error)}
        <div class="pageerrorbox" style="margin: 20px 0;">
            {$module->Lang($error)}
        </div>
    {/if}
    
    {if isset($show_result) && isset($smarty.session.mas_ai_generated_content)}
        <div class="pagesuccessbox" style="margin: 20px 0;">
            <h3>{$module->Lang('generated_content')}</h3>
            <div style="background: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ddd; max-height: 400px; overflow-y: auto;">
                <pre style="white-space: pre-wrap; word-wrap: break-word;">{$smarty.session.mas_ai_generated_content}</pre>
            </div>
            <p style="margin-top: 10px;">
                <button onclick="copyToClipboard()" class="pagebutton">{$module->Lang('copy_to_clipboard')}</button>
            </p>
        </div>
    {/if}
    
    {$content_form_start}
    
    <div class="pageoverflow" style="margin: 20px 0;">
        <p class="pagetext"><strong>{$module->Lang('provider')}:</strong></p>
        <p class="pageinput">{$provider_dropdown}</p>
    </div>
    
    <div class="pageoverflow" style="margin: 20px 0;">
        <p class="pagetext"><strong>{$module->Lang('content_type')}:</strong></p>
        <p class="pageinput">{$content_type_dropdown}</p>
    </div>
    
    <div class="pageoverflow" style="margin: 20px 0;">
        <p class="pagetext"><strong>{$module->Lang('topic')}:</strong> *</p>
        <p class="pageinput">
            {$topic_input}
            <br/><span class="pagetext">{$module->Lang('topic_help')}</span>
        </p>
    </div>
    
    <div class="pageoverflow" style="margin: 20px 0;">
        <p class="pagetext"><strong>{$module->Lang('word_count')}:</strong></p>
        <p class="pageinput">
            {$word_count_input}
            <br/><span class="pagetext">{$module->Lang('word_count_help')}</span>
        </p>
    </div>
    
    <div class="pageoverflow" style="margin: 20px 0;">
        <p class="pagetext"><strong>{$module->Lang('keywords')}:</strong></p>
        <p class="pageinput">
            {$keywords_input}
            <br/><span class="pagetext">{$module->Lang('keywords_help')}</span>
        </p>
    </div>
    
    <div class="pageoverflow" style="margin: 30px 0;">
        <p class="pagetext">&nbsp;</p>
        <p class="pageinput">{$generate_button}</p>
    </div>
    
    {$content_form_end}
</div>

<script>
function copyToClipboard() {
    var content = document.querySelector('.mas-ai-content-generator pre').textContent;
    navigator.clipboard.writeText(content).then(function() {
        alert('{$module->Lang("copied")|escape:"javascript"}');
    });
}
</script>

