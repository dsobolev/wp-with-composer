@servers(['local' => '127.0.0.1'])

@setup

    $repo = 'git@github.com:dsobolev/wp-with-composer.git';
    $branch = 'master';

    date_default_timezone_set('Europe/Kiev');    
    $date = date('YmdHis');

    $appDir = '/var/www/wp-composer';
    $buildsDir = $appDir . '/releases';
    $deploymentDir = $buildsDir . '/' . $date;
    $serve = $appDir . '/current';
    
@endsetup

@task('dir')
    echo "New Deployment directory..."

    cd {{ $buildsDir }}
    mkdir {{ $date }}

    echo "Deployment directory - completed."
@endtask

@task('git')
    echo "Cloning repository..."

    cd {{ $deploymentDir }}
    git clone --depth 1 -b {{ $branch }} "{{ $repo }}" {{ $deploymentDir }}

    echo "Cloning repository - completed."
@endtask

@task('install')
    echo "Installing dependencies...";

    composer install --prefer-dist
    cp ../../wp-config.php ./wp-config.php

    echo "Installing dependencies - completed."
@endtask

@task('live')
    echo "Creating symlinks for the live version..."

    cd {{ $deploymentDir }}
    ln -nfs {{ $deploymentDir }} {{ $serve }}
    ln -nfs {{ $appDir }}/uploads {{ $serve }}/wp-content/

    echo "Creating symlinks - completed."
@endtask

@task('deployment_cleanup')
    echo "Cleaning up old deployments..."

    cd {{ $buildsDir }}
    ls -t | tail -n +4 | xargs rm -rf

    echo "Cleaned up old deployments."
@endtask

@story('deploy-local', ['on' => 'local'])
    dir
    git
    install
    live
    deployment_cleanup
@endstory


@task('update-plugin')
    cd {{  $deploymentDir  }}
    wp plugin update {{ $plugin }} --version={{ $pluginVersion }}
@endtask

@story('update')
    update-plugin
@endstory
