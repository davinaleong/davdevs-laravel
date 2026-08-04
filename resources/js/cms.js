import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import EasyMDE from 'easymde';
import TomSelect from 'tom-select';
import { createIcons, icons } from 'lucide';

createIcons({icons});

Alpine.plugin(intersect);
window.Alpine = Alpine;

// CMS theme toggle
document.addEventListener('DOMContentLoaded', () => {
    const theme = localStorage.getItem('cms_theme') || 'light';
    document.documentElement.setAttribute('data-theme', theme);
});

Alpine.start();

// Markdown editor — attach EasyMDE to any textarea with data-markdown-editor
// Instances are stored globally so Alpine.js AI helpers can call .value()
window._easyMdeInstances = {};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('textarea[data-markdown-editor]').forEach((el) => {
        const instance = new EasyMDE({
            element: el,
            spellChecker: false,
            autofocus: false,
            toolbar: [
                'bold', 'italic', 'heading', '|',
                'quote', 'unordered-list', 'ordered-list', '|',
                'link', 'image', 'code', 'table', '|',
                'preview', 'side-by-side', 'fullscreen', '|',
                'guide',
            ],
            previewClass: ['editor-preview', 'prose'],
            minHeight: '300px',
        });
        if (el.id) window._easyMdeInstances[el.id] = instance;
    });
});

// Searchable dropdowns — Tom Select with sort bar and lazy loading
// Sort bar appears for selects with > SORT_THRESHOLD non-empty options.
// Lazy loading activates for selects with > LAZY_THRESHOLD non-empty options.
document.addEventListener('DOMContentLoaded', () => {
    const SORT_THRESHOLD = 10;
    const LAZY_THRESHOLD = 50;
    const PAGE_SIZE      = 50;

    document.querySelectorAll('select:not([x-model]):not([data-no-ts])').forEach((el) => {
        const nonEmpty = el.querySelectorAll('option[value]:not([value=""])').length;
        const showSort = nonEmpty > SORT_THRESHOLD;
        const lazyLoad = nonEmpty > LAZY_THRESHOLD;

        const ts = new TomSelect(el, {
            create:      false,
            maxOptions:  lazyLoad ? PAGE_SIZE : null,
            selectOnTab: true,
        });

        // Snapshot all options at init, split into blank (placeholder) vs. value-bearing
        const blankOpts = [];
        const valueOpts = [];
        Object.values(ts.options).forEach(o =>
            (o.value === '' ? blankOpts : valueOpts).push({ value: o.value, text: o.text })
        );

        let sortMode = null;  // null = server order
        let loaded   = lazyLoad ? Math.min(PAGE_SIZE, valueOpts.length) : valueOpts.length;

        const applyMode = (mode, arr) => {
            const c = arr.slice();
            if (mode === 'alpha-asc') return c.sort((a, b) => a.text.localeCompare(b.text));
            if (mode === 'alpha-dsc') return c.sort((a, b) => b.text.localeCompare(a.text));
            if (mode === 'newest')    return c.sort((a, b) => { const [na,nb]=[+a.value,+b.value]; return (na&&nb) ? nb-na : b.text.localeCompare(a.text); });
            if (mode === 'oldest')    return c.sort((a, b) => { const [na,nb]=[+a.value,+b.value]; return (na&&nb) ? na-nb : a.text.localeCompare(b.text); });
            return c;
        };

        const rebuild = () => {
            const savedVal = ts.getValue();
            const sorted   = [...blankOpts, ...applyMode(sortMode, valueOpts)];
            const slice    = sorted.slice(0, blankOpts.length + loaded);

            // Always keep the currently-selected option in the visible slice
            if (savedVal && !slice.some(o => String(o.value) === String(savedVal))) {
                const found = sorted.find(o => String(o.value) === String(savedVal));
                if (found) slice.push(found);
            }

            ts.clearOptions();
            slice.forEach(o => ts.addOption(o));
            ts.settings.maxOptions = slice.length + 1;  // prevent internal re-truncation
            ts.refreshOptions(false);
            if (savedVal) ts.setValue(savedVal, true);
        };

        if (lazyLoad) rebuild();  // initial setup for large selects

        // When user types in a lazy-loaded select, load ALL matching options from
        // the full valueOpts snapshot so search isn't limited to the current page.
        if (lazyLoad) {
            let searching = false;
            ts.on('type', (query) => {
                if (query && !searching) {
                    searching = true;
                    const savedVal = ts.getValue();
                    const all = [...blankOpts, ...applyMode(sortMode, valueOpts)];
                    ts.clearOptions();
                    all.forEach(o => ts.addOption(o));
                    ts.settings.maxOptions = all.length + 1;
                    ts.refreshOptions(false);
                    if (savedVal) ts.setValue(savedVal, true);
                } else if (!query && searching) {
                    searching = false;
                    loaded = Math.min(PAGE_SIZE, valueOpts.length);
                    rebuild();
                }
            });
        }

        ts.on('dropdown_open', (dropdown) => {
            // Inject sort bar once per dropdown element
            if (showSort && !dropdown.querySelector('.ts-sort-bar')) {
                const bar = document.createElement('div');
                bar.className = 'ts-sort-bar';
                [['alpha-asc','A→Z'],['alpha-dsc','Z→A'],['newest','↓ Newest'],['oldest','↑ Oldest']]
                    .forEach(([mode, label]) => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.textContent = label;
                        btn.dataset.tsSort = mode;
                        if (sortMode === mode) btn.classList.add('active');
                        btn.addEventListener('mousedown', e => {
                            e.preventDefault();  // keep dropdown open
                            sortMode = sortMode === mode ? null : mode;  // toggle off if re-clicked
                            loaded   = lazyLoad ? Math.min(PAGE_SIZE, valueOpts.length) : valueOpts.length;
                            bar.querySelectorAll('[data-ts-sort]').forEach(b =>
                                b.classList.toggle('active', b.dataset.tsSort === sortMode)
                            );
                            rebuild();
                        });
                        bar.appendChild(btn);
                    });
                dropdown.insertBefore(bar, dropdown.querySelector('.ts-dropdown-content'));
            }

            // Scroll-to-load-more (attached once per content element)
            if (lazyLoad) {
                const content = dropdown.querySelector('.ts-dropdown-content');
                if (content && !content._tsLazy) {
                    content._tsLazy = true;
                    content.addEventListener('scroll', () => {
                        if (content.scrollTop + content.clientHeight < content.scrollHeight - 40) return;
                        if (loaded >= valueOpts.length) return;
                        const prevScroll = content.scrollTop;
                        loaded = Math.min(loaded + PAGE_SIZE, valueOpts.length);
                        rebuild();
                        requestAnimationFrame(() => { content.scrollTop = prevScroll; });
                    });
                }
            }
        });
    });
});

