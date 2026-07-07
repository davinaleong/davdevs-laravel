export default function ToolPanel({
  children,
  title,
  description,
  icon: Icon,
  className = '',
  maxWidth = 'max-w-6xl',
}) {
  return (
    <article className={`tool-panel ${maxWidth} mx-auto ${className}`}>
      <header className="flex items-center gap-3 mb-8">
        <div className="tool-panel-icon">
          <Icon size={24} />
        </div>
        <div>
          <h2 className="tool-panel-title">{title}</h2>
          <p className="tool-panel-description">{description}</p>
        </div>
      </header>

      <div className="space-y-6">{children}</div>
    </article>
  );
}
