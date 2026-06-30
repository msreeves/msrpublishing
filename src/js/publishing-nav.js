/**
 * Publishing navigation — fullscreen overlay (< lg), desktop megamenus (lg+), mobile search panel.
 */
document.addEventListener( 'DOMContentLoaded', function () {
	const panel = document.getElementById( 'publishingFullscreenNav' );
	const toggle = document.querySelector( '.publishing-menu-toggle' );
	const searchToggle = document.querySelector( '.site-header__search-toggle' );
	const searchPanel = document.getElementById( 'msr-header-search-panel' );
	const reduced = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	let lastFocus = null;

	function setFullscreenOpen( open ) {
		if ( ! panel || ! toggle ) {
			return;
		}

		toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		panel.setAttribute( 'aria-hidden', open ? 'false' : 'true' );
		panel.classList.toggle( 'is-open', open );
		document.body.classList.toggle( 'publishing-nav-open', open );

		if ( open ) {
			setSearchOpen( false );
			lastFocus = document.activeElement;
			panel.removeAttribute( 'inert' );
			const first = panel.querySelector( 'a, button' );
			if ( first ) {
				first.focus();
			}
		} else {
			panel.setAttribute( 'inert', '' );
			if ( lastFocus && typeof lastFocus.focus === 'function' ) {
				lastFocus.focus();
			}
		}
	}

	if ( toggle && panel ) {
		toggle.addEventListener( 'click', function () {
			const open = toggle.getAttribute( 'aria-expanded' ) !== 'true';
			setFullscreenOpen( open );
		} );

		panel.querySelectorAll( 'a[href]' ).forEach( function ( link ) {
			link.addEventListener( 'click', function () {
				setFullscreenOpen( false );
			} );
		} );

		if ( ! reduced ) {
			let staggerIndex = 0;
			panel.querySelectorAll(
				'.publishing-fullscreen-nav__link, .publishing-fullscreen-nav__sublink, .publishing-fullscreen-nav__view-all'
			).forEach( function ( link ) {
				link.style.transitionDelay = staggerIndex * 45 + 'ms';
				staggerIndex += 1;
			} );
		}
	}

	function setSearchOpen( open ) {
		if ( ! searchToggle || ! searchPanel ) {
			return;
		}

		searchToggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		searchPanel.hidden = ! open;
		searchPanel.classList.toggle( 'is-open', open );
		document.body.classList.toggle( 'publishing-search-open', open );
		searchToggle.setAttribute(
			'aria-label',
			open ? closeSearchLabel : defaultSearchLabel
		);

		if ( open ) {
			setFullscreenOpen( false );
			const input = searchPanel.querySelector( 'input[type="search"]' );
			if ( input ) {
				input.focus();
			}
		}
	}

	const defaultSearchLabel = searchToggle ? searchToggle.getAttribute( 'aria-label' ) || '' : '';
	const closeSearchLabel = searchToggle
		? searchToggle.getAttribute( 'data-search-close-label' ) || defaultSearchLabel
		: '';

	if ( searchToggle && searchPanel ) {
		searchToggle.addEventListener( 'click', function () {
			const open = searchToggle.getAttribute( 'aria-expanded' ) !== 'true';
			setSearchOpen( open );
		} );
	}

	document.addEventListener( 'click', function ( event ) {
		if ( searchPanel && searchToggle && searchToggle.getAttribute( 'aria-expanded' ) === 'true' ) {
			if ( ! event.target.closest( '.site-header__search-panel, .site-header__search-toggle' ) ) {
				setSearchOpen( false );
			}
		}
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( event.key !== 'Escape' ) {
			return;
		}
		if ( toggle && toggle.getAttribute( 'aria-expanded' ) === 'true' ) {
			setFullscreenOpen( false );
		}
		if ( searchToggle && searchToggle.getAttribute( 'aria-expanded' ) === 'true' ) {
			setSearchOpen( false );
		}
		closeDesktopMegamenus();
	} );

	function closeDesktopMegamenus( exceptItem ) {
		document.querySelectorAll( '[data-publishing-megamenu]' ).forEach( function ( item ) {
			if ( exceptItem && item === exceptItem ) {
				return;
			}
			const trigger = item.querySelector( '.publishing-desktop-nav__trigger' );
			const panel = item.querySelector( '.publishing-megamenu' );
			if ( trigger ) {
				trigger.setAttribute( 'aria-expanded', 'false' );
			}
			if ( panel ) {
				panel.hidden = true;
			}
		} );
	}

	document.querySelectorAll( '[data-publishing-megamenu]' ).forEach( function ( item ) {
		const trigger = item.querySelector( '.publishing-desktop-nav__trigger' );
		const panel = item.querySelector( '.publishing-megamenu' );
		if ( ! trigger || ! panel ) {
			return;
		}

		trigger.addEventListener( 'click', function ( event ) {
			event.preventDefault();
			event.stopPropagation();
			const open = trigger.getAttribute( 'aria-expanded' ) !== 'true';
			closeDesktopMegamenus( open ? item : null );
			trigger.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			panel.hidden = ! open;
		} );
	} );

	document.addEventListener( 'click', function ( event ) {
		if ( ! event.target.closest( '[data-publishing-megamenu]' ) ) {
			closeDesktopMegamenus();
		}
	} );
} );
