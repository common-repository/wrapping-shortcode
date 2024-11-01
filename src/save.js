/**
 * Block save JS
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
import { useBlockProps, RichText } from '@wordpress/block-editor';

/**
 * WordPress Components & block-editor
 */
	// import { TextControl } from '@wordpress/components';
import { InnerBlocks } from '@wordpress/block-editor';
	// import { useState } from '@wordpress/element';

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
export default function Save( { attributes } ) {
	const blockProps = useBlockProps.save();

// return (
// <div class="wrapping-sc" { ...blockProps }>
// 	<InnerBlocks.Content />
// </div>);
return ( <InnerBlocks.Content /> );
}
