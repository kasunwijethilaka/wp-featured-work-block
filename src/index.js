/**
 * Block registration entry point.
 *
 * Registers `fueled/featured-work` using its block.json metadata, wiring the
 * editor component (edit) and the save component (which returns null because
 * this is a dynamic, server-rendered block).
 */
import { registerBlockType } from '@wordpress/blocks';

import Edit from './edit';
import save from './save';
import metadata from './block.json';

import './style.scss';

registerBlockType( metadata.name, {
	edit: Edit,
	save,
} );
