import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { __, _e } from '@wordpress/i18n';
import { PanelBody, PanelRow } from '@wordpress/components';
import { ServerSideRender } from '@wordpress/editor';
import React, { Fragment } from 'react';
import Select from 'react-select';
import { useEffect, useState } from 'react';

const plugin = 'acf-frontend-form-element';

const Edit = (props) => {
    const { attributes, setAttributes } = props;
    const { fields_select, fields_exclude } = attributes;
    const [list, setList] = useState(null);
    const [loading, setLoading] = useState(true);
        
    // Get the grouped ACF fields data from the store
    useEffect(() => {
      // Make an API call to retrieve data
      runApiFetch();
    }, []);  // The empty array ensures that the effect only runs on mount
    
    const runApiFetch = () => {
      wp.apiFetch({
        path: 'frontend-admin/v1/grouped-acf-fields',
      }).then(data => {
        setList(data);
        setLoading(false);
      });
    };

    const handleAddFields = (selected) => {
      if (selected.length === 0) {
        setAttributes({fields_select: []});
        return;
      }
    
      const values = selected.map(obj => obj.value)
      setAttributes({fields_select: values});
    }     
    const handleAddExcludedFields = (selected) => {
      if (selected.length === 0) {
        setAttributes({fields_exclude: []});
        return;
      }
    
      const values = selected.map(obj => obj.value)
      setAttributes({fields_exclude: values});
    }     

    const handleFieldsSelect = (objects, fieldOptions) => {
      return objects.map(value => {
        const option = fieldOptions.find(option => option.value === value)
        return { value, label: option ? option.label : value }
      })
    }
    
    const fieldOptions = [];
    const fieldExcludes = [];
    
    if( ! loading ){
      for (const groupKey in list) {
          const group = list[groupKey];
          fieldOptions.push({
            value: groupKey,
            label: sprintf( __( 'All fields from %s', 'acf-frontend-form-element' ), group.label )
          });
          for (const fieldKey in group.fields) {
            fieldOptions.push({
              value: fieldKey,
              label: group.fields[fieldKey]
            });
          }
          if (fields_select.includes(groupKey)) {
            for (const fieldKey in group.fields) {
              fieldExcludes.push({
                value: fieldKey,
                label: group.fields[fieldKey] + ' (' + group.label + ')'
              });
            }
          }
      }

    } 

	const blockProps = useBlockProps();

	return (
        <div { ...blockProps }>
        <InspectorControls
            key = 'fea-inspector-controls'
        >
            <PanelBody
                    title           = {__( "ACF Fields", plugin )}
                    initialOpen     = {true}
                >
                { fieldOptions ? (
                <div className={props.className}>
                <Select 
                  placeholder={__("Select fields or field groups",plugin)}
                  value={handleFieldsSelect(fields_select, fieldOptions)}
                  options={fieldOptions}
                  onChange={selected => handleAddFields(selected)}
                  isMulti
                />
                { 
                  fieldExcludes.length > 0 && 
                  <div>
                    <label>{__('Exclude Fields',plugin)}</label>
                    <Select 
                      placeholder={__("Select fields to exclude",plugin)}
                      value={handleFieldsSelect(fields_exclude, fieldOptions)}
                      options={fieldExcludes}
                      onChange={selected => handleAddExcludedFields(selected)}
                      isMulti
                    /> 
                  </div>
                }
                </div>              
                 ) : (
                    <p>{__('Loading ACF fields...', plugin)}</p>
                ) }
            </PanelBody>
        </InspectorControls>
        { fields_select.length > 0 ? (
          <ServerSideRender
              block      = {props.name}
              attributes = {attributes}
          /> ) : ( 
            ( fieldOptions && (
              <Fragment>
                <label>{__("ACF Fields",plugin)}</label>
                <Select 
                  placeholder={__("Select fields or field groups",plugin)}
                  value={fields_select}
                  options={fieldOptions}
                  onChange={selected => handleAddFields(selected)}
                  isMulti
                  isSearchable
                /> 
              </Fragment>  
              )
            )
          )
        }
        </div>
	)

}

export default Edit;