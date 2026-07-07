import { useState } from 'react';
import { ChevronDown, ChevronUp, Search } from 'lucide-react';

export default function DropdownMenu({ groups = [], options = [], value = '', onChange, placeholder = 'Select', className = '' }) {
  const [isOpen, setIsOpen] = useState(false);
  const [searchTerm, setSearchTerm] = useState('');

  const allOptions = [...options, ...groups.flatMap((group) => group.options)];
  const totalItemCount = allOptions.length;
  const showSearch = totalItemCount > 20;

  const filteredOptions = options.filter((option) => option.label.toLowerCase().includes(searchTerm.toLowerCase()));
  const filteredGroups = groups
    .map((group) => ({
      ...group,
      options: group.options.filter((option) => option.label.toLowerCase().includes(searchTerm.toLowerCase())),
    }))
    .filter((group) => group.options.length > 0);

  const selectedOption = allOptions.find((option) => option.value === value);
  const displayLabel = selectedOption ? selectedOption.label : placeholder;

  const handleToggle = () => {
    setIsOpen((prev) => !prev);
    if (!isOpen) setSearchTerm('');
  };

  const handleSelect = (optionValue) => {
    onChange?.(optionValue);
    setIsOpen(false);
  };

  const renderOption = (option) => (
    <li
      key={option.value}
      onClick={() => handleSelect(option.value)}
      className="px-3 py-2 cursor-pointer transition-colors"
      style={{ background: 'transparent' }}
      onMouseEnter={(e) => (e.currentTarget.style.background = 'var(--bg-surface-1)')}
      onMouseLeave={(e) => (e.currentTarget.style.background = 'transparent')}
    >
      {option.label}
    </li>
  );

  const renderGroup = (group, index) => (
    <li key={group.label}>
      <div
        className="px-3 py-1 text-xs font-semibold uppercase tracking-wide"
        style={{ background: 'var(--bg-surface-2)', color: 'var(--text-muted)' }}
      >
        {group.label}
      </div>
      <ul>{group.options.map((option) => renderOption(option))}</ul>
      {index < groups.length - 1 && <hr style={{ borderColor: 'var(--border-default)' }} />}
    </li>
  );

  return (
    <div className={`relative ${className}`}>
      <div className="tool-input flex items-center overflow-hidden" style={{ padding: 0 }}>
        <span className="block flex-1 px-3 py-2">{displayLabel}</span>
        <button type="button" onClick={handleToggle} className="tool-btn tool-btn-icon" style={{ border: 'none' }}>
          {isOpen ? <ChevronUp size={16} /> : <ChevronDown size={16} />}
        </button>
      </div>

      {isOpen && (
        <div
          className="absolute top-full left-0 right-0 z-10 mt-1 rounded-sm shadow-lg"
          style={{ background: 'var(--bg-page)', border: '1px solid var(--border-default)' }}
        >
          {showSearch && (
            <div className="sticky top-0 p-2" style={{ borderBottom: '1px solid var(--border-default)', background: 'var(--bg-page)' }}>
              <div className="tool-input flex items-center gap-2" style={{ padding: '0.25rem 0.5rem' }}>
                <Search size={14} style={{ color: 'var(--text-muted)' }} />
                <input
                  type="text"
                  placeholder="Search options..."
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  className="flex-1 text-sm bg-transparent border-none outline-none"
                  onClick={(e) => e.stopPropagation()}
                />
              </div>
            </div>
          )}

          <ul className="max-h-60 overflow-y-auto">
            {showSearch && searchTerm && filteredOptions.length === 0 && filteredGroups.length === 0 && (
              <li className="px-3 py-4 text-center" style={{ color: 'var(--text-muted)' }}>
                No options found for <q>{searchTerm}</q>
              </li>
            )}

            {filteredOptions.map((option) => renderOption(option))}

            {filteredOptions.length > 0 && filteredGroups.length > 0 && <hr style={{ borderColor: 'var(--border-default)' }} />}

            {filteredGroups.map((group, index) => renderGroup(group, index))}
          </ul>
        </div>
      )}
    </div>
  );
}
