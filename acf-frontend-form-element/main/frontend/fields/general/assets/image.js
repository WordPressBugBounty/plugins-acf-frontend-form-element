import { store } from '@wordpress/interactivity';

store('frontend-admin/image-field', {
	state: {
		get imageUrl() {
			const { value } = this;
			return value?.url || '';
		}
	},

	actions: {
		selectImage() {
			const frame = wp.media({
				title: 'Select Image',
				multiple: false,
				library: { type: 'image' }
			});

			frame.on('select', () => {
				const attachment = frame.state().get('selection').first().toJSON();

				this.value = {
					id: attachment.id,
					url: attachment.url
				};
			});

			frame.open();
		},

		removeImage() {
			this.value = null;
		}
	}
});