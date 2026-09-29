/**
 * Publishing home — sticky subscribe bar, hero intersection, mobile more-bands panel.
 */
document.addEventListener( 'DOMContentLoaded', function () {
	const loadMoreInsights = document.querySelector( '[data-msr-insights-load-more]' );
	if ( loadMoreInsights ) {
		const grid = loadMoreInsights.closest( '.container' )?.querySelector( '.publishing-home-commentary-preview__grid' );
		loadMoreInsights.addEventListener( 'click', function () {
			const hidden = grid ? grid.querySelectorAll( '.publishing-commentary-grid__cell--more' ) : [];
			hidden.forEach( function ( cell ) {
				cell.classList.remove( 'd-none' );
				cell.querySelectorAll( '.msr-reveal' ).forEach( function ( el ) {
					el.classList.add( 'is-visible' );
				} );
			} );
			loadMoreInsights.setAttribute( 'aria-expanded', 'true' );
			const wrap = loadMoreInsights.closest( '.publishing-home-commentary-preview__load-more-wrap' );
			if ( wrap ) {
				wrap.hidden = true;
			}
		} );
	}

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

	const stickyCta = sticky.querySelector( '.publishing-subscribe-sticky__cta' );

	function setStickyVisible( visible ) {
		if ( desktop.matches ) {
			visible = false;
		}
		sticky.classList.toggle( 'is-visible', visible );
		sticky.setAttribute( 'aria-hidden', visible ? 'false' : 'true' );
		if ( visible ) {
			sticky.removeAttribute( 'inert' );
			sticky.removeAttribute( 'hidden' );
			if ( stickyCta ) {
				stickyCta.removeAttribute( 'tabindex' );
			}
		} else {
			sticky.setAttribute( 'inert', '' );
			sticky.setAttribute( 'hidden', '' );
			if ( stickyCta ) {
				stickyCta.setAttribute( 'tabindex', '-1' );
			}
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

	setStickyVisible( false );
} );
