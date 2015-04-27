<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Docit\Log;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Log\Writer as BaseWriter;
use Laradic\Docit\Contracts\DocitLog as DocitLogContract;
use Monolog\Handler\TestHandler;
use Monolog\Logger as MonologLogger;

/**
 * This is the Log class.
 *
 * @package        Laradic\Docit
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
class Writer extends BaseWriter implements DocitLogContract
{
    /**
     * {@inheritDoc}
     */
    public function __construct(MonologLogger $monolog, Dispatcher $dispatcher = null)
    {
        parent::__construct($monolog, $dispatcher);
        $this->getMonolog()->pushHandler(new TestHandler());
    }


    /**
     * Returns all log entries as array
     *
     * @param bool $messageAsKeys
     * @return array Array of log entries
     * @throws \ErrorException
     */
    public function getLogEntries($messageAsKeys = false)
    {
        foreach ( $this->monolog->getHandlers() as $handler )
        {
            if ( $handler instanceof TestHandler )
            {
                if ( $messageAsKeys )
                {
                    $entries = [ ];
                    foreach ( $handler->getRecords() as $entry )
                    {
                        $entries[ $entry[ 'message' ] ] = $entry;
                    }

                    return $entries;
                }

                return $handler->getRecords();
            }
        }
        throw new \ErrorException("Could not get log entries. The logger should have a TestHandler binded.");
    }

}
