<?php

defined('ABSPATH') || exit;
/**
 * Register a meta box using a class.
 */
class EvCwPluginMetaBox
{

    /**
     * Constructor.
     */
    public function __construct()
    {
        if (is_admin()) {
            $this->init_metabox();
            add_action('admin_enqueue_scripts', array($this, 'enqueue_custom_admin_style'));
            add_action('wp_ajax_evcw_ai_generate_ajax_controller', array($this, 'evcw_ai_generate_ajax_controller'));
            add_action('wp_ajax_evcw_api_generate_ajax_controller', array($this, 'evcw_api_generate_ajax_controller'));
        }
    }

    /**
     * EV API Generate Ajax Callback
     */
    public function evcw_api_generate_ajax_controller()
    {
        // Call Ajax Service

        // Add nonce for security and authentication.
        $nonce_name   = isset($_POST['evcw_editor_nonce']) ? $_POST['evcw_editor_nonce'] : '';
        $nonce_action = 'evcw_editor_nonce_action';

        // Check if nonce is valid.
        if (! wp_verify_nonce($nonce_name, $nonce_action)) {
            wp_send_json_error(new WP_Error('forbidden', 'You are not allowed to perform this action'));
        }

        if (empty($_POST['wordlist'])) {
            wp_send_json_error(new WP_Error('wrong_data', 'The request is invalid'));
        }

        // error_log(print_r($_POST, true));

        $wordlist = sanitize_textarea_field($_POST['wordlist']);

        error_log($wordlist);

        // $api_res = $this->sample_api_service($prompt);

        wp_send_json_success('OK');
    }

    /**
     * AI Generate Ajax Callback
     */
    public function evcw_ai_generate_ajax_controller()
    {
        // Call Ajax Service

        // Add nonce for security and authentication.
        $nonce_name   = isset($_POST['evcw_editor_nonce']) ? $_POST['evcw_editor_nonce'] : '';
        $nonce_action = 'evcw_editor_nonce_action';

        // Check if nonce is valid.
        if (! wp_verify_nonce($nonce_name, $nonce_action)) {
            wp_send_json_error(new WP_Error('forbidden', 'You are not allowed to perform this action'));
        }

        if (empty($_POST['prompt'])) {
            wp_send_json_error(new WP_Error('wrong_data', 'The request is invalid'));
        }

        $prompt = sanitize_text_field($_POST['prompt']);

        $api_res = AIAPI::completion($prompt);

        wp_send_json_success($api_res);
    }

    private function sample_api_service($prompt)
    {

        return
            "word;;hint\nword;;hint\nword;;hint\nword;;hint\nword;;hint\nword;;hint\nword;;hint\nword;;hint\nword;;hint\nword;;hint\nword;;hint";
    }


    /**
     * Meta box initialization.
     */
    public function init_metabox()
    {
        add_action('add_meta_boxes', array($this, 'add_metabox'));
        add_action('save_post',      array($this, 'save_metabox'), 10, 2);
    }

    /**
     * Adds the meta box.
     */
    public function add_metabox()
    {
        add_meta_box(
            'crossword-config',
            __('Crossword Details', 'ev-crosswords'),
            array($this, 'render_metabox'),
            'ev_crossword',
            'advanced',
            'default'
        );
    }

