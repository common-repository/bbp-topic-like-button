=== Topic Like Button for bbPress ===
Contributors: dahousecatz
Tags: bbPress, topic, like button, like
Requires at least: 4.7
Tested up to: 4.9.8
Requires PHP: 5.6
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Defines two functions: bbp_topic_like_button() and get_bbp_topic_like_button(). Use these in your bbPress topic template
file to add an ajax powered like button.

== Description ==

Once the function bbp_topic_like_button() is placed in your bbPress topic template file it creates a div placeholder.
This is the same for every user so the initial page build can be globally cached.
Once the document is ready an ajax request checks if the current user has liked the current topic and then
displays a like or unlike button.
Upon clicking the button a ajax request is sent to save the updated topic likes.
Topic likes are stored as post meta attached to the topic.

== Installation ==

The easiest way to install this plugin is to go to Add New in the Plugins section of your blog admin and search for
"bbPress Topic Like Button." On the far right side of the search results, click "Install."

If the automatic process above fails, follow these simple steps to do a manual install:

1. Extract the contents of the zip file into your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Can I customise the button? =

Yes. The easiest way to do this is to pass in attributes when initially calling bbp_topic_like_button().

You can pass in attributes to set a custom user_id or topic_id, set before or after html, customise the button text
or add custom classes to the button.

Here is an example setting all possible custom options:

    $args = [
        'user_id'  => get_current_user_id(),
        'topic_id' => bbp_get_topic_id(),
        'before'   => '',
        'after'    => '',
        'like'     => __('Like', 'your-domain'),
        'unlike'   => __('Unlike', 'your-domain'),
        'class'    => ['my-custom-class', 'another-custom-class'],
    ];
    bbp_topic_like_button($args);

It is also possible to implement the filter get_bbp_topic_like_button to make any final changes to the rendered html.

= Can I trigger a custom event when the like button is clicked =

Yes, the action bbp_topic_like_button_clicked is fired on every button press.

E.g. implement like this:

    add_action('bbp_topic_like_button_clicked', 'my_plugin_bbp_topic_like_button_clicked', 10, 3);
    function my_plugin_bbp_topic_like_button_clicked($res, $user_id, $topic_id) {
        // Add code here to do something.
    }

== Changelog ==

= 1.0 =
* First version released.

== Upgrade Notice ==

None yet.

== Additional Info ==

The plugin cannot be activated without bbPress first being activated.
If bbPress is deactivated then this plugin will deactivate itself.

== Credits ==

Thanks to elhardoum author of bbp-messages for inspiration for the CheckReady class.
