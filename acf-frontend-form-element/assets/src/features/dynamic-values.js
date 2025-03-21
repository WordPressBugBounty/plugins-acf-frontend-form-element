import FieldSelect from '../components/fieldSelectControl';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __, _e } from '@wordpress/i18n';

const dynamicValueControls = wp.compose.compose(
    wp.compose.createHigherOrderComponent(
        (BlockEdit) => {
            return (props) => {
                if (props.name !== 'core/paragraph') {
                    return(<BlockEdit {...props} />);
                    }
        const { Fragment } = wp.element;
        const { attributes, isSelected } = props;
        /* const newProps = {
                ...props,
                attributes: {
                ...attributes,
                className: newClassName
                },
                } */
                     
                return (
                <Fragment>
                <div className={newClassName}>
                        <BlockEdit {...newProps} />
                        {isSelected && (props.name == 'core/paragraph') && 
                            <InspectorControls>
                            <PanelBody
                                title={ __('Dynamic Values') }
                            >
                                <FieldSelect
                                    label={__('Field', 'frontend-admin')}
                                    value=''
                                    onChange=''
                                />
                            </PanelBody>     
                            </InspectorControls>   
                               }
                       
                </div>
                </Fragment>
             );
            };
        }, 'dynamicValueControls'
    )
);
 
wp.hooks.addFilter(
    'editor.BlockEdit',
    'frontend-admin/dynamic-values',
    dynamicValueControls
);