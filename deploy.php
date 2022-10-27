<?php
namespace Deployer;

require 'recipe/symfony.php';

// Config

set('application', 'lottery-api');
set('allow_anonymous_stats', false);

set('repository', 'git@github.com:abashkatov/lottery-api.git');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts

host('lottery-api')
//    ->set('remote_user', 'deployer')
    ->set('deploy_path', '/var/www/lottery/{{application}}');

// Hooks

after('deploy:failed', 'deploy:unlock');
