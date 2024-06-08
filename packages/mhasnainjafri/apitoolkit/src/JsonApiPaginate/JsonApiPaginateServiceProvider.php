<?php

namespace Mhasnainjafri\APIToolkit\JsonApiPaginate;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Query\Builder as BaseBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class JsonApiPaginateServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/config.php' => config_path('config.php'),
            ], 'config');
        }

        $this->registerMacro();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'config');
    }

    protected function registerMacro()
    {
        $macro = function (int $maxResults = null, int $defaultSize = null) {
            $maxResults = $maxResults ?? config('config.max_results');
            $defaultSize = $defaultSize ?? config('config.default_size');
            $numberParameter = config('config.number_parameter');
            $cursorParameter = config('config.cursor_parameter');
            $sizeParameter = config('config.size_parameter');
            $paginationParameter = config('config.pagination_parameter');
            $paginationMethod = config('config.use_cursor_pagination')
                ? 'cursorPaginate'
                : (config('config.use_simple_pagination') ? 'simplePaginate' : 'paginate');

            $size = (int) request()->input($paginationParameter.'.'.$sizeParameter, $defaultSize);
            $cursor = (string) request()->input($paginationParameter.'.'.$cursorParameter);

            if ($size <= 0) {
                $size = $defaultSize;
            }

            if ($size > $maxResults) {
                $size = $maxResults;
            }

            $paginator = $paginationMethod === 'cursorPaginate'
                ? $this->{$paginationMethod}($size, ['*'], $paginationParameter.'['.$cursorParameter.']', $cursor)
                    ->appends(Arr::except(request()->input(), $paginationParameter.'.'.$cursorParameter))
                : $this
                    ->{$paginationMethod}($size, ['*'], $paginationParameter.'.'.$numberParameter)
                    ->setPageName($paginationParameter.'['.$numberParameter.']')
                    ->appends(Arr::except(request()->input(), $paginationParameter.'.'.$numberParameter));

            if (! is_null(config('config.base_url'))) {
                $paginator->setPath(config('config.base_url'));
            }

            return $paginator;
        };

        EloquentBuilder::macro(config('config.method_name'), $macro);
        BaseBuilder::macro(config('config.method_name'), $macro);
        BelongsToMany::macro(config('config.method_name'), $macro);
        HasManyThrough::macro(config('config.method_name'), $macro);
    }
}
