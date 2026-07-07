import { useState, useEffect, useCallback } from 'react';
import Button from './shared/Button';
import MemoryCard from './shared/MemoryCard';
import ToolPanel from './shared/ToolPanel';
import {
  Brain, Heart, Star, Moon, Sun, Zap, Shield, Crown, Flame, Snowflake, Leaf,
  Droplet, Music, Camera, Coffee, Plane, Car, Bike, Ship, Train, Rocket,
  Mountain, Trees, Flower2, Bug, Fish, Bird, Cat, Dog, Rabbit, Trophy,
  Gift, Gem, Key, Lock, Bell, Clock, Calendar, Map, Compass, Target,
  Flag, Bookmark, Book, Pen, Palette, Brush, Scissors, Wrench, Hammer,
  Gamepad2, Dice1, Dice2, Dice3, Dice4, Dice5, Dice6,
} from 'lucide-react';

const ALL_ICONS = [
  Heart, Star, Moon, Sun, Zap, Shield, Crown, Flame, Snowflake, Leaf,
  Droplet, Music, Camera, Coffee, Plane, Car, Bike, Ship, Train, Rocket,
  Mountain, Trees, Flower2, Bug, Fish, Bird, Cat, Dog, Rabbit, Trophy,
  Gift, Gem, Key, Lock, Bell, Clock, Calendar, Map, Compass, Target,
  Flag, Bookmark, Book, Pen, Palette, Brush, Scissors, Wrench, Hammer,
  Gamepad2, Dice1, Dice2, Dice3, Dice4, Dice5, Dice6,
];

