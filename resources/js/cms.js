import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import EasyMDE from 'easymde';
import TomSelect from 'tom-select';

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

// Searchable dropdowns — Tom Select on all <select> except Alpine x-model bindings
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('select:not([x-model]):not([data-no-ts])').forEach((el) => {
        new TomSelect(el, {
            create: false,
            maxOptions: null,
            selectOnTab: true,
            // Preserve server-side ordering; don't re-sort client-side
            sortField: false,
        });
    });
});
