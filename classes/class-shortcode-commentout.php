<?php
/**
 * Class Name:        ShortCode - Comment Out
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
 * ShortCode Commentout Class
 * 
 * @inheritDoc
 */
class ShortCode_CommentOut extends Base {

	/**
	 * ShortCode Setting
	 *
	 * @var array
	 */
	private $shortcode = [
		'name'				=> 'commentout',	// ShortCode Name
		'callback'		=> 'sc_commnetout',	// callback method
		'attribures'	=> [
			'trim_br'			 => 'yes',	// Remove < br > before and after content.
			'do_trim'			 => 'no',		// Remove whitespace before and after content.
			'do_shortcode' => 'yes', 	// Run a content shortcode.
			'esc_html'		 => 'yes', 	// Escape HTML tags in content.
		],
		'no2false'		=> [	// boolean attributes
				'do_shortcode',
				'do_trim', 
				'trim_br', 
				'esc_html'
		],
	];

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct(){
		// Construct Base Class
		parent::__construct();
		// set callback
		$this->shortcode['callback'] = 
			[ &$this, $this->shortcode['callback'] ];
		// Init Hook - This init
		add_action( 'init', array( &$this, 'init') );
	}

	/**
	 * Action Hook. init
	 *
	 * @return void
	 */
	public function init(){
		// add shortcode
		add_shortcode( 
			$this->shortcode['name'], 
			$this->shortcode['callback'] 
		); 
	}

	/**
	 * shortcode: HTML Comment out
	 *
	 * @param array $atts Attributes
	 * @param string $content Content
	 * @param integer $tags	shortcode tag
	 * @return string commented out HTML
	 */
	public function sc_commnetout( 
			$atts, $content = '', $tags = 0 ){
		// Initialize Attribures
		$atts = wp_parse_args( $atts, 
			(array) $this->shortcode['attribures'] );
		$atts = $this->chageNo2False($atts);
		// content keep & hooking
		$content_org = $content;
		$content = (string) apply_filters(
			$this->getHookName( 'contentOrginal' ),
			(string) $content_org
		);
		// Conversion
		$content = $this->trimBr( $content, $atts['trim_br'] );
		$content = $this->doTrim( $content, $atts['do_trim'] );
		$content = $this->doShortcode( $content, $atts['do_shortcode'] );
		$content = $this->escHtml( $content, $atts['esc_html'] );
		$content = (string) apply_filters(
			$this->getHookName( 'content' ),
			(string) $content
		);
		// Comment Out
		$res = "<!-- {$content} -->";
		$res = (string) apply_filters(
			$this->getHookName( 'content' ),
			(string) $res
		);
		return $res;
	}
}

// call this class
new ShortCode_CommentOut();