import { forwardRef, useId } from 'react';

const RESIZE_CLASS = {
  none: 'resize-none',
  both: 'resize',
  horizontal: 'resize-x',
  vertical: 'resize-y',
};

const Textarea = forwardRef(function Textarea(
  { placeholder, className = '', name, rows = 4, cols, minLength, maxLength, wrap = 'soft', resize = 'vertical', ...props },
  ref
) {
  const uniqueId = useId();
  const textareaId = `textarea-${name || 'textarea'}-${uniqueId}`;

  const textareaSpecificProps = { rows, wrap };
  if (cols !== undefined) textareaSpecificProps.cols = cols;
  if (minLength !== undefined) textareaSpecificProps.minLength = minLength;
  if (maxLength !== undefined) textareaSpecificProps.maxLength = maxLength;

  const finalClassName = `tool-input ${RESIZE_CLASS[resize]} ${className}`.trim();

  return (
    <textarea
      ref={ref}
      id={textareaId}
      className={finalClassName}
      placeholder={placeholder}
      name={name}
      {...textareaSpecificProps}
      {...props}
    />
  );
});

export default Textarea;
