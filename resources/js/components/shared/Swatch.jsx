import { useState } from 'react';
import { Copy, Check } from 'lucide-react';

const colorFormatLabels = {
  hex: 'HEX',
  rgb: 'RGB',
  hsl: 'HSL',
  oklch: 'OKLCH',
  lab: 'LAB',
  lch: 'LCH',
};

export default function Swatch({ title, hex, rgb, hsl, oklch, lab, lch, textColor, selectedFormat }) {
  const [copied, setCopied] = useState(false);

  const colorValues = { hex, rgb, hsl, oklch, lab, lch };

  const handleCopy = (text) => {
    navigator.clipboard.writeText(text).then(() => {
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    });
  };

  const primaryColorValue = colorValues[selectedFormat];

  return (
    <div
      className="p-4 rounded-sm shadow text-center cursor-pointer hover:opacity-60"
      style={{ color: textColor, backgroundColor: hex }}
      onClick={() => handleCopy(primaryColorValue)}
    >
      <div className="font-semibold mb-2">{title}</div>

      <div className="flex items-center justify-center gap-1 text-sm font-mono">
        {copied ? (
          <>
            <Check size={16} />
            <span>Copied!</span>
          </>
        ) : (
          <>
            <Copy size={16} />
            <span className="break-all">
              {colorFormatLabels[selectedFormat]}: {primaryColorValue}
            </span>
          </>
        )}
      </div>

      <div className="mt-2 space-y-1 text-xs opacity-75">
        {Object.entries(colorValues)
          .filter(([format]) => format !== selectedFormat)
          .slice(0, 2)
          .map(([format, value]) => (
            <div key={format} className="font-mono">
              {colorFormatLabels[format]}: {value}
            </div>
          ))}
      </div>
    </div>
  );
}
