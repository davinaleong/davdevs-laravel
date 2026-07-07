const VARIANT_CLASS = {
  primary: 'tool-btn-primary',
  secondary: 'tool-btn-secondary',
  danger: 'tool-btn-danger',
  success: 'tool-btn-success',
  gray: 'tool-btn-gray',
  icon: 'tool-btn-icon',
  list: 'tool-btn-list',
};

export default function Button({
  children,
  type = 'button',
  variant = 'primary',
  disabled = false,
  className = '',
  ...props
}) {
  const classes = `tool-btn ${VARIANT_CLASS[variant] ?? VARIANT_CLASS.primary} ${className}`.trim();

  return (
    <button type={type} disabled={disabled} className={classes} {...props}>
      {children}
    </button>
  );
}
