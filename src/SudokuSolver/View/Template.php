<?php

namespace SudokuSolver\View;

/**
 * This is meant to be an extremely simple templating engine with no fancy functionality.
 *
 * It is not meant to make templates the view-layer, as some frameworks do.
 * Its only purpuse is to break out HTML from the View-classes to their own files.
 *
 * It does this by taking an associative array, and replacing the keys in the template-file
 * with their corresponding value. Any logic is supposed to be handled in view-classes.
 */
class Template
{
    /**
     * @var string
     */
    private static $begin = '{{';

    /**
     * @var string
     */
    private static $end = '}}';

    /**
     * @var string
     */
    private $fileName;

    /**
     * @param string $fileName Path to file containing the template
     */
    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    public function render(array $options)
    {
        $template = $this->getTemplateString();

        // Replace all the variables in template
        foreach ($options as $key => $value) {
            $search = self::$begin . $key . self::$end;
            $template = str_replace($search, $value, $template);
        }

        return $template;
    }

    private function getTemplateString()
    {
        return 'testing {{test}} and stuff {{test2}}';
    }
}