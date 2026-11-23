(() => {
  'use strict';

  const ROOT_ID = 'blackbox-compass-bar-root';
  const DATA_ID = 'blackbox-compass-bar-data';

  const ICONS = {
    compass: '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76" fill="currentColor" opacity="0.25"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>',
    apps: '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/></svg>',
    edit: '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>',
    plus: '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>',
    chevron: '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>',
    external: '<svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>',
    wp: '<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>',
    logout: '<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>',
    switchBar: '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="6"/><circle cx="16" cy="12" r="3" fill="currentColor"/></svg>',
    tools: '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
    bell: '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>',
    grid: '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>',
  };

  const STYLES = `
    :host { all: initial; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; color: #e6f1fc; box-sizing: border-box; }
    *, *::before, *::after { box-sizing: border-box; }
    .bb-backdrop { position: fixed; inset: 0; bottom: calc(64px + env(safe-area-inset-bottom, 0px)); background: rgba(4, 9, 18, 0.35); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); z-index: 999990; opacity: 0; pointer-events: none; transition: opacity 0.2s ease; }
    .bb-backdrop.is-open { opacity: 1; pointer-events: auto; }
    .bb-bottom-nav { position: fixed; left: 0; right: 0; bottom: 0; height: calc(64px + env(safe-area-inset-bottom, 0px)); padding-bottom: env(safe-area-inset-bottom, 0px); display: flex; align-items: stretch; justify-content: center; background: rgba(8, 16, 30, 0.85); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); box-shadow: 0 -6px 24px rgba(0, 0, 0, 0.38); z-index: 999995; border-top: 1px solid transparent; user-select: none; }
    .bb-bottom-nav::before { content: ""; position: absolute; left: 0; right: 0; top: 0; height: 1px; background: linear-gradient(90deg, transparent 0%, rgba(98, 201, 255, 0.75) 30%, rgba(167, 120, 255, 0.65) 70%, transparent 100%); box-shadow: 0 0 10px rgba(98, 201, 255, 0.45); pointer-events: none; }
    .bb-nav-inner { display: flex; width: 100%; max-width: 680px; height: 64px; }
    .bb-slot { flex: 1 1 0; min-width: 0; height: 100%; display: flex; align-items: center; justify-content: center; }
    .bb-nav-btn { appearance: none; border: 0; background: transparent; color: rgba(210, 228, 246, 0.62); width: 100%; height: 100%; padding: 8px 4px 6px; display: flex; flex-direction: column; align-items: center; justify-content: flex-start; gap: 3px; cursor: pointer; text-decoration: none; transition: color 0.2s ease, background-color 0.2s ease, transform 0.15s ease; border-radius: 8px; margin: 0 2px; position: relative; }
    .bb-nav-btn:hover, .bb-nav-btn.is-active { color: #62c9ff; background: rgba(98, 201, 255, 0.08); }
    .bb-nav-btn:hover .bb-nav-icon, .bb-nav-btn.is-active .bb-nav-icon { color: #62c9ff; filter: drop-shadow(0 0 6px rgba(98, 201, 255, 0.35)); }
    .bb-nav-icon { height: 20px; min-height: 20px; display: inline-flex; align-items: center; justify-content: center; color: #62c9ff; transition: color 0.2s ease, filter 0.2s ease, transform 0.2s ease; }
    .bb-nav-avatar { width: 22px; height: 22px; border-radius: 50%; object-fit: cover; border: 1.5px solid rgba(255, 255, 255, 0.25); transition: border-color 0.2s ease, box-shadow 0.2s ease; }
    .bb-nav-btn:hover .bb-nav-avatar, .bb-nav-btn.is-active .bb-nav-avatar { border-color: rgba(98, 201, 255, 0.85); box-shadow: 0 0 8px rgba(98, 201, 255, 0.4); }
    .bb-nav-label { font-size: 11px; font-weight: 500; line-height: 1.15; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%; color: inherit; }
    .bb-nav-btn--youmeos { overflow: visible; }
    .bb-pi-wrap { display: inline-flex; align-items: center; justify-content: center; font-family: monospace; user-select: none; line-height: 1; height: 20px; }
    .bb-pi-symbol { font-size: 19px; font-weight: 700; display: inline-block; color: rgba(98, 201, 255, 0.85); transition: transform 0.3s ease, background 0.3s ease, color 0.3s ease; }
    .bb-pi-text { max-width: 0; overflow: hidden; opacity: 0; white-space: nowrap; transition: max-width 0.3s ease, opacity 0.3s ease; font-size: 13px; font-weight: 600; display: inline-block; margin-left: 2px; color: rgba(98, 201, 255, 0.85); }
    .bb-nav-btn--youmeos:hover .bb-pi-symbol { transform: rotate(180deg); }
    .bb-nav-btn--youmeos:hover .bb-pi-symbol, .bb-nav-btn--youmeos:hover .bb-pi-text { background: linear-gradient(135deg, #62c9ff 0%, #a78bfa 100%); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; color: transparent; }
    .bb-nav-btn--youmeos:hover .bb-pi-text { max-width: 60px; opacity: 1; }
    .bb-nav-btn--youmeos::after { content: attr(data-tooltip); position: absolute; bottom: calc(100% + 8px); left: 50%; transform: translateX(-50%) translateY(4px); opacity: 0; pointer-events: none; background: rgba(10, 15, 25, 0.92); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); color: #e2f2ff; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 500; white-space: nowrap; border: 1px solid rgba(98, 201, 255, 0.25); box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3), 0 0 12px rgba(98, 201, 255, 0.15); transition: opacity 0.25s ease, transform 0.25s ease; z-index: 10; }
    .bb-nav-btn--youmeos:hover::after { opacity: 1; transform: translateX(-50%) translateY(0); }
    .bb-flyout { position: fixed; right: 14px; bottom: calc(74px + env(safe-area-inset-bottom, 0px)); width: min(360px, calc(100vw - 28px)); max-height: min(74vh, 620px); display: flex; flex-direction: column; border-radius: 14px; border: 1px solid rgba(255, 255, 255, 0.12); border-top-color: transparent; overflow: hidden; background: radial-gradient(ellipse 70% 42% at 32% 100%, rgba(98, 201, 255, 0.22) 0%, transparent 72%), radial-gradient(ellipse 70% 42% at 68% 100%, rgba(167, 120, 255, 0.2) 0%, transparent 72%), linear-gradient(180deg, rgba(16, 30, 52, 0.92) 0%, rgba(10, 18, 34, 0.94) 55%, rgba(14, 28, 48, 0.92) 100%); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); box-shadow: 0 -12px 36px rgba(0, 0, 0, 0.4), 0 16px 40px rgba(0, 0, 0, 0.55), inset 0 1px 0 rgba(255, 255, 255, 0.08); z-index: 999994; opacity: 0; transform: translateY(10px) scale(0.98); pointer-events: none; transition: opacity 0.2s ease, transform 0.22s cubic-bezier(0.2, 0, 0, 1); }
    .bb-flyout::before { content: ""; position: absolute; left: 0; right: 0; top: 0; height: 1px; background: linear-gradient(90deg, rgba(255, 255, 255, 0.14) 0%, rgba(98, 201, 255, 0.75) 25%, rgba(167, 120, 255, 0.65) 75%, rgba(255, 255, 255, 0.14) 100%); box-shadow: 0 0 10px rgba(98, 201, 255, 0.4); pointer-events: none; z-index: 2; }
    .bb-flyout.is-open { opacity: 1; transform: translateY(0) scale(1); pointer-events: auto; }
    .bb-flyout--apps { width: min(340px, calc(100vw - 28px)); right: auto; left: 50%; transform: translate(-50%, 10px) scale(0.98); }
    .bb-flyout--apps.is-open { transform: translate(-50%, 0) scale(1); }
    .bb-flyout-header { display: flex; align-items: center; gap: 10px; padding: 12px 14px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); }
    .bb-apps-header { display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); }
    .bb-apps-title-stack { display: flex; flex-direction: column; gap: 2px; }
    .bb-apps-title { font-size: 14px; font-weight: 700; color: #f0f7ff; }
    .bb-apps-subtitle { font-size: 11px; color: rgba(210, 228, 246, 0.6); }
    .bb-identity { display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0; text-decoration: none; color: inherit; padding: 4px 6px; border-radius: 10px; transition: background-color 0.2s ease; }
    .bb-identity:hover { background: rgba(255, 255, 255, 0.06); }
    .bb-header-avatar { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 1px solid rgba(98, 201, 255, 0.4); flex-shrink: 0; }
    .bb-identity-meta { min-width: 0; }
    .bb-display-name { font-size: 13.5px; font-weight: 600; color: #f0f7ff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .bb-subtitle { font-size: 11px; color: rgba(210, 228, 246, 0.62); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .bb-quick-strip { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; padding: 10px 14px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); }
    .bb-pill-link { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px 10px; border-radius: 10px; font-size: 12px; font-weight: 600; text-decoration: none; color: #e6f1fc; background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.1); transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease; }
    .bb-pill-link:hover { background: rgba(98, 201, 255, 0.14); border-color: rgba(98, 201, 255, 0.45); color: #62c9ff; }
    .bb-pill-link--primary { background: rgba(98, 201, 255, 0.16); border-color: rgba(98, 201, 255, 0.45); color: #62c9ff; }
    .bb-pill-link--primary:hover { background: rgba(98, 201, 255, 0.26); }
    .bb-scroll-body { overflow-y: auto; padding: 8px 10px; flex: 1; scrollbar-width: thin; scrollbar-color: rgba(98, 201, 255, 0.3) transparent; }
    .bb-category-block { margin-bottom: 10px; }
    .bb-category-header { display: flex; align-items: center; gap: 6px; font-size: 10px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: rgba(98, 201, 255, 0.75); padding: 6px 8px 4px; border-bottom: 1px solid rgba(98, 201, 255, 0.15); margin-bottom: 4px; }
    .bb-category-icon { display: inline-flex; align-items: center; justify-content: center; color: #62c9ff; }
    .bb-create-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; padding: 4px 6px; }
    .bb-create-pill { display: flex; align-items: center; gap: 6px; padding: 6px 8px; border-radius: 8px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.08); color: #dbeaf9; text-decoration: none; font-size: 12px; font-weight: 500; transition: background-color 0.16s ease, color 0.16s ease, border-color 0.16s ease; }
    .bb-create-pill:hover { background: rgba(98, 201, 255, 0.14); border-color: rgba(98, 201, 255, 0.35); color: #62c9ff; }
    .bb-create-symbol { font-size: 13px; font-weight: 700; color: #62c9ff; line-height: 1; }
    .bb-node-row { display: flex; align-items: center; border-radius: 9px; transition: background-color 0.16s ease; }
    .bb-node-row:hover { background: rgba(255, 255, 255, 0.07); }
    .bb-node-trigger { appearance: none; border: 0; background: transparent; color: #dbeaf9; flex: 1; min-width: 0; display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 7px 8px; font-size: 12.5px; font-weight: 500; text-align: left; text-decoration: none; cursor: pointer; }
    .bb-node-trigger:hover { color: #62c9ff; }
    .bb-node-text { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .bb-node-go { display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; margin-right: 4px; border-radius: 6px; color: rgba(210, 228, 246, 0.55); text-decoration: none; flex-shrink: 0; transition: color 0.16s ease, background-color 0.16s ease; }
    .bb-node-go:hover { color: #62c9ff; background: rgba(98, 201, 255, 0.14); }
    .bb-chevron { display: inline-flex; color: rgba(210, 228, 246, 0.5); transition: transform 0.2s ease, color 0.2s ease; flex-shrink: 0; }
    .bb-node-group.is-expanded > .bb-node-row .bb-chevron { transform: rotate(90deg); color: #62c9ff; }
    .bb-sublist { display: none; border-left: 1px solid rgba(98, 201, 255, 0.24); margin: 2px 0 4px 14px; padding-left: 6px; }
    .bb-node-group.is-expanded > .bb-sublist { display: block; }
    .bb-apps-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; padding: 12px 14px; }
    .bb-app-card { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; padding: 10px 6px; border-radius: 10px; background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08); text-decoration: none; color: #dbeaf9; transition: background-color 0.2s ease, border-color 0.2s ease, transform 0.15s ease; }
    .bb-app-card:hover { background: rgba(98, 201, 255, 0.14); border-color: rgba(98, 201, 255, 0.4); color: #62c9ff; transform: translateY(-2px); }
    .bb-app-avatar { width: 36px; height: 36px; border-radius: 9px; background: linear-gradient(135deg, rgba(98, 201, 255, 0.2) 0%, rgba(167, 120, 255, 0.2) 100%); border: 1px solid rgba(98, 201, 255, 0.35); display: flex; align-items: center; justify-content: center; font-size: 15px; font-weight: 700; color: #62c9ff; }
    .bb-app-name { font-size: 11px; font-weight: 500; text-align: center; max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .bb-footer { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 10px 14px; border-top: 1px solid rgba(255, 255, 255, 0.08); background: rgba(4, 10, 20, 0.35); }
    .bb-footer-btn { appearance: none; border: 0; background: transparent; color: rgba(210, 228, 246, 0.7); display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 500; padding: 5px 8px; border-radius: 8px; text-decoration: none; cursor: pointer; transition: color 0.16s ease, background-color 0.16s ease; }
    .bb-footer-btn:hover { color: #62c9ff; background: rgba(255, 255, 255, 0.06); }
    .bb-footer-btn--danger:hover { color: #ff7b8a; background: rgba(255, 100, 120, 0.12); }
    .bb-empty { font-size: 12px; color: rgba(210, 228, 246, 0.55); padding: 10px 8px; text-align: center; }
  `;

  function parsePayload() {
    const dataEl = document.getElementById(DATA_ID);
    return dataEl ? JSON.parse(dataEl.textContent || '{}') : null;
  }

  function createEl(tag, className, text) {
    const el = document.createElement(tag);
    const hasClass = Boolean(className);
    if (hasClass) el.className = className;
    const hasText = typeof text === 'string';
    if (hasText) el.textContent = text;
    return el;
  }

  function resolveEditDescriptor(urls) {
    const hasEditUrl = Boolean(urls && urls.editUrl);
    if (hasEditUrl) return { href: urls.editUrl, label: urls.editLabel || 'Edit' };
    const hasCustomizeUrl = Boolean(urls && urls.customizeUrl);
    if (hasCustomizeUrl) return { href: urls.customizeUrl, label: 'Customize' };
    return { href: urls ? urls.wpAdmin : '', label: 'WP Admin' };
  }

  function buildNodeItem(node, signal) {
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
      const isAlreadyExpanded = group.classList.contains('is-expanded');
      group.classList.toggle('is-expanded', !isAlreadyExpanded);
      toggleBtn.setAttribute('aria-expanded', String(!isAlreadyExpanded));
    };
    toggleBtn.addEventListener('click', handleToggle, { signal });
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
    node.children.forEach((child) => sublist.appendChild(buildNodeItem(child, signal)));
    group.appendChild(sublist);
    return group;
  }

  function buildAppsFlyout(data) {
    const flyout = createEl('div', 'bb-flyout bb-flyout--apps');
    flyout.setAttribute('role', 'dialog');
    flyout.setAttribute('aria-label', 'Compass Apps');
    const header = createEl('div', 'bb-apps-header');
    const titleStack = createEl('div', 'bb-apps-title-stack');
    titleStack.appendChild(createEl('span', 'bb-apps-title', 'Compass Apps'));
    titleStack.appendChild(createEl('span', 'bb-apps-subtitle', 'Ecosystem Launch Pad'));
    header.appendChild(titleStack);
    const hubLink = createEl('a', 'bb-pill-link bb-pill-link--primary');
    hubLink.href = data.urls.compass;
    hubLink.innerHTML = ICONS.compass + '<span>Hub</span>';
    header.appendChild(hubLink);
    flyout.appendChild(header);
    const body = createEl('div', 'bb-scroll-body');
    const hasApps = Array.isArray(data.apps) && data.apps.length > 0;
    const appsList = hasApps ? data.apps : [{ title: 'My Compass', url: data.urls.compass }, { title: 'WP Admin', url: data.urls.wpAdmin }];
    const grid = createEl('div', 'bb-apps-grid');
    appsList.forEach((app) => {
      const card = createEl('a', 'bb-app-card');
      card.href = app.url;
      const avatar = createEl('div', 'bb-app-avatar');
      avatar.textContent = (app.title || 'A').charAt(0).toUpperCase();
      card.appendChild(avatar);
      card.appendChild(createEl('span', 'bb-app-name', app.title));
      grid.appendChild(card);
    });
    body.appendChild(grid);
    flyout.appendChild(body);
    const footer = createEl('div', 'bb-footer');
    const fullLaunchLink = createEl('a', 'bb-footer-btn');
    fullLaunchLink.href = data.urls.apps || data.urls.compass;
    fullLaunchLink.innerHTML = ICONS.apps + '<span>Open Apps in Compass</span>';
    footer.appendChild(fullLaunchLink);
    flyout.appendChild(footer);
    return flyout;
  }

  function buildMyMenuFlyout(data, signal) {
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
    const hasCategories = Array.isArray(data.categories) && data.categories.length > 0;
    const categories = hasCategories ? data.categories : [{ id: 'general', title: 'WP Admin Bar', icon: 'chevron', nodes: data.nodes || [] }];
    categories.forEach((cat) => {
      const block = createEl('div', 'bb-category-block');
      const catHeader = createEl('div', 'bb-category-header');
      const iconSpan = createEl('span', 'bb-category-icon');
      iconSpan.innerHTML = ICONS[cat.icon] || ICONS.chevron;
      catHeader.appendChild(iconSpan);
      catHeader.appendChild(createEl('span', null, cat.title));
      block.appendChild(catHeader);
      const isCreateCategory = cat.id === 'create';
      if (isCreateCategory) {
        const createGrid = createEl('div', 'bb-create-grid');
        cat.nodes.forEach((item) => {
          const pill = createEl('a', 'bb-create-pill');
          pill.href = item.href;
          pill.appendChild(createEl('span', 'bb-create-symbol', '+'));
          pill.appendChild(createEl('span', null, item.title));
          createGrid.appendChild(pill);
        });
        block.appendChild(createGrid);
      } else {
        cat.nodes.forEach((node) => block.appendChild(buildNodeItem(node, signal)));
      }
      body.appendChild(block);
    });
    flyout.appendChild(body);
    const footer = createEl('div', 'bb-footer');
    const switchBtn = createEl('button', 'bb-footer-btn');
    switchBtn.type = 'button';
    switchBtn.innerHTML = ICONS.switchBar + '<span>Native WP Bar</span>';
    const handleSwitchToNative = async () => {
      const canManage = Boolean(data.canManageOptions);
      if (!canManage) {
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
        const isSuccess = Boolean(res.ok);
        if (isSuccess) {
          window.location.reload();
          return;
        }
      } catch {
        // chemx-allow: best-effort fallback to native bar URL redirection
      }
      window.location.href = data.urls.nativeBarUrl;
    };
    switchBtn.addEventListener('click', handleSwitchToNative, { signal });
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
    const isPayloadValid = Boolean(root && data && data.user);
    if (!isPayloadValid) return;
    const abortController = new AbortController();
    const { signal } = abortController;
    const shadow = root.attachShadow({ mode: 'open' });
    const styleEl = document.createElement('style');
    styleEl.textContent = STYLES;
    shadow.appendChild(styleEl);
    const backdrop = createEl('div', 'bb-backdrop');
    shadow.appendChild(backdrop);
    const appsFlyout = buildAppsFlyout(data);
    shadow.appendChild(appsFlyout);
    const menuFlyout = buildMyMenuFlyout(data, signal);
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
    const hasYoumeos = Boolean(data.canManageOptions && data.urls && data.urls.youmeosUrl);
    if (hasYoumeos) {
      const youmeosSlot = createEl('div', 'bb-slot');
      const youmeosBtn = createEl('a', 'bb-nav-btn bb-nav-btn--youmeos');
      youmeosBtn.href = data.urls.youmeosUrl;
      youmeosBtn.target = '_blank';
      youmeosBtn.setAttribute('aria-label', 'YouMeOS');
      youmeosBtn.setAttribute('data-tooltip', 'Discover a 4D browsing experience...');
      const piWrap = createEl('span', 'bb-nav-icon bb-pi-wrap');
      piWrap.appendChild(createEl('span', 'bb-pi-symbol', 'π'));
      piWrap.appendChild(createEl('span', 'bb-pi-text', 'meos'));
      youmeosBtn.appendChild(piWrap);
      youmeosBtn.appendChild(createEl('span', 'bb-nav-label', 'YouMeOS'));
      youmeosSlot.appendChild(youmeosBtn);
      navInner.appendChild(youmeosSlot);
    }
    navInner.appendChild(createLinkSlot(data.urls.compass, ICONS.compass, 'My Compass'));
    const appsSlot = createEl('div', 'bb-slot');
    const appsBtn = createEl('button', 'bb-nav-btn bb-nav-btn--apps');
    appsBtn.type = 'button';
    appsBtn.setAttribute('aria-label', 'Compass Apps');
    appsBtn.setAttribute('aria-expanded', 'false');
    const appsIconWrap = createEl('span', 'bb-nav-icon');
    appsIconWrap.innerHTML = ICONS.apps;
    appsBtn.appendChild(appsIconWrap);
    appsBtn.appendChild(createEl('span', 'bb-nav-label', 'Apps'));
    appsSlot.appendChild(appsBtn);
    navInner.appendChild(appsSlot);
    const editDescriptor = resolveEditDescriptor(data.urls);
    navInner.appendChild(createLinkSlot(editDescriptor.href, ICONS.edit, editDescriptor.label));
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
      const isAlreadyActive = activePanel === panelName;
      activePanel = isAlreadyActive ? null : panelName;
      const isMenuOpen = activePanel === 'menu';
      const isAppsOpen = activePanel === 'apps';
      const hasOpenPanel = isMenuOpen || isAppsOpen;
      backdrop.classList.toggle('is-open', hasOpenPanel);
      menuFlyout.classList.toggle('is-open', isMenuOpen);
      menuBtn.classList.toggle('is-active', isMenuOpen);
      menuBtn.setAttribute('aria-expanded', String(isMenuOpen));
      appsFlyout.classList.toggle('is-open', isAppsOpen);
      appsBtn.classList.toggle('is-active', isAppsOpen);
      appsBtn.setAttribute('aria-expanded', String(isAppsOpen));
    };
    const closeAll = () => {
      const hasActivePanel = activePanel !== null;
      if (hasActivePanel) setActivePanel(null);
    };
    appsBtn.addEventListener('click', () => setActivePanel('apps'), { signal });
    menuBtn.addEventListener('click', () => setActivePanel('menu'), { signal });
    backdrop.addEventListener('click', closeAll, { signal });
    document.addEventListener('keydown', (event) => {
      const isEscapeKey = event.key === 'Escape';
      if (isEscapeKey) closeAll();
    }, { signal });
  }

  const isDocLoading = document.readyState === 'loading';
  if (isDocLoading) {
    document.addEventListener('DOMContentLoaded', mount, { once: true });
  } else {
    mount();
  }
})();
