/**
 * Publishing singles — copy link feedback.
 */
function initPublishingSingleShare() {
	document.querySelectorAll( '.publishing-single-share__copy' ).forEach( ( button ) => {
		if ( button.dataset.copyBound === '1' ) {
			return;
		}
		button.dataset.copyBound = '1';

		const url = button.getAttribute( 'data-copy-url' ) || '';
		const status = button
			.closest( '.publishing-single-share' )
			?.querySelector( '.publishing-single-share__status' );

		button.addEventListener( 'click', async () => {
			if ( ! url ) {
				return;
			}

			let copied = false;
			try {
				if ( navigator.clipboard?.writeText ) {
					await navigator.clipboard.writeText( url );
					copied = true;
				}
			} catch {
				copied = false;
			}

			if ( ! copied ) {
				const input = document.createElement( 'input' );
				input.value = url;
				document.body.appendChild( input );
				input.select();
				copied = document.execCommand( 'copy' );
				input.remove();
			}

			if ( status ) {
				status.textContent = copied ? 'Link copied' : 'Copy failed';
				status.hidden = false;
				window.setTimeout( () => {
					status.hidden = true;
					status.textContent = '';
				}, 2400 );
			}
		} );
	} );
}

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', initPublishingSingleShare );
} else {
	initPublishingSingleShare();
}
