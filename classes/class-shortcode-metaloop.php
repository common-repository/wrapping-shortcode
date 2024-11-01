<?php
/**
 * Class Name:        ShortCode - PostMeta Loop
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

use function PHPUnit\Framework\at;

/**
 * ShortCode PostMeta Loop Class
 * 
 * @inheritDoc
 */
class ShortCode_MetaLoop extends Base {

	// private $prefix;				// Hook prefix name
	// Variables
	/**
	 * ShortCode Setting
	 *
	 * @var array
	 */
	private $shortcode = [
		'name'				=> 'meta-loop',	// ShortCode Name
		'callback'		=> 'sc_MetaLoop',	// callback method
		'attribures'	=> [
			// Arguments without keys are meta keys.
			// Count up from start to end if no argument with no key.
			'start'					=> 0,					// loop start position. default 0. confirm to 'offset' on 'array_slice'.
			'end'						=> null,			// loop end position. default 0. confirm to 'length' on 'array_slice'.
			'step'					=> 1,					// loop step count.
			'order'					=> 'none',			// Sort Type. none|asc|desc|random
			'orderby'				=> 'counter',	// Sort key. counter|metakey|metavalue
			'reorder'				=> 'yes',			// reorder output key
			'replaceRangeVal'	=> '%rangeVal%',// string to replace with number.
			'replaceNumber'	=> '%number%',// string to replace with number.
			'replaceCounter'=> '%counter%',// string to replace with count. %number%+1.
			'repkaceKey'		=> '%key%',		// string to replace with meta-key.
			'repkaceValue'	=> '%value%',	// string to replace with meta-value.
			'noContent'			=> 	// Output no content.
				'<div class="no-%number%">%key%:%value%</div>',
			'lines'					=> null,	// Number of outputs. null is output until the end. 0 is hidden.
			'nl2br'					=> 'yes',	// \n\r to <br> in meta value.
		],
		'no2false'	=> [	// boolean attributes
			'reorder',
			'nl2br',
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
	 * shortcode: PostMeta Loop
	 *
	 * @param array $atts Attributes
	 * @param string $content Content
	 * @param integer $tags	shortcode tag
	 * @return string commented out HTML
	 */
	public function sc_MetaLoop( 
			$atts, $content = '', $tags = 0 ){
		// Initialize Attribures
		$atts = wp_parse_args( $atts, 
			(array) $this->shortcode['attribures'] );
		$atts['no2false'] = $this->shortcode['no2false'];
		$atts = $this->chageNo2False($atts);
		$options = (array) array_filter( (array) $atts, 
			array( &$this, 'af_withKey' ), ARRAY_FILTER_USE_KEY );
		$options = $this->chageNo2False($options);
		$options = (array) apply_filters(
			$this->getHookName( 'options' ),
			$options,
			$atts, $content, $tags
		);
		// get meta keys
		$metaKeys = (array) array_filter( (array) $atts, 
			array( &$this, 'af_withoutKey' ), ARRAY_FILTER_USE_KEY );
		$metaKeys = (array) apply_filters(
				$this->getHookName( 'metaKeys' ),
				$metaKeys,
				$atts, $content, $tags, $options
		);
		$options += compact( 'metaKeys' );
		// Range
		$oneset = (int) count($metaKeys); 
		$start = (int) $options['start'];
		$length = $end = $options['end'];
		$length = empty( $length ) && $length !== 0 && $length !== '0' 
						? $oneset - 1 : (int) $length;
		$step = (int) $options['step'];
		if( empty($step) ) return '';
		$range = (array) range(
			$start,
			$length,
			$step
		);
		$lines = $options['lines'];
		$lines	= empty($lines) && $lines !== 0 && $lines !== '0'
						? count( $range ) : (int) $lines;
		$lines = abs( $lines );
		$min = (int) min( $range );
		$max = (int) max( $range );
		$options += compact( 'range','min','max','oneset','length','lines' );
		// Output Meta Keys
		$outputKeys = [];
		$first = $first0 = null;
		if( $oneset > 0 ){
			// prepare to find.
			$first = $first0 = $min % $oneset;
			$current = $first = $first < 0 ? $oneset + $first : $first;
			foreach( $range as $akey => $aval ){
				$outputKeys[$akey] = $metaKeys[$current];
				$current = ( $current + $step ) % $oneset;
			}
		}
		else $outputKeys = $range;
		$options += compact( 'outputKeys', 'first' );
		// content keep & hooking
		$content_org = $content;
		$content = (string) $content;
		$content = trim( $content );
		$content = empty( $content ) 
						&& $content !== 0 
						&& $content !== '0'
						? html_entity_decode( $options['noContent'] )
						: $content;
		$content = (string) apply_filters(
			$this->getHookName( 'contentOrginal' ),
			(string) $content,
			$atts, $content_org, $tags, $options
		);
		$nl2br = $options['nl2br'];
		$options += compact( 'content', 'nl2br' );
		// PostMeta
		$metas = [];
		$postId = (int) get_the_ID();
		$metaVals = [];
		if( empty($metaKeys) ){
			$metaKeys = (array) array_keys( $range );
			$metaVals = $range;
		}
		else
		{
			foreach( (array) $metaKeys as $akey ){
				$aval = (string) get_post_meta( $postId, $akey, true );
				$aval = $nl2br ? nl2br( $aval ) : $aval;
				$metaVals[ $akey ] = $aval;
			}
		}
		$metaVals = (array) apply_filters(
			$this->getHookName( 'contentOrginal' ),
			$metaVals,
			$atts, $content, $tags, $options
		);
		// Output Values
		$outputArray = [];
		foreach( $range as $akey => $aval ){
			$ckey = $oneset < 1 ? $akey : $outputKeys[$akey];
			$outputArray[$akey] = [ $ckey => $metaVals[$ckey], null, null, $aval ];
		}
		$outputArray = (array) apply_filters(
			$this->getHookName( 'outputArray' ),
			$outputArray,
			$atts, $content, $tags, $options
		);
		$outputArray = $this->sortArray( $outputArray, $options );
		// Loop
		$res = "";
		$cnt = 0;
		foreach( $outputArray as $counter => $aval ){
			$cnt++;
			if( $cnt > $lines ) break;
			$number = $counter + 1;
			$aval = (array) $aval;
			$meta = $this->each( $aval ); 
			$rangeVal = isset( $aval[2] ) ? $aval[2] : null;
			$aLine = $content;
			$aLine = str_replace( $options['replaceRangeVal'], $rangeVal, $aLine );
			$aLine = str_replace( $options['replaceCounter'], $counter, $aLine );
			$aLine = str_replace( $options['replaceNumber'], $number, $aLine );
			$aLine = str_replace( $options['repkaceKey'], $meta['key'], $aLine );
			$aLine = str_replace( $options['repkaceValue'], $meta['value'], $aLine );
			$res .= $aLine . "\n";
		}
		$res = (string) apply_filters(
			$this->getHookName( 'content' ),
			(string) $res,
			$atts, $content, $tags, $options
		);
		return $res;
	}

	/**
	 * Sorting array
	 *
	 * @param array $arr	array to be sorted
	 * @param array $options the array of option values
	 * @return array sorted array
	 */
	private function sortArray( $arr = [], $options = [] ){
		$res = $arr;
		// order attributes
		$order = strtolower( (string) $options['order'] );
		$orderby = strtolower( (string) $options['orderby'] );
		$oneset = isset( $options['onenet'] ) ? (int) $options['onenet'] : 0;
		$this->shortcode['_sort'] = compact('order','orderby','oneset');
		if( $order == 'random' ){
			// random
			shuffle( $res );
		}
		else
		if( ! in_array( $order, ['asc','desc']) ){
			// not sort
		}
		else
		switch( $orderby ){
			case 'metakey':
			case 'metavalue':
				// sort value
				usort( $res, [ &$this, 'cb_sortMeta']);
				break;
			default:
				// sort key
				switch( $order ){
					case 'desc':
						krsort( $res );
						break 2;
					default :
						ksort( $res );
						break 2;
				}
				break;
		}
		if( $options['reorder'] ) $res = (array) array_values( $res );
		$res = (array) apply_filters(
			$this->getHookName( 'sortArray' ),
			$res,
			$arr, $options
		);
		$this->shortcode['_sort'] = null;
		return $res;
	}

	/**
	 * Callback for usort. comparison for sorting.
	 *
	 * @param array $v1	Associative array to be compared
	 * @param array $v2	Associative array to compare
	 * @return int	Comparison result. -1|0|1.
	 */
	private function cb_sortMeta( $v1, $v2 ){
		$res = 0;
		// order attributes
		$sort = (array) $this->shortcode['_sort'];
		extract($sort);
		$src = $orderby;
		// search key
		$pos = strpos( $src, 'key');
		$src = empty($pos) && $pos !== 0 ? $src : 'key';
		// search value
		$pos = strpos( $src, 'val');
		$src = empty($pos) && $pos !== 0 ? $src : 'value';
		// search rangeVal
		$src = in_array( $src, ['key','value'] ) ? $src : 'range';
		// ASC|DESC only
		if( ! in_array( $order, ['asc','desc'])) return $res;
		// v1: meta key|value|range
		$v1 = (array) $v1;
		$meta1 = $this->each( $v1 ); 
		$meta1['range'] = $v1[2];
		$a1 = $meta1[$src];
		// v2: meta key|value|range
		$v2 = (array) $v2;
		$meta2 = $this->each( $v2 ); 
		$meta2['range'] = $v2[2];
		$a2 = $meta2[$src];
		// compare
		if( $src == 'range' ) $res = $this->numCmp( $a1, $a2, $order );
		else
		if( $src == 'value' && $oneset < 1 )
			$res = $this->numCmp( $a1, $a2, $order );
		else
		{
			$res = strcmp( $a1, $a2 );
			$res = $order == 'desc' ? $res * -1 : $res;
		}
		return $res;
	}
}

new ShortCode_MetaLoop();