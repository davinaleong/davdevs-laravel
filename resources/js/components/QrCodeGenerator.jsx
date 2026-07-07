import { useState, useRef } from 'react';
import { QrCode, Download } from 'lucide-react';
import { QRCode } from 'react-qrcode-logo';
import ToolPanel from './shared/ToolPanel';
import Input from './shared/Input';
import Group from './shared/Group';
import Label from './shared/Label';
import Button from './shared/Button';

export default function QrCodeGenerator() {
  const [text, setText] = useState('https://example.com');
  const [size, setSize] = useState(200);
  const [fgColor, setFgColor] = useState('#000000');
  const [bgColor, setBgColor] = useState('#ffffff');
  const qrRef = useRef(null);

  const downloadQrCode = () => {
    const canvas = qrRef.current?.querySelector('canvas');
    if (!canvas) return;

    canvas.toBlob((blob) => {
      if (!blob) return;
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = 'qrcode.png';
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      URL.revokeObjectURL(url);
    });
  };

  return (
    <ToolPanel title="QR Code Generator" description="Create custom QR codes for URLs or text" icon={QrCode} className="max-w-lg mx-auto">
      <Group variant="vertical" className="gap-4">
        <Group variant="vertical">
          <Label>Text / URL:</Label>
          <Input type="text" value={text} onChange={(e) => setText(e.target.value)} placeholder="Enter text or URL to encode" />
        </Group>

        <Group variant="vertical">
          <Label>Size (px):</Label>
          <Input type="number" min={50} max={1000} value={size} onChange={(e) => setSize(Number(e.target.value))} />
        </Group>

        <Group variant="vertical">
          <Label>Foreground Color:</Label>
          <Input type="color" value={fgColor} onChange={(e) => setFgColor(e.target.value)} />
        </Group>

        <Group variant="vertical">
          <Label>Background Color:</Label>
          <Input type="color" value={bgColor} onChange={(e) => setBgColor(e.target.value)} />
        </Group>
      </Group>

      <div className="flex flex-col items-center gap-4">
        <div className="tool-input-surface flex justify-center p-4" style={{ minWidth: '250px' }} ref={qrRef}>
          <QRCode value={text} size={size} fgColor={fgColor} bgColor={bgColor} quietZone={10} />
        </div>

        <Button onClick={downloadQrCode} variant="primary" className="px-4 py-3">
          <Download size={16} />
          Download QR Code
        </Button>
      </div>
    </ToolPanel>
  );
}
