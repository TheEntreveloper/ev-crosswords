<?php
/**
 * Functions to register client-side assets (scripts and stylesheets) for the
 * Gutenberg block.
 *
 * @package ev-crosswords
 */

/**
 * Registers all block assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 *
 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/tutorials/block-tutorial/applying-styles-with-stylesheets/
 */
function ev_crossword_block_init() {
	// Skip block registration if Gutenberg is not enabled/merged.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}
	$dir = dirname( __FILE__ );

	$index_js = 'ev-crossword/index.js';
	wp_register_script(
		'ev-crossword-block-editor',
		plugins_url( $index_js, __FILE__ ),
		[
			'wp-blocks',
			'wp-i18n',
			'wp-element',
		],
		filemtime( "{$dir}/{$index_js}" )
	);

	$editor_css = 'ev-crossword/editor.css';
	wp_register_style(
		'ev-crossword-block-editor',
		plugins_url( $editor_css, __FILE__ ),
		[],
		filemtime( "{$dir}/{$editor_css}" )
	);

	$style_css = 'ev-crossword/style.css';
	wp_register_style(
		'ev-crossword-block',
		plugins_url( $style_css, __FILE__ ),
		[],
		filemtime( "{$dir}/{$style_css}" )
	);

	register_block_type( 'ev-crosswords/ev-crossword', [
		'editor_script' => 'ev-crossword-block-editor',
		'editor_style'  => 'ev-crossword-block-editor',
		'style'         => 'ev-crossword-block',
	] );
}

add_action( 'init', 'ev_crossword_block_init' );
