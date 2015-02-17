<?php
/*
Plugin Name: Preview Jump
Plugin URI: 
Description: 
Version: 0.0.1
Author: Caleb Stauffer
Author URI: http://develop.calebstauffer.com
*/

new css_preview_jump;
class css_preview_jump {

	function __construct() {
		add_filter('the_content',array(__CLASS__,'add_first_diff_marker'));
		add_action('admin_bar_menu',array(__CLASS__,'admin_bar_menu'),99);
	}

	public static function add_first_diff_marker($content) {
		global $post;
		$revisions = wp_get_post_revisions($post->ID);

		foreach ($revisions as $id => $revision)
			if ($revision->post_content !== $post->post_content) {
				$diff = strspn($post->post_content ^ $revision->post_content,"\0");
				define('HAS_DIFF_ANCHOR',true);
				break;
			}
		if (false === ($before = strrpos(substr($post->post_content,0,$diff),'<')))
			$before = $diff;
		return substr($post->post_content,0,$before) .  '<span id="first-diff" class="revision-' . $revision->ID . '"></span>' . substr($post->post_content,$before);
	}

	public static function admin_bar_menu($bar) {
		global $post;
		$bar->add_node(array(
			'id'	=> 'preview-jump',
			'title'	=> 'Jump to Content Change',
			'href'	=> get_permalink($post->ID) . '#first-diff',
		));
	}

}

?>