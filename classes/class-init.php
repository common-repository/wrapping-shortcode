<?php
/**
 * Class Name:        Wrapping ShortCode - Init
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
 * require Base Class
 */
require_once 'class-base.php';

/**
 * Init Class
 * 
 * @inheritDoc
 */
class Init extends Base {

	/**
	 * Including class files
	 *
	 * @var array
	 */
	private $classes = [
		'class-template.php',
		'class-enqueue.php',
		'class-shortcode-commentout.php',
		'class-shortcode-metaloop.php',
	];

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct(){
		// Construct Base Class
		parent::__construct();
		// Load classes.
		$this->loadClasses();
	}

	/**
	 * load Classes
	 *
	 * @return void
	 */
	public function loadClasses() {
		$classes = (array) apply_filters(
			$this->getHookName( 'getClasses' ),
			(array) $this->classes
		);
		foreach( (array) $classes as $aval ){
			// Load other classes
			if( $path = $this->getClassesPath($aval) ){
				require_once($path);
			}
		}
	}

	/**
	 * Include Class File Path
	 *
	 * @param string $file class file name.
	 * @return void
	 */
	private function getClassesPath( $file = null ){
		// Class path
		$path0 = ASMWSC_PATH . '/classes/';
		if( ! empty($file) )	$path0 .= (string) $file;
		$path = realpath($path0);
		return $path;
	}
}
