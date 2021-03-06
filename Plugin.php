<?php

namespace Initbiz\SeoStorm;

use System\Classes\PluginBase;
use System\Classes\SettingsManager;
use Initbiz\SeoStorm\Classes\Helper;
use Twig\Extension\StringLoaderExtension;

/**
 * Initbiz Plugin Information File
 */
class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            'Initbiz\SeoStorm\Components\Seo' => 'seo',
            'Initbiz\SeoStorm\Components\SchemaVideo' => 'schemaVideo',
            'Initbiz\SeoStorm\Components\SchemaArticle' => 'schemaArticle',
            'Initbiz\SeoStorm\Components\SchemaProduct' => 'schemaProduct',
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'initbiz.seostorm::lang.form.settings.label',
                'description' => 'initbiz.seostorm::lang.form.settings.description',
                'icon'        => 'icon-search',
                'category'    => 'initbiz.seostorm::lang.form.settings.category_label', 
                'class'       => 'Initbiz\SeoStorm\Models\Settings',
                'order'       => 100,
                'permissions' => ['initbiz.manage_seo'],
            ],
            'htaccess' => [
                'label'       => 'initbiz.seostorm::lang.form.htaccess.label',
                'description' => 'initbiz.seostorm::lang.form.htaccess.description',
                'icon'        => 'icon-file-text-o',
                'category'    => 'initbiz.seostorm::lang.form.settings.category_label', 
                'class'       => 'Initbiz\SeoStorm\Models\Htaccess',
                'order'       => 200,
                'permissions' => ['initbiz.manage_seo'],
            ]
        ];
    }

    public function registerMarkupTags()
    {
        $helper = new Helper();
        $minifier = \Initbiz\SeoStorm\Classes\Minifier::class;
        $schema = \Initbiz\SeoStorm\Classes\Schema::class;
        return [
            'filters' => [
                'minifyjs' => [$minifier, 'minifyJs'],
                'minifycss' => [$minifier, 'minifyCss'],
                // TODO: Backward compatibility, to be removed soon
                'arcane_seo_schema' => [$schema, 'toScript'],
                'initbiz_seostorm_schema' => [$schema, 'toScript'],
                'removenulls' => [$helper, 'removeNullsFromArray'],
                'fillparams'  => ['Initbiz\SeoStorm\Classes\Helper', 'replaceUrlPlaceholders'],
                'url' => [$helper, 'url'],
            ],
            'functions' => [
                'template_from_string' => [$this, 'templateFromString'],
            ]
        ];
    }

    public function templateFromString($template)
    {
        $twig = $this->app->make('twig.environment');

        if (!$twig->hasExtension(StringLoaderExtension::class)) {
            $stringLoader = new StringLoaderExtension();
            $twig->addExtension($stringLoader);
        }

        return twig_template_from_string($twig, $template);
    }

    public function registerPageSnippets()
    {
        return [
            '\Initbiz\SeoStorm\Components\SchemaVideo' => 'schemaVideo',
            '\Initbiz\SeoStorm\Components\SchemaArticle' => 'schemaArticle',
            '\Initbiz\SeoStorm\Components\SchemaProduct' => 'schemaProduct',
        ];
    }

    public function register()
    {
        $this->registerConsoleCommand('migrate:arcane', 'Initbiz\SeoStorm\Console\MigrateArcane');
    }

    public function registerStormedModels()
    {
        return [
            'Cms\Classes\Page' => [
                'prefix' => 'settings',
                'placement' => 'tabs',
            ],
            'Rainlab\Blog\Models\Post' => [
                'placement' => 'secondaryTabs',
                'excludeFields' => [
                    'model_class',
                    'model_scope',
                    'model_params',
                    'lastmod',
                    'use_updated_at',
                    'changefreq',
                    'priority',
                    'enabled_in_sitemap',
                ],
            ],
            '\RainLab\Pages\Classes\Page' => [
                'excludeFields' => [
                    'model_class',
                    'model_scope',
                    'model_params',
                ],
                'prefix' => 'viewBag',
                'placement' => 'tabs',
            ],
        ];
    }

}
