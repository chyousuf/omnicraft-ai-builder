/**
 * OmniCraft AI Frontend Scroll-Driven Animation & Motion Controller
 */
(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		// 1. Initialize Intersection Observer for Scroll-Reveal on all sections and cards
		if ('IntersectionObserver' in window) {
			const animatedElements = document.querySelectorAll(
				'.elementor-section, .elementor-column, .wp-block-group, .wp-block-columns, .wp-block-column, .oc-hover-card'
			);

			const observerOptions = {
				root: null,
				rootMargin: '0px 0px -60px 0px',
				threshold: 0.12,
			};

			const revealObserver = new IntersectionObserver(function (entries, observer) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('omni-revealed');
						entry.target.style.opacity = '1';
						entry.target.style.transform = 'translateY(0)';
						observer.unobserve(entry.target);
					}
				});
			}, observerOptions);

			animatedElements.forEach(function (el, index) {
				if (!el.classList.contains('omni-reveal') && !el.closest('#navbar') && !el.closest('header')) {
					el.classList.add('omni-reveal');
					// Add staggered delay to columns
					if (el.classList.contains('elementor-column') || el.classList.contains('wp-block-column')) {
						const colIndex = Array.from(el.parentNode.children).indexOf(el);
						if (colIndex > 0) {
							el.style.transitionDelay = (colIndex * 0.15) + 's';
						}
					}
					revealObserver.observe(el);
				}
			});
		}

		// 2. Animated Number Counters
		const statNumbers = document.querySelectorAll('.elementor-widget-counter .elementor-counter-number, .wp-block-column p[style*="font-size:36px"]');
		if (statNumbers.length && 'IntersectionObserver' in window) {
			const counterObserver = new IntersectionObserver(function (entries, observer) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.style.transition = 'transform 0.5s ease-out';
						entry.target.style.transform = 'scale(1.1)';
						setTimeout(function () {
							entry.target.style.transform = 'scale(1)';
						}, 300);
						observer.unobserve(entry.target);
					}
				});
			}, { threshold: 0.5 });

			statNumbers.forEach(function (num) {
				counterObserver.observe(num);
			});
		}

		// 3. Smooth scrolling for internal anchor links
		document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
			anchor.addEventListener('click', function (e) {
				const targetId = this.getAttribute('href').substring(1);
				if (!targetId) return;
				const targetEl = document.getElementById(targetId) || document.querySelector('[data-id="' + targetId + '"]');
				if (targetEl) {
					e.preventDefault();
					targetEl.scrollIntoView({
						behavior: 'smooth',
						block: 'start',
					});
				}
			});
		});
	});
})();
