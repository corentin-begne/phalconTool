<?
use Sami\Sami;
use Symfony\Component\Finder\Finder;
$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in(__DIR__.'/apps/frontend/tasks')
    ->in(__DIR__.'/apps/frontend/classes')
    ->in(__DIR__.'/apps/frontend/controllers')
    ->in(__DIR__.'/apps/frontend/plugins')
;
return new Sami($iterator, [
    'title'                => 'Template frontend',
    'build_dir'            => __DIR__.'/docs/php',
    'cache_dir'            => __DIR__.'/docs/php/cache',
    'default_opened_level' => 2,
]);