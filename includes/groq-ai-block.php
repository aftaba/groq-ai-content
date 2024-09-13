<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function groq_ai_content_block_init() {
	register_block_type( GROQ_DIR . '/build' );
}
add_action( 'init', 'groq_ai_content_block_init' );
