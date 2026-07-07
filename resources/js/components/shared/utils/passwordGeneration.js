export const symbols = ['!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '_', '+', '-', '=', '{', '}', '[', ']', ':', ';', '.', '?'];

export const basePhrases = [
  'embrace the beauty of the journey and never stop learning or dreaming',
  'the light of understanding shines brightest when shared with the world',
  'peaceful hearts create powerful change in chaotic times',
  'follow the rhythm of your heart and let passion guide your path',
  'every challenge is a step closer to strength and wisdom',
];

export const foreignWords = {
  journey: 'voyage',
  light: 'lumière',
  dream: 'sueño',
  world: 'mundo',
  peace: 'salem',
  challenge: 'reto',
  strength: 'forza',
  heart: 'corazón',
};

export function spellOut(word) {
  return word.normalize('NFD').replace(/\p{Diacritic}/gu, '');
}

export function generateSecurePassword() {
  const phrase = basePhrases[Math.floor(Math.random() * basePhrases.length)];

  const words = phrase.split(' ');
  const toCapCount = Math.floor(Math.random() * 3) + 2;
  const indexesToCap = new Set();
  while (indexesToCap.size < toCapCount) {
    indexesToCap.add(Math.floor(Math.random() * words.length));
  }

  const capitalizedPhrase = words
    .map((word, index) => (indexesToCap.has(index) ? word.charAt(0).toUpperCase() + word.slice(1) : word))
    .join('');

  let replaced = capitalizedPhrase;
  for (const [en, foreign] of Object.entries(foreignWords)) {
    const regex = new RegExp(en, 'i');
    if (regex.test(replaced)) {
      replaced = replaced.replace(regex, spellOut(foreign));
      break;
    }
  }

  const symbol = symbols[Math.floor(Math.random() * symbols.length)];
  const number = Math.floor(Math.random() * 900 + 100);

  return `${replaced}${symbol}${number}`;
}
