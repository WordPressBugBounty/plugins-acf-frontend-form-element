import { __, _e } from '@wordpress/i18n';
import { SelectControl } from '@wordpress/components';
import { useEffect, useState } from 'react';

const plugin = 'acf-frontend-form-element';

const FormSelectControl = (props) => {
    const [list, setList] = useState(null);
    const [loading, setLoading] = useState(true);
        
    // Get the grouped ACF fields data from the store
    useEffect(() => {
      // Make an API call to retrieve data
      runApiFetch();
    }, []);  // The empty array ensures that the effect only runs on mount
    
    const runApiFetch = () => {
      wp.apiFetch({
        path: 'frontend-admin/v1/frontend-forms',
      }).then(data => {
        let newData = [{ value: 0, label: __('Select a form', plugin), disabled: true }].concat(data)
        setList(newData);
        setLoading(false);
      });
    };

    // Show the loading state if we're still waiting.
    if (loading) {
        return <p>{__('Loading forms...', plugin)}</p>;
    }
    if (list.length < 1) {
        return <p>{__('No forms found...', plugin)}</p>;
    }
    
    return(
        <SelectControl
            options={list}
            label={__('Form', plugin)}
            labelPosition='side'
            value={props.value}
            onChange={props.onChange}
            onClick={props.onClick}
        />
    );
}


 
export default FormSelectControl;