import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, ToggleControl, PanelRow } from '@wordpress/components';
import { __, _e } from '@wordpress/i18n';
import FieldWrap from '../../components/fieldWrap';
import FieldControls from '../../components/fieldControls';

const plugin = 'acf-frontend-form-element';

const Edit = (props) => {
	const { attributes, setAttributes } = props;
	const { default_value } = attributes;

	const blockProps = useBlockProps();


	return (
	<div { ...blockProps }>
		<FieldControls
			{...props}
		>
			<TextControl
				type        = 'color'
				label       = {__( 'Default Value', plugin )}
				value       = {default_value}
				onChange    = {(newval) => setAttributes( { default_value: newval } )}
			/>

		</FieldControls>
			
		<FieldWrap
			{...props}
		>	
		<input
			type        = 'color'
			value   	= {default_value}
			onChange	= {(e) => {
				setAttributes({ default_value: e.target.value });
			}}
		/>
		</FieldWrap>
	</div>

	)

}

export default Edit;
