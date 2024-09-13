<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function groq_ai_menu_page() {
    add_submenu_page('options-general.php', 'Groq AI Settings', 'Groq AI Settings', 'manage_options', 'groq-ai-settings', 'groq_ai_menu_page_content', null, 70); 
}
add_action('admin_menu', 'groq_ai_menu_page');

function groq_ai_menu_page_content(){

    if( isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST" ) {
        if ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_key($_REQUEST['_wpnonce']), 'groq-settings' ) ) {

            $groq_api_key = isset($_POST["groq_api_key"]) ? sanitize_text_field( wp_unslash($_POST["groq_api_key"]) ) : "";
            $groq_max_tokens = isset($_POST["groq_max_tokens"]) ? sanitize_text_field( wp_unslash($_POST["groq_max_tokens"])) : "2048";
            $groq_temperature = isset($_POST["groq_temperature"]) ? sanitize_text_field( wp_unslash($_POST["groq_temperature"])) : "0.7";
            
            if( $groq_api_key ) {
                update_option("groq_api_key", $groq_api_key, false );
            }

            if( $groq_max_tokens && ($groq_max_tokens >= 0 && $groq_max_tokens <= 8192)  ) {
                update_option("groq_max_tokens", $groq_max_tokens, false );
            }
            
            if( $groq_temperature && ( $groq_temperature >= 0 && $groq_temperature <= 2) ) {
                update_option("groq_temperature", $groq_temperature, false );
            }
        } else {
            wp_die(-1);
        }
    }
   
    ob_start();

    $groq_api_key = get_option("groq_api_key");
    $groq_max_tokens = get_option("groq_max_tokens","2048");
    $groq_temperature = get_option("groq_temperature","0.7");
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Groq AI Settings</h1>
    </div>
    <div class="wrap">
        <form method="POST">
            <?php wp_nonce_field('groq-settings'); ?>
            <table class="form-table">
                <tr class="form-field">
                    <td><strong>API Endpoint</strong></td>
                    <td><input type="text" name="groq_api_endpoint" value="https://api.groq.com/openai/v1/chat/completions" readonly></td>
                </tr>
                <tr class="form-field">
                    <td><strong>Model</strong></td>
                    <td><input type="text" name="groq_model" value="llama3-8b-8192" readonly></td>
                </tr>
                <tr class="form-field">
                    <td><strong>API Key</strong></td>
                    <td>
                        <input type="text" name="groq_api_key" value="<?php echo esc_attr($groq_api_key); ?>">
                        <br/><span>Generate the API key from <a target="_blank" href="https://console.groq.com/keys">here</a></span>
                    </td>

                </tr>
                <tr class="form-field">
                    <td><strong>Max Token</strong></td>
                    <td>
                        <input type="number" name="groq_max_tokens" value="<?php echo esc_attr($groq_max_tokens); ?>">
                        <br/><span> Use value between 0 to 8192 (Default : 2048) </span>
                    </td>
                </tr>
                <tr class="form-field">
                    <td><strong>Temperature</strong></td>
                    <td>
                        <input type="text" name="groq_temperature" value="<?php echo esc_attr($groq_temperature); ?>">
                        <br/><span> Use value between 0 to 2 (Default : 0.7 )</span>
                    </td>
                </tr>
                <tr class="form-field">
                    <td></td>
                    <td><input class="button button-primary button-large" type="submit" value="Save"></td>
                </tr>
            </table>
        </form>

    <?php 

    echo wp_kses( ob_get_clean(), groq_form_allowed_html());
}

function groq_form_allowed_html() {
	return array(
		'h1' => array(),
        'form' => array(
            'action' => [],
            'method' => [],
            'class' => [],
        ),
        'input' => array(
            'value' => [],
            'name' => [],
            'type' => [],
            'class' => [],
            'readonly' => []
        ),
        'table' => array(
            'class' => []
        ),
        'tr' => array(
            'class' => [],
        ),
        'td' => array(),
        'br' => array(),
        'strong' => array(),
        'a' => array(
            'href' => [],
            'target' => []
        ),
        'span' => array(),
	);
}