<?php
namespace Frontend_Admin\Gutenberg;

if (! defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

if(! class_exists('Frontend_Admin\Gutenberg\Repeater') ) :

    class Repeater
    {    

        public function render($attributes, $content, $block)
        {
            $label       = $attributes['label'] ?? 'Repeater';
            $active_row  = intval( $attributes['active_row'] ?? 0 );
            $layout      = $attributes['repeater_layout'] ?? 'table';
            $add_text    = $attributes['add_button_text'] ?? 'Add Row';
            $min_rows    = intval( $attributes['min_rows'] ?? 0 );
            $max_rows    = intval( $attributes['max_rows'] ?? 0 );

            // Initial rows based on min_rows
            $rows = [];

            for ( $i = 0; $i < max( 1, $min_rows ); $i++ ) {
				$key = uniqid( 'row-', true );
                $rows[] = [ 'id' => $i, 'key' => $key ];
            }

            ob_start();
            ?>

<style>
.fe-repeater-row {
    position: relative;
	padding-right: 40px; /* Space for action buttons */
	border: 1px solid #ddd;
	border-radius: 4px;
	padding: 12px;
	margin-bottom: 8px;
	background: #fff;
    transition: transform 0.15s ease;
    cursor: grab;
}
.fe-repeater-row.fe-repeater-drag-over {
    border-top: 2px solid #007cba;
    margin-top: -2px; /* compensate so layout doesn't shift */
}
.fe-repeater-row[draggable="true"]:active {
    cursor: grabbing;
    opacity: 0.4;
}
.fe-repeater-row-actions {
    position: absolute;
    top: 14px;
    right: 2px;
    transform: translateY(-50%);
    display: flex;
    flex-direction: row;
    gap: 4px;
    opacity: 0;
    transition: opacity 0.15s ease;
    pointer-events: none;
}
.fe-repeater-row:hover .fe-repeater-row-actions {
    opacity: 1;
    pointer-events: auto;
}
span.fe-repeater-row-id{
    font-size: 16px;
    color: #888;
    position: absolute;
    display:none;
    bottom: 14px;
    right: 2px;
}
.fe-repeater-row:hover span.fe-repeater-row-id{
    display: block;
}
.fe-repeater-row-actions button{
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    width: 28px;
    height: 28px;
    padding: 0;
    cursor: pointer;
    align-items: center;
    justify-content: center;
    color: #555;
    transition: background 0.1s, color 0.1s, border-color 0.1s;
    line-height: 1;
}
.fe-repeater-row-actions button:hover {
    background: #f0f0f0;
    border-color: #aaa;
    color: #222;
}
.fe-repeater-remove:hover {
    background: #fff0f0 !important;
    border-color: #e07070 !important;
    color: #c00 !important;
}
.fe-repeater-duplicate:hover {
    background: #f0f7ff !important;
    border-color: #70a0e0 !important;
    color: #0055cc !important;
}
.fe-repeater-add-before:hover {
    background: #f0fff0 !important;
    border-color: #70e070 !important;
    color: #007700 !important;
}

.fe-repeater-add {
    margin-top: 12px;
    padding: 8px 16px;
    background: #007cba;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.1s ease;
}
.fe-repeater-add:hover {
    background: #005a8c;
}
</style>

<div
    class="fe-repeater fe-repeater-layout-<?php echo esc_attr( $layout ); ?>"
    data-wp-interactive="frontend-admin/repeater"
    data-wp-context='<?php echo wp_json_encode( [
        'rows'         => $rows,
        'repeaterName' => uniqid( 'repeater-', true ),
        'activeRow'    => $active_row,
        'minRows'      => $min_rows,
        'maxRows'      => $max_rows,
		'dragId'       => null,
    ] ); ?>'
>

    <?php if ( $label ) : ?>
        <label class="fe-field-label">
            <?php echo esc_html( $label ); ?>
            <!-- Rows hidden input to record number of rows -->
            <input
                type="hidden"
                name="<?php echo esc_attr( $repeater_name ); ?>"
                data-wp-bind--value="context.rows.length"

            />
        </label>
    <?php endif; ?>

    

    <div class="fe-repeater-rows">

        <template data-wp-each--row="context.rows" data-wp-each-key="context.row.id">
            <div 
				class="fe-repeater-row" 
				draggable="true" 
				data-wp-on--dragstart="actions.startDrag" 
				data-wp-on--dragover="actions.dragOver" 
				data-wp-on--drop="actions.drop"
				data-wp-on--dragenter="actions.dragEnter"
				data-wp-on--dragleave="actions.dragLeave"
				data-wp-class--fe-repeater-drag-over="callbacks.isDragOver"
			>

                <div class="fe-repeater-row-fields">
                    <?php echo $content; ?>
                </div>

				
				
                    <span class="fe-repeater-row-id" data-wp-text="callbacks.getRowId"></span> 

                <div class="fe-repeater-row-actions">
                    <!-- Duplicate row -->
                    <button
                        type="button"
                        class="fe-repeater-duplicate"
                        data-wp-on--click="actions.duplicateRow"
                        data-wp-bind--hidden="callbacks.isAddDisabled"
                        title="<?php esc_attr_e( 'Duplicate row', 'frontend-admin' ); ?>"
                        aria-label="<?php esc_attr_e( 'Duplicate row', 'frontend-admin' ); ?>"
                    >
                        <!-- Copy icon (two overlapping squares) -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                        </svg>
                    </button>

                    <!-- Add before -->
                    <button
                        type="button"
                        class="fe-repeater-add-before"
                        data-wp-on--click="actions.addRowBefore"
                        data-wp-bind--hidden="callbacks.isAddDisabled"
                        title="<?php esc_attr_e( 'Add row before', 'frontend-admin' ); ?>"
                        aria-label="<?php esc_attr_e( 'Add row before', 'frontend-admin' ); ?>"
                    >
                        <!-- Plus icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                    </button>
                    
                    <!-- Remove row -->
                    <button
                        type="button"
                        class="fe-repeater-remove"
                        data-wp-on--click="actions.removeRow"
                        data-wp-bind--hidden="callbacks.isRemoveHidden"
                        title="<?php esc_attr_e( 'Remove row', 'frontend-admin' ); ?>"
                        aria-label="<?php esc_attr_e( 'Remove row', 'frontend-admin' ); ?>"
                    >
                        <!-- × icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>

                </div>

            </div>
        </template>

    </div>

    <button
        type="button"
        class="fe-repeater-add"
        data-wp-on--click="actions.addRow"
        data-wp-bind--hidden="callbacks.isAddDisabled"
    >
        <?php echo esc_html( $add_text ); ?>
    </button>

    <?php if ( $max_rows > 0 ) : ?>
    <!--counter for max rows-->
    <div style="margin-top: 8px; font-size: 13px; color: #555;">
        <?php esc_html_e( 'Rows:', 'frontend-admin' ); ?> <span data-wp-text="context.rows.length"></span> / <?php echo esc_html( $max_rows ); ?>
    </div>

    <div class="fe-repeater-max-rows-notice" data-wp-bind--hidden="callbacks.notAddDisabled" style="margin-top: 12px; color: #c00; font-size: 13px;">
        <?php esc_html_e( 'Maximum number of rows reached', 'frontend-admin' ); ?>
    </div>
    <?php endif; ?>

</div>

            <?php
            wp_enqueue_script_module(
                'fea-repeater',
                plugins_url( 'assets/repeater.js', __FILE__ ),
                [],
                '1.0.0',
            );
            return ob_get_clean();
        }
    }

endif;