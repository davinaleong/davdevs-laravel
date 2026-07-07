import { useState, useCallback, useMemo } from 'react';
import loyaltyData from './shared/data/loyalty-programs.json';
import { CreditCard, Plane, TrendingUp, ArrowRightLeft, Copy, Check, Info, Star, DollarSign, Globe, Search, X, SlidersHorizontal } from 'lucide-react';
import Button from './shared/Button';
import Input from './shared/Input';
import Label from './shared/Label';
import Group from './shared/Group';
import DropdownMenu from './shared/DropdownMenu';
import ToolPanel from './shared/ToolPanel';

export default function CardMilesConverter() {
  const [sourceAmount, setSourceAmount] = useState('');
  const [sourceProgramId, setSourceProgramId] = useState('dbs-points');
  const [targetProgramId, setTargetProgramId] = useState('singapore-airlines');
  const [selectedCurrency, setSelectedCurrency] = useState('SGD');

  const [copied, setCopied] = useState(null);

  const [searchTerm, setSearchTerm] = useState('');
  const [showFilters, setShowFilters] = useState(false);
  const [selectedTypes, setSelectedTypes] = useState(['airline', 'hotel', 'credit_card']);
  const [selectedRegions, setSelectedRegions] = useState([]);
  const [minValue, setMinValue] = useState('');
  const [maxValue, setMaxValue] = useState('');
  const [sortBy, setSortBy] = useState('value');

  const exchangeRates = useMemo(() => loyaltyData.exchangeRates, []);
  const currencies = loyaltyData.currencies;
  const loyaltyPrograms = useMemo(() => loyaltyData.loyaltyPrograms, []);

  const getProgram = useCallback((id) => loyaltyPrograms.find((p) => p.id === id), [loyaltyPrograms]);

  const convertToUSD = useCallback((amount, fromCurrency) => amount * exchangeRates[fromCurrency], [exchangeRates]);
  const convertFromUSD = useCallback((amount, toCurrency) => amount / exchangeRates[toCurrency], [exchangeRates]);

  const conversionResults = useMemo(() => {
    const amount = parseFloat(sourceAmount);
    if (!amount || amount <= 0) return [];

    const sourceProgram = getProgram(sourceProgramId);
    if (!sourceProgram) return [];

    const results = loyaltyPrograms.map((targetProgram) => {
      let convertedAmount = amount;
      let transferValue;

      if (sourceProgram.id !== targetProgram.id) {
        if (sourceProgram.type === 'hotel' && targetProgram.type === 'airline') {
          convertedAmount = amount / sourceProgram.transferRatio;
        } else if (sourceProgram.type === 'airline' && targetProgram.type === 'hotel') {
          convertedAmount = amount * targetProgram.transferRatio;
        } else if (sourceProgram.type === 'credit_card' && targetProgram.type !== 'credit_card') {
          convertedAmount = amount / sourceProgram.transferRatio;
        }
        transferValue = convertedAmount;
      }

      let cashValueUSD;
      if (targetProgram.baseCurrency === 'USD') {
        cashValueUSD = (convertedAmount * targetProgram.cashValue) / 100;
      } else {
        const localCashValue = (convertedAmount * targetProgram.cashValue) / 100;
        cashValueUSD = convertToUSD(localCashValue, targetProgram.baseCurrency);
      }

      const cashValueLocal = convertFromUSD(cashValueUSD, selectedCurrency);

      return { program: targetProgram, amount: convertedAmount, cashValueUSD, cashValueLocal, localCurrency: selectedCurrency, transferValue };
    });

    results.sort((a, b) => b.cashValueUSD - a.cashValueUSD);
    return results;
  }, [sourceAmount, sourceProgramId, selectedCurrency, convertFromUSD, convertToUSD, getProgram, loyaltyPrograms]);

  const getFilteredAndSortedResults = () => {
    const filtered = conversionResults.filter((result) => {
      const matchesSearch =
        searchTerm === '' ||
        result.program.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
        result.program.type.toLowerCase().includes(searchTerm.toLowerCase()) ||
        result.program.region.toLowerCase().includes(searchTerm.toLowerCase());

      const matchesType = selectedTypes.length === 0 || selectedTypes.includes(result.program.type);
      const matchesRegion = selectedRegions.length === 0 || selectedRegions.includes(result.program.region);
      const matchesMinValue = minValue === '' || result.cashValueLocal >= parseFloat(minValue);
      const matchesMaxValue = maxValue === '' || result.cashValueLocal <= parseFloat(maxValue);

      return matchesSearch && matchesType && matchesRegion && matchesMinValue && matchesMaxValue;
    });

    filtered.sort((a, b) => {
      switch (sortBy) {
        case 'name':
          return a.program.name.localeCompare(b.program.name);
        case 'type':
          return a.program.type.localeCompare(b.program.type);
        default:
          return b.cashValueUSD - a.cashValueUSD;
      }
    });

    return filtered;
  };

  const clearFilters = () => {
    setSearchTerm('');
    setSelectedTypes(['airline', 'hotel', 'credit_card']);
    setSelectedRegions([]);
    setMinValue('');
    setMaxValue('');
    setSortBy('value');
  };

  const toggleType = (type) => {
    setSelectedTypes((prev) => (prev.includes(type) ? prev.filter((t) => t !== type) : [...prev, type]));
  };

  const toggleRegion = (region) => {
    setSelectedRegions((prev) => (prev.includes(region) ? prev.filter((r) => r !== region) : [...prev, region]));
  };

  const swapPrograms = () => {
    setSourceProgramId(targetProgramId);
    setTargetProgramId(sourceProgramId);
  };

  const copyToClipboard = async (text, id) => {
    try {
      await navigator.clipboard.writeText(text);
      setCopied(id);
      setTimeout(() => setCopied(null), 2000);
    } catch (error) {
      console.error('Failed to copy:', error);
    }
  };

  const formatCurrency = (amount, currency = 'USD') => new Intl.NumberFormat('en-US', { style: 'currency', currency }).format(amount);
  const formatPoints = (amount, currency) => `${amount.toLocaleString()} ${currency}`;

  const getProgramTypeIcon = (type) => {
    switch (type) {
      case 'airline':
        return <Plane size={16} />;
      case 'hotel':
        return <Star size={16} />;
      case 'credit_card':
        return <CreditCard size={16} />;
      default:
        return <Star size={16} />;
    }
  };

  const getRegionFlag = (region) => {
    const flags = {
      singapore: '🇸🇬', malaysia: '🇲🇾', thailand: '🇹🇭', indonesia: '🇮🇩', philippines: '🇵🇭',
      hong_kong: '🇭🇰', taiwan: '🇹🇼', australia: '🇦🇺', middle_east: '🇦🇪', asia_pacific: '🌏', asean: '🌏', global: '🌍',
    };
    return flags[region] || '🌍';
  };

  const sourceProgram = getProgram(sourceProgramId);
  const bestValue = conversionResults[0];

  const dropdownGroups = [
    { label: 'ASEAN Credit Cards', options: loyaltyPrograms.filter((p) => p.type === 'credit_card' && ['singapore', 'malaysia', 'indonesia'].includes(p.region)) },
    { label: 'Global Credit Cards', options: loyaltyPrograms.filter((p) => p.type === 'credit_card' && p.region === 'global') },
    { label: 'ASEAN Airlines', options: loyaltyPrograms.filter((p) => p.type === 'airline' && ['asean', 'singapore', 'malaysia', 'thailand', 'indonesia', 'philippines'].includes(p.region)) },
    { label: 'Asia Pacific Airlines', options: loyaltyPrograms.filter((p) => p.type === 'airline' && ['asia_pacific', 'australia', 'hong_kong', 'taiwan'].includes(p.region)) },
    { label: 'Middle East Airlines', options: loyaltyPrograms.filter((p) => p.type === 'airline' && p.region === 'middle_east') },
    { label: 'Global Airlines', options: loyaltyPrograms.filter((p) => p.type === 'airline' && p.region === 'global') },
    { label: 'Hotels', options: loyaltyPrograms.filter((p) => p.type === 'hotel') },
  ].map((group) => ({ ...group, options: group.options.map((program) => ({ label: `${program.icon} ${program.name}`, value: program.id })) }));

  return (
    <ToolPanel
      title="Asia Pacific Card Miles Converter"
      description="Convert points and miles between loyalty programs across Asia Pacific with multi-currency support"
      icon={CreditCard}
      maxWidth="max-w-7xl"
    >
      <div className="flex flex-col lg:flex-row gap-4 mb-8 lg:items-end">
        <div className="flex-1 lg:flex-1">
          <Group variant="vertical">
            <Label>Amount to Convert</Label>
            <Input type="number" value={sourceAmount} onChange={(e) => setSourceAmount(e.target.value)} placeholder="Enter points/miles amount" />
          </Group>
        </div>

        <div className="flex-1 lg:flex-2">
          <Group variant="vertical">
            <Label>From Program</Label>
            <DropdownMenu value={sourceProgramId} onChange={setSourceProgramId} groups={dropdownGroups} className="w-full" />
          </Group>
        </div>

        <div className="flex-1">
          <Group variant="vertical">
            <Label>Display Currency</Label>
            <DropdownMenu
              value={selectedCurrency}
              onChange={setSelectedCurrency}
              options={currencies.map((currency) => ({ label: `${currency.flag} ${currency.code}`, value: currency.code }))}
              className="w-full"
            />
          </Group>
        </div>

        <div className="shrink-0">
          <Button onClick={swapPrograms} variant="secondary" className="w-full px-3 py-2 justify-center">
            <ArrowRightLeft size={16} />
            <span className="hidden sm:inline">Swap</span>
          </Button>
        </div>
      </div>

      {conversionResults.length > 0 && (
        <div className="mb-6">
          <div className="relative max-w-md">
            <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 z-10 tool-text-muted" />
            <Input type="search" placeholder="Quick search programs..." value={searchTerm} onChange={(e) => setSearchTerm(e.target.value)} className="pl-10 pr-10" />
            {searchTerm && (
              <Button onClick={() => setSearchTerm('')} variant="icon" className="absolute right-1 top-1/2 transform -translate-y-1/2 p-1">
                <X size={16} />
              </Button>
            )}
          </div>
        </div>
      )}

      {selectedCurrency !== 'USD' && (
        <div className="mb-6 p-3 rounded-sm tool-callout-accent">
          <div className="flex items-center gap-2 text-sm">
            <Globe size={16} />
            <span>
              Exchange Rate: 1 {selectedCurrency} = {exchangeRates[selectedCurrency].toFixed(4)} USD
            </span>
          </div>
        </div>
      )}

      {sourceProgram && sourceAmount && parseFloat(sourceAmount) > 0 && (
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
          <div className="tool-callout-accent p-4 rounded-sm">
            <div className="flex items-center gap-2 mb-2">
              <DollarSign className="w-4 h-4 tool-text-accent" />
              <span className="text-sm font-medium">Current Value</span>
            </div>
            <div className="text-2xl font-bold tool-text-accent">
              {formatCurrency(
                convertFromUSD(((parseFloat(sourceAmount) * sourceProgram.cashValue) / 100) * (sourceProgram.baseCurrency === 'USD' ? 1 : exchangeRates[sourceProgram.baseCurrency]), selectedCurrency),
                selectedCurrency
              )}
            </div>
            <div className="text-xs flex items-center gap-1 opacity-75">
              {getRegionFlag(sourceProgram.region)} {sourceProgram.cashValue}¢ per {sourceProgram.currency.slice(0, -1)}
            </div>
          </div>

          {bestValue && (
            <div className="tool-callout-success p-4 rounded-sm">
              <div className="flex items-center gap-2 mb-2">
                <TrendingUp className="w-4 h-4 tool-text-success" />
                <span className="text-sm font-medium">Best Value</span>
              </div>
              <div className="text-2xl font-bold tool-text-success">{formatCurrency(bestValue.cashValueLocal, selectedCurrency)}</div>
              <div className="text-xs flex items-center gap-1 opacity-75">
                {getRegionFlag(bestValue.program.region)} {bestValue.program.name}
              </div>
            </div>
          )}

          <div className="tool-surface-muted p-4 rounded-sm">
            <div className="flex items-center gap-2 mb-2">
              <Star className="w-4 h-4 tool-text-accent" />
              <span className="text-sm font-medium">Programs</span>
            </div>
            <div className="text-2xl font-bold">{loyaltyPrograms.length}</div>
            <div className="text-xs opacity-75">Asia Pacific + Global programs</div>
          </div>
        </div>
      )}

      {conversionResults.length > 0 && (
        <div>
          <div className="flex items-center justify-between mb-4">
            <h3 className="text-lg font-medium opacity-90">Conversion Results</h3>
            <div className="flex items-center gap-2">
              <Button
                onClick={() => setShowFilters(!showFilters)}
                variant={showFilters || selectedTypes.length < 3 || selectedRegions.length > 0 || searchTerm || minValue || maxValue ? 'primary' : 'gray'}
                className="px-3 py-1.5 text-sm"
              >
                <SlidersHorizontal size={16} />
                Filters
              </Button>
              <div className="text-sm tool-text-muted">
                {getFilteredAndSortedResults().length} of {conversionResults.length} programs
              </div>
            </div>
          </div>

          <div className={`mb-4 space-y-4 ${showFilters ? 'block' : 'hidden'}`}>
            <div className="relative">
              <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 z-10 tool-text-muted" />
              <Input type="search" placeholder="Search programs by name, type, or region..." value={searchTerm} onChange={(e) => setSearchTerm(e.target.value)} className="pl-10 pr-10" />
              {searchTerm && (
                <Button onClick={() => setSearchTerm('')} variant="icon" className="absolute right-1 top-1/2 transform -translate-y-1/2 p-1">
                  <X size={16} />
                </Button>
              )}
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-4 gap-4 p-4 tool-surface-muted rounded-sm">
              <div>
                <label className="block text-sm font-medium opacity-75 mb-2">Program Types</label>
                <div className="space-y-2">
                  {[
                    { value: 'airline', label: 'Airlines', icon: '✈️' },
                    { value: 'hotel', label: 'Hotels', icon: '🏨' },
                    { value: 'credit_card', label: 'Credit Cards', icon: '💳' },
                  ].map((type) => (
                    <label key={type.value} className="flex items-center">
                      <input type="checkbox" checked={selectedTypes.includes(type.value)} onChange={() => toggleType(type.value)} style={{ accentColor: 'var(--accent)' }} />
                      <span className="ml-2 text-sm opacity-75">
                        {type.icon} {type.label}
                      </span>
                    </label>
                  ))}
                </div>
              </div>

              <div>
                <label className="block text-sm font-medium opacity-75 mb-2">Regions</label>
                <div className="space-y-2 max-h-32 overflow-y-auto">
                  {[
                    { value: 'singapore', label: 'Singapore', flag: '🇸🇬' },
                    { value: 'malaysia', label: 'Malaysia', flag: '🇲🇾' },
                    { value: 'thailand', label: 'Thailand', flag: '🇹🇭' },
                    { value: 'indonesia', label: 'Indonesia', flag: '🇮🇩' },
                    { value: 'philippines', label: 'Philippines', flag: '🇵🇭' },
                    { value: 'hong_kong', label: 'Hong Kong', flag: '🇭🇰' },
                    { value: 'taiwan', label: 'Taiwan', flag: '🇹🇼' },
                    { value: 'australia', label: 'Australia', flag: '🇦🇺' },
                    { value: 'middle_east', label: 'Middle East', flag: '🇦🇪' },
                    { value: 'asean', label: 'ASEAN', flag: '🌏' },
                    { value: 'asia_pacific', label: 'Asia Pacific', flag: '🌏' },
                    { value: 'global', label: 'Global', flag: '🌍' },
                  ].map((region) => (
                    <label key={region.value} className="flex items-center">
                      <input type="checkbox" checked={selectedRegions.includes(region.value)} onChange={() => toggleRegion(region.value)} style={{ accentColor: 'var(--accent)' }} />
                      <span className="ml-2 text-sm opacity-75">
                        {region.flag} {region.label}
                      </span>
                    </label>
                  ))}
                </div>
              </div>

              <div>
                <Group variant="vertical">
                  <Label>Value Range ({selectedCurrency})</Label>
                  <div className="space-y-2">
                    <Input type="number" placeholder="Min value" value={minValue} onChange={(e) => setMinValue(e.target.value)} className="text-sm" />
                    <Input type="number" placeholder="Max value" value={maxValue} onChange={(e) => setMaxValue(e.target.value)} className="text-sm" />
                  </div>
                </Group>
              </div>

              <div>
                <Group variant="vertical">
                  <Label>Sort By</Label>
                  <DropdownMenu
                    value={sortBy}
                    onChange={(value) => setSortBy(value)}
                    options={[
                      { label: 'Cash Value (High to Low)', value: 'value' },
                      { label: 'Program Name (A-Z)', value: 'name' },
                      { label: 'Program Type', value: 'type' },
                    ]}
                    className="w-full text-sm"
                  />
                  <Button onClick={clearFilters} variant="gray" className="mt-2 w-full px-3 py-1.5 text-sm">
                    Clear All Filters
                  </Button>
                </Group>
              </div>
            </div>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {getFilteredAndSortedResults().map((result) => {
              const originalIndex = conversionResults.findIndex((r) => r.program.id === result.program.id);
              return (
                <div key={result.program.id} className={`rounded-sm p-4 hover:shadow-md transition-shadow ${originalIndex === 0 ? 'tool-callout-success' : 'tool-surface-muted'}`}>
                  <div className="flex items-center justify-between mb-3">
                    <div className="flex items-center gap-2">
                      <span className="text-lg">{result.program.icon}</span>
                      <div className="flow">
                        <div className="font-medium opacity-90 text-sm">{result.program.name}</div>
                        <div className="text-xs flex items-center gap-1 opacity-50">
                          {getProgramTypeIcon(result.program.type)}
                          <span className="capitalize">{result.program.type.replace('_', ' ')}</span>
                          {getRegionFlag(result.program.region)}
                        </div>
                      </div>
                    </div>
                    {originalIndex === 0 && <Star className="w-4 h-4 tool-text-success" fill="currentColor" />}
                  </div>

                  <div className="space-y-2">
                    <div>
                      <div className="text-xs opacity-50">Amount</div>
                      <div className="font-semibold opacity-90">{formatPoints(Math.round(result.amount), result.program.currency)}</div>
                    </div>

                    <div>
                      <div className="text-xs opacity-50">Cash Value ({selectedCurrency})</div>
                      <div className="font-bold text-lg tool-text-success">{formatCurrency(result.cashValueLocal, selectedCurrency)}</div>
                      {selectedCurrency !== 'USD' && <div className="text-xs opacity-50">{formatCurrency(result.cashValueUSD, 'USD')}</div>}
                    </div>

                    <div className="flex items-center justify-between pt-2">
                      <div className="text-xs opacity-50">
                        {result.program.cashValue}¢ per {result.program.currency.slice(0, -1)}
                      </div>
                      <Button
                        onClick={() => copyToClipboard(`${formatPoints(Math.round(result.amount), result.program.currency)} = ${formatCurrency(result.cashValueLocal, selectedCurrency)}`, result.program.id)}
                        variant="icon"
                        title="Copy conversion"
                      >
                        {copied === result.program.id ? <Check className="w-4 h-4 tool-text-success" /> : <Copy size={16} />}
                      </Button>
                    </div>

                    {result.transferValue && result.transferValue !== result.amount && (
                      <div className="pt-2" style={{ borderTop: '1px solid var(--border-default)' }}>
                        <div className="text-xs tool-text-accent">
                          Transfer: {Math.round(result.transferValue)} {result.program.currency}
                        </div>
                      </div>
                    )}
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      )}

      {conversionResults.length > 0 && getFilteredAndSortedResults().length === 0 && (
        <div className="text-center py-8">
          <div className="opacity-50 mb-4">
            <Search className="w-12 h-12 mx-auto mb-2 opacity-30" />
            <p className="text-lg font-medium">No programs match your filters</p>
            <p className="text-sm">Try adjusting your search or filter criteria</p>
          </div>
          <Button onClick={clearFilters} variant="primary" className="px-4 py-2">
            Clear All Filters
          </Button>
        </div>
      )}

      <div className="mt-8 p-4 rounded-sm tool-callout-accent">
        <div className="flex items-start gap-2">
          <Info className="w-4 h-4 mt-0.5 tool-text-accent" />
          <div className="text-sm">
            <p className="font-medium mb-1">Asia Pacific Card Miles Converter:</p>
            <ul className="space-y-1 text-xs">
              <li>• Compare loyalty programs across Asia Pacific region (ASEAN, Australia, Hong Kong, Taiwan, Middle East)</li>
              <li>• Multi-currency support with live exchange rates (SGD, MYR, THB, AUD, HKD, TWD, AED, USD)</li>
              <li>• Major airlines: Singapore Airlines, Cathay Pacific, Qantas, EVA Air, Etihad, Qatar Airways, Flying Blue, etc.</li>
              <li>• Local credit card programs and global programs (DBS, OCBC, UOB, Maybank, Amex, Citi, etc.)</li>
              <li>• Hotel programs: Marriott, Hilton, IHG, Shangri-La</li>
              <li>• Transfer ratios considered for hotel-to-airline conversions</li>
              <li>• Green highlight shows the program with the highest cash value in your selected currency</li>
            </ul>
          </div>
        </div>
      </div>
    </ToolPanel>
  );
}
