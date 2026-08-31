/**
 * Settings toggle label updater
 */
document.addEventListener("DOMContentLoaded", function() {
	var toggles = document.querySelectorAll(".bb-toggle-input");
	toggles.forEach(function(toggle) {
		var container = toggle.closest(".bb-toggle-container");
		var label = container ? container.querySelector(".bb-status-label") : null;
		if (label) {
			toggle.addEventListener("change", function() {
				label.textContent = this.checked ? (label.getAttribute("data-active-text") || "Active") : (label.getAttribute("data-inactive-text") || "Disabled");
			});
		}
	});
});
