<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Docit\Phpdoc;

use PHPDocMD\Generator as BaseGenerator;

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
class Generator extends BaseGenerator
{
    public function __construct(array $classDefinitions, $outputDir, $templateDir, $linkTemplate = '%c.md')
    {
        parent::__construct($classDefinitions, $outputDir, $templateDir, $linkTemplate);
    }

    /**
     * get outputDir value
     *
     * @return string
     */
    public function getOutputDir()
    {
        return $this->outputDir;
    }

    /**
     * Set the outputDir value
     *
     * @param string $outputDir
     * @return Generator
     */
    public function setOutputDir($outputDir)
    {
        $this->outputDir = $outputDir;

        return $this;
    }

    /**
     * get classDefinitions value
     *
     * @return array
     */
    public function getClassDefinitions()
    {
        return $this->classDefinitions;
    }

    /**
     * Set the classDefinitions value
     *
     * @param array $classDefinitions
     * @return Generator
     */
    public function setClassDefinitions($classDefinitions)
    {
        $this->classDefinitions = $classDefinitions;

        return $this;
    }

    /**
     * get templateDir value
     *
     * @return string
     */
    public function getTemplateDir()
    {
        return $this->templateDir;
    }

    /**
     * Set the templateDir value
     *
     * @param string $templateDir
     * @return Generator
     */
    public function setTemplateDir($templateDir)
    {
        $this->templateDir = $templateDir;

        return $this;
    }

    /**
     * get linkTemplate value
     *
     * @return string
     */
    public function getLinkTemplate()
    {
        return $this->linkTemplate;
    }

    /**
     * Set the linkTemplate value
     *
     * @param string $linkTemplate
     * @return Generator
     */
    public function setLinkTemplate($linkTemplate)
    {
        $this->linkTemplate = $linkTemplate;

        return $this;
    }

}
