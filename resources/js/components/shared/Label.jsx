export default function Label({ children, required = false, className = '', ...props }) {
  return (
    <label className={`tool-label ${className}`.trim()} {...props}>
      {children}
      {required && <span className="tool-label-required">*</span>}
    </label>
  );
}
