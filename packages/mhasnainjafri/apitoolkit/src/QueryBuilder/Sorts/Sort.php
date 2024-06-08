<?php

namespace Mhasnainjafri\APIToolkit\QueryBuilder\Sorts;

use Illuminate\Database\Eloquent\Builder;

interface Sort
{
    public function __invoke(Builder $query, bool $descending, string $property);
}
