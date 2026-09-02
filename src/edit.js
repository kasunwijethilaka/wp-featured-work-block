/**
 * Editor component for the Featured Work block.
 *
 * Renders the InspectorControls (layout, query, and display panels) and a live
 * ServerSideRender preview so the editor shows the real PHP-rendered grid,
 * updating as the controls change.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	SelectControl,
	ToggleControl,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import ServerSideRender from '@wordpress/server-side-render';

import './editor.scss';

export default function Edit( { attributes, setAttributes } ) {
	const {
		postsToShow,
		columns,
		projectType,
		order,
		orderBy,
		showExcerpt,
		showFilter,
	} = attributes;

	const blockProps = useBlockProps();

	// Pull the available project_type terms straight from core data,
	// so the dropdown always reflects the taxonomy in the database.
	const terms = useSelect(
		( select ) =>
			select( coreStore ).getEntityRecords( 'taxonomy', 'project_type', {
				per_page: -1,
			} ),
		[]
	);

	const termOptions = [
		{ label: __( 'All project types', 'featured-work' ), value: '' },
		...( terms || [] ).map( ( term ) => ( {
			label: term.name,
			value: term.slug,
		} ) ),
	];

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Layout', 'featured-work' ) }>
					<RangeControl
						label={ __( 'Columns', 'featured-work' ) }
						value={ columns }
						onChange={ ( value ) => setAttributes( { columns: value } ) }
						min={ 2 }
						max={ 4 }
					/>
					<RangeControl
						label={ __( 'Number of case studies', 'featured-work' ) }
						value={ postsToShow }
						onChange={ ( value ) => setAttributes( { postsToShow: value } ) }
						min={ 1 }
						max={ 12 }
					/>
				</PanelBody>

				<PanelBody title={ __( 'Query', 'featured-work' ) } initialOpen={ false }>
					<SelectControl
						label={ __( 'Project type', 'featured-work' ) }
						value={ projectType }
						options={ termOptions }
						onChange={ ( value ) => setAttributes( { projectType: value } ) }
					/>
					<SelectControl
						label={ __( 'Order by', 'featured-work' ) }
						value={ orderBy }
						options={ [
							{ label: __( 'Date', 'featured-work' ), value: 'date' },
							{ label: __( 'Title', 'featured-work' ), value: 'title' },
							{ label: __( 'Menu order', 'featured-work' ), value: 'menu_order' },
						] }
						onChange={ ( value ) => setAttributes( { orderBy: value } ) }
					/>
					<SelectControl
						label={ __( 'Order', 'featured-work' ) }
						value={ order }
						options={ [
							{ label: __( 'Descending', 'featured-work' ), value: 'desc' },
							{ label: __( 'Ascending', 'featured-work' ), value: 'asc' },
						] }
						onChange={ ( value ) => setAttributes( { order: value } ) }
					/>
				</PanelBody>

				<PanelBody title={ __( 'Display', 'featured-work' ) } initialOpen={ false }>
					<ToggleControl
						label={ __( 'Show excerpt', 'featured-work' ) }
						checked={ showExcerpt }
						onChange={ ( value ) => setAttributes( { showExcerpt: value } ) }
					/>
					<ToggleControl
						label={ __( 'Show filter bar', 'featured-work' ) }
						checked={ showFilter }
						onChange={ ( value ) => setAttributes( { showFilter: value } ) }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<ServerSideRender
					block="featured/work"
					attributes={ attributes }
				/>
			</div>
		</>
	);
}
