import NestedForm from './nested-form';

export default class Module {

	constructor() {
		elementor.elementsManager.registerElementType( new NestedForm( 'nested-frontend-form' ) );
		elementor.elementsManager.registerElementType( new NestedForm( 'nested-edit-post-form' ) );
		elementor.elementsManager.registerElementType( new NestedForm( 'nested-new-term-form' ) );
		elementor.elementsManager.registerElementType( new NestedForm( 'nested-edit-term-form' ) );
		elementor.elementsManager.registerElementType( new NestedForm( 'nested-new-post-form' ) );
		elementor.elementsManager.registerElementType( new NestedForm( 'nested-new-product-form' ) );
		
	}
}
