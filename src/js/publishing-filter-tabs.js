/**
 * Publishing filter tabs — active sync, pane fade, scroll active tab into view.
 */
document.addEventListener('DOMContentLoaded', function () {
	var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var compactTabs = window.matchMedia('(max-width: 991px)');

	document.querySelectorAll('[data-msr-filter-tabs], [data-commentary-filter]').forEach(function (root) {
		var tabControls = root.querySelectorAll('[data-bs-toggle="tab"]');
		if (!tabControls.length) {
			return;
		}

		function syncTabActiveState(activeTab) {
			tabControls.forEach(function (control) {
				var isActive = control === activeTab;
				control.classList.toggle('active', isActive);
				control.classList.toggle('is-active', isActive);
				control.setAttribute('aria-selected', isActive ? 'true' : 'false');
			});
		}

		function scrollTabIntoView(tab) {
			if (!tab || !compactTabs.matches) {
				return;
			}
			tab.scrollIntoView({
				inline: 'nearest',
				block: 'nearest',
				behavior: reduced ? 'auto' : 'smooth',
			});
		}

		tabControls.forEach(function (control) {
			control.addEventListener('shown.bs.tab', function (event) {
				syncTabActiveState(event.target);
				scrollTabIntoView(event.target);
			});
		});

		syncTabActiveState(root.querySelector('[data-bs-toggle="tab"].active') || tabControls[0]);
	});
});
