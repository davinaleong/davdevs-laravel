import { useState } from 'react';
import { Key } from 'lucide-react';
import zxcvbn from 'zxcvbn';
import ToolPanel from './shared/ToolPanel';
import Button from './shared/Button';
import { generateSecurePassword } from './shared/utils/passwordGeneration';

export default function PasswordCreator() {
  const [password, setPassword] = useState('');
  const [crackTime, setCrackTime] = useState('');

  const generatePassword = () => {
    const finalPassword = generateSecurePassword();
    setPassword(finalPassword);

    const result = zxcvbn(finalPassword);
    setCrackTime(String(result.crack_times_display.offline_slow_hashing_1e4_per_second));
  };

  return (
    <ToolPanel title="Password Creator" description="Generate a secure, memorable password" icon={Key}>
      <p>This tool builds a strong password based on an expressive phrase with creative modifications.</p>
      <Button onClick={generatePassword} variant="primary">
        Generate Password
      </Button>
      {password && (
        <div className="mt-4">
          <p className="text-lg font-mono break-all">
            <strong>🔐 Your Password:</strong> {password}
          </p>
          <p className="mt-2">
            🛡️ Estimated Crack Time: <strong>{crackTime}</strong>
          </p>
        </div>
      )}
    </ToolPanel>
  );
}
