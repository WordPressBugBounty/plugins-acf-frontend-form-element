import { useBlockProps } from '@wordpress/block-editor';
import { ToggleControl, TextControl } from '@wordpress/components';
import { __, _e } from '@wordpress/i18n';
import FieldWrap from '../../components/fieldWrap';
import FieldControls from '../../components/fieldControls';
import ChoicesControl from '../../components/choicesControl';


const plugin = 'acf-frontend-form-element';

const Edit = (props) => {
	const { attributes, setAttributes } = props;
	const { default_value, choices, ajax, ui, allow_null, multiple, placeholder } = attributes;

	const blockProps = useBlockProps();

	return (
	<div { ...blockProps }>
		<FieldControls
			{...props}
		>		
			<ToggleControl
				label={__('Allow Null', plugin)}
				checked={allow_null}
				onChange={ ( value ) => setAttributes( { allow_null: value } ) }
			/>
			<TextControl
				label       = {__( 'Placeholder', plugin )}
				value       = {placeholder}
				onChange    = {(newval) => setAttributes( { placeholder: newval } )}
			/>
			<ToggleControl
					label={__('Select Multiple Values', plugin)}
					checked={multiple}
					onChange={ ( value ) => setAttributes( { multiple: value } ) }
			/>
			<ToggleControl
				label={__('Stylized UI', plugin)}
				checked={ui}
				onChange={ ( value ) => setAttributes( { ui: value } ) }
			/>
			{ ui &&
				<ToggleControl
					label={__('Lazy Load Choices', plugin)}
					checked={ajax}
					onChange={ ( value ) => setAttributes( { ajax: value } ) }
				/>
			}
					
			<ChoicesControl
				{...props}
			/>
		
		</FieldControls>
			
		<FieldWrap
			{...props}
		>
			<select>
				{choices.map((choice, index) => (
					<option key={index}
						value={choice.value}
					>
						{choice.label}
					</option>
				))}
			</select>
		</FieldWrap>
	</div>

	)

}

export default Edit;
