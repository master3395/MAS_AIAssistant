<div class="mas-ai-content-history">
    <h2>{$module->Lang('content_history')}</h2>
    
    {if isset($message)}
        <div class="pagesuccessbox" style="margin: 20px 0;">
            {$message}
        </div>
    {/if}
    
    {if isset($restored_content)}
        <div class="restored-content" style="margin: 20px 0; padding: 15px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px;">
            <h4>{$module->Lang('restored_content')}</h4>
            <p><strong>{$module->Lang('original_prompt')}:</strong> {$restored_prompt}</p>
            <div style="background: white; padding: 10px; border-radius: 3px; margin: 10px 0; max-height: 300px; overflow-y: auto;">
                <pre style="white-space: pre-wrap; word-wrap: break-word;">{$restored_content}</pre>
            </div>
            <button onclick="copyToClipboard('{$restored_content|escape:"javascript"}')" class="pagebutton">
                {$module->Lang('copy_to_clipboard')}
            </button>
        </div>
    {/if}
    
    <!-- Statistics Dashboard -->
    <div class="history-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0;">
        <div class="stat-card" style="padding: 20px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">
            <h3>{$module->Lang('total_generations')}</h3>
            <p style="font-size: 2em; font-weight: bold; color: #0073aa;">{$stats.total_generations}</p>
        </div>
        
        <div class="stat-card" style="padding: 20px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">
            <h3>{$module->Lang('providers_used')}</h3>
            <p style="font-size: 2em; font-weight: bold; color: #46b450;">{$stats.providers_used}</p>
        </div>
        
        <div class="stat-card" style="padding: 20px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">
            <h3>{$module->Lang('avg_content_length')}</h3>
            <p style="font-size: 1.5em; font-weight: bold; color: #f56e28;">{$stats.avg_content_length|number_format:0} {$module->Lang('characters')}</p>
        </div>
        
        <div class="stat-card" style="padding: 20px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">
            <h3>{$module->Lang('last_generation')}</h3>
            <p style="font-size: 1em; font-weight: bold; color: #666;">{$stats.last_generation|date_format:"%d.%m.%Y %H:%M"}</p>
        </div>
    </div>
    
    <!-- Provider Usage Chart -->
    <div class="provider-stats" style="margin: 30px 0;">
        <h3>{$module->Lang('provider_usage')}</h3>
        <div style="margin-top: 15px;">
            {foreach from=$provider_stats item=stat}
                <div style="padding: 10px; margin: 5px 0; border-left: 4px solid #0073aa; background: #f9f9f9;">
                    <strong>{$stat.provider}</strong>
                    <span style="float: right; font-weight: bold;">{$stat.count} {$module->Lang('generations')}</span>
                </div>
            {/foreach}
        </div>
    </div>
    
    <!-- Filters -->
    <div class="history-filters" style="margin: 30px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">
        <h3>{$module->Lang('filter_history')}</h3>
        
        <form method="get" action="?module=MAS_AIAssistant&action=content_history" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;">
            <input type="hidden" name="module" value="MAS_AIAssistant" />
            <input type="hidden" name="action" value="content_history" />
            
            <div>
                <label><strong>{$module->Lang('provider')}:</strong></label><br/>
                <select name="filter_provider" style="width: 100%; padding: 5px;">
                    <option value="">{$module->Lang('all_providers')}</option>
                    {foreach from=$provider_filter_options key=key item=name}
                        <option value="{$key}" {if $current_filters.provider == $key}selected{/if}>{$name}</option>
                    {/foreach}
                </select>
            </div>
            
            <div>
                <label><strong>{$module->Lang('content_type')}:</strong></label><br/>
                <select name="filter_type" style="width: 100%; padding: 5px;">
                    <option value="">{$module->Lang('all_types')}</option>
                    {foreach from=$type_filter_options key=key item=name}
                        <option value="{$key}" {if $current_filters.type == $key}selected{/if}>{$name}</option>
                    {/foreach}
                </select>
            </div>
            
            <div>
                <label><strong>{$module->Lang('date_from')}:</strong></label><br/>
                <input type="date" name="filter_date_from" value="{$current_filters.date_from}" style="width: 100%; padding: 5px;" />
            </div>
            
            <div>
                <label><strong>{$module->Lang('date_to')}:</strong></label><br/>
                <input type="date" name="filter_date_to" value="{$current_filters.date_to}" style="width: 100%; padding: 5px;" />
            </div>
            
            <div>
                <label><strong>{$module->Lang('search')}:</strong></label><br/>
                <input type="text" name="search_term" value="{$current_filters.search}" placeholder="{$module->Lang('search_prompt_content')}" style="width: 100%; padding: 5px;" />
            </div>
            
            <div style="display: flex; align-items: end;">
                <button type="submit" class="pagebutton" style="padding: 8px 16px; margin-right: 10px;">
                    {$module->Lang('filter')}
                </button>
                <a href="?module=MAS_AIAssistant&action=content_history" class="pagebutton" style="padding: 8px 16px; text-decoration: none;">
                    {$module->Lang('clear_filters')}
                </a>
            </div>
        </form>
    </div>
    
    <!-- History List -->
    <div class="history-list">
        <h3>{$module->Lang('generation_history')} ({$total_count} {$module->Lang('items')})</h3>
        
        {if empty($history_data)}
            <p class="pagetext">{$module->Lang('no_history_found')}</p>
        {else}
            <div class="history-items">
                {foreach from=$history_data item=item}
                    <div class="history-item" style="margin: 15px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background: white;">
                        <div class="item-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <div>
                                <strong>{$item.prompt}</strong>
                                <br/><small style="color: #666;">
                                    {$item.provider} • {$item.type} • {$item.created_date|date_format:"%d.%m.%Y %H:%M"}
                                </small>
                            </div>
                            <div class="item-actions">
                                <button onclick="showContent({$item.id})" class="pagebutton" style="padding: 5px 10px; font-size: 12px; margin-right: 5px;">
                                    {$module->Lang('view')}
                                </button>
                                <button onclick="restoreContent({$item.id})" class="pagebutton" style="padding: 5px 10px; font-size: 12px; margin-right: 5px; background: #46b450;">
                                    {$module->Lang('restore')}
                                </button>
                                <button onclick="deleteVersion({$item.id})" class="pagebutton" style="padding: 5px 10px; font-size: 12px; background: #dc3232;">
                                    {$module->Lang('delete')}
                                </button>
                            </div>
                        </div>
                        
                        <div id="content_{$item.id}" class="item-content" style="display: none; background: #f9f9f9; padding: 10px; border-radius: 3px; margin-top: 10px; max-height: 300px; overflow-y: auto;">
                            <pre style="white-space: pre-wrap; word-wrap: break-word; font-size: 12px;">{$item.result}</pre>
                        </div>
                    </div>
                {/foreach}
            </div>
            
            <!-- Pagination -->
            {if $total_pages > 1}
                <div class="pagination" style="margin: 30px 0; text-align: center;">
                    {if $current_page > 1}
                        <a href="?module=MAS_AIAssistant&action=content_history&page={$current_page-1}" class="pagebutton" style="margin: 0 5px;">« {$module->Lang('previous')}</a>
                    {/if}
                    
                    <span style="margin: 0 15px;">
                        {$module->Lang('page')} {$current_page} {$module->Lang('of')} {$total_pages}
                    </span>
                    
                    {if $current_page < $total_pages}
                        <a href="?module=MAS_AIAssistant&action=content_history&page={$current_page+1}" class="pagebutton" style="margin: 0 5px;">{$module->Lang('next')} »</a>
                    {/if}
                </div>
            {/if}
        {/if}
    </div>
</div>

<script>
function showContent(id) {
    const contentDiv = document.getElementById('content_' + id);
    if (contentDiv.style.display === 'none') {
        contentDiv.style.display = 'block';
    } else {
        contentDiv.style.display = 'none';
    }
}

function restoreContent(id) {
    if (confirm('{$module->Lang("confirm_restore")|escape:"javascript"}')) {
        window.location.href = '?module=MAS_AIAssistant&action=content_history&history_action=restore&version_id=' + id;
    }
}

function deleteVersion(id) {
    if (confirm('{$module->Lang("confirm_delete_version")|escape:"javascript"}')) {
        window.location.href = '?module=MAS_AIAssistant&action=content_history&history_action=delete&version_id=' + id;
    }
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('{$module->Lang("copied")|escape:"javascript"}');
    });
}
</script>
