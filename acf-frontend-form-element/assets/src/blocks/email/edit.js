import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, ToggleControl, PanelRow } from '@wordpress/components';
import { __, _e } from '@wordpress/i18n';
import FieldWrap from '../../components/fieldWrap';
import FieldControls from '../../components/fieldControls';

const plugin = 'acf-frontend-form-element';

const Edit = (props) => {
	const { attributes, setAttributes } = props;
	const { default_value, placeholder, prepend, append } = attributes;

	const blockProps = useBlockProps();


	return (
	<div { ...blockProps }>
		<FieldControls
			{...props}
		>
			<TextControl
				type        = 'email'
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


		</FieldControls>
			
		<FieldWrap
			{...props}
		>
		{ prepend &&
			<span className="acf-input-prepend">{prepend}</span>
		}
		<input
			type        = 'email'
			placeholder = {placeholder}
			value   	= {default_value}
			onChange	= {(e) => {
				setAttributes({ default_value: e.target.value });
			}}
			style		={{ width: 'auto', flexGrow: 1 }}
		/>
		{ append &&
			<span className="acf-input-append">{append}</span>
		}
		</FieldWrap>
	</div>

	)

}

export default Edit;
