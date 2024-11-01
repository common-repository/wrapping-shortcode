<?php
/**
 * Class Name:        Wrapping ShortCode - Base Class
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
 * Base Class
 */
class Base {

	/**
	 * Hook prefix name
	 *
	 * @var string
	 */
	public $prefix;
	/**
	 * Exclude shortcode in comment labels
	 *
	 * @var array
	 */
	private $withoutCommentLabel = [
		'wp_caption',
		'caption',
		'gallery',
		'playlist',
		'audio',
		'video',
		'embed',
		'commentout',
		'meta-loop',
	];

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct(){
		// Hook prefix
		$this->prefix = $this->getHookPrefix();
	}

	/**
	 * Get Hook Prefix
	 * 	'(namespace)/(class name)'
	 *
	 * @return string Hook Prefix
	 */
	public function getHookPrefix(){
		$res = '';
		// Hook prefix
		$res = get_class($this);
		$res = str_replace( "\\", '/', $res );
		return $res;
	}

	/**
	 * Get Filter Hook Name
	 * 	'(namespace)/(class name)/($name)'
	 *
	 * @param string $name Hook last name.
	 * @return string Hook name.
	 */
	public function getHookName( $name ){
		$res	= (string) $this->prefix 
					. '/' . (string) $name;
		return $res;
	}

	/**
	 * Get Shortcode names list
	 *
	 * @return array Shortcode names list
	 */
	public function getShortcodes(){
		global $shortcode_tags;
		// get list
		$res = array_keys((array) $shortcode_tags);
		$res = (array) apply_filters(
			$this->getHookName( 'getShortcodes' ),
			(array) $res
		);
		return $res;
	}

	/**
	 * Change value from 'no' or 'false' to false
	 *
	 * @param array $atts Attributes
	 * @return array changed attributes
	 */
	public function chageNo2False( $atts = [] ){
		$res = (array) $atts;
		$atts['no2false'] = isset( $atts['no2false'] ) 
											? (array) $atts['no2false'] : [];
		foreach( 	(array) $atts['no2false'] as $akey ){
			// boolean attributes
			switch( strtolower( $res[$akey] ) ){
				case 'no':
				case 'false':
					$res[$akey] = false;
					break;
			}
		}
		return $res;
	}

	/**
	 * Remove < br > before and after content.
	 *
	 * @param string $content
	 * @param bool $flag Do or not
	 * @return string changed content
	 */
	public function trimBr( $content, $flag ){
		$res = (string) $content;
		if( ! empty($flag) ){
			// remove the first <br>
			$res = preg_replace( 
				'#^<br\s*\/?>#', "\n", $res );
			// remove the last <br>
			$res = preg_replace( 
				'#<br\s*\/?>$#', "\n", $res );
		}
		return $res;
	}

	/**
	 * Remove whitespace before and after content.
	 *
	 * @param string $content
	 * @param bool $flag Do or not
	 * @return string changed content
	 */
	public function doTrim( $content, $flag ){
		$res = (string) $content;
		// trim
		$res 	= empty($flag) 
					? $res 
					: trim( $res );
		return $res;
	}

	/**
	 * Run a content shortcode.
	 *
	 * @param string $content
	 * @param bool $flag Do or not
	 * @return string changed content
	 */
	public function doShortcode( $content, $flag ){
		$res = (string) $content;
		if( ! function_exists('do_shortcode') ) 
			return $content; 
		// do shortcode
		$res	= empty($flag) 
					? $res 
					: do_shortcode( $res );
		return $res;
	}

	/**
	 * Escape HTML tags in content.
	 *
	 * @param string $content
	 * @param bool $flag Do or not
	 * @return string changed content
	 */
	public function escHtml( $content, $flag ){
		$res = (string) $content;
		if( ! function_exists('esc_html') ) 
			return $content; 
		// escape html tags
		$res	= empty($flag) 
					? $res 
					: esc_html( $res );
		return $res;
	}

	/**
	 * Number Compare for sort
	 *
	 * @param mixed $a1 1st value
	 * @param mixed $a2 2nd value
	 * @param string $order asc|desc
	 * @return int compare value(-1|0|1)
	 */
	public function numCmp( $a1, $a2, $order = 'asc' ){
		$res = 0;
		if( $a1 != $a2 ){
			if( $order == 'desc' ){
				$res = ($a1 > $a2) ? -1 : 1;
			}
			else
			{
				$res = ($a1 < $a2) ? -1 : 1;
			}
		}
		return $res;
	}

