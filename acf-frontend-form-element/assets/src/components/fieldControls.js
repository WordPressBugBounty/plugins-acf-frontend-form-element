import { __, _e } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import { TextControl, TextareaControl, ToggleControl, PanelBody } from '@wordpress/components';

const plugin = 'acf-frontend-form-element';

const FieldControls = (props) => {
	const { attributes, setAttributes } = props;
	const { label, hide_label, required, instructions } = attributes;

	const slugify = ( str ) => {
		return str.toLowerCase().replace( /[^a-z0-9 ]/g, '' ).replace( /\s+/g, '_' );
	};

	return (
		<InspectorControls
			key                     = 'fea-inspector-controls'
			>
			<PanelBody
					title           = {__( "General", plugin )}
					initialOpen     = {true}
				>
				
				<TextControl
					label       = {__( 'Label', plugin )}
					value       = {label}
					onChange    = {(newval) => setAttributes( { label: newval } )}
				/>
				
				
					<ToggleControl
						label		= {__( 'Hide Label', plugin )}
						checked     = {hide_label}
						onChange={ ( value ) => setAttributes( { hide_label: value } ) } 
					/>
				
				{ "name" in attributes &&
				
					<TextControl
						label       = {__( 'Name', plugin )}
						value       = {attributes.name || slugify( label ) }
						onChange    = {(newval) => setAttributes( { name: newval } )}
					/>
				
				}	
				
					<TextareaControl
						label       = {__( 'Instructions', plugin )}
						rows		= "3"
						value       = {instructions}
						onChange    = {(newval) => setAttributes( { instructions: newval } )}
					/>
				
				
						<ToggleControl
							label		= {__( 'Required', plugin )}
							checked     = {required}
							onChange={ ( value ) => setAttributes( { required: value } ) } 
						/>
				
				{props.children}
			</PanelBody>
		</InspectorControls>
	  );
}



export default FieldControls;
