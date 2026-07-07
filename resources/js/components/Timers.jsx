import { useState, useEffect, useRef, useCallback } from 'react';
import { ArrowUp, ArrowDown, Clock, Play, Pause, RotateCcw, Volume2 } from 'lucide-react';
import ToolPanel from './shared/ToolPanel';
import Input from './shared/Input';
import Group from './shared/Group';
import Label from './shared/Label';
import Button from './shared/Button';
import DropdownMenu from './shared/DropdownMenu';
import timerPresets from './shared/data/timer-presets.json';
import timerSounds from './shared/data/timer-sounds.json';

const ZERO_TIME = { days: 0, hours: 0, minutes: 0, seconds: 0 };

export default function Timers() {
  const [isCountdown, setIsCountdown] = useState(true);
  const [time, setTime] = useState({ days: 0, hours: 0, minutes: 25, seconds: 0 });
  const [currentTime, setCurrentTime] = useState({ days: 0, hours: 0, minutes: 25, seconds: 0 });
  const [targetTime, setTargetTime] = useState({ days: 0, hours: 0, minutes: 1, seconds: 0 });
  const [isRunning, setIsRunning] = useState(false);
  const [isFinished, setIsFinished] = useState(false);
  const [selectedPreset, setSelectedPreset] = useState('pomodoro');
  const [selectedSound, setSelectedSound] = useState('bell');
  const [isFlashing, setIsFlashing] = useState(false);

  const intervalRef = useRef(null);
  const audioRef = useRef(null);

  useEffect(() => {
    audioRef.current = new Audio();
    return () => {
      if (audioRef.current) {
        audioRef.current.pause();
        audioRef.current = null;
      }
    };
  }, []);

  const playSound = useCallback(() => {
    if (selectedSound !== 'none' && audioRef.current) {
      const audioContext = new (window.AudioContext || window.webkitAudioContext)();
      const oscillator = audioContext.createOscillator();
      const gainNode = audioContext.createGain();

      oscillator.connect(gainNode);
      gainNode.connect(audioContext.destination);

      oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
      gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
      gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 1);

      oscillator.start(audioContext.currentTime);
      oscillator.stop(audioContext.currentTime + 1);
    }
  }, [selectedSound]);

  useEffect(() => {
    if (isRunning) {
      intervalRef.current = setInterval(() => {
        setCurrentTime((prev) => {
          const totalSeconds = prev.days * 86400 + prev.hours * 3600 + prev.minutes * 60 + prev.seconds;

          if (isCountdown) {
            if (totalSeconds <= 1) {
              setIsRunning(false);
              setIsFinished(true);
              setIsFlashing(true);
              playSound();
              setTimeout(() => setIsFlashing(false), 3000);
              return ZERO_TIME;
            }

            const newTotal = totalSeconds - 1;
            return {
              days: Math.floor(newTotal / 86400),
              hours: Math.floor((newTotal % 86400) / 3600),
              minutes: Math.floor((newTotal % 3600) / 60),
              seconds: newTotal % 60,
            };
          }

          const targetSeconds = targetTime.days * 86400 + targetTime.hours * 3600 + targetTime.minutes * 60 + targetTime.seconds;

          if (targetSeconds > 0 && totalSeconds >= targetSeconds) {
            setIsRunning(false);
            setIsFinished(true);
            setIsFlashing(true);
            playSound();
            setTimeout(() => setIsFlashing(false), 3000);
            return { ...targetTime };
          }

          const newTotal = totalSeconds + 1;
          return {
            days: Math.floor(newTotal / 86400),
            hours: Math.floor((newTotal % 86400) / 3600),
            minutes: Math.floor((newTotal % 3600) / 60),
            seconds: newTotal % 60,
          };
        });
      }, 1000);
    } else if (intervalRef.current) {
      clearInterval(intervalRef.current);
      intervalRef.current = null;
    }

    return () => {
      if (intervalRef.current) clearInterval(intervalRef.current);
    };
  }, [isRunning, isCountdown, playSound, targetTime.days, targetTime.hours, targetTime.minutes, targetTime.seconds]);

  const handleTimeChange = (field, value) => {
    const numValue = Math.max(0, parseInt(value) || 0);
    const newTime = { ...time, [field]: numValue };
    setTime(newTime);

    if (!isRunning) {
      if (isCountdown) setCurrentTime(newTime);
      else setTargetTime(newTime);
      setIsFinished(false);
    }
  };

  const handlePresetChange = (presetValue) => {
    setSelectedPreset(presetValue);
    const preset = timerPresets.find((p) => p.value === presetValue);
    if (!preset) return;

    const newTime = { days: preset.days, hours: preset.hours, minutes: preset.minutes, seconds: preset.seconds };

    if (isCountdown) {
      setTime(newTime);
      if (!isRunning) {
        setCurrentTime(newTime);
        setIsFinished(false);
      }
    } else {
      setTargetTime(newTime);
      if (!isRunning) {
        setCurrentTime(ZERO_TIME);
        setIsFinished(false);
      }
    }
  };

  const toggleDirection = () => {
    const newIsCountdown = !isCountdown;
    setIsCountdown(newIsCountdown);
    setIsFinished(false);
    setIsFlashing(false);

    if (!isRunning) {
      if (newIsCountdown) {
        setCurrentTime(time);
      } else {
        setTargetTime(time);
        setCurrentTime(ZERO_TIME);
      }
    }
  };

  const handleStart = () => {
    setIsRunning(true);
    setIsFinished(false);
    setIsFlashing(false);
  };

  const handlePause = () => setIsRunning(false);

  const handleReset = () => {
    setIsRunning(false);
    setIsFinished(false);
    setIsFlashing(false);
    setCurrentTime(isCountdown ? time : ZERO_TIME);
  };

  const formatTime = (t) => {
    const parts = [];
    if (t.days > 0) parts.push(`${t.days}d`);
    if (t.hours > 0 || t.days > 0) parts.push(`${t.hours.toString().padStart(2, '0')}h`);
    parts.push(`${t.minutes.toString().padStart(2, '0')}m`);
    parts.push(`${t.seconds.toString().padStart(2, '0')}s`);
    return parts.join(' ');
  };

  return (
    <ToolPanel title="Timers" description="Simple timers for countdowns to deadlines and tracking elapsed time." icon={Clock}>
      <div className="space-y-6">
        <div
          className={`text-center p-6 rounded-sm border-2 transition-colors ${isFlashing ? 'tool-callout-danger' : ''}`}
          style={{ borderColor: isFlashing ? 'var(--tertiary)' : 'var(--border-default)' }}
        >
          <div className="text-4xl lg:text-6xl font-mono font-bold" style={{ color: isFinished ? 'var(--tertiary)' : 'var(--text-primary)' }}>
            {formatTime(currentTime)}
          </div>
          <div className="text-sm mt-2 tool-text-muted">
            {isCountdown ? 'Countdown Timer' : 'Stopwatch'} {isRunning ? '(Running)' : '(Stopped)'}
          </div>
        </div>

        <div className="flex justify-center gap-3">
          <Button
            onClick={isRunning ? handlePause : handleStart}
            variant={isRunning ? 'secondary' : 'primary'}
            disabled={isCountdown && currentTime.days === 0 && currentTime.hours === 0 && currentTime.minutes === 0 && currentTime.seconds === 0}
          >
            {isRunning ? <Pause size={16} /> : <Play size={16} />}
            {isRunning ? 'Pause' : 'Start'}
          </Button>
          <Button onClick={handleReset} variant="secondary">
            <RotateCcw size={16} />
            Reset
          </Button>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <Group>
            <Label className="block text-sm font-semibold mb-2">Timer Type</Label>
            <Button onClick={toggleDirection} variant={isCountdown ? 'primary' : 'secondary'} className="w-full justify-center" disabled={isRunning}>
              {isCountdown ? <ArrowDown size={16} /> : <ArrowUp size={16} />}
              {isCountdown ? 'Countdown' : 'Stopwatch'}
            </Button>
          </Group>

          <Group>
            <Label className="block text-sm font-semibold mb-2">
              <Volume2 size={16} className="inline mr-1" />
              Alert Sound
            </Label>
            <DropdownMenu options={timerSounds} value={selectedSound} onChange={setSelectedSound} placeholder="Select sound" />
          </Group>
        </div>

        <Group>
          <Label className="block text-sm font-semibold mb-2">{isCountdown ? 'Countdown Presets' : 'Stopwatch Targets'}</Label>
          <DropdownMenu
            options={timerPresets.filter((preset) => preset.type === (isCountdown ? 'countdown' : 'stopwatch'))}
            value={selectedPreset}
            onChange={handlePresetChange}
            placeholder={isCountdown ? 'Select countdown preset' : 'Select target time'}
          />
        </Group>

        <div>
          <Label className="block text-sm font-semibold mb-2">Manual Time Setting</Label>
          <div className="flex items-end gap-4 justify-center">
            {[
              ['days', 'Days', undefined],
              ['hours', 'Hours', 23],
              ['minutes', 'Minutes', 59],
              ['seconds', 'Seconds', 59],
            ].map(([field, fieldLabel, max]) => (
              <Group key={field} className="flex flex-col items-center">
                <Label className="text-xs font-medium mb-1" htmlFor={field}>
                  {fieldLabel}
                </Label>
                <Input
                  name={field}
                  type="number"
                  value={time[field] || ''}
                  onChange={(e) => handleTimeChange(field, e.target.value)}
                  className="w-16 text-lg font-bold text-center"
                  min={0}
                  max={max}
                  step={1}
                  disabled={isRunning}
                />
              </Group>
            ))}
          </div>
        </div>
      </div>
    </ToolPanel>
  );
}
