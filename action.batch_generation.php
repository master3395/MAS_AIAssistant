<?php
/**
 * Batch Content Generation
 * Generate multiple pieces of content at once
 */

if (!function_exists('cmsms')) exit;

if (!$this->VisibleToAdminUser()) {
    return $this->DisplayErrorPage($id, $params, $returnid, $this->Lang('accessdenied'));
}

// Get Smarty instance
$smarty = cmsms()->GetSmarty();

// Handle form submission
if (isset($params['batch_submit'])) {
    $provider = isset($params['provider']) ? $params['provider'] : $this->GetDefaultProvider();
    $topics = isset($params['topics']) ? trim($params['topics']) : '';
    $content_type = isset($params['content_type']) ? $params['content_type'] : 'page';
    $word_count = isset($params['word_count']) ? (int)$params['word_count'] : 500;
    $keywords = isset($params['keywords']) ? trim($params['keywords']) : '';
    
    // Parse topics (one per line)
    $topic_list = array_filter(array_map('trim', explode("\n", $topics)));
    
    if (empty($topic_list)) {
        $this->Redirect($id, 'batch_generation', $returnid, array(
            'error' => 'no_topics'
        ));
        return;
    }
    
    if (count($topic_list) > 20) {
        $this->Redirect($id, 'batch_generation', $returnid, array(
            'error' => 'too_many_topics'
        ));
        return;
    }
    
    // Security check
    $this->LoadClass('SecurityHelper');
    $security = new SecurityHelper($this);
    
    if (!$security->ValidateProvider($provider)) {
        $this->Redirect($id, 'batch_generation', $returnid, array(
            'error' => 'invalid_provider'
        ));
        return;
    }
    
    // Check rate limit (more lenient for batch)
    $rate_check = $security->CheckRateLimit(count($topic_list) * 2);
    if (!$rate_check['allowed']) {
        $this->Redirect($id, 'batch_generation', $returnid, array(
            'error' => 'rate_limit_exceeded',
            'reset_in' => $rate_check['reset_in']
        ));
        return;
    }
    
    // Process batch generation
    $results = array();
    $success_count = 0;
    $error_count = 0;
    
    // Handle custom providers
    $custom_provider_name = null;
    if (strpos($provider, 'custom_') === 0) {
        $custom_provider_name = substr($provider, 7);
    }
    
    $this->LoadClass('ContentGenerator');
    
    foreach ($topic_list as $index => $topic) {
        try {
            $generator = new ContentGenerator($this, $provider, $custom_provider_name);
            
            $options = array('word_count' => $word_count);
            if (!empty($keywords)) {
                $keywords_array = array_map('trim', explode(',', $keywords));
                $options['keywords'] = $keywords_array;
            }
            
            $result = null;
            switch ($content_type) {
                case 'blog':
                    $result = $generator->GenerateBlogPost($topic, $options);
                    break;
                case 'meta':
                    $result = $generator->GenerateMetaDescription($topic);
                    break;
                case 'page':
                default:
                    $result = $generator->GeneratePageContent($topic, $options);
                    break;
            }
            
            if ($result['success']) {
                $results[] = array(
                    'topic' => $topic,
                    'content' => $result['content'],
                    'success' => true,
                    'word_count' => str_word_count(strip_tags($result['content']))
                );
                $success_count++;
                
                // Save to database
                $db = cmsms()->GetDb();
                $prefix = cms_db_prefix();
                
                $query = "INSERT INTO {$prefix}module_mas_ai_generations 
                          (type, provider, prompt, result, created_date) 
                          VALUES (?, ?, ?, ?, NOW())";
                
                $db->Execute($query, array(
                    'batch_' . $content_type,
                    $provider,
                    $topic,
                    $result['content']
                ));
                
            } else {
                $results[] = array(
                    'topic' => $topic,
                    'error' => $result['error'],
                    'success' => false
                );
                $error_count++;
            }
            
            // Small delay between requests to be respectful
            usleep(500000); // 0.5 seconds
            
        } catch (Exception $e) {
            $results[] = array(
                'topic' => $topic,
                'error' => $e->getMessage(),
                'success' => false
            );
            $error_count++;
        }
    }
    
    // Log the batch request
    $security->LogRequest(null, $provider, 'batch_generation');
    
    // Store results in session for display
    if (!isset($_SESSION)) {
        session_start();
    }
    $_SESSION['mas_ai_batch_results'] = $results;
    $_SESSION['mas_ai_batch_stats'] = array(
        'total' => count($topic_list),
        'success' => $success_count,
        'errors' => $error_count,
        'provider' => $provider,
        'content_type' => $content_type
    );
    
    // Audit log
    $this->Audit(0, $this->Lang('friendlyname'), 
        'Batch generated ' . $success_count . ' items using ' . $provider);
    
    $this->Redirect($id, 'batch_generation', $returnid, array(
        'msg' => 'batch_complete',
        'show_results' => 1
    ));
    return;
}

// Display form
$smarty->assign('batch_form_start', $this->CreateFormStart($id, 'batch_generation', $returnid));
$smarty->assign('batch_form_end', $this->CreateFormEnd());

// Provider dropdown
$providers = $this->GetAvailableProviders();
$smarty->assign('provider_dropdown', $this->CreateInputDropdown(
    $id, 'provider', $providers, -1, $this->GetDefaultProvider()
));

// Content type dropdown
$content_types = array(
    'page' => $this->Lang('content_type_page'),
    'blog' => $this->Lang('content_type_blog'),
    'meta' => $this->Lang('content_type_meta')
);
$smarty->assign('content_type_dropdown', $this->CreateInputDropdown(
    $id, 'content_type', $content_types, -1, 'page'
));

// Topics textarea
$smarty->assign('topics_textarea', $this->CreateTextArea(false, $id, '', 'topics', '', '', '', '', 60, 10));

// Word count input
$smarty->assign('word_count_input', $this->CreateInputText($id, 'word_count', '500', 10));

// Keywords input
$smarty->assign('keywords_input', $this->CreateInputText($id, 'keywords', '', 80));

// Submit button
$smarty->assign('batch_submit_button', $this->CreateInputSubmit($id, 'batch_submit', $this->Lang('generate_batch')));
$smarty->assign('batch_submit_hidden', $this->CreateInputHidden($id, 'batch_submit', '1'));

// Show results if available
if (isset($params['show_results']) && isset($_SESSION['mas_ai_batch_results'])) {
    $smarty->assign('batch_results', $_SESSION['mas_ai_batch_results']);
    $smarty->assign('batch_stats', $_SESSION['mas_ai_batch_stats']);
    
    // Clear session data
    unset($_SESSION['mas_ai_batch_results']);
    unset($_SESSION['mas_ai_batch_stats']);
}

// Messages
if (isset($params['msg'])) {
    $smarty->assign('message', $this->Lang($params['msg']));
    $smarty->assign('message_type', 'success');
}

if (isset($params['error'])) {
    $smarty->assign('error_message', $this->Lang($params['error']));
}

echo $this->ProcessTemplate('batch_generation.tpl');
?>
