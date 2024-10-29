<?php

class bbPtlbLikeButton
{
    /**
     * @var - Arguments used to build the like button.
     */
    private $args;

    /**
     * bbPressTopicLikeButton constructor.
     */
    function __construct() {

        if (is_admin() && get_site_option('bbptlb_force_deactivate')) {
            delete_site_option('bbptlb_force_deactivate');
            return add_action('admin_init', [$this, 'deactivate']);
        }

        add_action('wp_enqueue_scripts', [$this, 'wp_enqueue_scripts']);
        add_action('wp_ajax_bbpress_topic_like_button_get_button', [$this, 'get_button_ajax_callback']);
        add_action('wp_ajax_bbpress_topic_like_button_click', [$this, 'button_click_ajax_callback']);
    }

    /**
     * Deactivate this plugin.
     */
    public function deactivate() {
        deactivate_plugins(plugin_basename(BBP_TLB_FILE));
    }

    /**
     * Add JS script and set arguments to pass to front end.
     */
    public function wp_enqueue_scripts() {
        wp_enqueue_script('bbpress-topic-like-button', plugin_dir_url(BBP_TLB_FILE) . 'js/bbpress-topic-like-button.js', ['jquery']);
        $data = [
            'ajax_url' => admin_url('admin-ajax.php'),
            'args'     => $this->args,
        ];
        wp_localize_script('bbpress-topic-like-button', 'bbptlp_object', $data);
    }

    /**
     * Ajax callback to build the html for the like button.
     * The button is different depending on if the current user has liked this topic before so it's build in an ajax
     * callback to allow caching of the initial html page.
     */
    public function get_button_ajax_callback() {

        $args = $_POST['args'];

        $user_id = bbp_get_user_id($args['user_id'], false, true);
        $topic_id = bbp_get_topic_id($args['topic_id']);

        // Has the user already liked this topic?
        $existing_like_uids = get_post_meta($topic_id, 'bbpress_topic_like_button_like');

        $button_text = wp_kses_post(in_array($user_id, $existing_like_uids) ? $args['unlike'] : $args['like']);

        foreach($args['class'] as &$class) {
            $class = sanitize_html_class($class);
        }

        $params = [
            wp_kses_post($args['before']),
            strtolower($button_text),
            implode(' ', $args['class']),
            $button_text,
            wp_kses_post($args['after']),
        ];

        $html = vsprintf('%s<a href="#%s" class="%s">%s</a>%s', $params);

        // Let other plugins alter the final rendered html.
        wp_die(apply_filters('get_bbp_topic_like_button', $html, $args));

    }

    /**
     * Callback for after a user has clicked on the like button.
     * Used for liking and unliking topics.
     */
    function button_click_ajax_callback() {
        $args = $_POST['args'];

        $user_id = bbp_get_user_id($args['user_id'], false, true);
        $topic_id = bbp_get_topic_id($args['topic_id']);

        if ($_POST['method'] == 'like') {
            add_post_meta($topic_id, 'bbpress_topic_like_button_like', $user_id);
            $res = 'liked';
        } else {
            delete_post_meta($topic_id, 'bbpress_topic_like_button_like', $user_id);
            $res = 'unliked';
        }

        // Let other plugins  respond to the button being clicked.
        do_action('bbp_topic_like_button_clicked', $res, $user_id, $topic_id);

        wp_die(json_encode(['res' => $res]));
    }

    /**
     * Combine user arguments with defaults.
     *
     * @param $args
     */
    function set_args($args) {
        $this->args = bbp_parse_args($args, [
            'user_id'  => get_current_user_id(),
            'topic_id' => bbp_get_topic_id(),
            'before'   => '',
            'after'    => '',
            'like'     => __('Like', 'bbp-topic-like-button'),
            'unlike'   => __('Unlike', 'bbp-topic-like-button'),
            'class'    => [],
        ], 'get_topic_like_link');
    }

    /**
     * Build the html for the like button placeholder sent with the initial html request. Fully cachable.
     *
     * @return string
     */
    function placeholder() {
        return '<span class="bbpress-topic-like-button"></span>';
    }

}
