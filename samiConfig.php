<?
use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tasks')
    ->in(__DIR__.'/templates/project/app/tasks')
    ->in(__DIR__.'/templates/project/app/classes')
    ->in(__DIR__.'/templates/project/app/controllers')
    ->in(__DIR__.'/templates/project/app/plugins')
;

return new Sami($iterator, [
    'title'                => 'PhalconTool',
    'build_dir'            => __DIR__.'/docs',
    'cache_dir'            => __DIR__.'/docs/cache',
    'default_opened_level' => 2,
]);