export default function MemoryCards() {
  const [level, setLevel] = useState(1);
  const [cards, setCards] = useState([]);
  const [flippedCards, setFlippedCards] = useState([]);
  const [matchedPairs, setMatchedPairs] = useState(0);
  const [gameState, setGameState] = useState('idle');
  const [moves, setMoves] = useState(0);
  const [timeLeft, setTimeLeft] = useState(0);
  const [score, setScore] = useState(0);
  const [lastLevel, setLastLevel] = useState(() => {
    try {
      const savedLevel = localStorage.getItem('memoryCards_lastLevel');
      return savedLevel ? parseInt(savedLevel, 10) || 0 : 0;
    } catch {
      return 0;
    }
  });

  const [bestScore, setBestScore] = useState(() => {
    try {
      const savedScore = localStorage.getItem('memoryCards_bestScore');
      return savedScore ? parseInt(savedScore, 10) || 0 : 0;
    } catch {
      return 0;
    }
  });

  const saveProgress = useCallback(
    (currentLevel, currentScore) => {
      try {
        const newBestScore = Math.max(currentScore, bestScore);
        localStorage.setItem('memoryCards_lastLevel', currentLevel.toString());
        localStorage.setItem('memoryCards_bestScore', newBestScore.toString());
        if (newBestScore > bestScore) setBestScore(newBestScore);
      } catch {
        console.error('Could not save progress to localStorage');
      }
    },
    [bestScore]
  );

  const getGameParams = useCallback((currentLevel) => {
    const baseCards = 4;
    const additionalCards = Math.floor(currentLevel * 1.5) * 2;
    const totalCards = Math.min(baseCards + additionalCards, 24);

    const baseTime = 60;
    const timePerCard = Math.max(3 - Math.floor(currentLevel / 3), 1.5);
    const totalTime = Math.floor(baseTime + totalCards * timePerCard);

    return { totalCards, totalTime };
  }, []);

  const initializeGame = useCallback(() => {
    const { totalCards, totalTime } = getGameParams(level);
    const pairs = totalCards / 2;

    const selectedIcons = ALL_ICONS.sort(() => Math.random() - 0.5).slice(0, pairs);

    const gameCards = [];
    selectedIcons.forEach((icon, index) => {
      gameCards.push({ id: `${index}-a`, icon, isFlipped: false, isMatched: false }, { id: `${index}-b`, icon, isFlipped: false, isMatched: false });
    });

    const shuffledCards = gameCards.sort(() => Math.random() - 0.5);

    setCards(shuffledCards);
    setFlippedCards([]);
    setMatchedPairs(0);
    setMoves(0);
    setTimeLeft(totalTime);
    setGameState('playing');
  }, [level, getGameParams]);

  const handleCardClick = (cardId) => {
    if (gameState !== 'playing') return;
    if (flippedCards.length >= 2) return;
    if (flippedCards.includes(cardId)) return;

    const card = cards.find((c) => c.id === cardId);
    if (card?.isMatched) return;

    const newFlippedCards = [...flippedCards, cardId];
    setFlippedCards(newFlippedCards);

    setCards((prevCards) => prevCards.map((c) => (c.id === cardId ? { ...c, isFlipped: true } : c)));

    if (newFlippedCards.length === 2) {
      setMoves((prev) => prev + 1);

      const [firstId, secondId] = newFlippedCards;
      const firstCard = cards.find((c) => c.id === firstId);
      const secondCard = cards.find((c) => c.id === secondId);

      if (firstCard && secondCard && firstCard.icon === secondCard.icon) {
        setTimeout(() => {
          setCards((prevCards) => prevCards.map((c) => (c.id === firstId || c.id === secondId ? { ...c, isMatched: true } : c)));
          setMatchedPairs((prev) => prev + 1);
          setFlippedCards([]);
          setScore((prev) => prev + 100 * level + Math.floor(timeLeft / 10));
        }, 500);
      } else {
        setTimeout(() => {
          setCards((prevCards) => prevCards.map((c) => (c.id === firstId || c.id === secondId ? { ...c, isFlipped: false } : c)));
          setFlippedCards([]);
        }, 1000);
      }
    }
  };

  useEffect(() => {
    if (gameState === 'playing' && timeLeft > 0) {
      const timer = setTimeout(() => setTimeLeft((prev) => prev - 1), 1000);
      return () => clearTimeout(timer);
    } else if (timeLeft === 0 && gameState === 'playing') {
      setTimeout(() => setGameState('lost'), 0);
    }
  }, [gameState, timeLeft]);

  useEffect(() => {
    if (gameState === 'playing' && matchedPairs > 0 && matchedPairs === cards.length / 2) {
      setTimeout(() => {
        setGameState('won');
        saveProgress(level, score);
      }, 0);
    }
  }, [matchedPairs, cards.length, gameState, level, score, saveProgress]);

  const nextLevel = () => {
    const newLevel = level + 1;
    setLevel(newLevel);
    setLastLevel(newLevel);
    saveProgress(newLevel, score);
    initializeGame();
  };

  const restartLevel = () => initializeGame();

  const resetGame = () => {
    saveProgress(level, score);
    setLevel(1);
    setScore(0);
    setGameState('idle');
  };

  const formatTime = (seconds) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
  };

  const getGridCols = () => {
    const cardCount = cards.length;
    if (cardCount <= 16) return 'grid-cols-4';
    return 'grid-cols-6';
  };

  return (
    <ToolPanel title="Memory Cards" description="Match pairs of cards to advance through endless levels. Each level gets progressively harder!" icon={Brain}>
      <aside className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6" aria-label="Game Statistics">
        <div className="bg-blue-100 p-3 rounded-sm text-center">
          <div className="text-sm text-blue-600 font-medium">Level</div>
          <div className="text-xl font-bold text-blue-800">{level}</div>
        </div>
        <div className="bg-green-100 p-3 rounded-sm text-center">
          <div className="text-sm text-green-600 font-medium">Score</div>
          <div className="text-xl font-bold text-green-800">{score}</div>
        </div>
        <div className="bg-orange-100 p-3 rounded-sm text-center">
          <div className="text-sm text-orange-600 font-medium">Moves</div>
          <div className="text-xl font-bold text-orange-800">{moves}</div>
        </div>
        <div className="bg-red-100 p-3 rounded-sm text-center">
          <div className="text-sm text-red-600 font-medium">Time</div>
          <div className="text-xl font-bold text-red-800">{formatTime(timeLeft)}</div>
        </div>
      </aside>

      {(lastLevel > 0 || bestScore > 0) && (
        <aside className="grid grid-cols-2 gap-4 mb-6" aria-label="Previous Records">
          <div className="bg-purple-100 p-3 rounded-sm text-center">
            <div className="text-sm text-purple-600 font-medium">Last Level Reached</div>
            <div className="text-xl font-bold text-purple-800">{lastLevel}</div>
          </div>
          <div className="bg-yellow-100 p-3 rounded-sm text-center">
            <div className="text-sm text-yellow-600 font-medium">Best Score</div>
            <div className="text-xl font-bold text-yellow-800">{bestScore.toLocaleString()}</div>
          </div>
        </aside>
      )}

      {gameState === 'idle' && (
        <section className="text-center mb-6" aria-label="Game Introduction">
          <h3 className="text-2xl font-bold mb-4">Memory Cards Game</h3>
          <p className="opacity-80 mb-4">Match pairs of cards to advance through endless levels. Each level gets progressively harder!</p>
          <Button onClick={initializeGame} variant="primary" className="px-6 py-3 font-semibold">
            Start Game
          </Button>
        </section>
      )}

      {gameState === 'won' && (
        <section className="text-center mb-6 p-4 tool-callout-success rounded-sm" aria-label="Level Complete">
          <h3 className="text-2xl font-bold mb-2">Level Complete! 🎉</h3>
          <p className="mb-4">
            Great job! You completed level {level} in {moves} moves.
          </p>
          <nav className="flex gap-3 justify-center" aria-label="Game Actions">
            <Button onClick={nextLevel} variant="success" className="px-6 py-3 font-semibold">
              Next Level
            </Button>
            <Button onClick={resetGame} variant="gray" className="px-6 py-3 font-semibold">
              Restart Game
            </Button>
          </nav>
        </section>
      )}

      {gameState === 'lost' && (
        <section className="text-center mb-6 p-4 tool-callout-danger rounded-sm" aria-label="Game Over">
          <h3 className="text-2xl font-bold mb-2">Time&apos;s Up! ⏰</h3>
          <p className="mb-4">Don&apos;t give up! Try level {level} again or restart from the beginning.</p>
          <nav className="flex gap-3 justify-center" aria-label="Game Actions">
            <Button onClick={restartLevel} variant="danger" className="px-6 py-3 font-semibold">
              Try Again
            </Button>
            <Button onClick={resetGame} variant="gray" className="px-6 py-3 font-semibold">
              Restart Game
            </Button>
          </nav>
        </section>
      )}

      {gameState === 'playing' && (
        <section aria-label="Game Board">
          <div className={`grid ${getGridCols()} gap-3 justify-items-center`}>
            {cards.map((card) => (
              <MemoryCard
                key={card.id}
                id={card.id}
                icon={card.icon}
                isFlipped={card.isFlipped}
                isMatched={card.isMatched}
                isAnimating={flippedCards.includes(card.id)}
                onClick={handleCardClick}
                disabled={card.isMatched || flippedCards.length >= 2}
              />
            ))}
          </div>

          <div className="mt-6" role="region" aria-label="Game Progress">
            <div className="flex justify-between text-sm mb-2">
              <span className="opacity-80">Progress</span>
              <span className="opacity-80">
                {matchedPairs}/{cards.length / 2} pairs
              </span>
            </div>
            <div className="w-full rounded-full h-2" style={{ background: 'var(--bg-surface-2)' }} role="progressbar" aria-valuenow={matchedPairs} aria-valuemax={cards.length / 2} aria-valuemin={0}>
              <div className="h-2 rounded-full transition-all duration-300" style={{ width: `${(matchedPairs / (cards.length / 2)) * 100}%`, background: 'var(--accent)' }}></div>
            </div>
          </div>
        </section>
      )}
    </ToolPanel>
  );
}
