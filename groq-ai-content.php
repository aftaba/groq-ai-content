<?php
/**
 * Plugin Name:       Groq AI Content
 * Description:       Generate and summarize post content using Guternberg Block.
 * Requires at least: 5.6
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Aftab Alam
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       groq-ai-content
 *
 * @package GroqAIContent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define("GROQ_DIR", __DIR__);

require_once "includes/groq-ai-block.php";
require_once "includes/groq-settings.php";
require_once "includes/groq-rest-endpoint.php";





