<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Docit\Parsers;

use Laradic\Docit\Parsers\Phpdoc\File;

/**
 * This is the Parser class.
 *
 * @package        Laradic\Docit
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
class PhpdocParser
{

    protected $tree;

    protected $tree2 = [ ];

    public function parse($xmlString)
    {
        $xml       = simplexml_load_string($xmlString);
        $data      = json_decode(json_encode($xml), true);
        $filesData = [ ];

        # create file objects
        foreach ( $data[ 'file' ] as $file )
        {
            $file         = new File($file);
            $filesData[ ] = $file;
            $this->insertIntoTree($file);
        }

        # create the tree using the namespace we got in our files
        $this->makeEmptyTree($filesData);

        # now fill the tree
        foreach ( $filesData as $file )
        {
            $this->putIntoTree($file);
        }

        # return the tree, cuz we're done
        return $this->getTree();
    }

    protected function makeEmptyTree($filesData)
    {
        $this->tree = [ ];
        foreach ( $filesData as $file )
        {
            $current =& $this->tree;
            foreach ( explode('\\', $file->namespace) as $part )
            {
                if ( ! isset($part) )
                {
                    $a = 'a';
                }
                if ( ! isset($current[ $part ]) )
                {
                    $current[ $part ] = array();
                }
                $current =& $current[ $part ];
            }
        }
    }

    protected function putIntoTree(File $file)
    {
        $tree  =& $this->tree;
        $parts = array_merge(explode('\\', $file->namespace), [ $file->name ]);
        foreach ( $parts as $part )
        {
            if ( isset($tree[ $part ]) )
            {
                $tree =& $tree[ $part ];
            }
            else
            {
                $tree[ ] = $file;
            }
        }
    }

    protected function insertIntoTree(File $file)
    {
        $tree  =& $this->tree2;
        $parts = array_merge(explode('\\', $file->namespace), [ $file->name ]);
        foreach ( $parts as $i => $part )
        {
            if ( ! isset($tree[ $part ]) )
            {
                $tree[ $part ] = array();
            }
            elseif ( (count($parts) - 1) === $i  )
            {
                $tree[ ] = $file;
            }

            $tree =& $tree[ $part ];
            if(count($this->tree2) > 1)
            {
                $a ='a';
            }
        }
    }

    protected function getTree()
    {
        return $this->tree;
    }
}
