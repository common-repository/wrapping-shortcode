<?php
/**
 * Plugin Name:       Wrapping ShortCode
 * Plugin URI:        https://asumaru.com/plugins/asm-wrapping-shortcode-block/
 * Description:       This plugin provides a shortcode block. It is not just a shortcode, it is a shortcode block that wraps other blocks.
 * Requires at least: 5.9
 * Requires PHP:      7.4
 * Version:           0.1.0
 * Author:            Asumaru
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       asm-wrapping-shortcode-block
 * Domain Path:       (default) // Changed from 'languages'
 *
 * @package           asm-wrapping-shortcode
 */

 // CHECK!
defined( 'ABSPATH' ) || die( '' );

/**
 * Constant
 */
define( 'ASMWSC_BLOCK_CLASS', 
	'asm-wrapping-shortcode-block' 
);
define( 'ASMWSC_NAMESPACE', 
	'asm-wrapping-shortcode-block' 
);
define( 'ASMWSC_PATH', 
	untrailingslashit( plugin_dir_path( __FILE__ ) ) 
);
define( 'ASMWSC_URL', 
	untrailingslashit( plugin_dir_url( __FILE__ ) ) 
);

require_once __DIR__ . '/classes/class-init.php';

new Asm_Wrapping_ShortCode\Init();
