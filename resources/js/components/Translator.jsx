import { useState } from 'react';
import { Languages, ArrowRightLeft, Copy, Check, Volume2, Globe, MessageCircle, Sparkles } from 'lucide-react';
import Button from './shared/Button';
import Textarea from './shared/Textarea';
import Group from './shared/Group';
import DropdownMenu from './shared/DropdownMenu';
import ToolPanel from './shared/ToolPanel';

const languages = [
  { code: 'en', name: 'English', flag: '🇺🇸' },
  { code: 'es', name: 'Spanish', flag: '🇪🇸' },
  { code: 'fr', name: 'French', flag: '🇫🇷' },
  { code: 'de', name: 'German', flag: '🇩🇪' },
  { code: 'it', name: 'Italian', flag: '🇮🇹' },
  { code: 'pt', name: 'Portuguese', flag: '🇵🇹' },
  { code: 'ru', name: 'Russian', flag: '🇷🇺' },
  { code: 'ja', name: 'Japanese', flag: '🇯🇵' },
  { code: 'ko', name: 'Korean', flag: '🇰🇷' },
  { code: 'zh', name: 'Chinese', flag: '🇨🇳' },
];

const styles = [
  { value: 'formal', label: 'Formal', description: 'Professional tone', icon: '💼' },
  { value: 'casual', label: 'Casual', description: 'Conversational tone', icon: '😊' },
  { value: 'friendly', label: 'Friendly', description: 'Warm and approachable', icon: '🤝' },
  { value: 'technical', label: 'Technical', description: 'Precise and detailed', icon: '⚙️' },
];

function parseApiError(error) {
  const errorMessage = error?.message || (error instanceof Error ? error.message : '');
  const errorDetails = error?.details || '';

  if (errorDetails?.includes('exhausted') || errorDetails?.includes('quota')) {
    return { title: 'API Quota Exceeded', message: 'The translation service has reached its usage limit. Please try again later or contact support.' };
  }
  if (errorDetails?.includes('403') || errorDetails?.includes('Forbidden')) {
    return { title: 'Service Access Restricted', message: 'The translation service is currently unavailable. Please try again later.' };
  }
  if (errorMessage?.includes('fetch') || errorMessage?.includes('network')) {
    return { title: 'Connection Error', message: 'Unable to connect to the translation service. Please check your internet connection.' };
  }
  return { title: 'Translation Error', message: errorMessage || 'An unexpected error occurred. Please try again.' };
}

function buildPrompt(provider, text, fromLang, toLang, translationStyle) {
  const fromLanguage = languages.find((l) => l.code === fromLang)?.name || fromLang;
  const toLanguage = languages.find((l) => l.code === toLang)?.name || toLang;
  const styleDesc = styles.find((s) => s.value === translationStyle)?.description || translationStyle;

  return `Please translate the following text from ${fromLanguage} to ${toLanguage} using a ${translationStyle} style (${styleDesc}).

Source text: "${text}"

Please respond in the following JSON format:
{
  "provider": "${provider}",
  "translatedText": "your translation here",
  "sourceLanguage": "${fromLanguage}",
  "targetLanguage": "${toLanguage}",
  "style": "${translationStyle}",
  "confidence": 0.95
}`;
}

