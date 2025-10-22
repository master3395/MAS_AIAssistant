<?php
/**
 * MAS AI Assistant Module
 * 
 * AI-powered content generation, design creation, and SEO optimization for CMSMS
 * 
 * @package MAS_AIAssistant
 * @author master3395
 * @copyright 2025 master3395
 * @license GPL v3
 * @version 1.0.0
 */

if (!function_exists('cmsms')) exit;

class MAS_AIAssistant extends CMSModule
{
    /**
     * Module name
     */
    function GetName() {
        return 'MAS_AIAssistant';
    }
    
    /**
     * Friendly display name
     */
    function GetFriendlyName() {
        return $this->Lang('friendlyname');
    }

    /**
     * Module version
     */
    function GetVersion() {
        return '1.0.0';
    }
    
    /**
     * Help documentation
     */
    function GetHelp() {
        return $this->Lang('help');
    }
    
    /**
     * Author information
     */
    function GetAuthor() {
        return 'master3395';
    }

    function GetAuthorEmail() {
        return 'info [at] newstargeted [dot] com';
    }

    function GetAuthorUrl() {
        return 'https://newstargeted.com/contact/';
    }
    
    /**
     * Changelog
     */
    function GetChangeLog() {
        return $this->Lang('changelog');
    }
    
    /**
     * Module type
     */
    function IsPluginModule() {
        return true;
    }

    /**
     * Has admin interface
     */
    function HasAdmin() {
        return true;
    }
    
    /**
     * Admin section location
     */
    function GetAdminSection() {
        return 'extensions';
    }

    /**
     * Admin description
     */
    function GetAdminDescription() {
        return $this->Lang('moddescription');
    }

    /**
     * Visibility check
     */
    function VisibleToAdminUser() {
        return $this->CheckPermission('Use MAS_AIAssistant');
    }
    
    /**
     * Module dependencies
     */
    function GetDependencies() {
        return array();
    }

    /**
     * Minimum CMSMS version
     */
    function MinimumCMSVersion() {
        return "2.2.0";
    }

    /**
     * Minimum PHP version
     */
    function GetMinimumPHPVersion() {
        return "7.4.0";
    }

    /**
     * Frontend initialization
     */
    function InitializeFrontend() {
        $this->RegisterModulePlugin(true, false); 
        $this->RestrictUnknownParams();
        
        // Parameter sanitization
        $this->SetParameterType('action', CLEAN_STRING);
        $this->SetParameterType('provider', CLEAN_STRING);
        $this->SetParameterType('content_type', CLEAN_STRING);
        $this->SetParameterType('prompt', CLEAN_STRING);
    }

    /**
     * Admin initialization
     */
    function InitializeAdmin() {
        $this->CreateParameter('action', 'default', $this->Lang('help_action'));
        $this->CreateParameter('provider', 'huggingface', $this->Lang('help_provider'));
        $this->CreateParameter('content_type', 'page', $this->Lang('help_content_type'));
        $this->CreateParameter('prompt', '', $this->Lang('help_prompt'));
    }

    /**
     * Register events
     */
    function RegisterEvents() {
        // Future: hook into News, CMSContentManager events
    }

    /**
     * Post-install message
     */
    function InstallPostMessage() {
        return $this->Lang('postinstall');
    }

    /**
     * Post-uninstall message
     */
    function UninstallPostMessage() {
        return $this->Lang('postuninstall');
    }

    /**
     * Pre-uninstall message
     */
    function UninstallPreMessage() {
        return $this->Lang('really_uninstall');
    }

    /**
     * Show donations tab
     */
    function ShowDonationsTab() {
        return ($this->GetPreference("hidedonationstab") != $this->GetVersion());
    }
    
    /**
     * Load a class from lib/
     */
    function LoadClass($classname) {
        $filepath = dirname(__FILE__) . '/lib/class.' . $classname . '.php';
        if (file_exists($filepath)) {
            require_once($filepath);
            return true;
        }
        return false;
    }
    
    /**
     * Load a provider class
     */
    function LoadProvider($provider) {
        $filepath = dirname(__FILE__) . '/providers/class.' . $provider . 'Provider.php';
        if (file_exists($filepath)) {
            require_once($filepath);
            return true;
        }
        return false;
    }
    
    /**
     * Get available AI providers
     */
    function GetAvailableProviders() {
        $providers = array(
            'huggingface' => 'Hugging Face (Free)',
            'chatgpt' => 'ChatGPT (OpenAI)',
            'claude' => 'Claude (Anthropic)',
            'cursorai' => 'Cursor AI',
            'gemini' => 'Google Gemini',
            'groq' => 'Groq',
            'mistral' => 'Mistral AI',
            'perplexity' => 'Perplexity AI',
            'cohere' => 'Cohere AI'
        );
        
        // Add custom providers
        $custom_providers = json_decode($this->GetPreference('custom_providers_list', '[]'), true);
        foreach ($custom_providers as $provider_name) {
            try {
                $config_json = $this->GetPreference('custom_provider_' . $provider_name, '');
                $config = json_decode($config_json, true);
                if ($config) {
                    $providers['custom_' . $provider_name] = $config['name'] . ' (Custom)';
                }
            } catch (Exception $e) {
                // Skip invalid custom providers
            }
        }
        
        return $providers;
    }
    
    /**
     * Get default provider
     */
    function GetDefaultProvider() {
        return $this->GetPreference('default_provider', 'huggingface');
    }
    
    /**
     * Check if provider is configured
     */
    function IsProviderConfigured($provider) {
        if ($provider === 'huggingface') {
            return true; // No API key required for basic usage
        }
        
        // Check for API key in preferences or config.php
        $api_key = $this->GetProviderApiKey($provider);
        return !empty($api_key);
    }
    
    /**
     * Get API key for provider
     */
    function GetProviderApiKey($provider) {
        // Check config.php first
        $const_name = 'MAS_AI_' . strtoupper($provider) . '_KEY';
        if (defined($const_name)) {
            return constant($const_name);
        }
        
        // Fall back to encrypted preference
        if ($this->LoadClass('ConfigManager')) {
            $config = new ConfigManager($this);
            return $config->GetApiKey($provider);
        }
        return '';
    }
    
    /**
     * Get generation statistics
     */
    function GetGenerationStats() {
        try {
            $db = cmsms()->GetDb();
            $prefix = cms_db_prefix();
            
            // Check if table exists first
            $table_check = "SHOW TABLES LIKE '{$prefix}module_mas_ai_generations'";
            $table_exists = $db->GetOne($table_check);
            
            if (!$table_exists) {
                // Table doesn't exist, return default stats
                return array('total' => 0, 'last_week' => 0);
            }
            
            $query = "SELECT COUNT(*) as total, 
                      SUM(CASE WHEN created_date > DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as last_week
                      FROM {$prefix}module_mas_ai_generations";
            
            $result = $db->GetRow($query);
            return $result ? $result : array('total' => 0, 'last_week' => 0);
        } catch (Exception $e) {
            // Return default stats if there's any error
            return array('total' => 0, 'last_week' => 0);
        }
    }
}
?>


