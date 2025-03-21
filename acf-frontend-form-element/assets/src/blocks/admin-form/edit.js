import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, PanelRow } from '@wordpress/components';
import { __, _e } from '@wordpress/i18n';
import FormSelect from '../../components/formSelectControl';
import FormPreview from '../../components/formPreview';


const plugin = 'acf-frontend-form-element';

const Edit = (props) => {
	// const { pageCount, setPageCount } = useState(1);
	const { attributes, setAttributes } = props;
	const blockProps                    = useBlockProps();

	return (
		<div { ...blockProps }>
			<InspectorControls
				key                  = 'fea-inspector-controls'
			>
				<PanelBody
						title        = {__( "Form Settings", plugin )}
						initialOpen  = {true}
					>
						<PanelRow>
							<FormSelect
								value    = {attributes.formID}
								onChange = {(newval) => setAttributes( { formID: parseInt( newval ) } )}
							/>
						</PanelRow>
				</PanelBody>
			</InspectorControls>
			<FormSelect
				value            = {attributes.formID}
				onChange         = {(newval) => setAttributes( { formID: parseInt( newval ) } )}
			/>
			<FormPreview
				form             = {attributes.formID}
				block            = {props.name}
				/>
		</div>

	)
}

export default Edit;