export default function Translator() {
  const [sourceText, setSourceText] = useState('');
  const [translatedText, setTranslatedText] = useState('');
  const [sourceLang, setSourceLang] = useState('en');
  const [targetLang, setTargetLang] = useState('es');
  const [style, setStyle] = useState('formal');
  const [isTranslating, setIsTranslating] = useState(false);
  const [copied, setCopied] = useState(false);
  const [error, setError] = useState(null);

  const handleTranslate = async () => {
    if (!sourceText.trim()) return;

    setIsTranslating(true);
    setError(null);

    try {
      const prompt = buildPrompt('openai', sourceText, sourceLang, targetLang, style);

      const response = await fetch('https://proj-ai-wrapper.onrender.com/v2/chat', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ provider: 'openai', prompt }),
      });

      if (!response.ok) {
        throw new Error(`API request failed with status ${response.status}: ${response.statusText}`);
      }

      const data = await response.json();

      if (data.error) {
        throw { message: data.error, details: JSON.stringify(data) };
      }

      let responseText = '';
      if (data.content && Array.isArray(data.content) && data.content[0]?.text) {
        responseText = data.content[0].text;
      } else if (data.choices && Array.isArray(data.choices) && data.choices[0]?.message?.content) {
        responseText = data.choices[0].message.content;
      } else if (data.response && typeof data.response === 'string') {
        responseText = data.response;
      } else {
        responseText = data.message || data.text || data.content || '';
      }

      if (!responseText) {
        throw new Error('No response received from translation service');
      }

      let translationResult;
      try {
        let cleanedText = responseText;
        if (cleanedText.includes('```json')) {
          cleanedText = cleanedText.replace(/```json\s*/, '').replace(/\s*```\s*$/, '');
        } else if (cleanedText.includes('```')) {
          cleanedText = cleanedText.replace(/```\s*/, '').replace(/\s*```\s*$/, '');
        }
        translationResult = JSON.parse(cleanedText.trim());
      } catch (parseError) {
        console.error(parseError);
        if (typeof data.response === 'object' && data.response !== null) {
          translationResult = data.response;
        } else if (typeof data === 'object' && data.translatedText) {
          translationResult = data;
        } else {
          translationResult = {
            provider: data.provider || 'openai',
            translatedText: responseText,
            sourceLanguage: languages.find((l) => l.code === sourceLang)?.name,
            targetLanguage: languages.find((l) => l.code === targetLang)?.name,
            style,
            confidence: 0.85,
          };
        }
      }

      let finalTranslation = '';
      const possibleFields = ['translatedText', 'translation', 'text', 'content', 'result', 'output'];
      for (const field of possibleFields) {
        if (translationResult[field]) {
          finalTranslation = typeof translationResult[field] === 'string' ? translationResult[field].trim() : String(translationResult[field]).trim();
          break;
        }
      }

      if (!finalTranslation && translationResult) {
        finalTranslation = typeof translationResult === 'string' ? translationResult.trim() : String(translationResult).trim();
      }

      if (!finalTranslation) {
        throw new Error('Translation service returned empty result');
      }

      setTranslatedText(finalTranslation);
      setError(null);
    } catch (error) {
      console.error('Translation error:', error);
      const parsedError = parseApiError(error);
      setError(`${parsedError.title}: ${parsedError.message}`);
      setTranslatedText('');
    } finally {
      setIsTranslating(false);
    }
  };

  const swapLanguages = () => {
    setSourceLang(targetLang);
    setTargetLang(sourceLang);
    setSourceText(translatedText);
    setTranslatedText(sourceText);
  };

  const copyToClipboard = async () => {
    try {
      await navigator.clipboard.writeText(translatedText);
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    } catch (error) {
      console.error('Failed to copy:', error);
    }
  };

  const clearAll = () => {
    setSourceText('');
    setTranslatedText('');
    setError(null);
  };

  return (
    <ToolPanel title="Language Translator" description="Translate text between languages with different tones" icon={Languages}>
      <form
        onSubmit={(e) => {
          e.preventDefault();
          handleTranslate();
        }}
        className="space-y-6"
      >
        <div className="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
          <Group variant="vertical" className="md:col-span-2">
            <label className="flex items-center gap-2 text-sm font-medium opacity-75 mb-2">
              <Globe size={16} />
              From
            </label>
            <DropdownMenu options={languages.map((lang) => ({ value: lang.code, label: `${lang.flag} ${lang.name}` }))} value={sourceLang} onChange={setSourceLang} />
          </Group>

          <div className="flex justify-center">
            <Button type="button" onClick={swapLanguages} variant="icon" title="Swap languages">
              <ArrowRightLeft size={20} />
            </Button>
          </div>

          <Group variant="vertical" className="md:col-span-2">
            <label className="flex items-center gap-2 text-sm font-medium opacity-75 mb-2">
              <Globe size={16} />
              To
            </label>
            <DropdownMenu options={languages.map((lang) => ({ value: lang.code, label: `${lang.flag} ${lang.name}` }))} value={targetLang} onChange={setTargetLang} />
          </Group>
        </div>

        <section>
          <label className="flex items-center gap-2 text-sm font-medium opacity-75 mb-3">
            <MessageCircle size={16} />
            Translation Style
          </label>
          <div className="grid grid-cols-2 md:grid-cols-4 gap-3">
            {styles.map((styleOption) => (
              <label
                key={styleOption.value}
                className="relative cursor-pointer rounded-sm border p-3 hover:opacity-80"
                style={
                  style === styleOption.value
                    ? { borderColor: 'var(--accent)', background: 'var(--accent-tint)', boxShadow: '0 0 0 2px var(--accent-border)' }
                    : { background: 'var(--bg-surface-1)', borderColor: 'var(--border-default)' }
                }
              >
                <input type="radio" name="style" value={styleOption.value} checked={style === styleOption.value} onChange={(e) => setStyle(e.target.value)} className="sr-only" />
                <div className="flex items-center space-x-2">
                  <span className="text-lg">{styleOption.icon}</span>
                  <div>
                    <div className="text-sm font-medium opacity-75">{styleOption.label}</div>
                    <div className="text-xs opacity-75">{styleOption.description}</div>
                  </div>
                </div>
              </label>
            ))}
          </div>
        </section>

        <section className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <Group variant="vertical" className="space-y-2">
            <label className="flex items-center gap-2 text-sm font-medium opacity-75">
              <Sparkles size={16} />
              Text to translate
            </label>
            <Textarea value={sourceText} onChange={(e) => setSourceText(e.target.value)} rows={7} placeholder="Enter your text here..." resize="none" className="px-4 py-3" />
            <p className="text-right text-xs opacity-60">{sourceText.length} characters</p>
          </Group>

          <div className="flow">
            <label className="flex items-center gap-2 text-sm font-medium opacity-75">
              <Languages size={16} />
              Translation
            </label>
            <div className="relative">
              <div className="tool-input-surface w-full h-48 px-4 py-3 overflow-auto">
                {isTranslating ? (
                  <div className="flex items-center justify-center h-full">
                    <div className="flex items-center space-x-2 tool-text-accent">
                      <div className="w-4 h-4 border-2 border-t-transparent rounded-full animate-spin" style={{ borderColor: 'var(--accent)', borderTopColor: 'transparent' }}></div>
                      <span className="text-sm">Translating...</span>
                    </div>
                  </div>
                ) : error ? (
                  <div className="flex items-center justify-center h-full p-4">
                    <div className="text-center max-w-sm">
                      <div className="tool-text-danger mb-2">
                        <MessageCircle className="w-8 h-8 mx-auto" />
                      </div>
                      <div className="text-sm font-medium tool-text-danger mb-1">{error.split(':')[0] || 'Translation Error'}</div>
                      <div className="text-xs tool-text-danger leading-relaxed">{error.split(':')[1]?.trim() || error}</div>
                      <Button type="button" onClick={() => setError(null)} variant="secondary" className="mt-3 text-xs">
                        Dismiss
                      </Button>
                    </div>
                  </div>
                ) : translatedText ? (
                  <div className="opacity-75">{translatedText}</div>
                ) : (
                  <div className="opacity-40 italic tool-text-muted">Translation will appear here...</div>
                )}
              </div>
              {translatedText && !error && (
                <div className="absolute top-2 right-2 flex gap-1">
                  <Button type="button" onClick={copyToClipboard} variant="icon" title="Copy translation">
                    {copied ? <Check size={16} className="tool-text-success" /> : <Copy size={16} />}
                  </Button>
                  <Button type="button" variant="icon" title="Listen to translation">
                    <Volume2 size={16} />
                  </Button>
                </div>
              )}
            </div>
          </div>
        </section>

        <footer className="flex flex-wrap gap-3 justify-between pt-4" style={{ borderTop: '1px solid var(--border-default)' }}>
          <Button type="button" onClick={clearAll} variant="secondary" className="px-4 py-2">
            Clear All
          </Button>
          <div className="flex gap-3">
            <Button type="submit" disabled={!sourceText.trim() || isTranslating} variant="primary" className="px-6 py-2">
              {isTranslating ? (
                <>
                  <div className="w-4 h-4 border-2 border-t-transparent rounded-full animate-spin" style={{ borderColor: 'var(--bg-page)', borderTopColor: 'transparent' }}></div>
                  Translating...
                </>
              ) : (
                <>
                  <Languages size={16} />
                  Translate
                </>
              )}
            </Button>
          </div>
        </footer>
      </form>
    </ToolPanel>
  );
}
