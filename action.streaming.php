<?php
/**
 * Real-time Streaming Response Handler
 * Supports Server-Sent Events (SSE) for live AI response streaming
 */

if (!function_exists('cmsms')) exit;

if (!$this->VisibleToAdminUser()) {
    http_response_code(403);
    exit('Access denied');
}

// Get parameters
$action = isset($_GET['action']) ? $_GET['action'] : '';
$provider = isset($_GET['provider']) ? $_GET['provider'] : '';
$prompt = isset($_GET['prompt']) ? $_GET['prompt'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : 'content';

// Validate inputs
if (empty($provider) || empty($prompt)) {
    http_response_code(400);
    exit('Missing required parameters');
}

// Security check
$this->LoadClass('SecurityHelper');
$security = new SecurityHelper($this);

if (!$security->ValidateProvider($provider)) {
    http_response_code(400);
    exit('Invalid provider');
}

// Check rate limit
$rate_check = $security->CheckRateLimit();
if (!$rate_check['allowed']) {
    http_response_code(429);
    exit('Rate limit exceeded');
}

// Set up Server-Sent Events
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Cache-Control');

// Function to send SSE data
function sendSSE($data, $event = 'message') {
    echo "event: $event\n";
    echo "data: " . json_encode($data) . "\n\n";
    ob_flush();
    flush();
}

// Send initial connection
sendSSE(array('status' => 'connected', 'timestamp' => time()), 'connected');

try {
    // Handle custom providers
    $custom_provider_name = null;
    if (strpos($provider, 'custom_') === 0) {
        $custom_provider_name = substr($provider, 7);
    }
    
    // Load appropriate generator
    switch ($type) {
        case 'content':
            $this->LoadClass('ContentGenerator');
            $generator = new ContentGenerator($this, $provider, $custom_provider_name);
            $result = $generator->GeneratePageContentStreaming($prompt, array('word_count' => 500));
            break;
            
        case 'design':
            $this->LoadClass('DesignGenerator');
            $generator = new DesignGenerator($this, $provider, $custom_provider_name);
            $result = $generator->GenerateCSSStreaming($prompt);
            break;
            
        case 'template':
            $this->LoadClass('TemplateGenerator');
            $generator = new TemplateGenerator($this, $provider, $custom_provider_name);
            $result = $generator->GeneratePageTemplateStreaming($prompt);
            break;
            
        default:
            throw new Exception('Invalid generation type');
    }
    
    // Stream the response
    if ($result['success']) {
        $content = $result['content'];
        $chunk_size = 50; // Characters per chunk
        
        for ($i = 0; $i < strlen($content); $i += $chunk_size) {
            $chunk = substr($content, $i, $chunk_size);
            $progress = min(100, ($i / strlen($content)) * 100);
            
            sendSSE(array(
                'chunk' => $chunk,
                'progress' => round($progress, 2),
                'position' => $i,
                'total' => strlen($content)
            ), 'chunk');
            
            // Small delay to simulate streaming
            usleep(50000); // 50ms
        }
        
        // Send completion
        sendSSE(array(
            'status' => 'complete',
            'content' => $content,
            'word_count' => str_word_count(strip_tags($content)),
            'timestamp' => time()
        ), 'complete');
        
    } else {
        sendSSE(array(
            'status' => 'error',
            'error' => $result['error'],
            'timestamp' => time()
        ), 'error');
    }
    
} catch (Exception $e) {
    sendSSE(array(
        'status' => 'error',
        'error' => $e->getMessage(),
        'timestamp' => time()
    ), 'error');
}

// Log the request
$security->LogRequest(null, $provider, 'streaming_' . $type);

// End the stream
sendSSE(array('status' => 'end'), 'end');
?>
