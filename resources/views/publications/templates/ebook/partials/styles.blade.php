{{-- Shared chrome for standalone ebook product pages. Per-book look comes entirely from the
     --pub-color-primary / --pub-color-accent / --pub-font-primary / --pub-font-secondary
     custom properties set by the including template (from publication_meta) — this stylesheet
     never hardcodes a book-specific value. --}}
<style>
    .pub-book { background: var(--pub-color-primary); color: #fff; font-family: var(--pub-font-primary); margin: 0; }
    .pub-nav { display: flex; align-items: center; justify-content: space-between; padding: 20px 32px; }
    .pub-nav-title { font-family: var(--pub-font-secondary); font-size: 18px; font-weight: 700; }
    .pub-label { font-family: var(--pub-font-primary); font-size: 12px; text-transform: uppercase; letter-spacing: 0.12em; opacity: 0.6; margin: 0 0 12px; }
    .pub-heading { font-family: var(--pub-font-secondary); font-weight: 700; line-height: 1.1; margin: 0; }
    .pub-prose { font-family: var(--pub-font-primary); line-height: 1.7; opacity: 0.85; }
    .pub-accent { color: var(--pub-color-accent); }
    .pub-card { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 12px; padding: 28px; }
    .pub-quote { border-left: 3px solid var(--pub-color-accent); padding-left: 20px; margin: 32px 0; font-style: italic; }
    .pub-divider { border: none; border-top: 1px solid rgba(255, 255, 255, 0.12); }
    .pub-btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 28px; border-radius: 6px; font-family: var(--pub-font-primary); font-weight: 600; font-size: 14px; text-decoration: none; cursor: pointer; transition: opacity 0.2s; }
    .pub-btn:hover { opacity: 0.8; }
    .pub-btn-sm { padding: 8px 18px; font-size: 13px; }
    .pub-btn-lg { padding: 16px 36px; font-size: 16px; }
    .pub-btn-primary { background: var(--pub-color-accent); color: var(--pub-color-primary); border: none; }
    .pub-btn-outline { background: transparent; color: var(--pub-color-accent); border: 1.5px solid var(--pub-color-accent); }
    .pub-btn-coming-soon { background: rgba(255, 255, 255, 0.08); color: rgba(255, 255, 255, 0.5); border: none; cursor: default; }
    .pub-badge-dot { display: inline-block; width: 8px; height: 8px; border-radius: 999px; background: var(--pub-color-accent); margin-right: 8px; }
    .pub-footer { text-align: center; padding: 48px 32px; opacity: 0.5; font-family: var(--pub-font-primary); font-size: 13px; }
    .pub-footer a { color: inherit; }
    .pub-cover-img { width: 100%; max-width: 320px; box-shadow: 0 24px 60px rgba(0, 0, 0, 0.5); }
</style>
