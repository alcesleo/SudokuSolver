<?php

namespace SudokuSolver\View;

use SudokuSolver\Model\SudokuReader;
use SudokuSolver\View\MultipleSudokuInputView;
use SudokuSolver\View\Template;
use Exception;

// Common code for TextArea- and TextFile-inputs
abstract class TextSudokuInputView extends MultipleSudokuInputView
{
    /**
     * @var Template
     */
    private $template;

    /**
     * @var SudokuReader
     */
    private $reader;

    /**
     * Names of form elements
     * @var string
     */
    private static $zeroCharName = 'zeroChar';
    private static $delimiterName = 'sudokuDelimiter';

    public function __construct()
    {
        parent::__construct();
        $this->template = Template::getTemplate('sudokuTextInputLayout');
        $this->reader = new SudokuReader();
    }

    // ---------- Abstract methods ----------

    /**
     * Render the input text portion
     * @return string HTML
     */
    abstract protected function renderTextInput();

    /**
     * Get the input text, can throw an error if nothing was sent
     * @return string
     */
    abstract protected function getTextInput();

    // ---------- Implement SudokuInputView methods ----------

    /**
     * From SudokuInputView
     * @return string HTML
     */
    protected function renderSudokuInput()
    {
        return $this->template->render(
            array(
                'sudoku' => $this->renderTextInput(),
                'zeroCharName' => self::$zeroCharName,
                'delimiterName' => self::$delimiterName,
                'zeroCharValue' => $this->getZeroChar(),
                'delimiterValue' => $this->getDelimiter()
            )
        );
    }

    /**
     * From SudokuInputView
     * If no delimiter is set, tries to parse as single sudoku
     * @return Sudoku
     * @throws Exception If delimiter IS set
     */
    public function getSudoku()
    {
        if ($this->hasMultipleSudokus()) {
            throw new Exception('More than one sudoku sent');
        }
        return $this->parseSudoku($this->getTextInput());
    }

    // ---------- Implement MultipleSudokuInputView methods ----------

    /**
     * From MultipleSudokuInputView
     * If the user has sent multiple sudokus (AND a way to separate them)
     * @return boolean
     */
    public function hasMultipleSudokus()
    {
        // If no delimiter is set, can't treat as multiple sudokus
        return (bool)$this->getDelimiter();
    }

    /**
     * From MultipleSudokuInputView
     * @return Sudoku[]
     * @throws Exception If no delimiter is set
     */
    public function getSudokus()
    {
        if (! $this->hasMultipleSudokus()) {
            throw new Exception('No delimiter sent');
        }

        // Split the string by delimiter
        $regex = '/' . $this->getDelimiter() . '/';
        $sudokuStrings = preg_split($regex, $this->getTextInput());

        // NOTE: Takes care of empty first element if string begins with delimiter
        if ($sudokuStrings[0] == '') {
            array_shift($sudokuStrings);
        }


        // Parse all individual strings
        $sudokus = array();
        foreach ($sudokuStrings as $sudokuStr) {
            $sudokus[] = $this->parseSudoku($sudokuStr);
        }

        return $sudokus;
    }


    // ---------- Text helpers ----------

    /**
     * Parse a sudoku from a string
     * @param  string $str sudoku
     * @return Sudoku
     */
    private function parseSudoku($str)
    {
        $zeroChar = $this->getZeroChar();
        return $this->reader->fromString($str, $zeroChar);
    }

    /**
     * Returns input zeroChar or empty string if not sent
     * @return string char
     */
    private function getZeroChar()
    {
        return isset($_POST[self::$zeroCharName]) ? $_POST[self::$zeroCharName] : '';
    }

    /**
     * @return string delimiter
     */
    private function getDelimiter()
    {
        return isset($_POST[self::$delimiterName]) ? $_POST[self::$delimiterName] : '';
    }
}
