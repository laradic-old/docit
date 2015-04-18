<?php
 /**
 * Part of the Radic packages.
 */
namespace Laradic\Docit\Contracts;
/**
 * Class ProjectFactory
 *
 * @package     Laradic\Docit\Contracts
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
interface ProjectFactory
{

    /**
     * all
     *
     * @param bool $bySlug
     * @return array|\Laradic\Docit\Projects\Project[]
     */
    public function all($bySlug = false);

    public function has($slug);

    /**
     * get
     *
     * @param $slug
     * @return \Laradic\Docit\Projects\Project
     */
    public function get($slug);
    
    public function slugs();
    public function make($slug);
    public function path();

    public function url($project = null, $version = null, $page = null);

    /**
     * Get the value of config
     *
     * @return array
     */
    public function getConfig();

}
