<?php

namespace SudokuSolver\View;

use SudokuSolver\Model\Solution;
use SudokuSolver\View\SudokuGridHelper;

/**
 * Displays an immutable solution where cells are marked as
 * part of the original puzzle or solved by the system.
 */
class SolutionView
{
    /**
     * @var Solution
     */
    protected $solution;

    /**
     * @var Template
     */
    private $cellTpl;

    /**
     * @var SudokuGridHelper
     */
    private $gridHelper;

    private $optionsView;

    public function __construct(Solution $solution)
    {
        $this->solution = $solution;

        $this->cellTpl = Template::getTemplate('sudokuCellStatic');
        $this->layoutTpl = Template::getTemplate('sudokuSolutionLayout');
        $this->gridHelper = new SudokuGridHelper();
    }

    public function render()
    {
        return $this->layoutTpl->render(
            array(
                'solution' => $this->renderSolution(),
                'timer' => '3.5ms' // TODO: Real value
            )
        );
    }

    private function renderSolution()
    {
        return $this->gridHelper->render(function ($row, $col) {

            $options = array(
                'content' => $this->solution->getCell($row, $col),
                'class' => $this->solution->isGiven($row, $col) ? 'solved' : ''
            );

            return $this->cellTpl->render($options);
        });
    }
}
