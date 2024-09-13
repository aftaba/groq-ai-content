<?php

use RankMath\User;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define("GROQ_AI_ROUTE_NAMESPACE", "groq-ai-content/v1" );
define("GROQ_AI_ROUTE", "generate" );

add_action( 'rest_api_init', function () {
	register_rest_route( GROQ_AI_ROUTE_NAMESPACE, GROQ_AI_ROUTE, array(
	  	'methods' => 'POST',
	  	'callback' => 'groq_ai_content_block',
	  	'permission_callback' => function() {
      		return current_user_can('edit_others_posts');
		}	
	) );
} );

function groq_ai_content_block(){

	$jsonData = file_get_contents('php://input');
	$data = json_decode($jsonData, true);
	
	if( !(isset( $data["_nonce"] ) && wp_verify_nonce( sanitize_key($data['_nonce']), 'groq-ai-rest-api' ) ) ) {
		die(-1);
	}

	$ai_message = $data["ai_message"];
	$post_content = $data["post_content"];
	$use_post_content = $data["use_post_content"];
	
	$api_key = get_option("groq_api_key"); 

	if( $api_key == "" ) {
		wp_send_json_error("Enter Groq API Key in Settings", 422 );
	}

	if(trim($ai_message) == "" ) {
		wp_send_json_error( 'Enter AI Command', 400 );
	}

	if( $use_post_content && trim($post_content) != "" ) {
		$ai_message = "$ai_message for below content $post_content";
	}
	
	// API endpoint
	$url = 'https://api.groq.com/openai/v1/chat/completions';

	$postData = wp_json_encode([
		"model" => "llama3-8b-8192", // or the specific model available via Groq API
		"messages" => [
			["role" => "user", "content" => $ai_message]
		],
		"temperature" => (float)get_option("groq_temperature", 0.7), // Adjust as needed
		"max_tokens" => (int)get_option("groq_max_tokens", 2048)   // Limit response tokens
	]);


	$response = wp_remote_post($url,array(
		'body'    => $postData,
    	'headers' => array(
        	'Authorization' => 'Bearer ' . $api_key,
			'Content-Type' =>  'application/json',
    	),
		'timeout' => 30,
	));
	
	if( is_wp_error($response)) {
		wp_send_json_error($response['error'], 500 );
	} else {
		$data = json_decode($response["body"], true);

		if( array_key_exists("error", $data)) {
			wp_send_json_error($data["error"]["message"], $data["response"]["code"] );
		} else {
			wp_send_json_success( $data['choices'][0]['message']['content'] );
		}
	}

}


function groq_admin_script() {

	// localize the REST API URL and NONCE
	$rest_url = get_rest_url("",GROQ_AI_ROUTE_NAMESPACE."/".GROQ_AI_ROUTE);	
	$nonce = wp_create_nonce("groq-ai-rest-api");
	
	?>
		<script>
				var groq_rest_data = <?php echo wp_json_encode([
					'rest_api_url' => $rest_url,
					'_groq_nonce' => $nonce
				]); ?>;
		</script>
	<?php 
}
add_action("admin_head", "groq_admin_script");