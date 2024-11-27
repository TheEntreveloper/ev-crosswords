<?php

/**
 * Use AI API (connecting using your API Key) to generate a list of words and hints for your Crossword
 */
class AIAPI {
    const AIAPIKEY = 'AIAPIKEY';
    const AIAPIKEYPROVIDER = 'AIAPIKEYPROVIDER';

    // admin_action_
    public static function completion() {
        if (!is_admin()) { return; }
        //if (empty($_POST['prompt'])) { return; }
        // test code
        $isAdmin = is_admin();
        $prompt = 'please create a 2 columns words list about the soccer premier league, where the first column refers to a soccer star, and the second 
        row provides a one sentence hint about that soccer star. Separate each of the words in a row using ;;';
        $response = array();
        $apiKeyProvider = get_option(AIAPI::AIAPIKEYPROVIDER);
        $apiKey = get_option(AIAPI::AIAPIKEY);
        // test code:
        $apiKeyProvider = 'OpenAI';
        $apiKey = '';
        if ($apiKeyProvider === null || $apiKey === null || $apiKey === false) {
            echo(wp_kses_data('AI API Key undefined. Please enter a valid value in the EV-Crosswords Settings'));
            return;
        }
        $apiProviderUrl = get_option($apiKeyProvider);
        // test code:
        $apiProviderUrl = 'https://api.openai.com/v1/chat/completions';
        if ($apiProviderUrl === null) {
            echo(wp_kses_data('Could not find a connection to an AI API Provider. Please enter a valid value in the EV-Crosswords Settings'));
            do_action('EvCwAIAPIError', $response);
            return;
        }
        $model = get_option('ai_model', null);
        $args = AIAPI::prepareArgs($apiKeyProvider, $apiProviderUrl, $apiKey, $model, $prompt);
        //$apiResponse = wp_remote_post( $apiProviderUrl, $args);
        $apiResponse = '["r":"r2]';
        return AIAPI::getAIAPIResponse($apiResponse);
    }

    private static function getAIAPIResponse($apiResponse) {
        return array();
    }

    private static function prepareArgs($apiKeyProvider, $apiProviderUrl, $apiKey, $model, $prompt) {
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
        return array('headers' => array(

        ));
    }

    private static function getOpenAIArgs($apiKey, $model, $prompt) {
        $headers = array(
            'content-type' => 'application/json',
            'Authorization' => 'Bearer '.$apiKey,
        );
        $body = json_encode(array(
                'model' => $model ?? 'gpt-4o-mini',//'gpt-3.5-turbo',
                'messages' => array(
                    array('role' => 'user',
                    'content' => $prompt)
                )
            )
        );
        return array(
            'body'        => $body,
            'headers'     => $headers,
        );
    }

    // https://api.anthropic.com/v1/messages
    private static function getAnthropicArgs($apiKey, $model, $prompt) {
        $headers = array(
            'content-type' => 'application/json',
            'Authorization' => 'x-api-key '.$apiKey,
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

    private static function getLocalArgs($apiKey, $model, $prompt) {
        $headers = array();
        $body = array();
        return array(
            'body' => $body,
            'headers' => $headers,
        );
    }
}