<?php

namespace SudokuSolver\Model;

use SudokuSolver\Model\Sudoku;
use SudokuSolver\Model\SolverInterface;
use Exception;

/**
 * Contains a solution to a sudoku
 */
class Solution
{
    /**
     * The unsolved puzzle
     * @var Sudoku
     */
    private $original;

    /**
     * The puzzle with the answers
     * @var Sudoku
     */
    private $solution;

    /**
     * Time it took the algorithm to solve
     * @var float
     */
    private $timeToSolve;

    /**
     * @param Sudoku $original  Unsolved sudoku
     * @param Sudoku $solution  Solved sudoku
     * @param float  $timeToSolve Time in milliseconds it took the algorithm to complete
     */
    public function __construct(Sudoku $original, Sudoku $solution, $timeToSolve = 0.0)
    {
        $this->original = $original;
        $this->solution = $solution;
        $this->timeToSolve = $timeToSolve;
    }

    /**
     * Get a cell in the solved sudoku
     * @param  int $row
     * @param  int $col
     * @return int
     */
    public function getCell($row, $col)
    {
        return $this->solution->getCell($row, $col);
    }

    /**
     * Facade.
     *
     * If a cell in the sudoku was solved by the algorithm, or if it was
     * part of the original puzzle.
     *
     * @param  int  $row
     * @param  int  $col
     * @return bool      True if solved by system, false if there to begin with
     */
    public function isGiven($row, $col)
    {
        return ! $this->original->isFilled($row, $col);
    }

    /**
     * @return Sudoku
     */
    public function getOriginalSudoku()
    {
        // TODO: Maybe return clone, to prevent privacy leak
        return $this->original;
    }

    /**
     * @return Sudoku
     */
    public function getSolvedSudoku()
    {
        return $this->solution;
    }

    public function getExecutionTime()
    {
        return $this->timeToSolve;
    }

    /**
     * Convenience method to create a solution-object, does not alter original sudoku.
     * @param  Sudoku $sudoku
     * @return Solution
     */
    public static function getSolution(Sudoku $sudoku, SolverInterface $algorithm)
    {
        // Save original
        $puzzle = $sudoku;
        $original = clone($sudoku);

        // Time execution
        $timerStart = microtime(true);

        // Run the solver
        $algorithm->solve($puzzle);

        // Get the time
        $timerStop = microtime(true);
        $timeToSolve = $timerStop - $timerStart;

        // Don't take the solvers word for it...
        if ($puzzle->isSolved()) {
            return new Solution($original, $puzzle, $timeToSolve);
        } else {
            throw new Exception('Could not solve sudoku');
        }
    }
}
