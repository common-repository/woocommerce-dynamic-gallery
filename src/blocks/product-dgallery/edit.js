import Inspector from './inspector';

/**
 * WordPress dependencies
 */
const {
	BlockControls,
	useBlockProps,
} = wp.blockEditor || wp.editor;
const { __ } = wp.i18n;

const {
	Fragment
} = wp.element;

export default function DGalleryEdit( props ) {
	const blockProps = useBlockProps();

	const containerElement = (
		<div { ...blockProps }>
			<img
				src={ dgallery_block_editor.preview }
			/>
		</div>
	);

	return (
		<Fragment>
			<Inspector { ...{ ...props } } />
			<BlockControls group="block" />
			{ containerElement }
		</Fragment>
	);
}
