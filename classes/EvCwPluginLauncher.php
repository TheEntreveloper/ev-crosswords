<?php
defined( 'ABSPATH' ) || exit();

/**
 * The Heart of the Plugin
 */
class EvCwPluginLauncher {
    protected static $instance = null;
    const REQ_FN_MAP = array('viewall' => 'viewCwList', 'viewraw' => '', 'viewcw' => '', 'delete' => '');
    const TITLE = 'EV Crosswords Plugin Admin';

    private function __construct() {}

    public static function instantiatePlugin() {
        if (EvCwPluginLauncher::$instance == null) {
            EvCwPluginLauncher::$instance = new self();
            register_activation_hook(EVCWV_PLUGIN, array(self::$instance, 'onActivate'));
            register_deactivation_hook(EVCWV_PLUGIN, array(self::$instance, 'onDeactivate'));
            add_action( 'init', array(self::$instance, 'onInit'));
        }
        return EvCwPluginLauncher::$instance;
    }

    public function onActivate()
    {
        // no_op
    }

    public function registerPostTypes() {
        register_post_type('ev_crossword', array(
            'labels' => array('name' => __('crosswords', 'ev-crosswords'), 'singular_name' => __('crossword', 'ev-crosswords'),
                'add_new_item' => __('Add New Crossword', 'ev-crosswords'), 'edit_item' => __('Edit Crossword', 'ev-crosswords'),
                'new_item' => __('New Crossword', 'ev-crosswords'), 'view_item' => __('View Crossword', 'ev-crosswords'),
                'view_items' => __('View Crosswords', 'ev-crosswords'), 'search_items' => __('Search Crossword', 'ev-crosswords'),
                'not_found' => __('No Crosswords found', 'ev-crosswords'), 'not_found_in_trash' => __('No Crosswords found in Trash', 'ev-crosswords'),
                'attributes' => __('Crossword Attributes', 'ev-crosswords'), 'item_published' => __('Crossword published', 'ev-crosswords')),
            'public' => true,
            //'show_in_rest' => true,  <-- not in this version
            'taxonomies' => array('category'),
            'supports' => array(
                'title',
                'editor',
                'custom-fields',
                'thumbnail',
                'page-attributes'
            ),
            'has_archive' => true,
            'rewrite' => array( 'slug' => 'crossword' ),
        ));

        flush_rewrite_rules();
    }

    public function onDeactivate() {
        // no_op
    }

    public function onInit() {
        global $pagenow;
        $this->registerPostTypes();
        add_action( 'admin_menu', array(self::$instance, 'adminMenu'));
        if ($pagenow == 'post-new.php' && isset($_GET['post_type']) && sanitize_text_field($_GET['post_type']) === 'ev_crossword') {
            wp_safe_redirect( admin_url( 'admin.php?page=evcwv-plugin-settings' ) );
            exit;
        }
        add_action('wp_enqueue_scripts', array( $this, 'cwScripts'));
        add_action('wp_head', array( $this, 'cwHead'));
        add_action('pre_get_posts', array( $this, 'siteWideCwViews'));
        add_action('before_delete_post', array( $this, 'delCw'));
        add_action('wp_trash_post', array( $this, 'trashCw'));
        add_filter( 'template_include', array( $this, 'loadTemplates' ) );
        add_filter('the_posts', [$this, 'cwCnt']);
        load_plugin_textdomain('ev-crossword', false, plugin_basename( dirname(EVCWV_PLUGIN)).'/languages');
    }

    public function adminMenu() {
        add_menu_page(__('EvCwv Settings', 'ev-crosswords'), __('Ev Crosswords Settings', 'ev-crosswords'), 'administrator',
            'evcwv-plugin-settings', array(self::$instance, 'controller'), 'dashicons-admin-generic');
    }

    public function controller() {
        if (isset($_GET['evaction'])) {
            if (isset(EvCwPluginLauncher::REQ_FN_MAP[sanitize_text_field($_GET['evaction'])])) {
                // not in use in this version
                //[$this, EvCwPluginLauncher::REQ_FN_MAP[$_GET['evaction']]]();
            }
        } else {
            $this->addCw();
        }
    }

    public function siteWideCwViews($query) {
        if ( ($query->is_home() && $query->is_main_query()) || $query->is_category() ) {
            $query->set('post_type', array('post', 'ev_crossword'));
        }
    }

