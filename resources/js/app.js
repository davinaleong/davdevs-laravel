import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import { createIcons, icons } from 'lucide';

createIcons({icons});

Alpine.plugin(intersect);
window.Alpine = Alpine;

// Frontend theme
document.addEventListener('DOMContentLoaded', () => {
    const stored = localStorage.getItem('theme');
    const theme  = stored ?? (window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
    document.documentElement.setAttribute('data-theme', theme);
});

Alpine.start();
