export default function Group({ children, variant = 'vertical', className = '' }) {
  const base = variant === 'horizontal' ? 'flex items-center gap-2' : 'flow';

  return <div className={`${base} ${className}`.trim()}>{children}</div>;
}
