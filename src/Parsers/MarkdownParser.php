<?php
/**
 * Part of the Laradic packages.
 */
namespace Laradic\Docit\Parsers;

use Radic\BladeExtensions\Contracts\MarkdownRenderer;

/**
 * Class Parser
 *
 * @package     Laradic\Docit
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
class MarkdownParser
{

    protected $markdown;

    protected $tags = [ ];

    /**
     * Instantiates the class
     *
     * @param \Radic\BladeExtensions\Contracts\MarkdownRenderer $markdown
     * @param array                                             $parserConfig
     */
    public function __construct(MarkdownRenderer $markdown, array $parserConfig = array())
    {
        $this->markdown = $markdown;
        $this->tags     = isset($parserConfig[ 'tags' ]) ? $parserConfig[ 'tags' ] : [ ];
    }

    protected function parseDocBlocks($string)
    {
        foreach ( $this->tags as $tag => $replacement )
        {
            $string = preg_replace('/\<\!---\+\s' . $tag . '\s\+--\>/', "\n$replacement\n", $string);
        }

        return $string;
    }

    public function parse($string)
    {
        $rendered = $this->markdown->render($string);
        $parsed   = $this->parseDocBlocks($rendered);

        return $parsed;
    }
}
