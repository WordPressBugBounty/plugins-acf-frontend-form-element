import { store, getContext } from '@wordpress/interactivity';

store('frontend-admin/field', {
	callbacks: {

		getFieldName: () => {

			const context = getContext();

            const repeaterContext = getContext('frontend-admin/repeater');

			let name = context.fieldName || '';

			if (repeaterContext !== undefined) {
                const rows = repeaterContext.rows;

			    const id = repeaterContext.row.id;

			    const index = rows.findIndex(row => row.id === id);
				name = `${repeaterContext.repeaterName}[${index}][${name}]`;
			}

			return name;

		},

		getFieldValue: () => {

			const context = getContext();
			const repeaterContext = getContext('frontend-admin/repeater');

			let value = context.fieldValue || '';

			
			if (repeaterContext !== undefined) {
				const rows = repeaterContext.rows;
				const id = repeaterContext.row.id;
				const index = rows.findIndex(row => row.id === id);
				value = rows[index][context.fieldName] || '';
			}

			return value;
		},

		handleChange: (event) => {

			const context = getContext();

			const repeaterContext = getContext('frontend-admin/repeater');

			const value = event.target.value;

			if (repeaterContext !== undefined) {
				const rows = repeaterContext.rows;
				const id = repeaterContext.row.id;
				const index = rows.findIndex(row => row.id === id);
				repeaterContext.rows[index][context.fieldName] = value;
			}
			context.fieldValue = value;
		}

	}
});