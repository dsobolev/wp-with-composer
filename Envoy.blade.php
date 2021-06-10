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