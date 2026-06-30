/**
 * Hero / section reveal — CSS transitions + IntersectionObserver (replaces Animate.css).
 */
document.documentElement.classList.add('js-reveal');

document.addEventListener('DOMContentLoaded', function () {
	var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var staggerPresets = {
		hero: 90,
		bento: 110,
		grid: 75,
		default: 70,
	};

	function reveal(el) {
		el.classList.add('is-visible');
	}

	function inView(el) {
		var rect = el.getBoundingClientRect();
		return rect.top < window.innerHeight * 0.92 && rect.bottom > 0;
	}

	function observeReveal(els) {
		if (!els.length) {
			return;
		}

		if (reduced) {
			els.forEach(reveal);
			return;
		}

		var io = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						reveal(entry.target);
						io.unobserve(entry.target);
					}
				});
			},
			{ threshold: 0.1, rootMargin: '0px 0px -6% 0px' }
		);

		els.forEach(function (el) {
			if (inView(el)) {
				reveal(el);
			} else {
				io.observe(el);
			}
		});
	}

	var revealEls = document.querySelectorAll('.msr-reveal');
	observeReveal(revealEls);

	document.querySelectorAll('.msr-reveal-stagger').forEach(function (group) {
		var preset = group.getAttribute('data-msr-reveal-stagger') || 'default';
		var step = staggerPresets[preset] || staggerPresets.default;
		var children = group.querySelectorAll(':scope > *');

		children.forEach(function (child, index) {
			child.classList.add('msr-reveal', 'msr-reveal--up');
			if (!reduced) {
				child.style.transitionDelay = index * step + 'ms';
			}
		});
		observeReveal(children);
	});
});
