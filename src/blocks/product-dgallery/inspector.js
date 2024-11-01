/**
 * Internal dependencies
 */

/**
 * Inspector controls
 */

const { __ } = wp.i18n;

const {
	PanelBody,
	TextControl,
} = wp.components;

const {
	InspectorControls,
} = wp.blockEditor || wp.editor;

const { Component, Fragment } = wp.element;

export default class Inspector extends Component {
	render() {
		const {
			attributes: {
				postID
			},
			setAttributes,
		} = this.props;

		return (
			<InspectorControls>
				<PanelBody title={ __( 'Settings' ) }>
					<TextControl
						label={ __( 'Product ID' ) }
						help={ __( 'Leave empty for use ID of current product' ) }
						value={ postID }
						onChange={ ( newPostID ) =>
							setAttributes( { postID: newPostID } )
						}
					/>
				</PanelBody>
			</InspectorControls>
	 	);
	}
}