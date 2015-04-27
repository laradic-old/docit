<?php
/**
 * Part of the Radic packages.
 */
namespace Laradic\Docit\Github;

use GrahamCampbell\GitHub\GitHubManager;

/**
 * Class RepoContent
 *
 * @package     Laradic\Docit\Github
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 * @method exists($path, $ref)
 * @method show($path, $ref)
 */
class RepoContent
{

    /**
     * @var string
     */
    protected $username;

    /** @var string */
    protected $repository;

    /** @var \GrahamCampbell\GitHub\GitHubManager  */
    protected $gh;

    /**
     * Instanciates the class
     *
     * @param                                      $username
     * @param                                      $repository
     * @param \GrahamCampbell\GitHub\GitHubManager $gh
     */
    public function __construct($username, $repository, GithubManager $gh)
    {
        $this->username   = $username;
        $this->repository = $repository;
        $this->gh         = $gh;
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([ $this->gh->repo()->contents(), $name ], array_merge([ $this->username, $this->repository ], $arguments));
    }
}
