<div class="mas-ai-dashboard">
    <h2>{$module->Lang('dashboard_title')}</h2>
    
    <div class="dashboard-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0;">
        <div class="stat-card" style="padding: 20px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">
            <h3>{$module->Lang('total_generations')}</h3>
            <p style="font-size: 2em; font-weight: bold; color: #0073aa;">{$total_generations}</p>
        </div>
        
        <div class="stat-card" style="padding: 20px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">
            <h3>{$module->Lang('last_week')}</h3>
            <p style="font-size: 2em; font-weight: bold; color: #46b450;">{$last_week_generations}</p>
        </div>
        
        <div class="stat-card" style="padding: 20px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">
            <h3>{$module->Lang('default_provider')}</h3>
            <p style="font-size: 1.5em; margin-top: 10px;">{$providers[$default_provider]}</p>
        </div>
    </div>
    
    <div class="provider-status" style="margin: 30px 0;">
        <h3>{$module->Lang('configured_providers')}</h3>
        <div style="margin-top: 15px;">
            {foreach from=$providers key=provider_key item=provider_name}
                <div style="padding: 10px; margin: 5px 0; border-left: 4px solid {if in_array($provider_key, $configured_providers)}#46b450{else}#dc3232{/if}; background: #f9f9f9;">
                    <strong>{$provider_name}</strong>
                    {if in_array($provider_key, $configured_providers)}
                        <span style="color: #46b450;">✓ {$module->Lang('configured')}</span>
                    {else}
                        <span style="color: #dc3232;">✗ {$module->Lang('not_configured')}</span>
                    {/if}
                </div>
            {/foreach}
        </div>
    </div>
    
    <div class="quick-actions" style="margin: 30px 0;">
        <h3>{$module->Lang('quick_actions')}</h3>
        <div style="display: flex; gap: 10px; margin-top: 15px; flex-wrap: wrap;">
            <a href="#" onclick="jQuery('#systemtabs').tabs('option', 'active', 1); return false;" class="pageoptions" style="padding: 10px 20px; background: #0073aa; color: white; text-decoration: none; border-radius: 3px;">
                {$module->Lang('generate_content')}
            </a>
            <a href="#" onclick="jQuery('#systemtabs').tabs('option', 'active', 2); return false;" class="pageoptions" style="padding: 10px 20px; background: #46b450; color: white; text-decoration: none; border-radius: 3px;">
                {$module->Lang('generate_design')}
            </a>
            <a href="#" onclick="jQuery('#systemtabs').tabs('option', 'active', 3); return false;" class="pageoptions" style="padding: 10px 20px; background: #f56e28; color: white; text-decoration: none; border-radius: 3px;">
                {$module->Lang('configure_settings')}
            </a>
        </div>
    </div>
    
    <div class="info-box" style="padding: 15px; background: #e7f3ff; border-left: 4px solid #0073aa; margin-top: 30px;">
        <h4>{$module->Lang('getting_started')}</h4>
        <p>{$module->Lang('dashboard_help_text')}</p>
    </div>
</div>

