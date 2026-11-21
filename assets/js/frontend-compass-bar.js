(() => {
  'use strict';

  const ROOT_ID = 'blackbox-compass-bar-root';
  const DATA_ID = 'blackbox-compass-bar-data';

  const ICONS = {
    compass: '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76" fill="currentColor" opacity="0.25"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>',
    edit: '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>',
    plus: '<svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>',
    chevron: '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>',
    external: '<svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>',
    wp: '<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>',
    logout: '<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>',
    switchBar: '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="6"/><circle cx="16" cy="12" r="3" fill="currentColor"/></svg>',
  };

  const STYLES = `
    :host {
      all: initial;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif;
      color: #e6f1fc;
      box-sizing: border-box;
    }

    *, *::before, *::after {
      box-sizing: border-box;
    }

    .bb-backdrop {
      position: fixed;
      inset: 0;
      bottom: calc(64px + env(safe-area-inset-bottom, 0px));
      background: rgba(4, 9, 18, 0.35);
      backdrop-filter: blur(6px);
      -webkit-backdrop-filter: blur(6px);
      z-index: 999990;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.2s ease;
    }

    .bb-backdrop.is-open {
      opacity: 1;
      pointer-events: auto;
    }

    .bb-bottom-nav {
      position: fixed;
      left: 0;
      right: 0;
      bottom: 0;
      height: calc(64px + env(safe-area-inset-bottom, 0px));
      padding-bottom: env(safe-area-inset-bottom, 0px);
      display: flex;
      align-items: stretch;
      justify-content: center;
      background: rgba(8, 16, 30, 0.78);
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
      box-shadow: 0 -6px 24px rgba(0, 0, 0, 0.38);
      z-index: 999995;
    }

    .bb-bottom-nav::before {
      content: "";
      position: absolute;
      left: 0;
      right: 0;
      top: 0;
      height: 1px;
      background: linear-gradient(
        90deg,
        transparent 0%,
        rgba(98, 201, 255, 0.75) 30%,
        rgba(167, 120, 255, 0.65) 70%,
        transparent 100%
      );
      box-shadow: 0 0 10px rgba(98, 201, 255, 0.45);
      pointer-events: none;
    }

    .bb-nav-inner {
      display: flex;
      width: 100%;
      max-width: 680px;
      height: 64px;
    }

    .bb-slot {
      flex: 1 1 0;
      min-width: 0;
      height: 100%;
      display: flex;
    }

    .bb-nav-btn {
      appearance: none;
      border: 0;
      background: transparent;
      color: rgba(210, 228, 246, 0.68);
      width: 100%;
      height: 100%;
      padding: 8px 4px 6px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      gap: 4px;
      cursor: pointer;
      text-decoration: none;
      transition: color 0.2s ease, background-color 0.2s ease;
    }

    .bb-nav-btn:hover,
    .bb-nav-btn.is-active {
      color: #62c9ff;
    }

    .bb-nav-icon {
      height: 22px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .bb-nav-avatar {
      width: 22px;
      height: 22px;
      border-radius: 50%;
      object-fit: cover;
      border: 1.5px solid rgba(255, 255, 255, 0.25);
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .bb-nav-btn:hover .bb-nav-avatar,
    .bb-nav-btn.is-active .bb-nav-avatar {
      border-color: rgba(98, 201, 255, 0.85);
      box-shadow: 0 0 8px rgba(98, 201, 255, 0.4);
    }

    .bb-nav-label {
      font-size: 11px;
      font-weight: 500;
      line-height: 1.15;
      text-align: center;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 100%;
    }

    .bb-flyout {
      position: fixed;
      right: 14px;
      bottom: calc(74px + env(safe-area-inset-bottom, 0px));
      width: min(350px, calc(100vw - 28px));
      max-height: min(74vh, 620px);
      display: flex;
      flex-direction: column;
      border-radius: 14px;
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-top-color: transparent;
      overflow: hidden;
      background:
        radial-gradient(ellipse 70% 42% at 32% 100%, rgba(98, 201, 255, 0.22) 0%, transparent 72%),
        radial-gradient(ellipse 70% 42% at 68% 100%, rgba(167, 120, 255, 0.2) 0%, transparent 72%),
        linear-gradient(180deg, rgba(16, 30, 52, 0.92) 0%, rgba(10, 18, 34, 0.94) 55%, rgba(14, 28, 48, 0.92) 100%);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      box-shadow:
        0 -12px 36px rgba(0, 0, 0, 0.4),
        0 16px 40px rgba(0, 0, 0, 0.55),
        inset 0 1px 0 rgba(255, 255, 255, 0.08);
      z-index: 999994;
      opacity: 0;
      transform: translateY(10px) scale(0.98);
      pointer-events: none;
      transition: opacity 0.2s ease, transform 0.22s cubic-bezier(0.2, 0, 0, 1);
    }

    .bb-flyout::before {
      content: "";
      position: absolute;
      left: 0;
      right: 0;
      top: 0;
      height: 1px;
      background: linear-gradient(
        90deg,
        rgba(255, 255, 255, 0.14) 0%,
        rgba(98, 201, 255, 0.75) 25%,
        rgba(167, 120, 255, 0.65) 75%,
        rgba(255, 255, 255, 0.14) 100%
      );
      box-shadow: 0 0 10px rgba(98, 201, 255, 0.4);
      pointer-events: none;
      z-index: 2;
    }

    .bb-flyout.is-open {
      opacity: 1;
      transform: translateY(0) scale(1);
      pointer-events: auto;
    }

    .bb-flyout--compact {
      width: min(260px, calc(100vw - 28px));
      right: auto;
      left: 50%;
      transform: translate(-50%, 10px) scale(0.98);
    }

    .bb-flyout--compact.is-open {
      transform: translate(-50%, 0) scale(1);
    }

    .bb-flyout-header {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 12px 14px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .bb-identity {
      display: flex;
      align-items: center;
      gap: 10px;
      flex: 1;
      min-width: 0;
      text-decoration: none;
      color: inherit;
      padding: 4px 6px;
      border-radius: 10px;
      transition: background-color 0.2s ease;
    }

    .bb-identity:hover {
      background: rgba(255, 255, 255, 0.06);
    }

    .bb-header-avatar {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      object-fit: cover;
      border: 1px solid rgba(98, 201, 255, 0.4);
      flex-shrink: 0;
    }

    .bb-identity-meta {
      min-width: 0;
    }

    .bb-display-name {
      font-size: 13.5px;
      font-weight: 600;
      color: #f0f7ff;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .bb-subtitle {
      font-size: 11px;
      color: rgba(210, 228, 246, 0.62);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .bb-quick-strip {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px;
      padding: 10px 14px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .bb-pill-link {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      padding: 8px 10px;
      border-radius: 10px;
      font-size: 12px;
      font-weight: 600;
      text-decoration: none;
      color: #e6f1fc;
      background: rgba(255, 255, 255, 0.06);
      border: 1px solid rgba(255, 255, 255, 0.1);
      transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
    }

    .bb-pill-link:hover {
      background: rgba(98, 201, 255, 0.14);
      border-color: rgba(98, 201, 255, 0.45);
      color: #62c9ff;
    }

    .bb-pill-link--primary {
      background: rgba(98, 201, 255, 0.16);
      border-color: rgba(98, 201, 255, 0.45);
      color: #62c9ff;
    }

    .bb-pill-link--primary:hover {
      background: rgba(98, 201, 255, 0.26);
    }

    .bb-scroll-body {
      overflow-y: auto;
      padding: 8px 10px;
      flex: 1;
      scrollbar-width: thin;
      scrollbar-color: rgba(98, 201, 255, 0.3) transparent;
    }

    .bb-section-title {
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: rgba(210, 228, 246, 0.48);
      padding: 4px 8px 6px;
    }

    .bb-node-row {
      display: flex;
      align-items: center;
      border-radius: 9px;
      transition: background-color 0.16s ease;
    }

    .bb-node-row:hover {
      background: rgba(255, 255, 255, 0.07);
    }

    .bb-node-trigger {
      appearance: none;
      border: 0;
      background: transparent;
      color: #dbeaf9;
      flex: 1;
      min-width: 0;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      padding: 8px 10px;
      font-size: 13px;
      font-weight: 500;
      text-align: left;
      text-decoration: none;
      cursor: pointer;
    }

    .bb-node-trigger:hover {
      color: #62c9ff;
    }

    .bb-node-text {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .bb-node-go {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 28px;
      height: 28px;
      margin-right: 4px;
      border-radius: 7px;
      color: rgba(210, 228, 246, 0.55);
      text-decoration: none;
      flex-shrink: 0;
      transition: color 0.16s ease, background-color 0.16s ease;
    }

    .bb-node-go:hover {
      color: #62c9ff;
      background: rgba(98, 201, 255, 0.14);
    }

    .bb-chevron {
      display: inline-flex;
      color: rgba(210, 228, 246, 0.5);
      transition: transform 0.2s ease, color 0.2s ease;
      flex-shrink: 0;
    }

    .bb-node-group.is-expanded > .bb-node-row .bb-chevron {
      transform: rotate(90deg);
      color: #62c9ff;
    }

    .bb-sublist {
      display: none;
      border-left: 1px solid rgba(98, 201, 255, 0.24);
      margin: 2px 0 4px 14px;
      padding-left: 6px;
    }

    .bb-node-group.is-expanded > .bb-sublist {
      display: block;
    }

    .bb-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      padding: 10px 14px;
      border-top: 1px solid rgba(255, 255, 255, 0.08);
      background: rgba(4, 10, 20, 0.35);
    }

    .bb-footer-btn {
      appearance: none;
      border: 0;
      background: transparent;
      color: rgba(210, 228, 246, 0.7);
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 12px;
      font-weight: 500;
      padding: 5px 8px;
      border-radius: 8px;
      text-decoration: none;
      cursor: pointer;
      transition: color 0.16s ease, background-color 0.16s ease;
    }

    .bb-footer-btn:hover {
      color: #62c9ff;
      background: rgba(255, 255, 255, 0.06);
    }

    .bb-footer-btn--danger:hover {
      color: #ff7b8a;
      background: rgba(255, 100, 120, 0.12);
    }

    .bb-empty {
      font-size: 12px;
      color: rgba(210, 228, 246, 0.55);
      padding: 10px 8px;
    }
  `;

  function parsePayload() {
    const dataEl = document.getElementById(DATA_ID);
    if (!dataEl) return null;
    try {
      return JSON.parse(dataEl.textContent || '{}');
    } catch {
      return null;
    }
  }

  function createEl(tag, className, text) {
    const el = document.createElement(tag);
    if (className) el.className = className;
    if (typeof text === 'string') el.textContent = text;
    return el;
  }

  function buildNodeItem(node) {
    const hasChildren = Array.isArray(node.children) && node.children.length > 0;
    const hasHref = typeof node.href === 'string' && node.href.length > 0;

    if (!hasChildren) {
      const row = createEl('div', 'bb-node-row');
      const link = createEl(hasHref ? 'a' : 'div', 'bb-node-trigger');
      if (hasHref) {
        link.href = node.href;
        if (node.target) link.target = node.target;
      }
      link.appendChild(createEl('span', 'bb-node-text', node.title));
      row.appendChild(link);
      return row;
    }

    const group = createEl('div', 'bb-node-group');
    const row = createEl('div', 'bb-node-row');

    const toggleBtn = createEl('button', 'bb-node-trigger');
    toggleBtn.type = 'button';
    toggleBtn.setAttribute('aria-expanded', 'false');
    toggleBtn.appendChild(createEl('span', 'bb-node-text', node.title));

    const chevron = createEl('span', 'bb-chevron');
    chevron.innerHTML = ICONS.chevron;
    toggleBtn.appendChild(chevron);

    const handleToggle = () => {
      const nextExpanded = !group.classList.contains('is-expanded');
      group.classList.toggle('is-expanded', nextExpanded);
      toggleBtn.setAttribute('aria-expanded', String(nextExpanded));
    };
    toggleBtn.addEventListener('click', handleToggle);
    row.appendChild(toggleBtn);

    if (hasHref) {
      const goLink = createEl('a', 'bb-node-go');
      goLink.href = node.href;
      if (node.target) goLink.target = node.target;
      goLink.title = 'Open ' + node.title;
      goLink.setAttribute('aria-label', 'Open ' + node.title);
      goLink.innerHTML = ICONS.external;
      row.appendChild(goLink);
    }

    group.appendChild(row);

    const sublist = createEl('div', 'bb-sublist');
    node.children.forEach((child) => {
      sublist.appendChild(buildNodeItem(child));
    });
    group.appendChild(sublist);

    return group;
  }

  function buildNewPopover(newItems) {
    const flyout = createEl('div', 'bb-flyout bb-flyout--compact');
    flyout.setAttribute('role', 'dialog');
    flyout.setAttribute('aria-label', 'Create New');

    const body = createEl('div', 'bb-scroll-body');
    body.appendChild(createEl('div', 'bb-section-title', 'Create New'));

    newItems.forEach((item) => {
      const row = createEl('div', 'bb-node-row');
      const link = createEl('a', 'bb-node-trigger');
      link.href = item.href;
      link.appendChild(createEl('span', 'bb-node-text', item.title));
      row.appendChild(link);
      body.appendChild(row);
    });

    flyout.appendChild(body);
    return flyout;
  }

  function buildMyMenuFlyout(data) {
    const flyout = createEl('div', 'bb-flyout');
    flyout.setAttribute('role', 'dialog');
    flyout.setAttribute('aria-label', 'My Menu');

    const header = createEl('div', 'bb-flyout-header');
    const identity = createEl('a', 'bb-identity');
    identity.href = data.user.profileUrl;

    const avatar = createEl('img', 'bb-header-avatar');
    avatar.src = data.user.avatarUrl;
    avatar.alt = data.user.displayName;
    identity.appendChild(avatar);

    const meta = createEl('div', 'bb-identity-meta');
    meta.appendChild(createEl('div', 'bb-display-name', data.user.displayName));
    meta.appendChild(createEl('div', 'bb-subtitle', data.user.subtitle));
    identity.appendChild(meta);
    header.appendChild(identity);
    flyout.appendChild(header);

    const quickStrip = createEl('div', 'bb-quick-strip');
    const compassLink = createEl('a', 'bb-pill-link bb-pill-link--primary');
    compassLink.href = data.urls.compass;
    compassLink.innerHTML = ICONS.compass + '<span>My Compass</span>';
    quickStrip.appendChild(compassLink);

    const wpAdminLink = createEl('a', 'bb-pill-link');
    wpAdminLink.href = data.urls.wpAdmin;
    wpAdminLink.innerHTML = ICONS.wp + '<span>WP Admin</span>';
    quickStrip.appendChild(wpAdminLink);
    flyout.appendChild(quickStrip);

    const body = createEl('div', 'bb-scroll-body');
    body.appendChild(createEl('div', 'bb-section-title', 'WP Admin Bar'));

    const nodes = Array.isArray(data.nodes) ? data.nodes : [];
    if (nodes.length === 0) {
      body.appendChild(createEl('div', 'bb-empty', 'No admin bar items found.'));
    } else {
      nodes.forEach((node) => {
        body.appendChild(buildNodeItem(node));
      });
    }
    flyout.appendChild(body);

    const footer = createEl('div', 'bb-footer');
    const switchBtn = createEl('button', 'bb-footer-btn');
    switchBtn.type = 'button';
    switchBtn.innerHTML = ICONS.switchBar + '<span>Native WP Bar</span>';

    const handleSwitchToNative = async () => {
      if (!data.canManageOptions) {
        window.location.href = data.urls.nativeBarUrl;
        return;
      }

      switchBtn.disabled = true;
      const bodyParams = new URLSearchParams();
      bodyParams.set('action', 'blackbox_toggle_frontend_bar');
      bodyParams.set('nonce', data.nonce);
      bodyParams.set('enable', '0');

      try {
        const res = await fetch(data.ajaxUrl, {
          method: 'POST',
          credentials: 'same-origin',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: bodyParams.toString(),
        });
        if (res.ok) {
          window.location.reload();
          return;
        }
      } catch {
        // Fallback to URL escape hatch
      }
      window.location.href = data.urls.nativeBarUrl;
    };

    switchBtn.addEventListener('click', handleSwitchToNative);
    footer.appendChild(switchBtn);

    const logoutLink = createEl('a', 'bb-footer-btn bb-footer-btn--danger');
    logoutLink.href = data.user.logoutUrl;
    logoutLink.innerHTML = ICONS.logout + '<span>Log Out</span>';
    footer.appendChild(logoutLink);

    flyout.appendChild(footer);
    return flyout;
  }

  function mount() {
    const root = document.getElementById(ROOT_ID);
    const data = parsePayload();
    if (!root || !data || !data.user) return;

    const shadow = root.attachShadow({ mode: 'open' });
    const styleEl = document.createElement('style');
    styleEl.textContent = STYLES;
    shadow.appendChild(styleEl);

    const backdrop = createEl('div', 'bb-backdrop');
    shadow.appendChild(backdrop);

    const hasNewItems = Array.isArray(data.newItems) && data.newItems.length > 0;
    const newPopover = hasNewItems ? buildNewPopover(data.newItems) : null;
    if (newPopover) shadow.appendChild(newPopover);

    const menuFlyout = buildMyMenuFlyout(data);
    shadow.appendChild(menuFlyout);

    const nav = createEl('nav', 'bb-bottom-nav');
    nav.setAttribute('aria-label', 'My Compass Navigation');
    const navInner = createEl('div', 'bb-nav-inner');

    const createLinkSlot = (href, iconHtml, label) => {
      const slot = createEl('div', 'bb-slot');
      const link = createEl('a', 'bb-nav-btn');
      link.href = href;
      link.setAttribute('aria-label', label);
      const iconWrap = createEl('span', 'bb-nav-icon');
      iconWrap.innerHTML = iconHtml;
      link.appendChild(iconWrap);
      link.appendChild(createEl('span', 'bb-nav-label', label));
      slot.appendChild(link);
      return slot;
    };

    // Slot 1: My Compass
    navInner.appendChild(createLinkSlot(data.urls.compass, ICONS.compass, 'My Compass'));

    // Slot 2: Contextual Edit / Customize / WP Admin
    const editHref = data.urls.editUrl || data.urls.customizeUrl || data.urls.wpAdmin;
    const editLabel = data.urls.editUrl
      ? data.urls.editLabel
      : (data.urls.customizeUrl ? 'Customize' : 'WP Admin');
    navInner.appendChild(createLinkSlot(editHref, ICONS.edit, editLabel));

    // Slot 3: + New
    let newBtn = null;
    if (hasNewItems) {
      const newSlot = createEl('div', 'bb-slot');
      newBtn = createEl('button', 'bb-nav-btn');
      newBtn.type = 'button';
      newBtn.setAttribute('aria-label', 'Create New');
      newBtn.setAttribute('aria-expanded', 'false');
      const iconWrap = createEl('span', 'bb-nav-icon');
      iconWrap.innerHTML = ICONS.plus;
      newBtn.appendChild(iconWrap);
      newBtn.appendChild(createEl('span', 'bb-nav-label', 'New'));
      newSlot.appendChild(newBtn);
      navInner.appendChild(newSlot);
    }

    // Slot 4: My Menu (avatar + flyout)
    const menuSlot = createEl('div', 'bb-slot');
    const menuBtn = createEl('button', 'bb-nav-btn');
    menuBtn.type = 'button';
    menuBtn.setAttribute('aria-label', 'My Menu');
    menuBtn.setAttribute('aria-expanded', 'false');

    const avatarWrap = createEl('span', 'bb-nav-icon');
    const navAvatar = createEl('img', 'bb-nav-avatar');
    navAvatar.src = data.user.avatarUrl;
    navAvatar.alt = data.user.displayName;
    avatarWrap.appendChild(navAvatar);
    menuBtn.appendChild(avatarWrap);
    menuBtn.appendChild(createEl('span', 'bb-nav-label', 'My Menu'));
    menuSlot.appendChild(menuBtn);
    navInner.appendChild(menuSlot);

    nav.appendChild(navInner);
    shadow.appendChild(nav);

    let activePanel = null;

    const setActivePanel = (panelName) => {
      activePanel = activePanel === panelName ? null : panelName;

      const isMenuOpen = activePanel === 'menu';
      const isNewOpen = activePanel === 'new';
      const hasOpenPanel = isMenuOpen || isNewOpen;

      backdrop.classList.toggle('is-open', hasOpenPanel);
      menuFlyout.classList.toggle('is-open', isMenuOpen);
      menuBtn.classList.toggle('is-active', isMenuOpen);
      menuBtn.setAttribute('aria-expanded', String(isMenuOpen));

      if (newPopover && newBtn) {
        newPopover.classList.toggle('is-open', isNewOpen);
        newBtn.classList.toggle('is-active', isNewOpen);
        newBtn.setAttribute('aria-expanded', String(isNewOpen));
      }
    };

    const closeAll = () => {
      if (activePanel !== null) {
        setActivePanel(null);
      }
    };

    menuBtn.addEventListener('click', () => setActivePanel('menu'));
    if (newBtn) {
      newBtn.addEventListener('click', () => setActivePanel('new'));
    }
    backdrop.addEventListener('click', closeAll);
    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') {
        closeAll();
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mount);
  } else {
    mount();
  }
})();
