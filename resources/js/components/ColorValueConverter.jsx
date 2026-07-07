import { useState } from 'react';
import { colord } from 'colord';
import { Palette, Copy, Check, AlertCircle } from 'lucide-react';
import Button from './shared/Button';
import Input from './shared/Input';
import ToolPanel from './shared/ToolPanel';

function buildFormats(color) {
  const rgb = color.toRgb();
  const hsv = color.toHsv();

  const r = rgb.r / 255;
  const g = rgb.g / 255;
  const b = rgb.b / 255;
  const k = 1 - Math.max(r, g, b);
  const c = k === 1 ? 0 : (1 - r - k) / (1 - k);
  const m = k === 1 ? 0 : (1 - g - k) / (1 - k);
  const y = k === 1 ? 0 : (1 - b - k) / (1 - k);

  const oklL = (0.2126 * r + 0.7152 * g + 0.0722 * b) * 100;
  const oklC = Math.sqrt((r - 0.5) ** 2 + (g - 0.5) ** 2 + (b - 0.5) ** 2) * 0.4;
  const oklH = ((Math.atan2(g - 0.5, r - 0.5) * 180) / Math.PI + 360) % 360;

  return [
    { name: 'HEX', value: color.toHex(), format: 'hex' },
    { name: 'HEX (with alpha)', value: color.toHslString(), format: 'hex8' },
    { name: 'RGB', value: color.toRgbString(), format: 'rgb' },
    { name: 'HSL', value: color.toHslString(), format: 'hsl' },
    { name: 'HSV/HSB', value: `hsv(${Math.round(hsv.h)}, ${Math.round(hsv.s)}%, ${Math.round(hsv.v)}%)`, format: 'hsv' },
    { name: 'CMYK', value: `cmyk(${Math.round(c * 100)}%, ${Math.round(m * 100)}%, ${Math.round(y * 100)}%, ${Math.round(k * 100)}%)`, format: 'cmyk' },
    { name: 'RGB (0-1)', value: `rgb(${(rgb.r / 255).toFixed(3)}, ${(rgb.g / 255).toFixed(3)}, ${(rgb.b / 255).toFixed(3)})`, format: 'rgb-decimal' },
    { name: 'OKLCH', value: `oklch(${oklL.toFixed(1)}% ${oklC.toFixed(3)} ${oklH.toFixed(3)})`, format: 'oklch' },
    { name: 'CSS Name', value: 'N/A', format: 'name' },
  ];
}

function detectColorFormat(input) {
  const trimmed = input.trim();
  if (/^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6}|[0-9A-Fa-f]{8})$/.test(trimmed)) return 'hex';
  if (/^rgba?\s*\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*(,\s*[\d.]+)?\s*\)$/i.test(trimmed)) return 'rgb';
  if (/^hsla?\s*\(\s*\d+\s*,\s*\d+%\s*,\s*\d+%\s*(,\s*[\d.]+)?\s*\)$/i.test(trimmed)) return 'hsl';
  if (/^oklch\s*\(\s*[\d.]+%?\s+[\d.]+\s+[\d.]+\s*\)$/i.test(trimmed)) return 'oklch';
  if (/^[a-zA-Z]+$/.test(trimmed)) return 'name';
  return 'unknown';
}

function parseOklch(oklchString) {
  const match = oklchString.match(/oklch\s*\(\s*([\d.]+)%?\s+([\d.]+)\s+([\d.]+)\s*\)/i);
  if (!match) return null;

  const l = parseFloat(match[1]) / 100;
  const c = parseFloat(match[2]);
  const h = parseFloat(match[3]);

  const hRad = (h * Math.PI) / 180;
  const a = c * Math.cos(hRad);
  const b = c * Math.sin(hRad);

  const y = l;
  const x = a * 0.5 + 0.5;
  const z = b * 0.5 + 0.5;

  const r = Math.max(0, Math.min(1, y + x - 0.5));
  const g = Math.max(0, Math.min(1, y));
  const bRgb = Math.max(0, Math.min(1, y - z + 0.5));

  return { r: Math.round(r * 255), g: Math.round(g * 255), b: Math.round(bRgb * 255) };
}

const EXAMPLE_COLORS = ['#3b82f6', 'rgb(239, 68, 68)', 'hsl(120, 100%, 50%)', 'oklch(70% 0.25 142)', 'red', '#00ff00', 'rgb(255, 165, 0)'];

