import { useBlockProps } from '@wordpress/block-editor';
import { ToggleControl, RadioControl } from '@wordpress/components';
import { __, _e } from '@wordpress/i18n';
import FieldWrap from '../../components/fieldWrap';
import FieldControls from '../../components/fieldControls';
import ChoicesControl from '../../components/choicesControl';


const plugin = 'acf-frontend-form-element';

const Edit = (props) => {
	const { attributes, setAttributes } = props;
	const { default_value, layout, choices, allow_custom, save_custom, toggle } = attributes;

	const blockProps = useBlockProps();

	return (
	<div { ...blockProps }>
		<FieldControls
			{...props}
		>		
			<RadioControl
				label={__("Layout",plugin)}
				selected={layout}
				options={[
					{ label: "Horizontal", value: "horizontal" },
					{ label: "Vertical", value: "vertical" },
				]}
				onChange={ ( value ) => setAttributes( { layout: value } ) }
			/>
			<ToggleControl
				label={__('Allow Custom Choice', plugin)}
				checked={allow_custom}
				onChange={ ( value ) => setAttributes( { allow_custom: value } ) }
			/>
			{ allow_custom &&
				<ToggleControl
					label={__('Save Custom Choice', plugin)}
					checked={save_custom}
					onChange={ ( value ) => setAttributes( { save_custom: value } ) }
				/>
			}
			<ToggleControl
				label={__('Toggle All', plugin)}
				checked={toggle}
				onChange={ ( value ) => setAttributes( { toggle: value } ) }
			/>
				
			<ChoicesControl
				{...props}
			/>
		
		</FieldControls>
			
		<FieldWrap
			{...props}
		>
			<ul className={`acf-checkbox-list ${layout === "horizontal" ? "acf-hl" : "acf-bl"}`}>
				{choices.map((choice, index) => (
					<li key={index}>
						<input
							type="checkbox"
							value={choice.value}
							checked={default_value.includes(choice.value)}
							onChange={(event) => handleCheckboxChange(event, choice.value)}
						/>
						{choice.label}
					</li>
				))}
			</ul>
		</FieldWrap>
	</div>

	)

}

export default Edit;