    public function cwScripts() {
        if (is_admin()) return;
        wp_enqueue_script('cwviewer', plugins_url('../views/crossword/js/cwviewer.js', __FILE__), array( 'jquery' ), null, true);
        wp_enqueue_style('twboostrap',plugins_url('../views/crossword/css/bootstrap.min.css', __FILE__));
    }

    /**
     * If the keyboard doesn't show on mobile, check if the page contains the <meta entry below.
     * If it doesn't, uncomment the line below and try again, as your current theme might be missing it.
     */
    public function cwHead() {
        ?>
<!--        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">-->
    <?php
    }

    function loadTemplates($template) {
        global $wp_query, $_wp_current_template_content;
        $post = $wp_query->get_queried_object();
        if (isset($post) && $post->post_type === 'ev_crossword') {
            $_POST['cwtitle'] = $post->post_title;

            if (!file_exists(TEMPLATEPATH.'/theme.json')) {
                $template = EVCWV_PLUGIN_DIR . 'views/crossword/single-crossword.php';
            } else {
                $_wp_current_template_content = file_get_contents(EVCWV_PLUGIN_DIR . 'views/crossword/blockdata');
                $_wp_current_template_content = _inject_theme_attribute_in_block_template_content($_wp_current_template_content);
                $template = EVCWV_PLUGIN_DIR . 'views/crossword/single-block-crossword.php';
            }
        }
        return $template;
    }

    private function addCw() {
        if ( !current_user_can( 'upload_files' ) ) {
            wp_die("Unable to continue: access denied");
        }
        $post = $_POST;
        if (isset($post) && isset($post['cwname'])) {
            $cwdata = $_FILES['cwdata'];
            $file = wp_handle_upload($cwdata, array('test_form' => false, 'test_type' => false));
            if (isset($file['error'])) {
                // handle error
                esc_html_e('Unable to save your crossword', 'ev-crosswords');
                return;
            }
            $url = $file['url'];
            $type = $file['type'];
            $file = urldecode(str_replace( array( '%2F', '%5C' ), '/', urlencode( $file['file'] ) ));
            $filename = wp_basename($file);
            // encode selected characters, like &
            $cnt = file_get_contents($file);
            $cnt = str_replace('&', '&amp;', $cnt);
            file_put_contents($file, $cnt);
            // Prepare the ev_crossword post.
            $data = array(
                'post_title' => $post['cwname'],
                'post_content' => $post['descr'],
                'post_excerpt' => $file,
                'post_mime_type' => $type,
                'guid' => $url,
                'context' => 'EV Crosswords',
                'post_status' => 'publish',
                'file' => $file,
                'post_parent' => 0,
            );

            $data['post_type'] = 'ev_crossword';

            $id = wp_insert_post($data, false, true);
            echo(wp_kses_post('<b>').__('The crossword has been saved. You could now upload another Crossword', 'ev-crosswords').wp_kses_post('</b><br>'));
            echo(wp_kses_post("<h3>").__(EvCwPluginLauncher::TITLE, 'ev-crosswords').wp_kses_post("</h3>"));
            include_once EVCWV_PLUGIN_DIR . 'views/newcw.php';
        } else {
            echo(wp_kses_post("<h3>").__(EvCwPluginLauncher::TITLE, 'ev-crosswords').wp_kses_post("</h3>"));
            include_once EVCWV_PLUGIN_DIR . 'views/newcw.php';
        }
    }

    public function cwCnt($qposts) {
        if($qposts == null || count($qposts) == 0) return $qposts;
        foreach ($qposts as $post) {
            if ($post->post_type === 'ev_crossword') {
                // just for visualization, to avoid having to use a metafield instead
                $post->post_excerpt = $post->post_content;
            }
        }
        return $qposts;
    }

    public function delCw($postid, $post = null) {
        if (isset($post) && $post instanceof WP_Post && $post->post_type === 'ev_crossword' && isset($post->post_excerpt)) {
            if ( !current_user_can( 'delete_post', $postid ) ) {
                wp_die("Unable to continue: access denied");
            }
            if (!@unlink($post->post_excerpt)) {
                // for now only logging the failure
                error_log('Failed to delete crossword file: '.$post->post_excerpt);
            }
        }
    }

    public function trashCw($post_id) {
        $post = get_post($post_id);
        $this->delCw($post_id, $post);
    }
}
