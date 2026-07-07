import { useState, useCallback } from 'react';
import { RotateCcw, Flag, Bomb } from 'lucide-react';

const DIFFICULTIES = {
  beginner: { rows: 9, cols: 9, mines: 10 },
  intermediate: { rows: 16, cols: 16, mines: 40 },
  expert: { rows: 16, cols: 30, mines: 99 },
};

function createBoard(rows, cols) {
  const newBoard = [];
  for (let row = 0; row < rows; row++) {
    newBoard[row] = [];
    for (let col = 0; col < cols; col++) {
      newBoard[row][col] = { isMine: false, isRevealed: false, isFlagged: false, adjacentMines: 0 };
    }
  }
  return newBoard;
}

export default function Minesweeper() {
  const [difficulty, setDifficulty] = useState('beginner');
  const [board, setBoard] = useState(() => {
    const { rows, cols } = DIFFICULTIES.beginner;
    return createBoard(rows, cols);
  });
  const [gameState, setGameState] = useState('playing');
  const [mineCount, setMineCount] = useState(DIFFICULTIES.beginner.mines);
  const [firstClick, setFirstClick] = useState(true);

  const initializeBoard = useCallback((rows, cols) => createBoard(rows, cols), []);

  const placeMines = useCallback((board, rows, cols, mines, firstRow, firstCol) => {
    const newBoard = board.map((row) => row.map((cell) => ({ ...cell })));
    let minesPlaced = 0;

    while (minesPlaced < mines) {
      const row = Math.floor(Math.random() * rows);
      const col = Math.floor(Math.random() * cols);

      if (!newBoard[row][col].isMine && !(row === firstRow && col === firstCol)) {
        newBoard[row][col].isMine = true;
        minesPlaced++;
      }
    }

    for (let row = 0; row < rows; row++) {
      for (let col = 0; col < cols; col++) {
        if (!newBoard[row][col].isMine) {
          let count = 0;
          for (let dr = -1; dr <= 1; dr++) {
            for (let dc = -1; dc <= 1; dc++) {
              const newRow = row + dr;
              const newCol = col + dc;
              if (newRow >= 0 && newRow < rows && newCol >= 0 && newCol < cols) {
                if (newBoard[newRow][newCol].isMine) count++;
              }
            }
          }
          newBoard[row][col].adjacentMines = count;
        }
      }
    }

    return newBoard;
  }, []);

  const revealCell = useCallback(
    (board, row, col) => {
      const { rows, cols } = DIFFICULTIES[difficulty];
      const newBoard = board.map((r) => r.map((c) => ({ ...c })));

      const revealRecursively = (currentRow, currentCol) => {
        if (currentRow < 0 || currentRow >= rows || currentCol < 0 || currentCol >= cols) return;

        const cell = newBoard[currentRow][currentCol];
        if (cell.isFlagged || cell.isRevealed) return;

        cell.isRevealed = true;

        if (cell.adjacentMines === 0 && !cell.isMine) {
          for (let dr = -1; dr <= 1; dr++) {
            for (let dc = -1; dc <= 1; dc++) {
              if (dr !== 0 || dc !== 0) revealRecursively(currentRow + dr, currentCol + dc);
            }
          }
        }
      };

      revealRecursively(row, col);
      return newBoard;
    },
    [difficulty]
  );

  const handleCellClick = useCallback(
    (row, col) => {
      if (gameState !== 'playing') return;

      const { rows, cols, mines } = DIFFICULTIES[difficulty];

      let newBoard = board;

      if (firstClick) {
        newBoard = placeMines(board, rows, cols, mines, row, col);
        setFirstClick(false);
      }

      if (newBoard[row][col].isFlagged) return;

      if (newBoard[row][col].isMine) {
        newBoard = newBoard.map((r) => r.map((c) => (c.isMine ? { ...c, isRevealed: true } : c)));
        setBoard(newBoard);
        setGameState('lost');
        return;
      }

      newBoard = revealCell(newBoard, row, col);
      setBoard(newBoard);

      const totalCells = rows * cols;
      const revealedCells = newBoard.flat().filter((cell) => cell.isRevealed).length;
      if (revealedCells === totalCells - mines) {
        setGameState('won');
      }
    },
    [board, gameState, difficulty, firstClick, placeMines, revealCell]
  );

  const handleCellRightClick = useCallback(
    (e, row, col) => {
      e.preventDefault();
      if (gameState !== 'playing' || board[row][col].isRevealed) return;

      const newBoard = board.map((r) => r.map((c) => ({ ...c })));
      newBoard[row][col].isFlagged = !newBoard[row][col].isFlagged;

      const flaggedCount = newBoard.flat().filter((cell) => cell.isFlagged).length;
      setMineCount(DIFFICULTIES[difficulty].mines - flaggedCount);
      setBoard(newBoard);
    },
    [board, gameState, difficulty]
  );

  const resetGame = useCallback(() => {
    const { rows, cols, mines } = DIFFICULTIES[difficulty];
    setBoard(initializeBoard(rows, cols));
    setGameState('playing');
    setMineCount(mines);
    setFirstClick(true);
  }, [difficulty, initializeBoard]);

  const changeDifficulty = useCallback(
    (newDifficulty) => {
      setDifficulty(newDifficulty);
      const { rows, cols, mines } = DIFFICULTIES[newDifficulty];
      setBoard(initializeBoard(rows, cols));
      setGameState('playing');
      setMineCount(mines);
      setFirstClick(true);
    },
    [initializeBoard]
  );

  const getCellDisplay = (cell) => {
    if (cell.isFlagged) return <Flag size={16} className="text-red-500" />;
    if (!cell.isRevealed) return '';
    if (cell.isMine) return <Bomb size={16} className="text-red-600" />;
    return cell.adjacentMines > 0 ? cell.adjacentMines : '';
  };

  const getCellClassName = (cell) => {
    let className = 'w-8 h-8 border border-gray-400 flex items-center justify-center text-sm font-bold select-none cursor-pointer ';

    if (cell.isRevealed) {
      if (cell.isMine) {
        className += 'bg-red-500 text-white';
      } else {
        className += 'bg-gray-200 dark:bg-gray-600 ';
        if (cell.adjacentMines === 1) className += 'text-blue-600';
        else if (cell.adjacentMines === 2) className += 'text-green-600';
        else if (cell.adjacentMines === 3) className += 'text-red-600';
        else if (cell.adjacentMines === 4) className += 'text-purple-600';
        else if (cell.adjacentMines === 5) className += 'text-yellow-600';
        else if (cell.adjacentMines === 6) className += 'text-pink-600';
        else if (cell.adjacentMines === 7) className += 'text-black dark:text-white';
        else if (cell.adjacentMines === 8) className += 'text-gray-600';
      }
    } else {
      className += 'bg-gray-300 dark:bg-gray-700 hover:bg-gray-400 dark:hover:bg-gray-600';
      if (cell.isFlagged) className += ' bg-yellow-200 dark:bg-yellow-800';
    }

    return className;
  };

  const { cols } = DIFFICULTIES[difficulty];

  return (
    <div className="minesweeper-component max-w-4xl mx-auto p-4">
      <div className="mb-6">
        <h1 className="text-2xl font-bold mb-4" style={{ color: 'var(--text-primary)' }}>
          Minesweeper
        </h1>

        <div className="flex gap-2 mb-4">
          {Object.keys(DIFFICULTIES).map((diff) => (
            <button
              key={diff}
              onClick={() => changeDifficulty(diff)}
              className="px-3 py-1 rounded text-sm font-medium transition-colors"
              style={
                difficulty === diff
                  ? { background: 'var(--accent)', color: 'var(--bg-page)' }
                  : { background: 'var(--bg-surface-2)', color: 'var(--text-secondary)' }
              }
            >
              {diff.charAt(0).toUpperCase() + diff.slice(1)}
            </button>
          ))}
        </div>

        <div className="flex items-center gap-4 mb-4">
          <div className="flex items-center gap-2">
            <Flag size={20} className="text-red-500" />
            <span className="font-mono text-lg font-bold">{mineCount.toString().padStart(3, '0')}</span>
          </div>

          <button
            onClick={resetGame}
            className="flex items-center gap-2 px-3 py-1 rounded transition-colors"
            style={{ background: 'var(--accent)', color: 'var(--bg-page)' }}
          >
            <RotateCcw size={16} />
            New Game
          </button>

          <div className="text-lg font-bold">
            {gameState === 'won' && <span className="tool-text-success">You Won! 🎉</span>}
            {gameState === 'lost' && <span className="tool-text-danger">Game Over 💥</span>}
            {gameState === 'playing' && <span className="tool-text-muted">Playing...</span>}
          </div>
        </div>
      </div>

      <div className="inline-block border-2 border-gray-500 bg-gray-100 dark:bg-gray-800 p-2" style={{ maxWidth: 'fit-content' }}>
        <div className="grid gap-0" style={{ gridTemplateColumns: `repeat(${cols}, 1fr)` }}>
          {board.map((row, rowIndex) =>
            row.map((cell, colIndex) => (
              <button
                key={`${rowIndex}-${colIndex}`}
                className={getCellClassName(cell)}
                onClick={() => handleCellClick(rowIndex, colIndex)}
                onContextMenu={(e) => handleCellRightClick(e, rowIndex, colIndex)}
                disabled={gameState !== 'playing'}
              >
                {getCellDisplay(cell)}
              </button>
            ))
          )}
        </div>
      </div>

      <div className="mt-6 text-sm tool-text-muted">
        <p>
          <strong>How to play:</strong>
        </p>
        <ul className="list-disc list-inside mt-2 space-y-1">
          <li>Left click to reveal a cell</li>
          <li>Right click to flag/unflag a cell</li>
          <li>Numbers show how many mines are adjacent to that cell</li>
          <li>Flag all mines and reveal all safe cells to win</li>
        </ul>
      </div>
    </div>
  );
}
