<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Docit\Parsers\Phpdoc;

use ArrayAccess;
use Laradic\Support\Traits\DotArrayAccessTrait;

/**
 * This is the File class.
 *
 * @package        Laradic\Docit
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
class File implements ArrayAccess
{

    use DotArrayAccessTrait;

    protected function getArrayAccessor()
    {
        return 'attributes';
    }

    protected $attributes = [ ];


    public function __construct($f)
    {
        $this->dochead = [
            'description'     => $f[ 'docblock' ][ 'description' ],
            'longDescription' => $f[ 'docblock' ][ 'long_description' ],
        ];

        $this->type = 'trait';
        if ( array_key_exists('class', $f) )
        {
            $this->type = 'class';
        }
        elseif ( array_key_exists('interface', $f) )
        {
            $this->type = 'interface';
        }

        // object
        $o              = $f[ $this->type ];
        $this->name     = $o[ 'name' ];
        $this->fullName = $o[ 'full_name' ];
        $this->extends  = $o[ 'extends' ];
        $this->docblock = $this->parseDocblock($o[ 'docblock' ]);

        // object attributes
        $a                = $o[ '@attributes' ];
        $this->path       = $a[ 'path' ];
        $this->isFinal    = $a[ 'final' ] === "true";
        $this->isAbstract = $a[ 'abstract' ] === "true";
        $this->namespace  = $a[ 'namespace' ];
        $this->package    = $a[ 'package' ];
        if($this->name == 'ThemeFactory'){
            $aaa = 'aa';
        }
        $this->methods    = $this->parseMethods($o[ 'method' ]);
        $this->properties = $this->parseProperties($o[ 'property' ]);
        $this->source     = $f[ 'source' ];

    }

    protected function parseGlobal($p)
    {
        $a = $p[ '@attributes' ];

        return [
            'name'       => $p[ 'name' ],
            'fullName'   => $p[ 'full_name' ],
            'isStatic'   => $a[ 'static' ] === "true",
            'isFinal'    => $a[ 'final' ] === "true",
            'visibility' => $a[ 'visibility' ],
            'namespace'  => $a[ 'namespace' ],
            'package'    => $a[ 'package' ]
        ];
    }

    protected function parseProperties($properties)
    {
        $parsed = [ ];
        foreach ( $properties as $p )
        {
            $parsed[ ] = array_replace($this->parseGlobal($p), [
                'default' => $p[ 'default' ],
                'description' => $p['docblock']['description'],
                'longDescription' => $p['docblock']['long-description'],
                'type' => $p[ 'docblock' ]['tag']['type'],
                'docblock'   => $this->parseDocblock($p[ 'docblock' ])
            ]);
        }

        return $parsed;
    }

    protected function parseMethods($methods)
    {
        $parsed = [ ];
        foreach ( $methods as $p )
        {
            $g = $this->parseGlobal($p);
            $db = $this->parseDocblock($p[ 'docblock' ]);
            $args = [ ];
            $return = null;
            foreach($db['tags'] as $tag){
                if($tag['name'] === 'param'){
                    $args[] = $tag;
                } elseif($tag['name'] === 'return') {
                    $return = $tag;
                }
            }

            $parsed[ ] = array_replace($g, [
                'docblock'   => $this->parseDocblock($p[ 'docblock' ]),
                'isAbstract' => $p[ 'abstract' ] === 'true',
                'arguments'  => $args,
                'return' => $return
            ]);
        }

        return $parsed;
    }

    protected function parseDocblock($docblock)
    {
        $parsed = [
            'description'     => $docblock[ 'description' ],
            'longDescription' => $docblock[ 'long_description' ],
            'tags'            => [ ]
        ];


        foreach ( $docblock[ 'tag' ] as $tag )
        {
            $parsed[ 'tags' ][ ] = $tag[ '@attributes' ];
        }

        return $parsed;
    }

    /**
     * get source value
     *
     * @return mixed
     */
    public function getSource()
    {
        return gzuncompress(base64_decode($this->source));
    }

    /**
     * Dynamically access container services.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this[ $key ];
    }

    /**
     * Dynamically set container services.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this[ $key ] = $value;
    }
}
