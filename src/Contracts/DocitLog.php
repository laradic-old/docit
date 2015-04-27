<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Docit\Contracts;

use Illuminate\Contracts\Logging\Log;

/**
 * This is the DocitLog class.
 *
 * @package        Laradic\Docit
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
interface DocitLog extends Log
{

    /**
     * Returns all log entries as array
     *
     * @param bool $messageAsKeys
     * @return array Array of log entries
     * @throws \ErrorException
     */
    public function getLogEntries($messageAsKeys = false);
}
