import { registerBlockType } from '@wordpress/blocks';
import { __, _e } from '@wordpress/i18n';
import Edit from '../../components/textFieldEdit';
import name from './block.json';

registerBlockType(
	name,
	{
		edit: Edit,
		save: () => { return null }
	}
);
