import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, RangeControl } from '@wordpress/components';
import { __, _e } from '@wordpress/i18n';
import FieldWrap from '../../components/fieldWrap';
import FieldControls from '../../components/fieldControls';

const plugin = 'acf-frontend-form-element';

const Edit = (props) => {
	const { attributes, setAttributes } = props;
	const { default_value, placeholder, prepend, append, min, max, step } = attributes;

    const blockProps = useBlockProps();

    return (
    <div { ...blockProps }>
        <FieldControls
            {...props}
        >
			<RangeControl
				min         = {min}	
				max         = {max}	
				step        = {step}	
                label       = {__( 'Default Value', plugin )}
                value       = {default_value}
                onChange    = {(newval) => setAttributes( { default_value: newval } )}
            />
			<TextControl
				label       = {__( 'Placeholder', plugin )}
				value       = {placeholder}
				onChange    = {(newval) => setAttributes( { placeholder: newval } )}
			/>
			
			<TextControl
				label       = {__( 'Prepend', plugin )}
				value       = {prepend}
				onChange    = {(newval) => setAttributes( { prepend: newval } )}
			/>
			<TextControl
				label       = {__( 'Append', plugin )}
				value       = {append}
				onChange    = {(newval) => setAttributes( { append: newval } )}
			/>
			<TextControl
                type 		= "number"
                label       = {__( 'Minimum Value', plugin )}
				max			= {max}
                value       = {min}
                onChange    = {(newval) => setAttributes({ min: Math.min(newval, max) })}
            />
            <TextControl
                type 		= "number"
                label       = {__( 'Maximum Value', plugin )}
				min			= {min}
                value       = {max}
                onChange    = {(newval) => setAttributes({ max: Math.max(newval, min) })}
            />
            <TextControl
                type 		= "number"
                label       = {__( 'Step', plugin )}
                value       = {step}
                onChange    = {(newval) => setAttributes( { step: newval } )}
            />
		</FieldControls>
			
		<FieldWrap
			{...props}
		>
		{ prepend &&
			<span className="acf-input-prepend">{prepend}</span>
		}
		<div style ={{ width: 'auto', flexGrow: 1 }} >
		<RangeControl
			min         = {min}	
			max         = {max}	
			step        = {step}	
			hideLabelFromVision = {true}
			value       = {default_value}
			onChange    = {(newval) => setAttributes( { default_value: newval } )}
		/>
		</div>
		{ append &&
			<span className="acf-input-append">{append}</span>
		}
		</FieldWrap>
	</div>

	)

}

export default Edit;
