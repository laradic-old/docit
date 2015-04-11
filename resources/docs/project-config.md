Per project config overview
===========================

```php
return array(
    'title'                   => 'Test documentation',
    'subtitle'                => 'For development purposes',
    'icon'                    => 'https://raw.githubusercontent.com/laravel/art/master/laravel-l-slant.png',
    'default_version'         => null,
    'default_page_attributes' => array(
        'disqus' => true,
        # 'share_buttons' => ['facebook']
    ),
    'github'                  => array(
        // enable git synchronisation using the console command, webhook or if debug is enabled, the web syncer.
        'enabled'        => true,

        // enables the login button. authorized collaborators of the repository that have write access can edit
        // the documentation files for branches and the menu using the Ace editor. Saving a file will commit it to github
        // and if the webhook is enabled, within a few seconds the local file is also updated.
        'enable_editor'  => true,

        'username'       => 'laradic',              # username/owner for the repo
        'repository'     => 'docs-test',            # repo name
        'branches'       => ['master', 'develop'],  # branches that are included when syncing
        'webhook_secret' => 'test',                 # the webhook secret. you can configure it on github, go to repository settings > webhooks. Use //{yourdomain}/{base_route}/github-sync-webhook/push
        'exclude_tags'   => ['v2.2.0'],             # these tags will be ignored and not synced
        'start_at_tag'   => 'v1.1.0',               # exclude a starting range of tags
        'path_bindings'  => array(
            'logs'     => 'build/logs',
            'docs'     => 'docs',                   # the path to the docs folder, relative to the git root
            'index_md' => null # 'README.md' # null for default, otherwise path relative to git root, ex: README.md OR documents/intro.md
        )
    )
);
```
