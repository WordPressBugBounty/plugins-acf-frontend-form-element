(function($) {
	$( 'body' ).on(
		'click',
		'.sub-fields-close',
		function() {
			$( this ).removeClass( 'sub-fields-close' ).addClass( 'sub-fields-open' );
			removePopup( type );
		}
	);

	$( 'body' ).on(
		'click',
		'.new-fea-form',
		function(event) {
			$link = $( this ).data( 'link' );

			window.open( $link, '_blank' );
		}
	);
	$( 'body' ).on(
		'click',
		'.edit-fea-form',
		function(event) {
			event.stopPropagation();
			var $form = $( this ).parents( '.elementor-control' ).siblings( '.elementor-control-admin_forms_select' ).find( 'select[data-setting=admin_forms_select]' ).val();
			$link     = $( this ).data( 'link' );

			window.open( $link + '?post=' + $form + '&action=edit', '_blank' );
		}
	);

	$( 'body' ).on(
		'click',
		'.sub-fields-open',
		function(event) {
			event.stopPropagation();
			type      = $( this ).data( 'type' );
			var popup = $( '<div class="sub-fields-container popup_' + type + '"><button class="add-sub-field" type="button"><i class="eicon-plus" aria-hidden="true"></i></button></div>' );

			$parent_section = $( this ).parents( '.elementor-control-fields_selection' );

			$( this ).after( popup );

			$subfields_section = $parent_section.siblings( '.elementor-control-' + type + '_fields' );

			$subfields_section.css( 'display','block' );

			popup.prepend( $subfields_section );

			$( this ).removeClass( 'sub-fields-open' ).addClass( 'sub-fields-close' );

		}
	);

	function removePopup(type){
		var $popup         = $( '.popup_' + type );
		$subfields_section = $popup.find( '.elementor-control-' + type + '_fields' );
		$subfields_section.css( 'display','none' );

		$parent_section.after( $subfields_section );
		$popup.remove();
	}

	$( 'body' ).on(
		'click',
		'.add-sub-field',
		function() {
			var repeaterWrapper = $subfields_section.find( '.elementor-repeater-fields-wrapper' );
			repeaterWrapper.find( '.elementor-repeater-fields:last-child' ).find( '.elementor-repeater-tool-duplicate' ).click();

			var newField = repeaterWrapper.find( '.elementor-repeater-fields:last-child' );
			// newField.find('.elementor-control:gt(1)').addClass('elementor-hidden-control');
			newField.find( 'input[data-setting="field_label_on"]' ).val( 'true' ).change();
			var fieldType = newField.find( 'select[data-setting="field_type"]' );
			fieldType.val( 'description' ).change();
			newField.find( 'input[data-setting="label"]' ).val( fieldType.find( 'option[value="description"]' ).text() ).change().trigger( 'input' );

		}
	);
	
	const select = elementor.modules.controls.Select2.extend({
        onReady: async function() {
			this.controlSelect = this.$el.find( '.custom-control-select' );
			this.savedValue = this.$el.find( '.saved-value' ).val();			

			if( ! feaRestData ){
				return;
			}


			const action = this.controlSelect.data( 'action' );
			if( ! action ){
				return;
			}

			if( feaRestData[action] ){
				const options = this.getOptions( feaRestData[action] );
				this.controlSelect.select2( {
					data: options,
					
				} );

				return;
			} 
			
			//add spinner
			this.controlSelect.select2( {
				data: [ { id: 0, text: 'Loading...' } ],
				placeholder: 'Loading Options...',	

			} );

			const response = await fetch( feaRestData.url + 'frontend-admin/v1/' + action,
				{
					method: 'GET',
					headers: {
						'Content-Type': 'application/json',
						'X-WP-Nonce': feaRestData.nonce,
					}
				}
			 );

			const data = await response.json();
			

			feaRestData[action ] = data;	
			
			const options = this.getOptions( data );


			this.controlSelect.select2( {
				data: options,
			} );

			//remove the loading option
			this.controlSelect.find( 'option[value="0"]' ).remove();

			if( this.savedValue ){
				this.controlSelect.val( this.savedValue );
			}
 
			this.controlSelect.on( 'change', () => {
				this.saveValue();
			} );

		},

		getOptions: function( data ) {
			const values = this.savedValue.split( ',' );
			const children_of = this.controlSelect.data( 'children_of' );
			let parentVal = null;
			/* if( children_of ){
				const element = this.$el;
				parentVal = element.siblings( '.elementor-control-' + children_of ).find( '.saved-value' ).val().split( ',' );
			} */
			const options = data.map( ( item ) => {
				let selected = false;
				if( values.includes( item.id ) ){
					selected = true;
				}

				if( item.children ){
					if( parentVal && item.id && ! parentVal.includes( item.id ) ){
						return;
						
					}

					item.children = item.children.map( ( child ) => {
						

						let selected = false;
						if( values.includes( child.id ) ){
							selected = true;
						}
						return { id: child.id, text: child.text, selected: selected };
					} );
				}else{
					if( item.id && values.includes( item.id ) ){
						selected = true;
					}
					item.selected = selected;
				}
				return item;
			} );
			return options;
		},

		saveValue: function() {
			let val = this.controlSelect.val();
			this.setValue( val );

			/* const changeOthers = this.controlSelect.data( 'change_others' );

			if( changeOthers ){
				const others = changeOthers.split( ',' );
				const element = this.$el;

				others.forEach( ( other ) => {
					const control = element.siblings( '.elementor-control-' + other );
					const select = control.find( '.custom-control-select' );
					
					if( select.length ){
						//make the option only children of the selected parent
						const action = select.data( 'action' );
						const data = feaRestData[action];

						const options = data.map( ( item ) => {
							if( item.children && item.id && ! val.includes( item.id ) ){
								return;								
							}
								
							return item;
						} );

						select.select2( {
							data: options,
						} );

					}
				} );
			} */


		},
	} );

	elementor.addControlView( 'fea_select', select );
})( jQuery );


