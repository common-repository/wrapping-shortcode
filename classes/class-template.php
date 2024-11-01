<?php
/**
 * Class Name:        Wrapping ShortCode - Template
 * 										use this class in 'src/build/template.php'
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

use WP_Block;

/**
 * Template Class
 * 
 * @inheritDoc
 */
 class Template extends Base {

	/**
	 * Block attributes
	 *
	 * @var array
	 */
	private $attributes = []; 
	/**
	 * Block content
	 *
	 * @var string
	 */
	private $content = '';
	/**
	 * WP_Block Object
	 *
	 * @var WP_Block
	 */
	private $block = null;
	/**
	 * SHortcode Attributes
	 *
	 * @var array
	 */
	private $shortcodeAtts = [];
	/**
	 * Replace keywords
	 *
	 * @var array
	 */
	private $replaceKeywords = [
		'shortcode' 			=> '%shortcode%',				// Selected shortcode name
		'shortcodeAttrs'	=> '%shortcodeAttrs%',	// Arguments of the set shortcode
		'shortcodeStr'		=> '%shortcodeStr%',		// Generated shortcode string
		'className'				=> '%wscClass%',				// Additional CSS class
	];
	/**
	 * Pre Replace keywords
	 *
	 * @var array
	 */
	private $preReplaces = [
		'className',
	];
	/**
	 * Pre Replace keywords
	 *
	 * @var array
	 */
	private $cache = [];

	/**
	 * Constructor
	 *
	 * @param array    $attributes The block attributes.
	 * @param string   $content    The block content.
	 * @param WP_Block $block      Block instance.
	 * @return string shortcode strings
	 */
	 public function __construct( $attributes = [], $content = '', $block = null ){
		// Construct Base Class
		parent::__construct();
		// Set Variables
		$this->cache = [];
		$this->attributes = $attributes;		// Block attributes
		$this->content = $content;			// Block content
		$this->block = $block;	// Block Object
	}

	/**
	 * To Sting
	 *
	 * @return string
	 */
	public function __toString(){
		$this->cache = [];
		$res = $this->getShortCode();
		return $res;
	}

	/**
	 * Get ShortCode
	 *
	 * @return string The Shortcode.
	 */
	public function getShortCode(){
		$res = '';
		// get shortcode attributes
		$shortcodeAttrStr = $this->getShortcodeAttrStr( $this->shortcodeAtts );
		// get shortcode strings
		$res = $this->getShortcodeStr( $this->content, $shortcodeAttrStr );
		// wrapping comment label
		$res = $this->wrapComment( $res );
		// replace keywords
		$res = $this->replaceKeywords( $res );
		return $res;
	}

	/**
	 * Get Shortcode Attribute Strings
	 *
	 * @param array $shortcodeAtts shortcode attributes
	 * @return string shortcode attribute strings
	 */
	private function getShortcodeAttrStr( $shortcodeAtts = []){
		$res = '';
		// if options values exists
		if( !empty($this->attributes['options']) ){
			// analyse attributes
			$attrOrg = preg_replace(
				'/[\n\r]+/', '&',
				$this->attributes['options']
			);
			$shortcodeAtts = (array) wp_parse_args( 
				$attrOrg, (array) $shortcodeAtts );
			// get attributes without key
			$optKeyOnly = (array) array_filter( 
				$shortcodeAtts,
				array( &$this, 'af_keyOnly' ),
				ARRAY_FILTER_USE_BOTH
			);
			$optKeyOnly = (array) array_keys( $optKeyOnly );
			// shortcode attributes array
			$shortcodeAtts = (array) array_merge( 
				(array) array_filter( $shortcodeAtts ),
				$optKeyOnly
			);
			ksort( $shortcodeAtts, SORT_STRING );
			// make shortcode attributes strings
			foreach( $shortcodeAtts as $akey => $aval ){
				$res .= empty($res) ? '' : ' ';
				// sanitize quotations
				$aval = $this->sanitize_quotations($aval);
				if( is_numeric( $akey ) ){
					// attribute without key
					$res .= trim( $aval );
				}
				else
				{
					// attribute with key
					$res .= $akey . '=';
					$res .= $aval;
				}
			}
		}
		$res = (string) apply_filters(
			$this->getHookName( 'getShortcodeAttrStr' ),
			$res,
			$this->attributes, $this->content, $this->block,
			$shortcodeAtts
		);
		$this->attributes['shortcodeAttrs'] = $res;
		return $res;
	}

	/**
	 * Get Shortcode Strings
	 *
	 * @param string $content Block Content
	 * @param string $attrStr shortcode attributte stirngs
	 * @return string shortcode strings
	 */
	private function getShortcodeStr( $content = '', $attrStr = '' ){
		$res = $content;
		$res = (string) apply_filters(
			$this->getHookName( 'pre_getShortcodeStr' ),
			$res,
			$this->attributes, $this->content, $this->block,
			$attrStr
		);
		$shortcode = $this->attributes['shortcode'];
		$shortcodeStr = '';
		if( !empty($shortcode) ){
			// shortcode name exists
			$res = $shortcodeStr = "[{$shortcode} {$attrStr}]";
			$res .= $content;
			$res .= "[/{$shortcode}]";
		}
		$res = (string) do_shortcode( $res );
		$res = (string) apply_filters(
			$this->getHookName( 'getShortcodeStr' ),
			$res,
			$this->attributes, $this->content, $this->block,
			$attrStr
		);
		// keyword '%shortcodeStr%' value
		$shortcodeStr = $this->esc_shortcode($shortcodeStr, true);
		$shortcodeStr = (string) apply_filters(
			$this->getHookName( 'shortcodeStr' ),
			$shortcodeStr,
			$this->attributes, $this->content, $this->block,
			$attrStr
		);
		$this->attributes['shortcodeStr'] = $shortcodeStr;
		return $res;
	}

	/**
	 * Wrap Comment to the shortcode strings
	 *
	 * @param string $shortcodeStr shortcode strings
	 * @return void wrapped shortcode strings
	 */
	private function wrapComment( $shortcodeStr = '' ){
		$res = $shortcodeStr;
		$comment = $this->attributes['comment'];
		$comment = $this->donotShortcode( $comment );
		$comment = do_shortcode( $comment );
		$comment = (string) apply_filters(
			$this->getHookName( 'comment' ),
			$comment,
			$this->attributes, $this->content, $this->block
		);
		if( !empty( $comment ) ){
			// wrapping comment label exists
			$comment = $this->esc_shortcode($comment);
			$comment = esc_html($comment);
			// comment out
			$text = "\n<!-- " . $comment . ' -->';
			$text .= $res;
			$text .= '<!-- /' . $comment . " -->\n";
			$res = $text;
		}
		$res = (string) apply_filters(
			$this->getHookName( 'wrapComment' ),
			$res,
			$this->attributes, $this->content, $this->block
		);
		return $res;
	}

	/**
	 * Replace KeyWords to the shortcode strings
	 *
	 * @param string $shortcodeStr shortcode strings
	 * @param bool $pre run first
	 * @return void re@laced shortcode strings
	 */
	private function replaceKeywords( $shortcodeStr = '', $pre = true ){
		$res = $shortcodeStr;
		if( empty( $this->cache['attributes'] ) ){
			$attributes = (array) $this->attributes;
			if( $pre ){
				$pre = false;
				foreach( (array) $this->preReplaces as $akey ){
					if( empty( $attributes[$akey] ) ) continue;
					$attributes[$akey] 
						= $this->replaceKeywords( $attributes[$akey], $pre );
				}
			}
			$this->cache['attributes'] = (array) $attributes;
		}
		else
		{
			$attributes = (array) $this->cache['attributes'];
		}
		$replKW = (array) $this->replaceKeywords;
		$replKW = (array) apply_filters(
			$this->getHookName( 'shortcodeStr' ),
			$replKW,
			$this->attributes, $this->content, $this->block
			, $attributes
		);
		foreach( $replKW as $akey => $aWord ){
			// replace kwywords
			$aval = empty($attributes[$akey]) ? '' : $attributes[$akey];
			$res = str_replace( $aWord, $aval, $res );
		}
		$res = (string) apply_filters(
			$this->getHookName( 'shortcodeStr' ),
			$res,
			$this->attributes, $this->content, $this->block
			,$attributes
		);
		return $res;
	}
}

