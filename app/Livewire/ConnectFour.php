<?php

namespace App\Livewire;

use Livewire\Component;

class ConnectFour extends Component
{
    public $rows = 6;
    public $cols = 7;
    public $board = [];
    public $current_player = 1;
    public $winner = null;


    public function mount()
    {
        $this->board = array_fill(0, $this->rows, array_fill(0, $this->cols, 0));
    }

    private function placePiece($col, $player)
    {
        //place at the bottom 
        for ($r = $this->rows - 1; $r >= 0; $r--) {
            if ($this->board[$r][$col] === 0) {
                $this->board[$r][$col] = $player;
                break;
            }
        }
    }
    public function dropPiece($col)
    {
        if ($this->winner) return;

        $this->placePiece($col, $this->current_player);

        if ($this->checkWinner($this->board)) {
            $this->winner = "Human";
            return;
        }

        //Cpu move using minimax
        $cpuMove = $this->getBestMove($this->board, 6);
        $this->placePiece($cpuMove, 2);

        if ($this->checkWinner($this->board)) {
            $this->winner = "CPU";
            return;
        }
    }

    private function checkWinner($board)
    {
        $rows = count($board);
        $cols = count($board[0]);

        for ($r = 0; $r < $rows; $r++) {
            for ($c = 0; $c < $cols; $c++) {
                $player = $board[$r][$c];
                if ($player === 0) continue;

                if (
                    $c + 3 < $cols
                    && $player === $board[$r][$c + 1]
                    && $player === $board[$r][$c + 2]
                    && $player === $board[$r][$c + 3]
                ) return $player;


                if (
                    $r + 3 < $rows
                    && $player === $board[$r + 1][$c]
                    && $player === $board[$r + 2][$c]
                    && $player === $board[$r + 3][$c]
                ) return $player;


                if (
                    $r + 3 < $rows && $c + 3 < $cols
                    && $player === $board[$r + 1][$c + 1]
                    && $player === $board[$r + 2][$c + 2]
                    && $player === $board[$r + 3][$c + 3]
                ) return $player;


                if (
                    $r - 3 >= 0 && $c + 3 < $cols
                    && $player === $board[$r - 1][$c + 1]
                    && $player === $board[$r - 2][$c + 2]
                    && $player === $board[$r - 3][$c + 3]
                ) return $player;
            }
        }

        return null;
    }

    private function getBestMove($board, $depth)
    {
        $bestScore = -INF;
        $move = null;

        for ($col = 0; $col < $this->cols; $col++) {
            if ($board[0][$col] === 0) {
                $tempBoard = $board;
                $this->simulateMove($tempBoard, $col, 2);
                $score = $this->minimax($tempBoard, $depth - 1, false, -INF, INF);

                if ($score > $bestScore) {
                    $bestScore = $score;
                    $move = $col;
                }
            }
        }
        return $move;
    }

    private function minimax(&$board, $depth, $isMaximizing, $alpha, $beta)
    {
        $winner = $this->checkWinner($board);

        if ($winner === 2) return 10 + $depth;
        if ($winner === 1) return -10 - $depth;
        if ($depth === 0) return 0;


        if ($isMaximizing) {
            $maxEval = -INF;
            for ($col = 0; $col < $this->cols; $col++) {
                if ($board[0][$col] === 0) {
                    $temp = $board;
                    $this->simulateMove($temp, $col, 2);
                    $eval = $this->minimax($temp, $depth - 1, false, $alpha, $beta); // turn for human 
                    $maxEval = max($maxEval, $eval); // cpu keeps the best move
                    $alpha = max($alpha, $eval);
                    if ($beta <= $alpha) break;
                }
            }
            return $maxEval;
        } else {
            $minEval = INF;
            for ($col = 0; $col < $this->cols; $col++) {
                if ($board[0][$col] === 0) {
                    $temp = $board;
                    $this->simulateMove($temp, $col, 1);
                    $eval = $this->minimax($temp, $depth - 1, true, $alpha, $beta);
                    $minEval = min($minEval, $eval);
                    $beta = min($beta, $eval);
                    if ($beta <= $alpha) break;
                }
            }
            return $minEval;
        }
    }

    private function simulateMove(&$board, $col, $player)
    {
        for ($r = $this->rows - 1; $r >= 0; $r--) {
            if ($board[$r][$col] === 0) {
                $board[$r][$col] = $player;
                break;
            }
        }
    }

   public function resetGame()
{
    $this->board = array_fill(0, $this->rows, array_fill(0, $this->cols, 0));
    $this->winner = null;
}


    public function render()
    {
        return view('livewire.connect-four');
    }
}
