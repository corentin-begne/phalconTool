<?
use Sami\Sami;
use Sami\RemoteRepository\GitHubRemoteRepository;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in(__DIR__.'/src')
;

return new Sami($iterator, [
    'title'                => 'PhalconTool',
    'build_dir'            => __DIR__.'/docs',
    'cache_dir'            => __DIR__.'/docs/cache',
    'default_opened_level' => 2,
]);