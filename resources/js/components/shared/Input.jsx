import { forwardRef, useState, useId, useEffect } from 'react';
import { Eye, EyeOff, Check } from 'lucide-react';
import Button from './Button';

const Input = forwardRef(function Input(
  {
    type = 'text',
    placeholder,
    className = '',
    name,
    minLength,
    maxLength,
    min,
    max,
    step,
    pattern,
    accept,
    multiple,
    checked,
    onChange,
    defaultChecked,
    ...props
  },
  ref
) {
  const [showPassword, setShowPassword] = useState(false);
  const [inputType, setInputType] = useState(type);
  const [internalChecked, setInternalChecked] = useState(checked ?? defaultChecked ?? false);
  const uniqueId = useId();
  const inputId = `input-${name || type}-${uniqueId}`;

  useEffect(() => {
    if (type !== 'radio' || !name) return undefined;

    const handleRadioChange = (e) => {
      const target = e.target;
      if (target.name === name && target.id !== inputId && checked === undefined) {
        setInternalChecked(false);
      }
    };

    document.addEventListener('change', handleRadioChange);
    return () => document.removeEventListener('change', handleRadioChange);
  }, [type, name, inputId, checked]);

  const togglePasswordVisibility = () => {
    setShowPassword((prev) => !prev);
    setInputType(showPassword ? 'password' : 'text');
  };

  const typeSpecificProps = {};
  if (type === 'number' || type === 'range') {
    if (min !== undefined) typeSpecificProps.min = min;
    if (max !== undefined) typeSpecificProps.max = max;
    if (step !== undefined) typeSpecificProps.step = step;
  }
  if (['text', 'email', 'password', 'tel', 'url', 'search'].includes(type)) {
    if (minLength !== undefined) typeSpecificProps.minLength = minLength;
    if (maxLength !== undefined) typeSpecificProps.maxLength = maxLength;
    if (pattern !== undefined) typeSpecificProps.pattern = pattern;
  }
  if (type === 'file') {
    if (accept !== undefined) typeSpecificProps.accept = accept;
    if (multiple !== undefined) typeSpecificProps.multiple = multiple;
  }

  const finalClassName = `tool-input ${className}`.trim();

  if (type === 'checkbox' || type === 'radio') {
    const isControlled = checked !== undefined;
    const isChecked = isControlled ? checked : internalChecked;

    return (
      <div className="relative inline-flex">
        <input
          ref={ref}
          id={inputId}
          type={type}
          className="sr-only"
          name={name}
          checked={isChecked}
          onChange={(e) => {
            if (!isControlled) setInternalChecked(e.target.checked);
            onChange?.(e);
          }}
          {...typeSpecificProps}
          {...props}
        />
        <div
          className="relative inline-flex items-center justify-center w-[1em] h-[1em] text-[1em] border-2 cursor-pointer transition-all duration-200"
          style={{
            borderRadius: type === 'checkbox' ? '4px' : '9999px',
            background: isChecked ? 'var(--accent)' : 'transparent',
            borderColor: isChecked ? 'var(--accent)' : 'var(--border-default)',
          }}
          onClick={() => document.getElementById(inputId)?.click()}
        >
          {isChecked && (type === 'checkbox' ? (
            <Check size="0.7em" strokeWidth={3} style={{ color: 'var(--bg-page)' }} />
          ) : (
            <div className="w-[0.4em] h-[0.4em] rounded-full" style={{ background: 'var(--bg-page)' }} />
          ))}
        </div>
      </div>
    );
  }

  if (type === 'password') {
    return (
      <div className="relative">
        <input
          ref={ref}
          id={inputId}
          type={inputType}
          className={finalClassName}
          placeholder={placeholder}
          name={name}
          onChange={onChange}
          {...typeSpecificProps}
          {...props}
        />
        <Button
          type="button"
          onClick={togglePasswordVisibility}
          variant="icon"
          className="absolute right-3 top-1/2 transform -translate-y-1/2 p-1"
          aria-label={showPassword ? 'Hide password' : 'Show password'}
        >
          {showPassword ? <EyeOff size={16} /> : <Eye size={16} />}
        </Button>
      </div>
    );
  }

  return (
    <input
      ref={ref}
      id={inputId}
      type={type}
      className={finalClassName}
      placeholder={placeholder}
      name={name}
      onChange={onChange}
      {...typeSpecificProps}
      {...props}
    />
  );
});

export default Input;
