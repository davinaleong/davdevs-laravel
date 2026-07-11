import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import EasyMDE from 'easymde';

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
