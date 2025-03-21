import { BlockEdit, InspectorControls, PanelBody, FormToggle, SelectControl, CheckboxControl, RadioControl } from '@wordpress/block-editor';
import { __, _e } from '@wordpress/i18n';

const FormStepEdit = (props) => {
  const {
    attributes: {
      steps,
      validate_steps,
      steps_display,
      steps_tabs_display,
      steps_counter_display,
      tabs_align,
    },
    setAttributes,
  } = props;

  return (
    <>
      <BlockEdit {...props} />
      <InspectorControls>
        <PanelBody title={__('Steps', 'acf-frontend-form-element')}>
          {steps.map((choice, index) => (
          <div style={ { display: 'flex', justifyContent: 'space-between' } } key={index}>
            <TextControl
              label={__('Step Name', plugin)}
              value={choice.name}
              onChange={(label) => handleChoiceLabelChange(index, label)}
            />
            { index < steps.length &&
            <TextControl
              label={__('Next', plugin)}
              value={choice.next}
              onChange={(label) => handleChoiceLabelChange(index, label)}
            />
            }
            { index > 0 &&
              <TextControl
                label={__('Previous', plugin)}
                value={choice.prev}
                onChange={(label) => handleChoiceLabelChange(index, label)}
              />
            }
            { steps.length > 1 &&
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
        </PanelBody>

        <PanelBody title={__('Step Settings', 'acf-frontend-form-element')}>
          <FormToggle
            label={__('Validate Each Step', 'acf-frontend-form-element')}
            checked={validate_steps}
            onChange={() => setAttributes({ validate_steps: !validate_steps })}
          />
          <SelectControl
            label={__('Steps Display', 'acf-frontend-form-element')}
            value={steps_display}
            options={[
              { value: 'tabs', label: __('Tabs', 'acf-frontend-form-element') },
              { value: 'counter', label: __('Counter', 'acf-frontend-form-element') },
            ]}
            onChange={(value) => setAttributes({ steps_display: value })}
          />
          <CheckboxControl
            label={__('Display Tabs On...', 'acf-frontend-form-element')}
            checked={steps_tabs_display}
            onChange={(value) => setAttributes({ steps_tabs_display: value })}
          />
          <CheckboxControl
            label={__('Display Counter On...', 'acf-frontend-form-element')}
            checked={steps_counter_display}
            onChange={(value) => setAttributes({ steps_counter_display: value })}
          />
          <RadioControl
            label={__('Tabs Align', 'acf-frontend-form-element')}
            selected={tabs_align}
            options={[              { value: 'left', label: __('Left', 'acf-frontend-form-element') },              { value: 'center', label: __('Center', 'acf-frontend-form-element') },              { value: 'right', label: __('Right', 'acf-frontend-form-element') },            ]}
            onChange={(value) => setAttributes({ tabs_align: value })}
          />
          <TextControl
            label={__('Next Button Label', 'acf-frontend-form-element')}
            value={nextButtonLabel}
            onChange={(value) => setAttributes({ nextButtonLabel: value })}
          />
          <RangeControl
            label={__('Width', 'acf-frontend-form-element')}
            value={width}
            onChange={(value) => setAttributes({ width: value })}
            min={50}
            max={100}
            step={1}
          />
          <ColorPicker
            label={__('Color', 'acf-frontend-form-element')}
            color={color}
            onChangeComplete={(value) => setAttributes({ color: value.hex })}
          />
        </PanelBody>
      </InspectorControls>
    </>
  );
};

export default FormStepEdit;
