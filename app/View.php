<?php
// Gerekli tüm Illuminate (Blade) ve diğer sınıfları dahil ediyoruz
use Illuminate\Container\Container; use Illuminate\Events\Dispatcher; use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler; use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver; use Illuminate\View\Factory; use Illuminate\View\FileViewFinder;

class View
{
    private static $latte;
    private static $blade;
    private static $mustache;

    public static function render(string $template, array $data, string $engine)
    {
        switch ($engine) {
            case 'latte':
                self::renderLatte($template, $data);
                break;
            case 'blade':
                self::renderBlade($template, $data);
                break;
            case 'mustache':
                self::renderMustache($template, $data);
                break;
        }
    }

    private static function renderLatte(string $template, array $data): void
    {
        if (!self::$latte) {
            self::$latte = new Latte\Engine;
            self::$latte->setTempDirectory(__DIR__ . '/../temp');
        }
        self::$latte->render(__DIR__ . '/../templates/' . $template . '.latte', $data);
    }

    private static function renderBlade(string $template, array $data): void
    {
        if (!self::$blade) {
            $viewPaths = [__DIR__ . '/../templates'];
            $cachePath = __DIR__ . '/../cache';
            $filesystem = new Filesystem;
            $eventDispatcher = new Dispatcher(new Container);
            $viewFinder = new FileViewFinder($filesystem, $viewPaths);
            $engineResolver = new EngineResolver;
            $bladeCompiler = new BladeCompiler($filesystem, $cachePath);
            $engineResolver->register('blade', function () use ($bladeCompiler) {
                return new CompilerEngine($bladeCompiler);
            });
            self::$blade = new Factory($engineResolver, $viewFinder, $eventDispatcher);
        }
        echo self::$blade->make($template, $data)->render();
    }
    
    private static function renderMustache(string $template, array $data): void
    {
        if (!self::$mustache) {
            self::$mustache = new Mustache_Engine;
        }
        $templateContent = file_get_contents(__DIR__ . '/../templates/' . $template . '.mustache');
        echo self::$mustache->render($templateContent, $data);
    }
}