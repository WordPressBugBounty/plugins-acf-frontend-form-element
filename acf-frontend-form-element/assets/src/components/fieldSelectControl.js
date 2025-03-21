import { __, _e } from '@wordpress/i18n';
import { SelectControl } from '@wordpress/components';
import { useEffect, useRef, useState } from '@wordpress/element';


const plugin = 'acf-frontend-form-element';

const FieldSelectControl              = (props) => {
	const [fieldsData, setFieldsData] = useState( false );
	const mounted                     = useRef( true );

	const fetchFieldsData = async( setFieldsData = () => void null ) => {
		await wp.apiFetch(
			{
				path: 'fea/v1/admin-fields'
			}
		).then(
			(data) => {
            setFieldsData( data );
			}
		);
	}

	useEffect(
		() => {
			const updateState = ( fieldsData ) => {
				if ( ! mounted.current ) {
					setFieldsData( fieldsData );
					}
			}
				fetchFieldsData( updateState );
		},
		[]
	);

if ( ! fieldsData) {
	return <p> {__( 'Loading fields...', plugin )} </p> ;
}

	return(
		<SelectControl
			label                     = {__( 'Field', plugin )}
			value                     = {props.value}
			onChange                  = ''
		>
			<option
				key                   = '0'
				value                 = ''
				disabled              = {true}
			> {__( 'Select a field', plugin )} </option>
		{
			fieldsData.map(
				(form) => {
					return ( <optgroup
					key               = {form.id}
					label             = {form.title}
					>
					{
						Object.keys( form.fields ).map(
							(key) => {
								return( <option
								key   = {key}
								value = {key}
								> {form.fields[key].label} </option> )
							}
						)
					}
					</optgroup> )
				}
			)
		}

		</SelectControl>

	);
}



export default FieldSelectControl;
