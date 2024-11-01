<?php
/**
 * Class Name:        Wrapping ShortCode - Enqueue
 * Author:            Asumaru Corp.
 * License:           GPL-2.0-or-later
 * Text Domain:       asm-wrapping-shortcode-block
 * Domain Path:       languages
 *
 * @author Asumaru corp.
 * @package           asm-wrapping-shortcode
 */

/**
 * namespace
 */
namespace Asm_Wrapping_ShortCode;

/**
 * Enqueue Class
 * 
 * @inheritDoc
 */
class Enqueue extends Base {

	/**
	 * Block paramaters
	 *
	 * @var array
	 */
	private $blocks = [
		'block.js'			=> '/build',			// location of block.js (base)
		'Properties'		=> [],						// registBlock properties (base)
		// 'lang_path'	=> '/languages',	// location of languages (base)
		'lang_path'			=> false,					// location of languages (default) 
		'script'				=> 'script',			// javascript handle (base)
		'dependencies'	=> [							// dependencies (default)
			'wp-block-editor', 
			'wp-blocks', 
			'wp-components', 
			'wp-element', 
			'wp-i18n',
		],
	];
	/**
	 * Translation textdomain
	 *
	 * @var string
	 */
	private $textdomain = ASMWSC_NAMESPACE;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct(){
		// Construct Base Class
		parent::__construct();
		// Paramaters Setting
		$this->blocks['block.js'] = 
			ASMWSC_PATH . $this->blocks['block.js'];
		$this->blocks['lang_path']	= empty($this->blocks['lang_path'])
																? false
																: ASMWSC_PATH . $this->blocks['lang_path'];
		$this->blocks['script'] = 
			ASMWSC_NAMESPACE . '-' . $this->blocks['script'];
		// Loaded Hook - i18n on PHP
		add_action('plugins_loaded', 
			array( &$this, 'translationTextDomain'));
		// Init Hook - Block regist
		add_action( 'init', 
			array( &$this, 'registBlock') );
		// Enqueue scripts Hook: normal
		add_action(
			'wp_enqueue_scripts', 
			array( &$this, 'enqueue_scripts'));
		// Enqueue scripts Hook: admin
		add_action( 
				'admin_enqueue_scripts', 
				array( &$this, 'enqueue_scripts'));
	}

	/**
	 * i18n PHP Translations
	 *
	 * @return void
	 */
	public function translationTextDomain(){
		// set translation paramaters in PHP
		$textdomain = $this->textdomain;
		$locale_name  = get_locale();
		$mofile_name  = $this->blocks['lang_path'];
		if( ! empty($mofile_name) ){
			$mofile_name .= "/{$textdomain}-{$locale_name}.mo";
			$mofile_name = realpath( $mofile_name );
		}
		// load textdomain
		load_textdomain($textdomain, $mofile_name);
		load_plugin_textdomain($textdomain, $mofile_name);
	}

	/**
	 * Registers the block using the metadata loaded from 	the `block.json` file.
	 * Behind the scenes, it registers also all assets so 	they can be enqueued
	 * through the block editor in the corresponding 	context.
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_block_type/
	 */
	public function registBlock(){
		// function check
		if( ! function_exists('register_block_type') )
			return false;
		// Arguments Set
		$js = realpath( $this->blocks['block.js'] );
		$properties = (array) $this->blocks['Properties'];
		$properties['render_callback'] = 
			array( &$this, 'renderCallback');
		// ragist
		register_block_type(
			$js,
			$properties
		);
	}

	/**
	 * Render callback function.
	 *
	 * @param array    $attributes The block attributes.
	 * @param string   $content    The block content.
	 * @param WP_Block $block      Block instance.
	 *
	 * @return string The rendered output.
	 */
	public function renderCallback( $attributes, $content, $block ){
		$res = '';
		// template file name
		$template = realpath(
			$this->blocks['block.js'] . '/template.php'
		);
		// show template
		if( class_exists( __NAMESPACE__ . '\Template') ){
			// call Template Class & get shortcode
			$res = (string) new Template($attributes, $content, $block);
		}
		return $res;
	}

	/**
	 * This Plugin Values for Javascript.
	 * i18n JavaScript Translations
	 *
	 * @return void
	 */
	public function enqueue_scripts(){
		// set parameters
		$script = $this->blocks['script'];
		$plugin_path = ASMWSC_PATH;
		$script_src = 
			realpath( $plugin_path . '/build/index.js' ); 
		$asset_path = 
			realpath( $plugin_path . '/build/index.asset.php' );
		$asset_file = (array) include( $asset_path );
		$dependencies = ! empty( $asset_file['dependencies'] )
									? (array) $asset_file['dependencies']
									: (array) $this->blocks['dependencies'];
		// JavaScript file
		wp_enqueue_script(
			$script, 				// handle
			$script_src,		// script file
			$dependencies,	// dependencies
			false,					// version
			false						// in_footer
		);
		// i18n
		$textdomain = $this->textdomain;
		$lang_path  = empty($this->blocks['lang_path'])
								? false
								: realpath( $this->blocks['lang_path'] );
		$wsst = wp_set_script_translations( 
			$script,			// Handle
			$textdomain,	// TextDomain
			$lang_path		// Languages Path
		);
  	// Set values for Javascript.
		$values = [];
		if( is_admin() ){
			// shortcode tagName list.
			$shortcodes = (array) $this->getShortcodes();
			sort($shortcodes, SORT_STRING);
			$values['shortcodes'] = $shortcodes;
		}
		// put script values.
		wp_localize_script(
			$script, 				// Handle
			'asm_WSC_Vals',	// Variable name
			$values					// Variable values
		);
	}
}

// call this class
new Enqueue();