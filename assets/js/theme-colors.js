/**
 * Live theme color preview on user profile page
 * Updates CSS custom properties when admin color scheme radio buttons are clicked
 */
document.addEventListener("DOMContentLoaded", function() {
	var palettes = {
		fresh:     ["#1d2327","#2c3338","#2271b1","#72aee6"],
		light:     ["#e5e5e5","#999999","#d64e07","#04a4cc"],
		blue:      ["#096484","#4796b3","#52accc","#74B6CE"],
		midnight:  ["#25282b","#363b3f","#69a8bb","#e14d43"],
		ectoplasm: ["#413256","#523f6d","#a3b745","#d46f15"],
		coffee:    ["#46403c","#59524c","#c7a589","#9ea476"],
		ocean:     ["#627c83","#738e96","#9ebaa0","#aa9d88"],
		sunrise:   ["#b43c38","#cf4944","#dd823b","#ccaf0b"]
	};
	var radios = document.querySelectorAll("input[name=admin_color]");
	radios.forEach(function(radio) {
		radio.addEventListener("click", function() {
			var c = palettes[this.value] || palettes.fresh;
			var r = document.documentElement.style;
			r.setProperty("--wp-theme-base", c[0]);
			r.setProperty("--wp-theme-focus", c[1]);
			r.setProperty("--wp-theme-color", c[2]);
			r.setProperty("--wp-theme-secondary", c[3]);
			r.setProperty("--wp-theme-active", this.value === "light" ? c[1] : c[2]);
		});
	});
});
