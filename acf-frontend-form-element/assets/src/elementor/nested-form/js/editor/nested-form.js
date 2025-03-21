import View from './view';
export class NestedForm extends elementor.modules.elements.types.NestedElementBase {

	constructor( $type ) {
		super();

		this.type = $type;	
	}
	getType() {
		return this.type;
	}

	getView() {
		return View;
	}
}

export default NestedForm;
