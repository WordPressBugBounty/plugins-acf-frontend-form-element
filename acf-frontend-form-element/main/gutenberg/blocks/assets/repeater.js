import { store, getContext, getElement } from '@wordpress/interactivity';

const reindexRows = (rows) => {
	return rows.map((row, index) => ({
		...row,
		id: index,
	}));
};

store('frontend-admin/repeater', {

	actions: {

		addRow: () => {
			const context = getContext();

			if (
				context.maxRows > 0 &&
				context.rows.length >= context.maxRows
			) {
				return;
			}

			context.rows.push({});

			context.rows = reindexRows(context.rows);

			context.activeRow = context.rows.length - 1;
		},
		duplicateRow: () => {
			const context = getContext();

			if (
				context.maxRows > 0 &&
				context.rows.length >= context.maxRows
			) {
				return;
			}

			// Find the index of the row that triggered the action
			const id = context.row.id;

			const index = context.rows.findIndex(row => row.id === id);

			// Clone the row data and assign a new unique id
			const sourceRow = context.rows[index];

			
			const newRow = {
				...sourceRow,
				id: Math.max(...context.rows.map(row => row.id)) + 1,
			};

			// Insert the clone directly after the current row
			context.rows.splice(index + 1, 0, newRow);

			context.rows = reindexRows(context.rows);

			context.activeRow = index + 1;
		},
		
		removeRow: () => {

			const context = getContext();

			const rows = context.rows;

			const id = context.row.id;

			const index = rows.findIndex(row => row.id === id);

			context.rows.splice(index, 1);

			context.rows = reindexRows(context.rows);

			if (context.activeRow >= context.rows.length) {
				context.activeRow = context.rows.length - 1;
			}

		},


		addRowBefore: () => {
			const context = getContext();
			if (
				context.maxRows > 0 &&
				context.rows.length >= context.maxRows
			) {
				return;
			}	
			const id = context.row.id;
			const index = context.rows.findIndex(row => row.id === id);
			context.rows.splice(index, 0, {});
			context.rows = reindexRows(context.rows);
			context.activeRow = index;
		},

		startDrag: () => {
			const context = getContext();
			const id = context.row.id;
			context.dragId = id;
		},

		dragOver: (event) => {
			    event.preventDefault();

		},

		drop: () => {
			const context = getContext();

			const dragId = context.dragId;
			const dropId = context.row.id;

			if (dragId === dropId) return;

			const fromIndex = context.rows.findIndex(row => row.id === dragId);
			const toIndex   = context.rows.findIndex(row => row.id === dropId);

			// Remove dragged row and insert at drop position
			const [moved] = context.rows.splice(fromIndex, 1);
			context.rows.splice(toIndex, 0, moved);
			context.rows = reindexRows(context.rows);

			context.dragId = null;
			context.dragOverId = null;
		},
		dragEnter: (event) => {
			event.preventDefault();
			const context = getContext();
			context.dragOverId = context.row.id;
		},

		dragLeave: (event) => {
			// Only clear if leaving the row entirely, not just moving between children
			const { ref } = getElement();
			if (!ref.contains(event.relatedTarget)) {
				const context = getContext();
				context.dragOverId = null;
			}
		},
	},
	callbacks: {
		getRowId: () => {
			const context = getContext();
			return 'Row Id: ' + context.row.id;
		},
		isDragOver: () => {
			const context = getContext();
			return context.dragOverId === context.row.id;
		},
		isAddDisabled: () => {
			const context = getContext();
			return context.maxRows > 0 && context.rows.length >= context.maxRows;
		},
		notAddDisabled: () => {
			const context = getContext();
			return !(context.maxRows > 0 && context.rows.length >= context.maxRows);
		}
	}

});