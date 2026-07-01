import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';

Alpine.plugin(intersect);
window.Alpine = Alpine;

// CMS theme toggle
document.addEventListener('DOMContentLoaded', () => {
    const theme = localStorage.getItem('cms_theme') || 'light';
    document.documentElement.setAttribute('data-theme', theme);
});

Alpine.start();
