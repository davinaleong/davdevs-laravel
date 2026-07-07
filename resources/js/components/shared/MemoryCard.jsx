export default function MemoryCard({ id, icon: IconComponent, isFlipped, isMatched, isAnimating, onClick, disabled = false }) {
  const isVisible = isFlipped || isMatched;

  return (
    <button
      onClick={() => onClick(id)}
      className={`
        w-16 h-16 md:w-20 md:h-20 rounded-sm border-2 transition-all duration-300
        flex items-center justify-center relative overflow-hidden shadow-md hover:opacity-60
        ${isMatched
          ? 'bg-linear-to-br from-emerald-200 to-green-300 border-emerald-400 cursor-default shadow-green-200'
          : isVisible
            ? 'bg-linear-to-br from-blue-200 to-indigo-300 border-blue-400 cursor-default shadow-blue-200'
            : 'bg-linear-to-br from-purple-200 to-pink-300 border-purple-400 hover:from-purple-300 hover:to-pink-400 cursor-pointer shadow-purple-200'
        }
        ${isAnimating ? 'animate-pulse' : ''}
      `}
      disabled={disabled}
    >
      <div className={`transition-opacity duration-300 ${isVisible ? 'opacity-100' : 'opacity-0'}`}>
        <IconComponent className={`w-8 h-8 md:w-10 md:h-10 ${isMatched ? 'text-emerald-700' : 'text-indigo-700'}`} />
      </div>
      {!isVisible && (
        <div className="absolute inset-0 bg-linear-to-br from-purple-300 to-pink-400 flex items-center justify-center">
          <div className="w-8 h-8 bg-white rounded-full opacity-40 flex items-center justify-center">
            <div className="w-4 h-4 bg-purple-500 rounded-full opacity-60"></div>
          </div>
        </div>
      )}
    </button>
  );
}
