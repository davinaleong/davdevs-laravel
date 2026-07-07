export default function Panel({ children, className = '', overwrite = false }) {
  const classes = overwrite ? className : `tool-panel ${className}`.trim();

  return <div className={classes}>{children}</div>;
}
