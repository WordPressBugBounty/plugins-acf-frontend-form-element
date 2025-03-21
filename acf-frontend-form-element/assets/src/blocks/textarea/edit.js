import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, TextareaControl, ToggleControl, PanelRow } from '@wordpress/components';
import { __, _e } from '@wordpress/i18n';
import FieldWrap from '../../components/fieldWrap';
import FieldControls from '../../components/fieldControls';

const plugin = 'acf-frontend-form-element';

const Edit = (props) => {
	const { attributes, setAttributes } = props;
	const { default_value, placeholder, maxlength, rows, cols } = attributes;

	const blockProps = useBlockProps();


	return (
	<div { ...blockProps }>
		<FieldControls
			{...props}
		>
			<TextareaControl
                label       = {__( 'Default Value', plugin )}
                value       = {default_value}
                onChange    = {(newval) => setAttributes( { default_value: newval } )}
            />		
			<TextareaControl
				label       = {__( 'Placeholder', plugin )}
				value       = {placeholder}
				onChange    = {(newval) => setAttributes( { placeholder: newval } )}
			/>
			<TextControl
				type 		= "number"
				label       = {__( 'Character Limit', plugin )}
				value       = {maxlength}
				onChange    = {(newval) => setAttributes( { maxlength: newval } )}
			/>
			<TextControl
				type 		= "number"
				label       = {__( 'Rows', plugin )}
				value       = {rows}
				onChange    = {(newval) => setAttributes( { rows: newval } )}
			/>

		</FieldControls>
			
		<FieldWrap
			{...props}
		>
		<textarea
                maxLength   = {maxlength}
                placeholder = {placeholder}
                rows        = {rows}
                value       = {default_value}
                onChange    = {(e) => {
                    setAttributes({ default_value: e.target.value });
                }}
		>{default_value}</textarea>
		</FieldWrap>
	</div>

	)

}

export default Edit;
