import { __, _e } from '@wordpress/i18n';
import { RichText } from '@wordpress/block-editor';


const plugin = 'acf-frontend-form-element';

const FieldWrap = (props) => {
	const { attributes, setAttributes } = props;
	const { label, hide_label, required, instructions } = attributes;
	
	return (
		<div className="acf-field">
		  <div className="acf-label">
			<label>
				{ ! hide_label &&
					<RichText
						tagName             = "label"
						onChange            = {(newval) => setAttributes( { label: newval } )}
						withoutInteractiveFormatting
						placeholder 		= {__( 'Text Field', plugin )}
						value               = {label}
					/>				
				}
				{ required && (
					<span className="acf-required">*</span>		
					)
				}
			</label>
		  </div>
		  <div className="acf-input">
			{ instructions &&
		  		<RichText
					tagName             = "p"
					className			="description"
					onChange            = {(newval) => setAttributes( { instructions: newval } )}
					withoutInteractiveFormatting
					value               = {instructions}
				/>		
			}
			<div 
				className="acf-input-wrap" 
				style={{ display: "flex", width: "100%" }}
			>
				{props.children}
			</div>
		  </div>
		</div>
	  );
}



export default FieldWrap;
