/**
 * Publishing home — sticky subscribe bar, hero intersection, mobile more-bands panel.
 */
document.addEventListener( 'DOMContentLoaded', function () {
	const sticky = document.querySelector( '.publishing-subscribe-sticky' );
	const hero = document.querySelector( '.publishing-home-hero' );
	const subscribeBand = document.getElementById( 'publishing-home-subscribe' );
	const moreBands = document.getElementById( 'publishing-home-more' );

	if ( moreBands ) {
		const tabletUp = window.matchMedia( '(min-width: 768px)' );

		function syncMoreBands() {
			if ( tabletUp.matches ) {
				moreBands.setAttribute( 'open', '' );
				moreBands.classList.add( 'is-static' );
			} else {
				moreBands.classList.remove( 'is-static' );
			}
		}

		tabletUp.addEventListener( 'change', syncMoreBands );
		syncMoreBands();
	}

	if ( ! sticky || ! hero ) {
		return;
	}

	const reduced = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	const desktop = window.matchMedia( '(min-width: 992px)' );

	function setStickyVisible( visible ) {
		if ( desktop.matches ) {
			visible = false;
		}
		sticky.classList.toggle( 'is-visible', visible );
		sticky.setAttribute( 'aria-hidden', visible ? 'false' : 'true' );
		if ( visible ) {
			sticky.removeAttribute( 'inert' );
		} else {
			sticky.setAttribute( 'inert', '' );
		}
	}

	const heroObserver = new IntersectionObserver(
		function ( entries ) {
			const heroVisible = entries.some( function ( entry ) {
				return entry.isIntersecting;
			} );
			setStickyVisible( ! heroVisible );
		},
		{ threshold: 0, rootMargin: '-1px 0px 0px 0px' }
	);
	heroObserver.observe( hero );

	if ( subscribeBand ) {
		const bandObserver = new IntersectionObserver(
			function ( entries ) {
				if ( entries.some( function ( entry ) {
					return entry.isIntersecting;
				} ) ) {
					setStickyVisible( false );
				}
			},
			{ threshold: 0.2 }
		);
		bandObserver.observe( subscribeBand );
	}

	desktop.addEventListener( 'change', function () {
		if ( desktop.matches ) {
			setStickyVisible( false );
		}
	} );

	if ( reduced ) {
		sticky.style.transition = 'none';
	}
} );
