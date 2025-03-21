import { registerBlockType } from '@wordpress/blocks';
import { __, _e } from '@wordpress/i18n';
import name from './block.json';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

registerBlockType(
	name,
	{
		edit: () => {
			const blockProps = useBlockProps();
			//make innerblocks props with template
			const innerBlocksProps = useInnerBlocksProps( blockProps, {
				template: [
					['core/heading', { content: 'Form' }],
					['frontend-admin/text-field', {}],
					['core/button', { text: 'Submit' }]
				]
			});
	
			return (
				<div {...innerBlocksProps} />
			);
		},
	
		save: () => {
			const blockProps = useBlockProps.save();
			const innerBlocksProps = useInnerBlocksProps.save( blockProps );
	
			return (
				<div {...innerBlocksProps} />
			);
		},
	}
);
