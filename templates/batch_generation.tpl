<div class="mas-ai-batch-generation">
    <h2>{$module->Lang('batch_content_generation')}</h2>
    
    {if isset($error_message)}
        <div class="pageerrorbox" style="margin: 20px 0;">
            {$error_message}
        </div>
    {/if}
    
    {if isset($message)}
        <div class="pagesuccessbox" style="margin: 20px 0;">
            {$message}
        </div>
    {/if}
    
    {if isset($batch_results)}
        <div class="batch-results" style="margin: 20px 0;">
            <h3>{$module->Lang('batch_results')}</h3>
            
            <div class="batch-stats" style="padding: 15px; background: #f9f9f9; border-radius: 5px; margin-bottom: 20px;">
                <strong>{$module->Lang('batch_summary')}:</strong><br/>
                {$module->Lang('total_items')}: {$batch_stats.total}<br/>
                {$module->Lang('successful')}: <span style="color: #46b450;">{$batch_stats.success}</span><br/>
                {$module->Lang('errors')}: <span style="color: #dc3232;">{$batch_stats.errors}</span><br/>
                {$module->Lang('provider')}: {$batch_stats.provider}<br/>
                {$module->Lang('content_type')}: {$batch_stats.content_type}
            </div>
            
            <div class="results-list" style="max-height: 600px; overflow-y: auto;">
                {foreach from=$batch_results item=result}
                    <div class="result-item" style="margin: 15px 0; padding: 15px; border: 1px solid {if $result.success}#46b450{else}#dc3232{/if}; border-radius: 5px; background: {if $result.success}#f9f9f9{else}#fff5f5{/if};">
                        <h4 style="margin: 0 0 10px 0; color: {if $result.success}#46b450{else}#dc3232{/if};">
                            {if $result.success}✓{else}✗{/if} {$result.topic}
                        </h4>
                        
                        {if $result.success}
                            <div class="content-preview" style="background: white; padding: 10px; border-radius: 3px; margin: 10px 0;">
                                <pre style="white-space: pre-wrap; word-wrap: break-word; font-size: 12px; max-height: 200px; overflow-y: auto;">{$result.content}</pre>
                            </div>
                            <div class="content-actions" style="margin-top: 10px;">
                                <button onclick="copyToClipboard('{$result.content|escape:"javascript"}')" class="pagebutton" style="padding: 5px 10px; font-size: 12px;">
                                    {$module->Lang('copy_to_clipboard')}
                                </button>
                                <span style="margin-left: 10px; font-size: 12px; color: #666;">
                                    {$module->Lang('word_count')}: {$result.word_count}
                                </span>
                            </div>
                        {else}
                            <div class="error-message" style="color: #dc3232; font-style: italic;">
                                {$result.error}
                            </div>
                        {/if}
                    </div>
                {/foreach}
            </div>
            
            <div class="batch-actions" style="margin-top: 20px; padding: 15px; background: #e7f3ff; border-radius: 5px;">
                <button onclick="exportBatchResults()" class="pagebutton" style="padding: 10px 20px; margin-right: 10px;">
                    {$module->Lang('export_results')}
                </button>
                <button onclick="downloadBatchResults()" class="pagebutton" style="padding: 10px 20px; background: #46b450;">
                    {$module->Lang('download_as_files')}
                </button>
            </div>
        </div>
    {/if}
    
    {$batch_form_start}
    {$batch_submit_hidden}
    
    <div class="batch-form" style="margin: 20px 0;">
        <div class="pageoverflow" style="margin: 20px 0;">
            <p class="pagetext"><strong>{$module->Lang('provider')}:</strong></p>
            <p class="pageinput">{$provider_dropdown}</p>
        </div>
        
        <div class="pageoverflow" style="margin: 20px 0;">
            <p class="pagetext"><strong>{$module->Lang('content_type')}:</strong></p>
            <p class="pageinput">{$content_type_dropdown}</p>
        </div>
        
        <div class="pageoverflow" style="margin: 20px 0;">
            <p class="pagetext"><strong>{$module->Lang('topics')}:</strong> *</p>
            <p class="pageinput">
                {$topics_textarea}
                <br/><span class="pagetext">{$module->Lang('topics_help')}</span>
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
            <p class="pageinput">{$batch_submit_button}</p>
        </div>
    </div>
    
    {$batch_form_end}
    
    <!-- Help Section -->
    <div class="help-section" style="margin-top: 40px; padding: 20px; background: #e7f3ff; border-left: 4px solid #0073aa;">
        <h4>{$module->Lang('batch_generation_help_title')}</h4>
        <p>{$module->Lang('batch_generation_help_text')}</p>
        
        <h5>{$module->Lang('batch_tips')}</h5>
        <ul>
            <li>{$module->Lang('batch_tip_1')}</li>
            <li>{$module->Lang('batch_tip_2')}</li>
            <li>{$module->Lang('batch_tip_3')}</li>
            <li>{$module->Lang('batch_tip_4')}</li>
        </ul>
        
        <h5>{$module->Lang('example_topics')}</h5>
        <pre style="background: #f9f9f9; padding: 10px; border-radius: 3px; font-size: 12px;">
Benefits of renewable energy
How to start a small business
Introduction to machine learning
Best practices for web security
Tips for healthy eating
        </pre>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('{$module->Lang("copied")|escape:"javascript"}');
    });
}

function exportBatchResults() {
    const results = {json_encode($batch_results)};
    const stats = {json_encode($batch_stats)};
    
    const exportData = {
        stats: stats,
        results: results,
        exported_at: new Date().toISOString()
    };
    
    const blob = new Blob([JSON.stringify(exportData, null, 2)], {type: 'application/json'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'batch_generation_results.json';
    a.click();
    URL.revokeObjectURL(url);
}

function downloadBatchResults() {
    const results = {json_encode($batch_results)};
    
    results.forEach((result, index) => {
        if (result.success) {
            const blob = new Blob([result.content], {type: 'text/plain'});
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'content_' + (index + 1) + '_' + result.topic.replace(/[^a-zA-Z0-9]/g, '_') + '.txt';
            a.click();
            URL.revokeObjectURL(url);
        }
    });
}
</script>
