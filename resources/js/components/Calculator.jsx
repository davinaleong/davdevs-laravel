import { useState } from 'react';
import { Calculator as CalculatorIcon, History, Trash2 } from 'lucide-react';
import Button from './shared/Button';
import Panel from './shared/Panel';
import ToolPanel from './shared/ToolPanel';

export default function Calculator() {
  const [display, setDisplay] = useState('0');
  const [operation, setOperation] = useState(null);
  const [previousValue, setPreviousValue] = useState(null);
  const [waitingForNewValue, setWaitingForNewValue] = useState(false);
  const [history, setHistory] = useState([]);
  const [showHistory, setShowHistory] = useState(false);

  const handleNumber = (num) => {
    if (waitingForNewValue) {
      setDisplay(num);
      setWaitingForNewValue(false);
    } else {
      setDisplay(display === '0' ? num : display + num);
    }
  };

  const calculate = (firstValue, secondValue, op) => {
    switch (op) {
      case '+':
        return firstValue + secondValue;
      case '-':
        return firstValue - secondValue;
      case '×':
        return firstValue * secondValue;
      case '÷':
        return firstValue / secondValue;
      default:
        return secondValue;
    }
  };

  const handleOperation = (nextOperation) => {
    const inputValue = parseFloat(display);

    if (previousValue === null) {
      setPreviousValue(inputValue);
    } else if (operation) {
      const currentValue = previousValue || 0;
      const newValue = calculate(currentValue, inputValue, operation);

      setDisplay(String(newValue));
      setPreviousValue(newValue);
    }

    setWaitingForNewValue(true);
    setOperation(nextOperation);
  };

  const handleEquals = () => {
    const inputValue = parseFloat(display);

    if (previousValue !== null && operation) {
      const newValue = calculate(previousValue, inputValue, operation);
      const expression = `${previousValue} ${operation} ${inputValue}`;
      const result = String(newValue);

      setHistory((prev) => [{ id: Date.now(), expression, result }, ...prev]);

      setDisplay(result);
      setPreviousValue(null);
      setOperation(null);
      setWaitingForNewValue(true);
    }
  };

  const handleClear = () => {
    setDisplay('0');
    setPreviousValue(null);
    setOperation(null);
    setWaitingForNewValue(false);
  };

  const handleDecimal = () => {
    if (waitingForNewValue) {
      setDisplay('0.');
      setWaitingForNewValue(false);
    } else if (display.indexOf('.') === -1) {
      setDisplay(display + '.');
    }
  };

  const handleHistoryClick = (historyItem) => {
    setDisplay(historyItem.result);
    setPreviousValue(null);
    setOperation(null);
    setWaitingForNewValue(true);
  };

  const clearHistory = () => setHistory([]);
  const toggleHistory = () => setShowHistory(!showHistory);

  return (
    <ToolPanel title="Calculator" description="Simple calculator with calculation history" icon={CalculatorIcon}>
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <Panel className={`${showHistory ? 'lg:col-span-1' : 'lg:col-span-2'} max-w-sm mx-auto lg:mx-0 flow`}>
          <section aria-label="Calculator display">
            <output
              className="tool-input-surface block p-4"
              style={{ display: 'block' }}
            >
              <div className="text-right text-2xl font-mono overflow-hidden">{display}</div>
            </output>
          </section>

          <section aria-label="Calculator buttons">
            <div className="grid grid-cols-4 gap-2">
              <Button onClick={handleClear} variant="secondary" className="col-span-2">Clear</Button>
              <Button onClick={() => handleOperation('÷')} variant="secondary">÷</Button>
              <Button onClick={() => handleOperation('×')} variant="secondary">×</Button>

              <Button onClick={() => handleNumber('7')} variant="primary">7</Button>
              <Button onClick={() => handleNumber('8')} variant="primary">8</Button>
              <Button onClick={() => handleNumber('9')} variant="primary">9</Button>
              <Button onClick={() => handleOperation('-')} variant="secondary">-</Button>

              <Button onClick={() => handleNumber('4')} variant="primary">4</Button>
              <Button onClick={() => handleNumber('5')} variant="primary">5</Button>
              <Button onClick={() => handleNumber('6')} variant="primary">6</Button>
              <Button onClick={() => handleOperation('+')} variant="secondary">+</Button>

              <Button onClick={() => handleNumber('1')} variant="primary">1</Button>
              <Button onClick={() => handleNumber('2')} variant="primary">2</Button>
              <Button onClick={() => handleNumber('3')} variant="primary">3</Button>
              <Button onClick={handleEquals} variant="secondary" className="row-span-2">=</Button>

              <Button onClick={() => handleNumber('0')} variant="primary" className="col-span-2">0</Button>
              <Button onClick={handleDecimal} variant="primary">.</Button>
            </div>
          </section>

          <section aria-label="History controls">
            <div className="flex justify-between items-center mt-4">
              <Button onClick={toggleHistory} variant="secondary" className="flex items-center gap-2">
                <History size={16} />
                {showHistory ? 'Hide' : 'Show'} History
              </Button>
              {history.length > 0 && <span className="text-sm opacity-75">{history.length} calculations</span>}
            </div>
          </section>
        </Panel>

        {showHistory && (
          <Panel className="lg:col-span-1">
            <aside>
              <header
                className="p-4 flex items-center justify-between"
                style={{ borderBottom: '1px solid var(--border-default)' }}
              >
                <h2 className="font-medium flex items-center gap-2">
                  <History size={16} />
                  History
                </h2>
                {history.length > 0 && (
                  <Button onClick={clearHistory} variant="icon" className="p-1" title="Clear history">
                    <Trash2 size={16} />
                  </Button>
                )}
              </header>
              <nav aria-label="Previous calculations" className="max-h-96 overflow-y-auto">
                {history.length === 0 ? (
                  <div className="p-4 text-center" style={{ color: 'var(--text-muted)' }}>No calculations yet</div>
                ) : (
                  <ul className="space-y-1">
                    {history.map((item) => (
                      <li key={item.id}>
                        <Button
                          onClick={() => handleHistoryClick(item)}
                          variant="list"
                          aria-label={`Use result ${item.result} from calculation ${item.expression}`}
                        >
                          <div className="text-sm opacity-75 font-mono">{item.expression}</div>
                          <div className="text-lg font-mono">{item.result}</div>
                        </Button>
                      </li>
                    ))}
                  </ul>
                )}
              </nav>
            </aside>
          </Panel>
        )}
      </div>
    </ToolPanel>
  );
}
