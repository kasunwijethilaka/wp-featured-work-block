/**
 * Front-end filtering for the Featured Work block.
 *
 * Progressive enhancement: with no JS, every card is visible. With JS, the
 * filter toolbar toggles cards by project type, following the WAI-ARIA toolbar
 * pattern — a single tab stop with arrow-key navigation (roving tabindex) — and
 * announces the result count through an aria-live region.
 */

function initFeaturedWork( root ) {
	const toolbar = root.querySelector( '.fw-filter' );
	const grid = root.querySelector( '.fw-grid' );

	if ( ! toolbar || ! grid ) {
		return;
	}

	const buttons = Array.from( toolbar.querySelectorAll( '.fw-filter__btn' ) );
	const cards = Array.from( grid.querySelectorAll( '.fw-grid__item' ) );
	const status = root.querySelector( '.fw-status' );

	if ( ! buttons.length ) {
		return;
	}

	// Roving tabindex: only the active button stays in the tab order.
	const setTabStops = ( activeIndex ) => {
		buttons.forEach( ( button, index ) => {
			button.tabIndex = index === activeIndex ? 0 : -1;
		} );
	};

	const applyFilter = ( filter ) => {
		let visible = 0;

		cards.forEach( ( card ) => {
			const types = ( card.getAttribute( 'data-project-types' ) || '' )
				.split( ' ' )
				.filter( Boolean );
			const match = '' === filter || types.includes( filter );
			card.hidden = ! match;
			if ( match ) {
				visible += 1;
			}
		} );

		if ( status ) {
			status.textContent =
				1 === visible
					? '1 project shown'
					: `${ visible } projects shown`;
		}
	};

	const activate = ( index ) => {
		buttons.forEach( ( button, buttonIndex ) => {
			const pressed = buttonIndex === index;
			button.setAttribute( 'aria-pressed', pressed ? 'true' : 'false' );
			button.classList.toggle( 'is-active', pressed );
		} );
		setTabStops( index );
		applyFilter( buttons[ index ].getAttribute( 'data-filter' ) || '' );
	};

	// Initialise the roving tab stop on whichever button starts active.
	const initialIndex = Math.max(
		0,
		buttons.findIndex(
			( button ) => 'true' === button.getAttribute( 'aria-pressed' )
		)
	);
	setTabStops( initialIndex );

	buttons.forEach( ( button, index ) => {
		button.addEventListener( 'click', () => activate( index ) );

		button.addEventListener( 'keydown', ( event ) => {
			let nextIndex = null;

			switch ( event.key ) {
				case 'ArrowRight':
				case 'ArrowDown':
					nextIndex = ( index + 1 ) % buttons.length;
					break;
				case 'ArrowLeft':
				case 'ArrowUp':
					nextIndex = ( index - 1 + buttons.length ) % buttons.length;
					break;
				case 'Home':
					nextIndex = 0;
					break;
				case 'End':
					nextIndex = buttons.length - 1;
					break;
				default:
					return;
			}

			event.preventDefault();
			setTabStops( nextIndex );
			buttons[ nextIndex ].focus();
		} );
	} );
}

function onReady( callback ) {
	if ( 'loading' !== document.readyState ) {
		callback();
	} else {
		document.addEventListener( 'DOMContentLoaded', callback );
	}
}

onReady( () => {
	document
		.querySelectorAll( '.wp-block-featured-work' )
		.forEach( initFeaturedWork );
} );
