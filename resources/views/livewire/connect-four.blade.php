<div class="grid gap-2">
    @if ($winner)
        <div class="text-center text-xl font-bold text-green-600">
            Winner: {{ $winner }}
        </div>
    @endif

    <div class="grid grid-cols-7 gap-1 bg-blue-500 p-2 rounded-lg">
        @foreach ($board as $rIndex => $row)
            @foreach ($row as $cIndex => $cell)
                <div wire:click="dropPiece({{ $cIndex }})"
                    class="w-12 h-12 flex items-center justify-center rounded-full cursor-pointer
                           {{ $cell === 1 ? 'bg-yellow-400' : ($cell === 2 ? 'bg-red-500' : 'bg-white') }}">
                </div>
            @endforeach
        @endforeach
    </div>
    <button wire:click="resetGame" class="bg-red-500 text-white px-4 py-2 rounded">
        Reset Game
    </button>
</div>