	/**
	 * Array Filter Callback: Without Key
	 *
	 * @param  mixed $key Array key.
	 * @return bool Filter result.
	 */
	public function af_withoutKey( $key ){
		$res = is_numeric( $key );
		$res = apply_filters(
			$this->getHookName( 'af_withoutKey' ),
			$res
		);
		return $res;
	}

	/**
	 * Array Filter Callback: With Key
	 *
	 * @param  mixed $key Array key.
	 * @return bool Filter result.
	 */
	public function af_withKey( $key ){
		$res = ! is_numeric( $key );
		$res = apply_filters(
			$this->getHookName( 'af_withKey' ),
			$res
		);
		return $res;
	}

	/**
	 * Array Filter Callback: Array key only. no value.
	 *
	 * @param mixed $value
	 * @return bool True( no value or empty strings )
	 */
	public function af_keyOnly( $value ){
		// no value without zero
		return $value === null || $value === '';
	}

	/**
	 * Each (PHP function completion)
	 * Return the top key and value pair from an array.
	 * Because it was deprecated in PHP8.
	 *
	 * @param array|object $src array with key
	 * @return array key and value pair
	 */
	public function each( $src ){
		$res = false;
		$tmp = $src;
		$arr = [null,null,'value'=>null,'key'=>null];
		if( is_object( $tmp ) ) $tmp = (array) get_object_vars( $src );
		if( is_array( $tmp ) ){
			$keys = array_keys( $tmp );
			$vals = array_values( $tmp );
			$arr[0] = $arr['key'] = $keys[0];
			$arr[1] = $arr['value'] = $vals[0];
			$res = $arr;
		}
		else return $res;
		return $res;
	}

	/**
	 * Do not shortcodes
	 *
	 * @param string $src source strings
	 * @return string	escaped shortcode strings
	 */
	public function donotShortcode( $src = '' ){
		$res = $src;
		// shortcodes list
		$shortcodes = (array) $this->getShortcodes();
		// without shortcodes
		$excludes = (array) apply_filters(
			$this->getHookName( 'donotShortcode' ),
			(array) $this->withoutCommentLabel );
		foreach( $excludes as $akey ){
			// escaped shortcode
			if( empty( $akey ) ) continue;
			if( ! is_scalar( $akey ) ) continue;
			if( ! in_array( $akey, $shortcodes ) ) continue;
			$rgx = '/\[(\/?' . $akey . '[^\]]*)\]/';
			$test = preg_match($rgx,$res);
			$res = preg_replace( $rgx, '[ $1 ]', $res );
		}
		$res = (string) apply_filters(
			$this->getHookName( 'donotShortcode' ),
			$res,
			$src
		);
		return $res;
	}

	/**
	 * Escaped shortcode
	 *
	 * @param string $src source strings
	 * @param boolean $only one or more shortcodes
	 * @return string	escaped shortcode strings
	 */
	public function esc_shortcode( $src = '', $only=false ){
		$res = $src;
		// one or more
		$rgx = $only ? '/^\[\s*(.+)\s*\]$/' : '/\[\s*([^\]]+)\]/';
		// escaped shortcode
		$res = preg_replace( $rgx, '[[ $1 ]]', $res );
		$res = (string) apply_filters(
			$this->getHookName( 'esc_shortcode' ),
			$res,
			$src
		);
		return $res;
	}

	/**
	 * Sanitize quotations
	 *
	 * @param string $src argument strings
	 * @return string sanitized strings
	 */
	public function sanitize_quotations( $src='' ){
		$res = $src;
		$quats = '';
		$res = trim($res);
		// remove \0
		$res = preg_replace( '/\0/', '', $res );
		if( preg_match('/^"(.*)"$/', $res, $matches)){
			// without wrapping double quotations
			$quats = '"';
			$res = $matches[1];
		}
		else
		if( preg_match("/^'(.*)'$/", $res, $matches)){
			// without wrapping single quotations
			$quats = "'";
			$res = $matches[1];
		}
		// add slashs
		$res = addslashes( $res );
		// escape attributes
		$res = esc_attr( $res );
		if( empty( $quats ) ){
			// wrap double quotations to value with space
			if( preg_match( '/[\s\t\n\r]/', $res ) ){
				$res = '"' . $res . '"';
				$quats = '"';
			}
		}
		else
		{
			// return wrapped quotes
			$res = $quats . $res . $quats;
		}
		$res = (string) apply_filters(
			$this->getHookName( 'sanitize_quotations' ),
			$res,
			$src, $quats
		);
		return $res;
	}
}