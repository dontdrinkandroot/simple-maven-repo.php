<?php

namespace Deployer;

require 'recipe/symfony4.php';

// Project name
set('application', 'maven.dontdrinkandroot.net');

// Project repository
set('repository', 'git@bitbucket.org:philipsorst/simple-maven-repo.php.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', false);

// Shared files/dirs between deploys
set('shared_files', ['.env.local']);
add('shared_dirs', []);

// Writable dirs by web server 
add('writable_dirs', []);

set('default_stage', 'production');

// Hosts

host('production')
    ->hostname('185.162.248.118')
    ->stage('production')
    ->user('deployer')
    ->set('deploy_path', '/var/www/maven.dontdrinkandroot.net');

// Tasks

task(
    'yarn:install',
    function () {
        run('cd {{release_path}} && yarn install');
    }
);

task(
    'encore:prod',
    function () {
        run('cd {{release_path}} && yarn run encore production');
    }
);

task(
    'load:env-vars',
    function () {
        $environment = run('cat {{deploy_path}}/shared/.env.local');
        $dotenv = new \Symfony\Component\Dotenv\Dotenv();
        $data = $dotenv->parse($environment);
        set('env', $data);
    }
);

before('deploy', 'load:env-vars');
before('rollback', 'load:env-vars');

//before('encore:prod', 'yarn:install');
//before('deploy:vendors', 'encore:prod');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'database:migrate');
