/**
 * Theme JS + CSS entry — Vite → dist/app.js / dist/app.css
 * Bootstrap + icons bundled locally (audit P0 — no CDN / FA kit).
 */
import Tab from 'bootstrap/js/dist/tab.js';
import Carousel from 'bootstrap/js/dist/carousel.js';

window.bootstrap = { Tab, Carousel };

import '../scss/app.scss';
import './scroll-reveal.js';
import './publishing-filter-tabs.js';
import './publishing-nav.js';
import './publishing-home.js';
import './publishing-single.js';
import './gate-download.js';

function msrPublishingLoadDeferredModules() {
	if (document.querySelector('[data-fancybox="gallery"]')) {
		import('./fancybox-init.js');
	}
}

if ('requestIdleCallback' in window) {
	requestIdleCallback(msrPublishingLoadDeferredModules, { timeout: 2500 });
} else {
	document.addEventListener('DOMContentLoaded', msrPublishingLoadDeferredModules);
}
