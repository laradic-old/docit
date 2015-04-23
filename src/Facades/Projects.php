<?php
/**
 * Part of the Radic packages.
 */
namespace Laradic\Docit\Facades;

use Illuminate\Support\Facades\Facade;
/**
 * Class ProjectFactory
 *
 * @package     Laradic\Docit\Contracts
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
class Projects extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        return 'docit.projects';
    }
}
