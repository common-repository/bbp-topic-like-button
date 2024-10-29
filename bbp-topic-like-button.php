<?php
/*
Plugin Name: Topic Like Button for bbPress
Description: Creates a function to add a like button on the topic page.
Version: 1.0
Author: Felix Eve
License: GPLv2 or later
Text Domain: bbp-topic-like-button
*/

if (!defined('BBP_TLB_FILE')) {
    define('BBP_TLB_FILE', __FILE__);
}

/**
 * Require version and dependencies check class.
 *
 * Make sure client has at least PHP 5.6 and parent plugin bbPress is activated.
 */
require_once('inc/bbPtlbCheckReady.php');
$bbPtlbCheckReady = new bbPtlbCheckReady('5.6', ['bbPress (parent plugin)' => 'bbpress/bbpress.php'], 'bbPress Topic Like Button');
register_activation_hook(BBP_TLB_FILE, [$bbPtlbCheckReady, 'check']);
$bbPtlbCheckReady->check();

/**
 * Required the main class to build the like button.
 */
require_once('inc/bbPtlbLikeButton.php');
$bbPtlbLikeButton = new bbPtlbLikeButton();

/**
 * Echo the html for the like button.
 *
 * @param array $args
 */
function bbp_topic_like_button($args = []) {
    echo get_bbp_topic_like_button($args);
}

/**
 * Build the html for the like button.
 * Actually builds a placeholder that is later replaced with the actual like button via an ajax request.
 *
 * @param array $args
 * @return string
 */
function get_bbp_topic_like_button($args = []) {
    global $bbPtlbLikeButton;
    $bbPtlbLikeButton->set_args($args);
    return $bbPtlbLikeButton->placeholder();
}
