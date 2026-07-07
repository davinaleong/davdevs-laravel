import { createRoot } from 'react-dom/client';

// resources/js/components/*.jsx only (not shared/*, those are internal helpers, not attachable tools)
const modules = import.meta.glob('./components/*.jsx');

function toKebabCase(str) {
  return str
    .replace(/([a-z0-9])([A-Z])/g, '$1-$2')
    .replace(/([A-Z]+)([A-Z][a-z])/g, '$1-$2')
    .toLowerCase();
}

function resolveModulePath(slug) {
  return Object.keys(modules).find((path) => {
    const stem = path.replace('./components/', '').replace(/\.jsx$/, '');
    return toKebabCase(stem) === slug;
  });
}

async function mount(el) {
  const slug = el.dataset.reactComponent;
  const modulePath = resolveModulePath(slug);

  if (!modulePath) {
    console.error(`[tool-loader] No component found for slug "${slug}"`);
    return;
  }

  const module = await modules[modulePath]();
  const Component = module.default;

  createRoot(el).render(<Component />);
}

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-react-component]').forEach(mount);
});
