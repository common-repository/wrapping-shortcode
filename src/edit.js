/**fil0
 * Block editor JS
 * 
 * @since 2022.10.14
 * @author Asumaru Corp.
 * 
 */


/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * WordPress Components & block-editor
 */
import {
	TextControl,
	TextareaControl,
	SelectControl,
} from '@wordpress/components';
import {
	// RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import { InnerBlocks } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit( { 
	attributes, 
//	isSelected, 
	setAttributes 
} ) {
	const blockProps = useBlockProps();

	// Simplify access to attributes
	const {
		shortcode, 
		content, 
		comment,
		options,
	} = attributes;

	const onChangeContent = ( newContent ) => {
		setAttributes( { content: newContent } );
	};
	const onChangeComment = ( newComment ) => {
		setAttributes( { comment: newComment } );
	};
	const onChangeShortcode = ( newShortcode ) => {
		setAttributes( { shortcode: newShortcode } );
	};
	const onChangeOptions = ( newOptions ) => {
		setAttributes( { options: newOptions } );
	};

	// Shortcode option list
	attributes.shortcode = 
			attributes.shortcode !== undefined
		? attributes.shortcode
		: '';
	var opts = [
		'commentout',
		'bloginfo'
	];
	var phpVals = asm_WSC_Vals
							? asm_WSC_Vals
							: {shortcodes:[]};
	var opts 	= phpVals.shortcodes 
						? phpVals.shortcodes
						: [];
	var optsSet = [ { 
		label: __('(none)', 'asm-wrapping-shortcode-block'), value: '' 
	} ];
	var optSame = 0;
	for( var aOpt in opts ){
		if( new String( attributes.shortcode ) == new String( opts[aOpt] ) ) optSame = optsSet.length;
		optsSet[optsSet.length] = { 
			label: '[' + opts[aOpt] + ']', 
			value: opts[aOpt],
		};
	}
	const optsShortcode = optsSet;
	const myTitle = attributes.shortcode == ''
								? __( 'Wrapping ShortCode', 'asm-wrapping-shortcode-block' )
								: '[' + attributes.shortcode + ']';

	// return Editor HTML
	return (
		<div { ...blockProps }>
			{/* 左パネル 設定項目 */}
      <InspectorControls key="setting">
  	    <div id="asm-wapping-shortcode-controls"  class="components-base-control asmwsc-control">
					{/* shortcodeの設定 */}
					<fieldset class="components-base-control__field">
            <legend className="blocks-base-control__label">
                { __( 'ShortCode', 'asm-wrapping-shortcode-block' ) }
            </legend>
						<p className="blocks-base-control__description">
							{ __( 'Please select a shortcode.', 'asm-wrapping-shortcode-block' ) }
						</p>
						<SelectControl
							className="shortcode"
							value={ attributes.shortcode }
							onChange={ onChangeShortcode }
							options={ optsShortcode }
						/>
          </fieldset>
					{/* optionsの設定 */}
					<fieldset class="components-base-control__field">
            <legend className="blocks-base-control__label">
                { __( 'Arguments', 'asm-wrapping-shortcode-block' ) }
            </legend>
						<p className="blocks-base-control__description">
							{ __( '(Optional) Set shortcode arguments.<br/>Write "argument" or "argument_key=value" on one line.', 'asm-wrapping-shortcode-block' ) }
						</p>
						<TextareaControl
							className="options"
							value={ attributes.options }
							onChange={ onChangeOptions }
						/>
          </fieldset>
					{/* commentの設定 */}
					<fieldset class="components-base-control__field">
            <legend className="blocks-base-control__label">
                { __( 'Comment Label', 'asm-wrapping-shortcode-block' ) }
            </legend>
						<p className="blocks-base-control__description">
							{ __( '(Optional) You can put HTML comments around the shortcode.', 'asm-wrapping-shortcode-block' ) }
						</p>
						<TextControl
							className="comment"
							value={ attributes.comment }
							onChange={ onChangeComment }
						/>
          </fieldset>
				</div>
      </InspectorControls>
			{/* ブロック内表示 */}
			<h5 class="wrapping-shortcode-edit-title">
				{	myTitle }
			</h5>
			<InnerBlocks />
		</div>
	);
}
