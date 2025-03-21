import { __, _e } from '@wordpress/i18n';
import { Fragment } from '@wordpress/element';
import { TextControl, Button } from '@wordpress/components';

const plugin = 'acf-frontend-form-element';

const ChoicesControl = (props) => {
	const { attributes, setAttributes } = props;
	const { choices } = attributes;

	const handleAddChoiceClick = () => {
		const newChoices = [...choices];
		const newChoice = {
			label: `Option ${newChoices.length + 1}`,
			value: `option_${newChoices.length + 1}`,
		};
		newChoices.push(newChoice);
		setAttributes({ choices: newChoices	});
	};

	const handleChoiceLabelChange = (index, label) => {
		const newChoices = [...choices];
		if (index >= 0 && index < newChoices.length) {
			newChoices[index] = { ...newChoices[index], label };
			setAttributes({ choices: newChoices });
		}
	};

	const handleChoiceValueChange = (index, key) => {
		const newChoices = [...choices];
		if (index >= 0 && index < newChoices.length) {
			newChoices[index] = { ...newChoices[index], value: key };
			setAttributes({ choices: newChoices });
		}
	};

	const handleRemoveChoiceClick = (index) => {
		const newChoices = [...choices];
		if (index >= 0 && index < newChoices.length) {
			newChoices.splice(index, 1);
			setAttributes({ choices: newChoices });
		}		
	};
	
	return (
		<Fragment>
			{choices.map((choice, index) => (
				<div style={ { display: 'flex', justifyContent: 'space-between' } } key={index}>
					<TextControl
						label={__('Choice Label', plugin)}
						value={choice.label}
						onChange={(label) => handleChoiceLabelChange(index, label)}
					/>
					<TextControl
						label={__('Choice Value', plugin)}
						value={choice.value || `option_${index+1}`}
						onChange={(value) => handleChoiceValueChange(index, value)}
					/>
					{ choices.length > 1 &&
					<Button
						onClick={() => handleRemoveChoiceClick(index)}
					>
						{"X"}
					</Button>
					}
				</div>
			))}
			<Button onClick={handleAddChoiceClick}>
				{__('Add Choice', plugin)}
			</Button>
		</Fragment>
	  );
}



export default ChoicesControl;
