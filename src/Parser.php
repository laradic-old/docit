<?php
/**
 * Part of the Laradic packages.
 */
namespace Laradic\Docit;

use Radic\BladeExtensions\Contracts\MarkdownRenderer;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Class Parser
 *
 * @package     Laradic\Docit
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
class Parser
{
    protected $markdown;
    protected $tags = [];

    /**
     * Instantiates the class
     *
     * @param \Radic\BladeExtensions\Contracts\MarkdownRenderer $markdown
     * @param array                                             $parserConfig
     */
    public function __construct(MarkdownRenderer $markdown, array $parserConfig = array())
    {
        $this->markdown = $markdown;
        $this->tags = isset($parserConfig['tags']) ? $parserConfig['tags'] : [];
    }

    protected function getDocBlocks($string)
    {
        $matches = [];
        $c       = preg_match_all('/\<\!---\+\s([\w\W]*?)\s\+--\>/', $string, $matches, PREG_PATTERN_ORDER);
        var_dump($c);
        var_dump($matches);

        return $matches;
    }

    protected function parseDocBlocks($string)
    {
        foreach ($this->tags as $tag => $replacement)
        {
            $string = preg_replace('/\<\!---\+\s(' . $tag . ')\s\+--\>/', $replacement, $string);
        }

        return $string;
    }

    public function parse($string)
    {
        $rendered = $this->markdown->render($string);

        return $this->parseDocBlocks($rendered);
    }
}
