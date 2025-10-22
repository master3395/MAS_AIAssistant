<div class="mas-ai-design-generator">
    <h2>{$module->Lang('design_generation')}</h2>
    
    {if isset($error)}
        <div class="pageerrorbox" style="margin: 20px 0;">
            {$module->Lang($error)}
        </div>
    {/if}
    
    {if isset($show_result) && isset($smarty.session.mas_ai_generated_design)}
        <div class="pagesuccessbox" style="margin: 20px 0;">
            <h3>{$module->Lang('generated_design')}</h3>
            <div style="background: #f9f9f9; padding: 20px; margin-top: 10px; border: 1px solid #ddd; max-height: 400px; overflow-y: auto;">
                <pre style="white-space: pre-wrap; word-wrap: break-word;">{$smarty.session.mas_ai_generated_design}</pre>
            </div>
            <p style="margin-top: 10px;">
                <button onclick="copyDesignToClipboard()" class="pagebutton">{$module->Lang('copy_to_clipboard')}</button>
            </p>
        </div>
    {/if}
    
    {$design_form_start}
    
    <div class="pageoverflow" style="margin: 20px 0;">
        <p class="pagetext"><strong>{$module->Lang('provider')}:</strong></p>
        <p class="pageinput">{$design_provider_dropdown}</p>
    </div>
    
    <div class="pageoverflow" style="margin: 20px 0;">
        <p class="pagetext"><strong>{$module->Lang('design_type')}:</strong></p>
        <p class="pageinput">{$design_type_dropdown}</p>
    </div>
    
    <div class="pageoverflow" style="margin: 20px 0;">
        <p class="pagetext"><strong>{$module->Lang('description')}:</strong> *</p>
        <p class="pageinput">
            {$description_input}
            <br/><span class="pagetext">{$module->Lang('description_help')}</span>
        </p>
    </div>
    
    <div class="pageoverflow" style="margin: 30px 0;">
        <p class="pagetext">&nbsp;</p>
        <p class="pageinput">{$design_generate_button}</p>
    </div>
    
    {$design_form_end}
</div>

<script>
function copyDesignToClipboard() {
    var content = document.querySelector('.mas-ai-design-generator pre').textContent;
    navigator.clipboard.writeText(content).then(function() {
        alert('{$module->Lang("copied")|escape:"javascript"}');
    });
}
</script>

