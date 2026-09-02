/**
 * Dashboard & License Manager Handler
 * Provides dynamic Tesseract tier filtering, live search, and asynchronous activation toggling.
 */
document.addEventListener('DOMContentLoaded', () => {
	// =========================================================================
	// 1. Tesseract Tier & Status Filter Engine
	// =========================================================================
	let currentTierFilter = 'all';
	let currentStatusFilter = 'all';
	let currentSearchQuery = '';

	const tierButtons = document.querySelectorAll('[data-tier-filter]');
	const statusButtons = document.querySelectorAll('[data-status-filter]');
	const searchInput = document.getElementById('blackbox-search');
	const searchClearBtn = document.getElementById('blackbox-search-clear');
	const visibleCountEl = document.getElementById('blackbox-visible-count');
	const emptyStateEl = document.getElementById('blackbox-empty-state');
	const resetBtn = document.getElementById('blackbox-reset-filters');
	const cards = document.querySelectorAll('.blackbox-card');

	const applyFilters = () => {
		let visibleCount = 0;
		const query = currentSearchQuery.trim().toLowerCase();

		cards.forEach((card) => {
			const cardTier = card.dataset.tier || '';
			const cardStatus = card.dataset.status || '';
			const cardSearchText = card.dataset.search || '';

			// Evaluate filter criteria
			const matchesTier = (currentTierFilter === 'all') || (cardTier === currentTierFilter);
			const matchesStatus = (currentStatusFilter === 'all') || (cardStatus === currentStatusFilter);
			const matchesSearch = (!query) || cardSearchText.includes(query);

			const isVisible = matchesTier && matchesStatus && matchesSearch;

			if (isVisible) {
				card.style.display = '';
				visibleCount++;
			} else {
				card.style.display = 'none';
			}
		});

		// Update visible counter
		if (visibleCountEl) {
			visibleCountEl.innerText = visibleCount;
		}

		// Handle empty state visibility
		if (emptyStateEl) {
			emptyStateEl.style.display = visibleCount === 0 ? 'block' : 'none';
		}
	};

	// Tier Pills Handler
	tierButtons.forEach((btn) => {
		btn.addEventListener('click', (e) => {
			e.preventDefault();
			tierButtons.forEach((b) => b.classList.remove('is-active'));
			btn.classList.add('is-active');
			currentTierFilter = btn.dataset.tierFilter || 'all';
			applyFilters();
		});
	});

	// Status Filter Handler
	statusButtons.forEach((btn) => {
		btn.addEventListener('click', (e) => {
			e.preventDefault();
			statusButtons.forEach((b) => b.classList.remove('is-active'));
			btn.classList.add('is-active');
			currentStatusFilter = btn.dataset.statusFilter || 'all';
			applyFilters();
		});
	});

	// Search Input Handler
	if (searchInput) {
		searchInput.addEventListener('input', (e) => {
			currentSearchQuery = e.target.value;
			if (searchClearBtn) {
				searchClearBtn.style.display = currentSearchQuery.length > 0 ? 'block' : 'none';
			}
			applyFilters();
		});
	}

	if (searchClearBtn) {
		searchClearBtn.addEventListener('click', () => {
			if (searchInput) {
				searchInput.value = '';
				currentSearchQuery = '';
				searchClearBtn.style.display = 'none';
				searchInput.focus();
				applyFilters();
			}
		});
	}

	// Reset All Filters Handler
	if (resetBtn) {
		resetBtn.addEventListener('click', () => {
			currentTierFilter = 'all';
			currentStatusFilter = 'all';
			currentSearchQuery = '';

			tierButtons.forEach((b) => b.classList.toggle('is-active', b.dataset.tierFilter === 'all'));
			statusButtons.forEach((b) => b.classList.toggle('is-active', b.dataset.statusFilter === 'all'));

			if (searchInput) searchInput.value = '';
			if (searchClearBtn) searchClearBtn.style.display = 'none';

			applyFilters();
		});
	}

	// =========================================================================
	// 2. Toggle Activation & Traffic Light Interactivity
	// =========================================================================
	document.body.addEventListener('mouseover', (e) => {
		const btn = e.target.closest('.btn-toggle');
		if (btn) {
			const actions = btn.closest('.blackbox-actions');
			if (actions) actions.classList.add('is-toggle-hovered');
		}
	});

	document.body.addEventListener('mouseout', (e) => {
		const btn = e.target.closest('.btn-toggle');
		if (btn) {
			const actions = btn.closest('.blackbox-actions');
			if (actions) actions.classList.remove('is-toggle-hovered');
		}
	});

	document.body.addEventListener('click', async (e) => {
		const btn = e.target.closest('.btn-toggle');
		if (!btn || btn.classList.contains('is-loading')) return;

		e.preventDefault();
		const action = btn.dataset.action;
		const plugin = btn.dataset.plugin;
		const card = btn.closest('.blackbox-card');
		const goBtn = card ? card.querySelector('.btn-go') : null;
		const originalHTML = btn.innerHTML;

		btn.classList.add('is-loading');
		btn.innerHTML = '<i class="fad fa-spinner-third fa-spin"></i>';
		if (goBtn) {
			goBtn.classList.add('is-disabled');
			goBtn.setAttribute('tabindex', '-1');
			goBtn.setAttribute('aria-disabled', 'true');
		}

		try {
			let response;
			if (window.blackbox_api && window.blackbox_api.root) {
				response = await fetch(window.blackbox_api.root + 'toggle', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-WP-Nonce': window.blackbox_api.nonce
					},
					body: JSON.stringify({
						plugin: plugin,
						toggle: action
					})
				});
			} else {
				const formData = new URLSearchParams();
				formData.append('action', 'blackbox_toggle_plugin');
				formData.append('nonce', window.blackbox_toggle_nonce || '');
				formData.append('toggle', action);
				formData.append('plugin', plugin);

				response = await fetch(ajaxurl, {
					method: 'POST',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
					body: formData
				});
			}

			const data = await response.json();
			const isSuccess = Boolean(data.success) || (response.ok && data.status);

			if (isSuccess) {
				const goUrl = btn.dataset.go;
				const actions = card.querySelector('.blackbox-actions');

				if (action === 'activate') {
					card.classList.add('is-active');
					card.dataset.status = 'active';
					card.querySelector('.blackbox-badge').className = 'blackbox-badge badge-active';
					card.querySelector('.blackbox-badge').innerText = 'Active';
					actions.innerHTML = `
						<a href="${goUrl}" class="btn-go">
							<span>Go</span>
							<i class="fad fa-traffic-light-go"></i>
						</a>
						<button type="button" class="btn-toggle btn-on" data-action="deactivate" data-plugin="${plugin}" data-go="${goUrl}">
							<span>On</span>
							<i class="fad fa-toggle-on"></i>
						</button>
					`;
				} else {
					card.classList.remove('is-active');
					card.dataset.status = 'inactive';
					card.querySelector('.blackbox-badge').className = 'blackbox-badge badge-inactive';
					card.querySelector('.blackbox-badge').innerText = 'Inactive';
					actions.innerHTML = `
						<a href="#" class="btn-go is-disabled" tabindex="-1" aria-disabled="true">
							<span>Go</span>
							<i class="fad fa-traffic-light-stop"></i>
						</a>
						<button type="button" class="btn-toggle btn-off" data-action="activate" data-plugin="${plugin}" data-go="${goUrl}">
							<span>Off</span>
							<i class="fad fa-toggle-off"></i>
						</button>
					`;
				}

				// Update active stats counter in header
				const activeCountEl = document.getElementById('stat-active-engines');
				if (activeCountEl) {
					const totalActive = document.querySelectorAll('.blackbox-card.is-active').length;
					activeCountEl.innerText = totalActive;
				}

				// Reapply filter state if active/inactive filtering was applied
				if (currentStatusFilter !== 'all') {
					applyFilters();
				}
			} else {
				const errorMsg = data.message || data.data || 'Failed to toggle';
				alert('Error: ' + errorMsg);
				btn.classList.remove('is-loading');
				btn.innerHTML = originalHTML;
				if (goBtn && action === 'deactivate') {
					goBtn.classList.remove('is-disabled');
					goBtn.removeAttribute('tabindex');
					goBtn.removeAttribute('aria-disabled');
				}
			}
		} catch (err) {
			alert('Network Error');
			btn.classList.remove('is-loading');
			btn.innerHTML = originalHTML;
			if (goBtn && action === 'deactivate') {
				goBtn.classList.remove('is-disabled');
				goBtn.removeAttribute('tabindex');
				goBtn.removeAttribute('aria-disabled');
			}
		}
	});
});
