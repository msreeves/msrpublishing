/**
 * Demo lead-gen forms — gate unlock + subscribe thank-you (no backend).
 */
function initGateDownloadForms() {
	document.querySelectorAll('[data-gate-form]').forEach((form) => {
		const panel = form.closest('[data-gate-panel]');
		const unlocked = panel?.querySelector('[data-gate-unlocked]');
		if (!panel || !unlocked) {
			return;
		}

		form.addEventListener('submit', (event) => {
			event.preventDefault();
			if (!form.checkValidity()) {
				form.reportValidity();
				return;
			}
			form.classList.add('d-none');
			unlocked.classList.remove('d-none');
		});
	});
}

function initSubscribeDemoForms() {
	document.querySelectorAll('[data-subscribe-demo-form]').forEach((form) => {
		const thanks = form.parentElement?.querySelector('[data-subscribe-thanks]');
		if (!thanks) {
			return;
		}

		form.addEventListener('submit', (event) => {
			event.preventDefault();
			if (!form.checkValidity()) {
				form.reportValidity();
				return;
			}

			const prefs = form.querySelector('[data-subscribe-preferences]');
			const prefsNote = thanks.querySelector('[data-subscribe-prefs-note]');
			if (prefs && prefsNote) {
				const labels = [...prefs.querySelectorAll('input[type="checkbox"]:checked')]
					.map((input) => input.labels?.[0]?.textContent?.trim())
					.filter(Boolean);
				if (labels.length) {
					prefsNote.textContent = `Preferences saved (demo): ${labels.join(', ')}.`;
					prefsNote.classList.remove('d-none');
				} else {
					prefsNote.textContent = '';
					prefsNote.classList.add('d-none');
				}
			}

			form.classList.add('d-none');
			thanks.classList.remove('d-none');
		});
	});
}

document.addEventListener('DOMContentLoaded', () => {
	initGateDownloadForms();
	initSubscribeDemoForms();
});
