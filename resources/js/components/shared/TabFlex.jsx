export default function TabFlex({ children, className = '' }) {
  return <div className={`flex gap-4 overflow-x-auto ${className}`.trim()}>{children}</div>;
}
