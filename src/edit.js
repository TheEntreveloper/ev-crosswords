/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * Imports the InspectorControls component, which is used to wrap
 * the block's custom controls that will appear in in the Settings
 * Sidebar when the block is selected.
 *
 * Also imports the React hook that is used to mark the block wrapper
 * element. It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#inspectorcontrols
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { RichText, InspectorControls, useBlockProps } from '@wordpress/block-editor';

/**
 * Imports the necessary components that will be used to create
 * the user interface for the block's settings.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/components/panel/#panelbody
 * @see https://developer.wordpress.org/block-editor/reference-guides/components/text-control/
 * @see https://developer.wordpress.org/block-editor/reference-guides/components/toggle-control/
 */
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';

/**
 * Imports the useEffect React Hook. This is used to set an attribute when the
 * block is loaded in the Editor.
 *
 * @see https://react.dev/reference/react/useEffect
 */
import { useEffect } from 'react';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @param {Object}   props               Properties passed to the function.
 * @param {Object}   props.attributes    Available block attributes.
 * @param {Function} props.setAttributes Function that updates individual attributes.
 *
 * @return {Element} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {
	const blockProps = useBlockProps();
	const {
		content,
		textField,
	} = attributes;

	function onChangeContent( newContent ) {
		setAttributes( { content: newContent } );
	}

	function onChangeTextField( newValue ) {
		setAttributes( { textField: newValue } );
	}

	// When the block loads ...

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'ev-crossword-viewer' ) }>

					{ (
						<TextControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label="EV-Crossword"
							help="Name of crossword to display"
							value={ textField }
							onChange={ onChangeTextField }
						/>

					) }
				</PanelBody>
			</InspectorControls>
			{/*<p { ...useBlockProps() }>© { cwtitle }</p>*/}

			<RichText
				{ ...blockProps }
				key="editable"
				tagName="p"
				onChange={ onChangeContent }
				value={ content }
				placeholder = "Crossword name"
			/>

		</>
	);
}