    /**
     * Renders the meta box.
     */
    public function render_metabox($post)
    {

?>
        <div class="evcw-post-editor-config" id="evcwPostEditorConfig">
            <?php
            // Add nonce for security and authentication.
            wp_nonce_field('evcw_editor_nonce_action', 'evcw_editor_nonce');

            ?>
            <div class="tab">
                <button class="tablinks" id="defaultOpen" onclick="openCrosswordTab(event, 'Wordlist')">Wordlist</button>
                <button class="tablinks" onclick="openCrosswordTab(event, 'Appearance')">Appearance</button>
                <button class="tablinks" onclick="openCrosswordTab(event, 'Preview')">Preview</button>
            </div>

            <!-- Tab content -->
            <div id="Wordlist" class="tabcontent">
                <h3>Word list</h3>
                <p>Enter the word list one per line, each line will start with the word and the hint divided by a double semicolon (<mark>;;</mark>). Ex: London;;Capital of England.</p>

                <p><b>Optionally you can configure and use AI to generate the word list for you by entering a prompt below:</b></p>

                <div class="evcw-field-group">
                    <div class="flex" id="evcwAiGenerateWidget">
                        <input id="" type="text" placeholder="Create a crossword lists of super cars brands">
                        <a class="button button-danger" href="">AI Generate</a>
                    </div>
                </div>

                <div class="evcw-field-group">
                    <textarea name="ev_wordlist" id=""></textarea>
                </div>

                <div class="evcw-field-group">
                    <div class="flex">
                        <div>
                            <label for="">Rows</label>
                            <input id="cwRows" type="number">
                        </div>
                        <div>
                            <label for="">Columns</label>
                            <input id="cwCols" type="number">
                        </div>
                    </div>
                </div>
                <div>
                    <a id="generateCrossword" class="button button-primary">Generate Crossword</a>
                </div>
            </div>
            <div id="Appearance" class="tabcontent">
                <h3>Appearance</h3>
                <p>Some description</p>
            </div>
            <div id="Preview" class="tabcontent">
                <h3>Preview</h3>
                <div class="row">
                    <div class="col-12 col-lg-8">
                        <div id="myParent">
                            <canvas id="cwCanvas" data-cw="<?php echo (esc_html($post->guid)); ?>" style="border:1px solid #0b0b0b"></canvas>
                        </div>
                        <input id="kb" type="text" autocomplete="off" style="position:fixed;left:-1700px;top:0px" ;>
                    </div>
                    <div class="col-6 col-lg-4">
                        <div class="row">
                            <div class="col-12 text-left" id="messages"><?php esc_html_e('Press on a cell to view hints here. Use Shift-click to change writing direction', 'ev-crosswords'); ?></div>
                            <div class="col-12">
                                <button type="button" class="button button-info" value="solve" onclick="cw.solve();"><?php esc_html_e('View solution', 'ev-crosswords'); ?></button>
                                <button type="button" class="button button-info" value="evaluate" onclick="cw.evaluate();"><?php esc_html_e('Check for mistakes', 'ev-crosswords'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>

        </script>
<?php
    }

    /**
     * Handles saving the meta box.
     *
     * @param int     $post_id Post ID.
     * @param WP_Post $post    Post object.
     * @return null
     */
    public function save_metabox($post_id, $post)
    {
        // Add nonce for security and authentication.
        $nonce_name   = isset($_POST['evcw_editor_nonce']) ? $_POST['evcw_editor_nonce'] : '';
        $nonce_action = 'evcw_editor_nonce_action';

        // Check if nonce is valid.
        if (! wp_verify_nonce($nonce_name, $nonce_action)) {
            return;
        }

        // Check if user has permissions to save data.
        if (! current_user_can('edit_post', $post_id)) {
            return;
        }

        // Check if not an autosave.
        if (wp_is_post_autosave($post_id)) {
            return;
        }

        // Check if not a revision.
        if (wp_is_post_revision($post_id)) {
            return;
        }

        // Implement saving
    }

    function enqueue_custom_admin_style($hook)
    {

        $screen = get_current_screen();

        if ($screen->post_type !== 'ev_crossword') return;

        wp_enqueue_script('cwviewer', EVCWV_PLUGIN_URL . 'views/crossword/js/cwviewer.js', array('jquery'), null, true);

        // Load Bootstrap grid only
        wp_enqueue_style('twboostrap', EVCWV_PLUGIN_URL . '/views/crossword/css/bootstrap-grid.min.css');


        // loading css
        wp_register_style('evcw-admin-css', EVCWV_PLUGIN_URL . 'admin/assets/admin.css', false, '1.0.0');
        wp_enqueue_style('evcw-admin-css');

        // loading js
        wp_register_script('evcw-admin-js', EVCWV_PLUGIN_URL . 'admin/assets/admin.js', array('jquery-core'), false, true);
        wp_enqueue_script('evcw-admin-js');



        wp_localize_script('evcw-admin-js', 'evcw_obj', array('ajax_url' => admin_url('admin-ajax.php')));
    }
}

new EvCwPluginMetaBox();
