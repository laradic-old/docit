<?php
/**
 * Part of the Radic packages.
 */
namespace Laradic\Docit\Github;

use Cache;
use File;
use GrahamCampbell\GitHub\GitHubManager;
use Laradic\Docit\Project;
use Laradic\Docit\ProjectFactory;
use Laradic\Support\Arr;
use Laradic\Support\Path;
use Laradic\Support\Str;
use Monolog\Handler\BufferHandler;
use Monolog\Handler\NullHandler;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Naneau\SemVer\Compare;
use Naneau\SemVer\Parser;
use Symfony\Component\Yaml\Yaml;


/**
 * Class SyncManager
 *
 * @package     Laradic\Docit
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
class GithubProjectSynchronizer
{

    protected $gm;

    protected $projects;

    protected $log;

    /**
     * Instanciates the class
     */
    public function __construct(GithubManager $gm, ProjectFactory $projects)
    {
        $this->gm       = $gm;
        $this->projects = $projects;
        $this->log      = new Logger('github_sync_manager', [new TestHandler()]);
    }

    public function getLog()
    {
        return $this->log;
    }

    public function setLog(Logger $log)
    {
        $this->log = $log;
    }

    /**
     * Returns all log entries
     *
     * @return array Array of log entries
     * @throws \ErrorException
     */
    public function getLogEntries($messageAsKeys = false)
    {
        //TestHandler
        $handlers = $this->log->getHandlers();
        foreach ($handlers as $handler)
        {
            if ( $handler instanceof TestHandler )
            {
                if ( $messageAsKeys )
                {
                    $entries = [];
                    foreach ($handler->getRecords() as $entry)
                    {
                        $entries[$entry['message']] = $entry;
                    }

                    return $entries;
                }

                return $handler->getRecords();
            }
        }
        throw new \ErrorException("Could not get log entries. The logger should have a TestHandler binded.");
    }

    protected function resolveProject($project)
    {
        if ( ! $project instanceof Project )
        {
            if ( ! $this->projects->has($project) )
            {
                $this->log->error('resolve project failed: could not find project', ['project' => $project]);

                return false; #throw new Exception('Project does not exists for sync by git');
            }

            $project = $this->projects->make($project);
        }

        $config = $project->getConfig()['github'];

        if ( $config['enabled'] !== true )
        {
            $this->log->error('resolve project failed: project has github disabled', ['project' => $project]);

            return false; #throw new Exception('Project has github disabled, what am i doing here..');
        }

        return $project;
    }

    protected function getCacheKey(Project $project, $ref)
    {
        return md5($project->getSlug() . $ref);
    }

    protected function getPaths(Project $project, $ref, $type)
    {
        $githubConfig = $project->getConfig()['github'];

        $paths = [
            'docs'     => 'docs',
            'logs'     => 'build/logs',
            'index_md' => 'docs/index.md'
        ];

        $b = $githubConfig['path_bindings'];

        if ( isset($b) )
        {
            foreach ($b as $k => $v)
            {
                $paths[$k] = $v;
            }
        }


        $paths['local.project'] = $project->getPath();

        $folder = $ref;

        if ( $type === 'tag' )
        {
            $tag    = Parser::parse(Str::remove($ref, 'v'));
            $folder = $tag->getMajor() . '.' . $tag->getMinor();
        }


        $paths['local.destination'] = Path::join($paths['local.project'], $folder);

        return $paths;
    }


    public function sync($project)
    {
        $project = $this->resolveProject($project);
        $tags    = $this->getUnsyncedTags($project);
        $this->log->info('synchronising project ' . $project->getSlug(), ['project' => $project]);

        foreach ($tags as $tag)
        {
            $this->syncTag($project, $tag);
        }

        $branches = $this->getUnsyncedBranches($project);
        foreach ($branches as $branch)
        {
            $this->syncBranch($project, $branch);
        }
        $this->log->info('synchronized project ' . $project->getSlug(), ['project' => $project]);
    }


    /**
     * @param string $ref  tag name OR branch name
     * @param string $type [ tag | branch ]
     */
    protected function syncRef(Project $project, $ref, $type)
    {
        $config = $project->getConfig()['github'];
        $paths  = $this->getPaths($project, $ref, $type);

        $content = new RepoContent($config['username'], $config['repository'], $this->gm);

        $hasDocs = $content->exists($paths['docs'], $ref);
        #$hasLogs  = $content->exists($paths['logs'], $ref);
        #$hasIndex = $content->exists($paths['index_md'], $ref);

        if ( $hasDocs )
        {
            $this->log->info("synchronizing docs for $type $ref ", ['project' => $project, "$type" => $ref]);

            # parse menu and get pages to sync
            $menu            = $content->show(Path::join($paths['docs'], 'menu.yml'), $ref);
            $menuContent     = base64_decode($menu['content']);
            $menuArray       = Yaml::parse($menuContent);
            $unfilteredPages = [];
            $this->extractPagesFromMenu($menuArray['menu'], $unfilteredPages);
            $filteredPages = [];
            foreach ($unfilteredPages as $page) # filter out pages that link to external sites
            {
                if ( Str::startsWith($page, 'http') || Str::startsWith($page, '//') || Str::startsWith($page, 'git') )
                {
                    continue;
                }
                if ( ! in_array($page, $filteredPages) )
                {
                    $filteredPages[] = $page;
                }
            }

            # get all pages their content and save to local
            foreach ($filteredPages as $pagePath)
            {
                $path = Path::join($paths['docs'], $pagePath . '.md');

                # check if page exists on remote
                $exists = $content->exists($path, $ref);
                if ( ! $exists )
                {
                    continue;
                }

                # the raw github page content response
                $pageRaw = $content->show('/' . $path, $ref);

                # transform remote directory path to local directory path
                $dir = Str::remove($pageRaw['path'], $paths['docs']);
                $dir = Str::remove($dir, $pageRaw['name']);
                $dir = Path::canonicalize(Path::join($paths['local.destination'], $dir));
                if ( ! File::isDirectory($dir) )
                {
                    File::makeDirectory($dir, 0777, true);
                }

                # raw github page to utf8 and save it to local
                File::put(Path::join($dir, $pageRaw['name']), base64_decode($pageRaw['content']));
            }

            # save the menu to local
            File::put(Path::join($paths['local.destination'], 'menu.yml'), $menuContent);

            # set cache sha for branches, not for tags (obviously)
            if ( $type === 'branch' )
            {
                $branchData = $this->gm->repo()->branches($config['username'], $config['repository'], $ref);
                Cache::forever($this->getCacheKey($project, $ref), $branchData['commit']['sha']);
            }
        }
    }

    public function syncBranch($project, $branch)
    {
        if ( ! $project = $this->resolveProject($project) )
        {
            return false;
        }

        $c = $project->getConfig()['github'];

        if ( ! isset($c['branches']) or ! is_array($c['branches']) or ! in_array($branch, $c['branches']) )
        {
            return false;
        }

        $this->syncRef($project, $branch, 'branch');
    }

    public function syncTag($project, $tag)
    {
        if ( ! $project = $this->resolveProject($project) )
        {
            return false;
        }

        $this->syncRef($project, $tag, 'tag');
    }


    public function getUnsyncedBranches($project)
    {
        $this->log->info('getting unsynced branches', ['project' => $project]);
        if ( ! $project = $this->resolveProject($project) )
        {
            return [];
        }

        $c = $project->getConfig()['github'];

        if ( ! isset($c['branches']) or ! is_array($c['branches']) or count($c['branches']) === 0 )
        {
            return [];
        }


        $branches       = $this->gm->repo()->branches($c['username'], $c['repository']);
        $branchesToSync = [];
        foreach ($branches as $branch)
        {
            $name  = $branch['name'];
            $paths = $this->getPaths($project, $name, 'branch');
            $sha      = $branch['commit']['sha'];
            $cacheKey = md5($project->getSlug() . $name);
            $branch   = Cache::get($cacheKey, false);
            if ( $branch !== $sha or $branch === false or ! File::isDirectory($paths['local.destination']) )
            {
                $branchesToSync[] = $name;
                $this->log->info("marking branch $name for synchronisation", ['project' => $project->getSlug(), 'branch' => $branch]);
            }
            else
            {
                $this->log->info("skipping branch $name", ['project' => $project->getSlug(), 'branch' => $branch]);
            }
        }
        $b = $branchesToSync;

        return $branchesToSync;
    }

    public function getUnsyncedTags($project)
    {
        $this->log->info('getting unsynced tags', ['project' => $project]);
        if ( ! $project = $this->resolveProject($project) )
        {
            return [];
        }

        $currentVersions = Arr::keys($project->getVersions());
        $pc              = $project->getConfig()['github']; #$project->getConfig());

        $tagsToSync = [];
        $excludes   = $pc['exclude_tags'];
        $start      = is_string($pc['start_at_tag']) ? Parser::parse(Str::remove($pc['start_at_tag'], 'v')) : false;

        $tags = $this->gm->repo()->tags($pc['username'], $pc['repository']);
        foreach ($tags as $tag)
        {
            $tagVersion = $tag['name'];
            #

            $tagVersionParsed = Parser::parse(Str::remove($tag['name'], 'v'));
            $tagVersionShort  = $tagVersionParsed->getMajor() . '.' . $tagVersionParsed->getMinor();

            if ( ($start !== false AND Compare::smallerThan(Parser::parse($tagVersionParsed), $start))
                OR
                (in_array($tagVersion, $excludes) OR in_array($tagVersionShort, $currentVersions))
            )
            {
                $this->log->info("skipping tag $tagVersion", ['project' => $project->getSlug(), 'tag' => $tagVersion]);
                continue;
            }
            $this->log->info("marking tag $tagVersion for synchronisation", ['project' => $project->getSlug(), 'tag' => $tagVersion]);

            $tagsToSync[] = $tagVersion;
        }

        return $tagsToSync;
    }

    public function extractPagesFromMenu($menuArray, &$pages = [])
    {
        foreach ($menuArray as $key => $val)
        {
            if ( is_string($key) && is_string($val) )
            {
                $pages[] = $val;
            }
            elseif ( is_string($key) && $key === 'children' && is_array($val) )
            {
                $this->extractPagesFromMenu($val, $pages);
            }
            elseif ( isset($val['name']) )
            {
                if ( isset($val['page']) )
                {
                    $pages[] = $val['page'];
                }
                if ( isset($val['href']) )
                {
                    //$item['href'] = $this->resolveLink($val['href']);
                }
                if ( isset($val['icon']) )
                {
                    //$item['icon'] = $val['icon'];
                }
                if ( isset($val['children']) && is_array($val['children']) )
                {
                    $this->extractPagesFromMenu($val['children'], $pages);
                }
            }
        }
    }


}
