export default function Tab({ children, isActive = false, onClick, className = '', disabled = false }) {
  const baseStyle = {
    border: '1px solid',
    borderRadius: '2px',
    padding: '0.5rem 0.75rem',
    background: 'var(--bg-page)',
    borderColor: isActive ? 'var(--border-default)' : 'var(--accent)',
    color: isActive ? 'var(--text-primary)' : 'var(--accent)',
    opacity: disabled ? 0.5 : 1,
    cursor: disabled ? 'not-allowed' : 'pointer',
  };

  return (
    <button
      type="button"
      className={className}
      style={baseStyle}
      onClick={!disabled ? onClick : undefined}
      disabled={disabled}
    >
      {children}
    </button>
  );
}
