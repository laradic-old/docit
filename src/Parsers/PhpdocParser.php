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

    public function parse($xmlString)
    {
        $xml = simplexml_load_string($xmlString);
        $data = json_decode(json_encode($xml), true);
        $filesData = [];

        # create file objects
        foreach ( $data[ 'file' ] as $file )
        {
            $filesData[ ] = new File($file);
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

    protected function getTree()
    {
        return $this->tree;
    }


}
