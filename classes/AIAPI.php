<?php

/**
 * Use AI API (connecting using your API Key) to generate a list of words and hints for your Crossword
 */
class AIAPI
{
    const AIAPIKEY = 'AIAPIKEY';
    const AIAPIKEYPROVIDER = 'AIAPIKEYPROVIDER';

    // admin_action_
    public static function completion($prompt)
    {
        if (!is_admin()) {
            return;
        }

        if(empty($prompt)){
            return new WP_Error('invalid_prompt', 'Prompt cannot be empty');
        }
        
        // Read options
        $evcw_options = get_option('evcw_config_options');


        // Prepare Prompt
        $prompt = 'You are a languange assistance who will help us create a two column list with a single word on the first column separated by ;; from a hint for the word on the second column. The list will about the following: ' . $prompt;

        $response = array();
        $apiKeyProvider = $evcw_options['evcw_ai_provider'];
        $apiKey = $evcw_options['evcw_ai_provider_api_key'];
       
        if ($apiKeyProvider === null || $apiKey === null || $apiKey === false) {
            return new WP_Error('wrong_settings', 'AI API Key undefined. Please enter a valid value in the EV-Crosswords Settings');
        }

        // Local Provider URL
        // $localAIProviderURL = $evcw_options['evcw_ai_local_provider_url'];

        // test code:
        $apiProviderUrl = 'https://api.openai.com/v1/chat/completions';
        if ($apiProviderUrl === null) {
            echo (wp_kses_data('Could not find a connection to an AI API Provider. Please enter a valid value in the EV-Crosswords Settings'));
            do_action('EvCwAIAPIError', $response);
            return;
        }

        $model = get_option('ai_model', null);

        $args = AIAPI::prepareArgs($apiKeyProvider, $apiProviderUrl, $apiKey, $model, $prompt);
        //$apiResponse = wp_remote_post( $apiProviderUrl, $args);
        $apiResponse = '["r":"r2]';
        return AIAPI::getAIAPIResponse($apiResponse);
    }

    private static function getAIAPIResponse($apiResponse)
    {
        return "word;;hint\nword;;hint\nword;;hint\nword;;hint\nword;;hint\nword;;hint\nword;;hint\nword;;hint\nword;;hint\nword;;hint\nword;;hint";
        return array();
    }

    private static function prepareArgs($apiKeyProvider, $apiProviderUrl, $apiKey, $model, $prompt)
    {
        switch ($apiKeyProvider) {
            case 'OpenAI':
                return AIAPI::getOpenAIArgs($apiKey, $model, $prompt);
                break;
            case 'Anthropic':
                return AIAPI::getAnthropicArgs($apiKey, $model, $prompt);
                break;
            case 'Google Gemini':
                break;
        }
        if ($apiProviderUrl === 'OpenAI') {
        }
        return array('headers' => array());
    }

    private static function getOpenAIArgs($apiKey, $model, $prompt)
    {
        $headers = array(
            'content-type' => 'application/json',
            'Authorization' => 'Bearer ' . $apiKey,
        );
        $body = json_encode(
            array(
                'model' => $model ?? 'gpt-4o-mini', //'gpt-3.5-turbo',
                'messages' => array(
                    array(
                        'role' => 'user',
                        'content' => $prompt
                    )
                )
            )
        );
        return array(
            'body'        => $body,
            'headers'     => $headers,
        );
    }

    // https://api.anthropic.com/v1/messages
    private static function getAnthropicArgs($apiKey, $model, $prompt)
    {
        $headers = array(
            'content-type' => 'application/json',
            'Authorization' => 'x-api-key ' . $apiKey,
            'anthropic-version' => '2023-06-01'
        );
        $body = array(
            'data' => array(
                'model' => $model ?? 'claude-3-5-sonnet-20241022',
                'messages' => array(
                    'role' => 'user',
                    'content' => $prompt,
                )
            ),
            'uri' => 'chat/completions'
        );
        return array(
            'body'        => $body,
            'headers'     => $headers,
        );
    }

    private static function getLocalArgs($apiKey, $model, $prompt)
    {
        $headers = array();
        $body = array();
        return array(
            'body' => $body,
            'headers' => $headers,
        );
    }
}
