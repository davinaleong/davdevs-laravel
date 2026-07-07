import { useState } from 'react';
import { Search, Copy, Download } from 'lucide-react';
import Button from './shared/Button';
import Input from './shared/Input';
import Table from './shared/Table';
import ToolPanel from './shared/ToolPanel';

function getCountHeatmapClass(count, maxCount) {
  const ratio = count / maxCount;
  if (ratio <= 0.33) return 'tool-callout-success';
  if (ratio <= 0.66) return 'tool-callout-accent';
  return 'tool-callout-danger';
}

export default function DuplicatedParagraphScanner() {
  const [url, setUrl] = useState('');
  const [duplicates, setDuplicates] = useState([]);
  const [error, setError] = useState('');

  const exportToCSV = () => {
    const headers = ['Paragraph', 'Page Title', 'Count'];
    const csvContent = [
      headers.join(','),
      ...duplicates.map((dup) =>
        [`"${(dup.text || '').replace(/"/g, '""')}"`, `"${(dup.pageTitle || '').replace(/"/g, '""')}"`, dup.count].join(',')
      ),
    ].join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `duplicate-paragraphs-${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };

  async function handleScan() {
    setError('');
    setDuplicates([]);

    try {
      const urlObj = new URL(url);
      const isLocalhost = urlObj.hostname === 'localhost' || urlObj.hostname === '127.0.0.1' || urlObj.hostname.endsWith('.local');

      let html = '';
      let lastError = null;

      if (isLocalhost) {
        try {
          const response = await fetch(url, { mode: 'cors', headers: { Accept: 'text/html, */*' } });
          if (!response.ok) throw new Error(`HTTP ${response.status}: ${response.statusText}`);
          html = await response.text();
        } catch (err) {
          console.error(err);
          throw new Error(
            'Cannot scan localhost URLs. CORS proxies cannot access your local development server. ' +
              'To scan localhost pages: 1) Deploy to a public URL, 2) Use browser extensions that disable CORS, ' +
              'or 3) Copy the page HTML directly and use a different tool.'
          );
        }
      } else {
        const proxyServices = [
          `https://api.allorigins.win/get?url=${encodeURIComponent(url)}`,
          `https://corsproxy.io/?${encodeURIComponent(url)}`,
          `https://cors-anywhere.herokuapp.com/${url}`,
        ];

        for (const proxyUrl of proxyServices) {
          try {
            const response = await fetch(proxyUrl, {
              headers: { Accept: 'application/json, text/html, */*', 'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36' },
            });

            if (!response.ok) throw new Error(`HTTP ${response.status}: ${response.statusText}`);

            if (proxyUrl.includes('allorigins.win')) {
              const data = await response.json();
              html = data.contents || data.data || '';
            } else {
              html = await response.text();
            }

            if (html) break;
          } catch (err) {
            lastError = err instanceof Error ? err : new Error(String(err));
            console.warn(`Proxy ${proxyUrl} failed:`, err);
            continue;
          }
        }
      }

      if (!html) {
        throw new Error(`All proxy services failed. Last error: ${lastError?.message || 'Unknown error'}. Try a different URL or ensure the website is accessible.`);
      }

      const doc = new DOMParser().parseFromString(html, 'text/html');

      let pageTitle = doc.querySelector('title')?.textContent?.trim();
      if (!pageTitle) {
        const headerH1 = doc.querySelector('header h1')?.textContent?.trim();
        pageTitle = headerH1 || doc.querySelector('h1')?.textContent?.trim() || 'Untitled Page';
      }

      const paras = Array.from(doc.querySelectorAll('p'))
        .map((p) => p.textContent?.trim() || '')
        .filter((p) => p.length > 0);

      if (paras.length === 0) {
        throw new Error('No paragraphs found on the page. The page might be using JavaScript to load content.');
      }

      const paragraphCounts = new Map();
      paras.forEach((p) => {
        const key = p.toLowerCase().trim();
        if (key.length > 0) {
          if (paragraphCounts.has(key)) {
            paragraphCounts.get(key).count++;
          } else {
            paragraphCounts.set(key, { text: p, count: 1, pageTitle });
          }
        }
      });

      const duplicatesWithCounts = Array.from(paragraphCounts.values())
        .filter((item) => item.count > 1)
        .sort((a, b) => b.count - a.count);

      setDuplicates(duplicatesWithCounts);
    } catch (error) {
      const errorMessage = error instanceof Error ? error.message : String(error);
      setError(
        `Failed to scan the page: ${errorMessage}. This might be due to CORS restrictions, the website blocking proxies, or the page requiring JavaScript to load content.`
      );
      console.error('Scanning error:', error);
    }
  }

  return (
    <ToolPanel title="Duplicate Paragraph Scanner" description="Scan a website for duplicate paragraphs" icon={Search}>
      <form
        onSubmit={(e) => {
          e.preventDefault();
          handleScan();
        }}
        className="space-y-6"
      >
        <section>
          <div className="flex gap-2">
            <Input type="url" placeholder="https://example.com" value={url} onChange={(e) => setUrl(e.target.value)} className="flex-1" required />
            <Button type="submit" variant="primary" className="px-6 py-2">
              <Search size={16} />
              Scan
            </Button>
          </div>
        </section>
      </form>

      {error && (
        <section className="mt-6 p-4 rounded-sm tool-callout-danger">
          <p>{error}</p>
        </section>
      )}

      {duplicates.length > 0 &&
        (() => {
          const maxCount = Math.max(...duplicates.map((d) => d.count));

          return (
            <section className="mt-6">
              <Table
                styles={['striped', 'bordered']}
                caption={`🔁 Duplicated Paragraphs Found (${duplicates.length})`}
                data={duplicates}
                columns={[
                  {
                    key: 'text',
                    label: 'Paragraph',
                    render: (value) => (
                      <div className="max-w-md truncate" title={String(value)}>
                        {String(value)}
                      </div>
                    ),
                  },
                  {
                    key: 'pageTitle',
                    label: 'Page Title',
                    render: (value) => (
                      <div className="max-w-xs truncate font-medium" title={String(value)}>
                        {String(value)}
                      </div>
                    ),
                  },
                  {
                    key: 'count',
                    label: 'Count',
                    render: (value) => (
                      <span className={`px-3 py-1 rounded-full text-sm font-semibold ${getCountHeatmapClass(Number(value), maxCount)}`}>
                        {Number(value)}
                      </span>
                    ),
                  },
                  {
                    key: 'actions',
                    label: 'Actions',
                    render: (_, row) => (
                      <Button
                        variant="icon"
                        onClick={() => navigator.clipboard.writeText(`${row.text} | ${row.pageTitle} | ${row.count}`)}
                        title="Copy this row"
                      >
                        <Copy size={14} />
                      </Button>
                    ),
                  },
                ]}
              >
                <Table.Foot>
                  <Table.Row>
                    <Table.Cell colSpan={4} className="text-center">
                      <Button variant="secondary" onClick={exportToCSV} title="Export results to CSV file">
                        <Download size={16} />
                        Export to CSV
                      </Button>
                    </Table.Cell>
                  </Table.Row>
                </Table.Foot>
              </Table>
            </section>
          );
        })()}

      {duplicates.length === 0 && url && !error && (
        <section className="mt-6 p-4 rounded-sm tool-callout-success">
          <p>✅ No duplicate paragraphs found! All content appears to be unique.</p>
        </section>
      )}
    </ToolPanel>
  );
}
