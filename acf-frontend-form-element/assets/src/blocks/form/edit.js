
import { __, _e } from '@wordpress/i18n';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';



const plugin = 'acf-frontend-form-element';

const Edit = (props) => {
    const blockProps = useBlockProps();
    const innerBlocksProps = useInnerBlocksProps( blockProps );

    return (
        <div {...innerBlocksProps} />
    );
}

export default Edit;