export default function ColorValueConverter() {
  const [inputValue, setInputValue] = useState('#3b82f6');
  const [colorFormats, setColorFormats] = useState(() => buildFormats(colord('#3b82f6')));
  const [isValidColor, setIsValidColor] = useState(true);
  const [copiedFormat, setCopiedFormat] = useState(null);

  const convertColor = (input) => {
    try {
      let color;

      if (detectColorFormat(input) === 'oklch') {
        const oklchRgb = parseOklch(input);
        if (!oklchRgb) {
          setIsValidColor(false);
          setColorFormats([]);
          return;
        }
        color = colord({ r: oklchRgb.r, g: oklchRgb.g, b: oklchRgb.b });
      } else {
        color = colord(input);
      }

      if (!color.isValid()) {
        setIsValidColor(false);
        setColorFormats([]);
        return;
      }

      setIsValidColor(true);
      setColorFormats(buildFormats(color));
    } catch (error) {
      setIsValidColor(false);
      setColorFormats([]);
      console.error(error);
    }
  };

  const copyToClipboard = async (value, format) => {
    try {
      await navigator.clipboard.writeText(value);
      setCopiedFormat(format);
      setTimeout(() => setCopiedFormat(null), 2000);
    } catch (error) {
      console.error('Failed to copy to clipboard:', error);
    }
  };

  const detectedFormat = detectColorFormat(inputValue);

  return (
    <ToolPanel
      title="Color Value Converter"
      description="Convert color values between different formats such as HEX, RGB, HSL, OKLCH, and more."
      icon={Palette}
      className="max-w-4xl mx-auto"
    >
      <form className="mb-6" onSubmit={(e) => e.preventDefault()}>
        <label htmlFor="colorInput" className="block text-sm font-medium opacity-75">
          Enter Color Value
        </label>
        <div className="flex gap-2">
          <Input
            type="text"
            value={inputValue}
            onChange={(e) => {
              const newValue = e.target.value;
              setInputValue(newValue);
              convertColor(newValue);
            }}
            placeholder="e.g., #3b82f6, rgb(59, 130, 246), oklch(70.7% 0.165 254.624), blue"
            style={{ borderColor: isValidColor ? undefined : 'var(--tertiary)' }}
          />
          {isValidColor && colorFormats.length > 0 && (
            <div
              className="w-12 h-12 rounded-sm border-2 shadow-sm shrink-0"
              style={{ backgroundColor: inputValue, borderColor: 'var(--border-default)' }}
              title="Color preview"
            />
          )}
        </div>

        {detectedFormat !== 'unknown' && isValidColor && (
          <p className="text-xs opacity-75 mt-1">Detected format: {detectedFormat.toUpperCase()}</p>
        )}

        {!isValidColor && (
          <div className="flex items-center gap-2 mt-2 tool-text-danger">
            <AlertCircle size={16} />
            <span className="text-sm">Invalid color value</span>
          </div>
        )}
      </form>

      {isValidColor && colorFormats.length > 0 && (
        <section>
          <h3 className="text-lg font-medium mb-4">Converted Values</h3>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {colorFormats.map((format) => (
              <div key={format.format} className="tool-surface-muted rounded-sm p-4 hover:opacity-60">
                <div className="flex items-center justify-between mb-2">
                  <span className="font-medium opacity-75">{format.name}</span>
                  <Button
                    onClick={() => copyToClipboard(format.value, format.format)}
                    variant="icon"
                    title="Copy to clipboard"
                    disabled={format.value === 'N/A'}
                  >
                    {copiedFormat === format.format ? <Check className="w-4 h-4 tool-text-success" /> : <Copy size={16} />}
                  </Button>
                </div>
                <code
                  className="text-sm tool-input-surface px-2 py-1 block"
                  style={{ color: format.value === 'N/A' ? 'var(--text-muted)' : 'var(--text-primary)' }}
                >
                  {format.value}
                </code>
              </div>
            ))}
          </div>
        </section>
      )}

      <section className="mt-8">
        <h3 className="text-lg font-medium opacity-75 mb-4">Example Colors</h3>
        <div className="flex flex-wrap gap-2">
          {EXAMPLE_COLORS.map((example) => (
            <Button
              key={example}
              onClick={() => {
                setInputValue(example);
                convertColor(example);
              }}
              variant="secondary"
              className="px-3 py-1 text-sm"
            >
              {example}
            </Button>
          ))}
        </div>
      </section>
    </ToolPanel>
  );
}
