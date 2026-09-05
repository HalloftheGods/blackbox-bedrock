/**
 * Menu accordion grouping script
 *
 * Expects globals set via wp_localize_script:
 *   window.blackbox_menu_config.bbIconUrl       - string URL to obsidian.png
 *   window.blackbox_menu_config.wpmudevIcons     - object mapping slugs to icon URLs
 */
document.addEventListener("DOMContentLoaded", function () {
  const adminMenu = document.getElementById("adminmenu");
  if (!adminMenu) return;

  const config = window.blackbox_menu_config || {};
  const bbIconUrl = config.bbIconUrl || "";
  const customIcons = config.wpmudevIcons || {};

  function createHeader(id, shortName, fullName, dashicon) {
    const li = document.createElement("li");
    const hasAcronym = shortName !== fullName ? " has-acronym" : "";
    li.className =
      "wp-not-current-submenu menu-top menu-top-last blackbox-group-header" + hasAcronym;
    li.id = id;
    li.innerHTML = `
			<a href="#" class="wp-has-submenu wp-not-current-submenu menu-top" aria-haspopup="true" style="display: flex !important; flex-direction: row !important; align-items: center !important; height: 34px !important; padding: 0 !important;">
				<div class="bb-plus-icon bb-arrow" style="order: 1; width: 36px; min-width: 36px; height: 34px; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: bold; color: rgba(255,255,255,0.5); flex-shrink: 0;">+</div>
				<div class="wp-menu-name" style="position: relative; flex: 1 1 auto; order: 2; height: 34px; display: flex; align-items: center; margin: 0; padding: 0; line-height: 34px;">
					<span class="bb-short-name" style="display:inline-block; transition:opacity 0.25s ease;">${shortName}</span>
					<span class="bb-expanded-name" style="position:absolute; left:-15px; width:calc(100% + 30px); text-align:center; top:50%; transform:translateY(-50%) translateX(5px); opacity:0; transition:all 0.25s ease; font-size: 11px; white-space: normal; line-height: 1.1; color: inherit; pointer-events: none;">${fullName}</span>
				</div>
				<div class="wp-menu-image dashicons-before ${dashicon}" style="order: 3; width: 36px; min-width: 36px; height: 34px; display: flex; align-items: center; justify-content: center; margin: 0; padding: 0; float: none; flex-shrink: 0;"></div>
			</a>
		`;
    return li;
  }

  const cmsHeader = createHeader(
    "blackbox-group-cms",
    "CMS",
    "Content Management System",
    "dashicons-portfolio"
  );
  const crmHeader = createHeader(
    "blackbox-group-crm",
    "CRM",
    "Customer Relationship Management",
    "dashicons-groups"
  );
  const maHeader = createHeader(
    "blackbox-group-ma",
    "MA",
    "Marketing Automation",
    "dashicons-megaphone"
  );
  const commerceHeader = createHeader(
    "blackbox-group-commerce",
    "POS",
    "Point of Sale",
    "dashicons-cart"
  );
  const itsmHeader = createHeader(
    "blackbox-group-itsm",
    "ITSM",
    "IT Service Management",
    "dashicons-sos"
  );
  const gamificationHeader = createHeader(
    "blackbox-group-gamification",
    "LXP",
    "Learning Experience Platform",
    "dashicons-awards"
  );
  const systemHeader = createHeader(
    "blackbox-group-system",
    "WordPress",
    "WP Platform",
    "dashicons-wordpress"
  );
  const damHeader = createHeader(
    "blackbox-group-dam",
    "DAM",
    "Digital Asset Management",
    "dashicons-format-image"
  );
  const osHeader = createHeader(
    "blackbox-group-os",
    "OS",
    "Operating Systems",
    "dashicons-desktop"
  );
  const extensionsHeader = createHeader(
    "blackbox-group-3rd",
    "Extensions",
    "Extensions",
    "dashicons-admin-plugins"
  );
  const biHeader = createHeader(
    "blackbox-group-bi",
    "BI",
    "Business Intelligence",
    "dashicons-chart-area"
  );

  // Categorize items
  adminMenu.querySelectorAll(".wp-menu-separator").forEach((sep) => (sep.style.display = "none"));
  const items = Array.from(adminMenu.querySelectorAll("li.menu-top"));

  // Core Launchpads (OS Group)
  const osSlugs = [
    "w4-protocol",
    "toplevel_page_w4-protocol",
    "xophz-compass",
    "toplevel_page_xophz-compass",
    "youmeos",
    "toplevel_page_youmeos",
    // "nook-phone",
    // "nook_os",
    // "nook_phone",
    // "dodo-air",
    // "dodo_airline",
    // "itinerary",
    // "itinerarys",
    "polos"
  ];

  const cmsIds = ["menu-posts", "menu-pages"];
  const cmsSlugs = [
    "enchiridion",
    "fusion",
    "portfolio",
    "faq",
    "properties",
    "elastic",
    "layerslider",
    "glow_post",
    "glowitheflow"
  ];

  const damIds = ["menu-appearance", "menu-media"];
  const damSlugs = ["menu-appearance", "smush", "branding", "wphb", "snapshot"];

  const crmIds = ["menu-users", "menu-comments"];
  const crmSlugs = ["questbook", "forminator", "requests", "request"];

  const maSlugs = [
    "hustle",
    "lead-magnet",
    "pixie-dust",
    "gale-boomerang",
    "beehive",
    "wds_wizard",
    "silver-arrow",
    "golden-keys",
    "bomb-bag"
  ];

  const commerceSlugs = [
    "woocommerce",
    "wc-admin",
    "wc-payments",
    "woocommerce-payments",
    "payment",
    "pay",
    "product",
    "products",
    "shop_order",
    "shop_coupon",
    "bazaar",
    "treasure-trove",
    "produce",
    "trade_listing",
    "trade-listing",
    "trade_listings"
  ];

  const itsmSlugs = [
    "bugnet",
    "compass_bug",
    "midnight_ticket",
    "magic-cape",
    "compass_cloak_hint",
    "blc_dash",
    "wpmudev-videos",
    "wp-defender",
    "hookshot",
    "lit-lamp",
    "magic-cloak",
    "midnight-nerd",
    "mirror-shield",
    "phantom-zone",
    "thors-hammer",
    "titans-mitt",

    "wpmudev-updates"
  ];

  const gamificationSlugs = [
    "xp_action",
    "xp_log",
    "xp_logs",
    "xp_goal",
    "xp_goals",
    "xp_badge",
    "xp_badges",
    "achievement",
    "ability",
    "accessory",
    "radio_station",
    "compass_xp",
    "cafeteria_topic",
    "xp"
  ];

  const systemIds = [
    ///
    "menu-plugins",
    "menu-tools",
    "menu-settings"
  ];
  const systemSlugs = [
    // "snippets", "code-snippets",
    "index-wp-mysql-for-speed",
    "shipper",
    "wpmudev"
  ];

  items.forEach((li) => {
    if (
      li.classList.contains("blackbox-group-header") ||
      li.id === "collapse-menu" ||
      li.id === "menu-dashboard"
    )
      return;

    let link = li.querySelector("a");
    let href = link ? link.getAttribute("href") : "";
    let lowerHref = href.toLowerCase();
    let lowerId = li.id ? li.id.toLowerCase() : "";

    if (li.id === "toplevel_page_blackbox-plugins") {
      li.dataset.bbGroup = "os";
      let nameDiv = li.querySelector(".wp-menu-name");
      if (nameDiv) nameDiv.innerText = "Software Manager";

      let iconDiv = li.querySelector(".wp-menu-image");
      if (iconDiv) {
        iconDiv.classList.remove("dashicons-before", "dashicons-grid-view");
        iconDiv.style.backgroundImage = `url('${bbIconUrl}')`;
        iconDiv.style.backgroundSize = "18px";
        iconDiv.style.backgroundPosition = "center";
        iconDiv.style.backgroundRepeat = "no-repeat";
        iconDiv.innerHTML = "";
      }
      let sub = li.querySelector(".wp-submenu");
      if (sub) sub.style.display = "none";
      li.classList.remove("wp-has-submenu");
    } else if (osSlugs.some((slug) => lowerHref.includes(slug) || lowerId.includes(slug))) {
      li.dataset.bbGroup = "os";
    } else if (
      (li.id && cmsIds.includes(li.id)) ||
      cmsSlugs.some((slug) => lowerHref.includes(slug) || lowerId.includes(slug))
    ) {
      li.dataset.bbGroup = "cms";
    } else if (
      (li.id && damIds.includes(li.id)) ||
      damSlugs.some((slug) => lowerHref.includes(slug) || lowerId.includes(slug))
    ) {
      li.dataset.bbGroup = "dam";
    } else if (
      (li.id && crmIds.includes(li.id)) ||
      crmSlugs.some((slug) => lowerHref.includes(slug) || lowerId.includes(slug))
    ) {
      li.dataset.bbGroup = "crm";
    } else if (maSlugs.some((slug) => lowerHref.includes(slug) || lowerId.includes(slug))) {
      li.dataset.bbGroup = "ma";
    } else if (commerceSlugs.some((slug) => lowerHref.includes(slug) || lowerId.includes(slug))) {
      li.dataset.bbGroup = "commerce";
    } else if (itsmSlugs.some((slug) => lowerHref.includes(slug) || lowerId.includes(slug))) {
      li.dataset.bbGroup = "itsm";
    } else if (
      gamificationSlugs.some((slug) => lowerHref.includes(slug) || lowerId.includes(slug))
    ) {
      li.dataset.bbGroup = "gamification";
    } else if (
      (li.id && systemIds.includes(li.id)) ||
      systemSlugs.some((slug) => lowerHref.includes(slug) || lowerId.includes(slug))
    ) {
      li.dataset.bbGroup = "system";
    } else if (li.classList.contains("wp-menu-separator")) {
      li.style.display = "none";
    } else {
      li.dataset.bbGroup = "3rd";
    }
  });

  // Re-insert grouped items into DOM so they flow naturally
  const collapseMenu = document.getElementById("collapse-menu");

  // OS Group
  let osItems = items.filter((li) => li.dataset.bbGroup === "os");
  if (osItems.length > 0) {
    adminMenu.insertBefore(osHeader, collapseMenu);
    osItems.sort((a, b) => {
      if (a.id === "toplevel_page_blackbox-plugins") return -1;
      if (b.id === "toplevel_page_blackbox-plugins") return 1;
      return 0;
    });
    osItems.forEach((li) => adminMenu.insertBefore(li, collapseMenu));
  }

  // CMS
  let cmsItems = items.filter((li) => li.dataset.bbGroup === "cms");
  if (cmsItems.length > 0) {
    adminMenu.insertBefore(cmsHeader, collapseMenu);
    const cmsOrder = [(li) => li.id === "menu-pages", (li) => li.id === "menu-posts"];
    cmsItems.sort((a, b) => {
      let aIdx = cmsOrder.findIndex((fn) => fn(a));
      let bIdx = cmsOrder.findIndex((fn) => fn(b));
      if (aIdx === -1) aIdx = 99;
      if (bIdx === -1) bIdx = 99;

      if (aIdx === 99 && bIdx === 99) {
        let aName = a.querySelector(".wp-menu-name")
          ? a.querySelector(".wp-menu-name").textContent.trim()
          : "";
        let bName = b.querySelector(".wp-menu-name")
          ? b.querySelector(".wp-menu-name").textContent.trim()
          : "";
        return aName.localeCompare(bName);
      }

      return aIdx - bIdx;
    });
    cmsItems.forEach((li) => adminMenu.insertBefore(li, collapseMenu));
  }

  // DAM
  let damItems = items.filter((li) => li.dataset.bbGroup === "dam");
  if (damItems.length > 0) {
    adminMenu.insertBefore(damHeader, collapseMenu);
    const damOrder = [
      (li) => li.id === "menu-media",
      (li) =>
        li.querySelector("a") &&
        li.querySelector("a").getAttribute("href") &&
        li.querySelector("a").getAttribute("href").includes("smush")
    ];
    damItems.sort((a, b) => {
      let aIdx = damOrder.findIndex((fn) => fn(a));
      let bIdx = damOrder.findIndex((fn) => fn(b));
      if (aIdx === -1) aIdx = 99;
      if (bIdx === -1) bIdx = 99;
      return aIdx - bIdx;
    });
    damItems.forEach((li) => adminMenu.insertBefore(li, collapseMenu));
  }

  // CRM
  let crmItems = items.filter((li) => li.dataset.bbGroup === "crm");
  if (crmItems.length > 0) {
    adminMenu.insertBefore(crmHeader, collapseMenu);
    crmItems.forEach((li) => adminMenu.insertBefore(li, collapseMenu));
  }

  // LXP (Gamification)
  let gamificationItems = items.filter((li) => li.dataset.bbGroup === "gamification");
  if (gamificationItems.length > 0) {
    adminMenu.insertBefore(gamificationHeader, collapseMenu);
    gamificationItems.forEach((li) => adminMenu.insertBefore(li, collapseMenu));
  }

  // MA
  let maItems = items.filter((li) => li.dataset.bbGroup === "ma");
  if (maItems.length > 0) {
    adminMenu.insertBefore(maHeader, collapseMenu);
    maItems.forEach((li) => adminMenu.insertBefore(li, collapseMenu));
  }

  // POS (Commerce)
  let commerceItems = items.filter((li) => li.dataset.bbGroup === "commerce");
  if (commerceItems.length > 0) {
    adminMenu.insertBefore(commerceHeader, collapseMenu);
    commerceItems.forEach((li) => adminMenu.insertBefore(li, collapseMenu));
  }

  // BI (Business Intelligence)
  let biItems = items.filter((li) => li.dataset.bbGroup === "bi");
  if (biItems.length > 0) {
    adminMenu.insertBefore(biHeader, collapseMenu);
    biItems.forEach((li) => adminMenu.insertBefore(li, collapseMenu));
  }

  // ITSM
  let itsmItems = items.filter((li) => li.dataset.bbGroup === "itsm");
  if (itsmItems.length > 0) {
    adminMenu.insertBefore(itsmHeader, collapseMenu);
    itsmItems.forEach((li) => adminMenu.insertBefore(li, collapseMenu));
  }

  // WP Platform (System)
  let systemItems = items.filter((li) => li.dataset.bbGroup === "system");
  if (systemItems.length > 0) {
    adminMenu.insertBefore(systemHeader, collapseMenu);
    systemItems.forEach((li) => adminMenu.insertBefore(li, collapseMenu));
  }

  // Extensions (3rd Party)
  let thirdItems = items.filter((li) => li.dataset.bbGroup === "3rd");
  if (thirdItems.length > 0) {
    adminMenu.insertBefore(extensionsHeader, collapseMenu);
    thirdItems.forEach((li) => adminMenu.insertBefore(li, collapseMenu));
  }

  // Redistribute Compass submenu items into sidebar groups
  const compassGroupMap = config.compassGroupMap || {};
  const compassMenu = document.getElementById("toplevel_page_xophz-compass");
  if (compassMenu) {
    const compassIcon = compassMenu.querySelector(".wp-menu-image");
    const compassIconSrc = compassIcon
      ? compassIcon.querySelector("img")
        ? compassIcon.querySelector("img").src
        : ""
      : "";
    const subItems = Array.from(compassMenu.querySelectorAll(".wp-submenu li"));

    const seenRoutes = new Set();
    subItems.forEach((li, idx) => {
      if (idx === 0) return;

      const link = li.querySelector("a");
      if (!link) return;

      const href = link.getAttribute("href") || "";
      const routeMatch = href.match(/#\/(?:plugin\/)?([a-z0-9-]+)/);
      if (!routeMatch) return;

      const routeSlug = routeMatch[1];
      if (seenRoutes.has(routeSlug)) {
        li.style.display = "none";
        return;
      }
      seenRoutes.add(routeSlug);

      if (adminMenu.querySelector(`li[data-compass-route="${routeSlug}"]`)) {
        li.style.display = "none";
        return;
      }

      const mapData = compassGroupMap[routeSlug];
      if (!mapData) return;

      const panelGroup = mapData.panel;
      const pluginIconSrc = mapData.icon;

      const newLi = document.createElement("li");
      newLi.className = "wp-not-current-submenu menu-top menu-icon-generic";
      newLi.dataset.bbGroup = panelGroup;
      newLi.dataset.compassRoute = routeSlug;

      const iconMarkup = pluginIconSrc
        ? `<div class="wp-menu-image"><img src="${pluginIconSrc}" alt="" /></div>`
        : compassIconSrc
          ? `<div class="wp-menu-image"><img src="${compassIconSrc}" alt="" /></div>`
          : `<div class="wp-menu-image dashicons-before dashicons-marker"></div>`;

      newLi.innerHTML = `<a href="${href}" class="wp-not-current-submenu menu-top">${iconMarkup}<div class="wp-menu-name">${link.textContent.trim()}</div></a>`;

      const targetHeader = document.getElementById(`blackbox-group-${panelGroup}`);
      if (targetHeader) {
        let insertPoint = targetHeader.nextElementSibling;
        while (insertPoint && insertPoint.dataset && insertPoint.dataset.bbGroup === panelGroup) {
          insertPoint = insertPoint.nextElementSibling;
        }
        adminMenu.insertBefore(newLi, insertPoint);
      }

      li.style.display = "none";
    });
  }

  // Apply custom WPMUDEV icons to their respective menus
  for (const [slug, icon] of Object.entries(customIcons)) {
    const link = adminMenu.querySelector(`a[href*="page=${slug}"]`);
    if (link && link.parentElement && link.parentElement.dataset.bbGroup !== "top") {
      const iconDiv = link.parentElement.querySelector(".wp-menu-image");
      if (iconDiv) {
        iconDiv.style.backgroundImage = `url('${icon}')`;
        iconDiv.style.backgroundSize = "18px";
        iconDiv.style.backgroundPosition = "center";
        iconDiv.style.backgroundRepeat = "no-repeat";
        iconDiv.classList.remove("dashicons-before");
        iconDiv.innerHTML = "";
      }
    }
  }

  const groups = [
    "os",
    "cms",
    "dam",
    "crm",
    "gamification",
    "ma",
    "commerce",
    "bi",
    "itsm",
    "system",
    "3rd"
  ];

  // Accordion interaction logic
  function toggleGroup(groupName, forceOpen = false) {
    const groupEl = document.getElementById(`blackbox-group-${groupName}`);
    if (!groupEl) return;

    const isOpening = forceOpen || !groupEl.classList.contains("bb-open");

    // Close all visually
    groups.forEach((gn) => {
      const header = document.getElementById(`blackbox-group-${gn}`);
      if (!header) return;
      header.classList.remove("bb-open", "wp-has-current-submenu");
      header.classList.add("wp-not-current-submenu");
      const arrow = header.querySelector(".bb-arrow");
      if (arrow) arrow.innerText = "+";
    });

    // Hide other groups
    const itemsToHide = adminMenu.querySelectorAll(
      groups.map((g) => `li[data-bb-group="${g}"]:not([data-bb-group="${groupName}"])`).join(", ")
    );
    if (window.jQuery) {
      jQuery(itemsToHide)
        .stop(true, true)
        .slideUp(200, function () {
          this.classList.remove("bb-open-item", "bb-first-item", "bb-last-item");
        });
    } else {
      itemsToHide.forEach((li) => {
        li.style.display = "none";
        li.classList.remove("bb-open-item", "bb-first-item", "bb-last-item");
      });
    }

    // Open target
    if (isOpening) {
      groupEl.classList.add("bb-open", "wp-has-current-submenu");
      groupEl.classList.remove("wp-not-current-submenu");
      const arrow = groupEl.querySelector(".bb-arrow");
      if (arrow) arrow.innerText = "-";
      const targetItems = Array.from(
        adminMenu.querySelectorAll(`li[data-bb-group="${groupName}"]`)
      );

      targetItems.forEach((li, idx) => {
        li.classList.add("bb-open-item");
        if (idx === 0) li.classList.add("bb-first-item");
        if (idx === targetItems.length - 1) li.classList.add("bb-last-item");
      });

      if (window.jQuery) {
        jQuery(targetItems).stop(true, true).slideDown(200);
      } else {
        targetItems.forEach((li) => (li.style.display = "block"));
      }
    } else {
      const targetItems = Array.from(
        adminMenu.querySelectorAll(`li[data-bb-group="${groupName}"]`)
      );
      if (window.jQuery) {
        jQuery(targetItems)
          .stop(true, true)
          .slideUp(200, function () {
            this.classList.remove("bb-open-item", "bb-first-item", "bb-last-item");
          });
      } else {
        targetItems.forEach((li) => {
          li.style.display = "none";
          li.classList.remove("bb-open-item", "bb-first-item", "bb-last-item");
        });
      }
    }
  }

  groups.forEach((gn) => {
    const header = document.getElementById(`blackbox-group-${gn}`);
    if (header)
      header.addEventListener("click", (e) => {
        e.preventDefault();
        toggleGroup(gn);
      });
  });

  // Auto-open group of current item
  const currentItem = adminMenu.querySelector(
    ".wp-has-current-submenu:not(.blackbox-group-header), .current:not(.blackbox-group-header)"
  );
  let activeGroup = "cms";

  if (currentItem && currentItem.dataset && currentItem.dataset.bbGroup) {
    activeGroup = currentItem.dataset.bbGroup === "top" ? null : currentItem.dataset.bbGroup;
  }

  if (activeGroup) {
    toggleGroup(activeGroup);
  } else {
    adminMenu
      .querySelectorAll(groups.map((g) => `li[data-bb-group="${g}"]`).join(", "))
      .forEach((li) => {
        li.style.display = "none";
      });
  }

  function updateActiveCompassMenu() {
    const hash = window.location.hash;
    const routeMatch = hash.match(/#\/([a-z0-9-]+)/);
    if (!routeMatch) return;

    const routeSlug = routeMatch[1];
    const mapData = compassGroupMap[routeSlug];
    if (!mapData) return;

    adminMenu.querySelectorAll(".wp-has-current-submenu, .current, .wp-menu-open").forEach((el) => {
      if (!el.classList.contains("blackbox-group-header")) {
        el.classList.remove("wp-has-current-submenu", "current", "wp-menu-open");
        el.classList.add("wp-not-current-submenu");
      }
    });

    const targetLi = adminMenu.querySelector(`li[data-compass-route="${routeSlug}"]`);
    if (targetLi) {
      targetLi.classList.remove("wp-not-current-submenu");
      targetLi.classList.add("wp-has-current-submenu", "wp-menu-open", "current");

      const a = targetLi.querySelector("a");
      if (a) {
        a.classList.remove("wp-not-current-submenu");
        a.classList.add("wp-has-current-submenu", "wp-menu-open", "current");
      }

      const group = targetLi.dataset.bbGroup;
      if (group) {
        toggleGroup(group, true);
      }
    }
  }

  updateActiveCompassMenu();
  window.addEventListener("hashchange", updateActiveCompassMenu);

  const originalPushState = history.pushState;
  history.pushState = function () {
    originalPushState.apply(this, arguments);
    updateActiveCompassMenu();
  };

  const originalReplaceState = history.replaceState;
  history.replaceState = function () {
    originalReplaceState.apply(this, arguments);
    updateActiveCompassMenu();
  };

  document.body.classList.add("blackbox-menu-grouped");
});
