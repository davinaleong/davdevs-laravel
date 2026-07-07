import { useState } from 'react';
import { Palette } from 'lucide-react';
import Tab from './shared/Tab';
import TabFlex from './shared/TabFlex';
import DropdownMenu from './shared/DropdownMenu';
import Swatch from './shared/Swatch';
import ToolPanel from './shared/ToolPanel';
import colorsData from './shared/data/colors.json';

const colorFormatLabels = {
  hex: 'HEX',
  rgb: 'RGB',
  hsl: 'HSL',
  oklch: 'OKLCH',
  lab: 'LAB',
  lch: 'LCH',
};

export default function ColorPalettes() {
  const groups = colorsData.colorGroups;
  const [activeTab, setActiveTab] = useState(0);
  const [selectedFormat, setSelectedFormat] = useState('hex');

  if (!groups || groups.length === 0) {
    return <div className="p-6 text-center tool-text-muted">No color palettes available.</div>;
  }

  const uniqueTabs = Array.from(new Set(groups.map((group) => group.groupTitle.split(' – ')[0])));

  return (
    <ToolPanel title="Color Palettes" description="Explore curated color palettes with multiple format options" icon={Palette}>
      <div className="flex gap-4">
        <TabFlex>
          {uniqueTabs.map((tab, index) => (
            <Tab key={index} isActive={activeTab === index} onClick={() => setActiveTab(index)}>
              {tab}
            </Tab>
          ))}
        </TabFlex>

        <DropdownMenu
          value={selectedFormat}
          onChange={(value) => setSelectedFormat(value)}
          options={Object.entries(colorFormatLabels).map(([format, label]) => ({
            label: `Format: ${label}`,
            value: format,
          }))}
          placeholder="Format: HEX"
          className="min-w-[140px]"
        />
      </div>

      {groups
        .filter((group) => group.groupTitle.startsWith(uniqueTabs[activeTab]))
        .map((group, index) => (
          <section key={index}>
            <h2 className="text-2xl font-bold mb-4">{group.groupTitle}</h2>
            <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
              {group.colors.map((color, colorIndex) => (
                <Swatch key={colorIndex} {...color} selectedFormat={selectedFormat} />
              ))}
            </div>
          </section>
        ))}
    </ToolPanel>
  );
}
