<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Docit\Phpdoc;

use PHPDocMD\Parser as BaseParser;

/**
 * This is the Generator class.
 *
 * @package        Laradic\Docit
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
class Parser extends BaseParser
{
    public function __construct($structureXmlFile)
    {
        parent::__construct($structureXmlFile);
    }

    /**
     * get structureXmlFile value
     *
     * @return string
     */
    public function getStructureXmlFile()
    {
        return $this->structureXmlFile;
    }

    /**
     * Set the structureXmlFile value
     *
     * @param string $structureXmlFile
     * @return Parser
     */
    public function setStructureXmlFile($structureXmlFile)
    {
        $this->structureXmlFile = $structureXmlFile;

        return $this;
    }

}
