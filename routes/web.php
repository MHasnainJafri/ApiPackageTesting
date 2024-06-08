<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Query\Builder;
use Mhasnainjafri\APIToolkit\APIToolkit;
use Mhasnainjafri\APIToolkit\QueryBuilder\QueryBuilder;
use Mhasnainjafri\APIToolkit\QueryBuilder\AllowedFilter;

Route::get('/', function () {

    // User::create([
    //     'name'=> Str::random(10),
    //     'email'=> Str::random(10).'@gmail.com',
    //     'password'=> bcrypt('1234'),
    //     ]);



//     ?filter[name]=ha
//    return $users = QueryBuilder::for(User::class)
//     ->allowedFilters('name')
//     ->get();


// /users?include=posts:

// return $users = QueryBuilder::for(User::class)
//     ->allowedIncludes('posts')
//     ->get();



// /users?sort=id:

// return $users = QueryBuilder::for(User::class)
//     ->allowedSorts('id')
//     ->get();


// Works together nicely with existing queries:

// $query = User::where('name', '!=','has');

// return $userQuery = QueryBuilder::for($query) // start from an existing Builder instance
//     ->allowedIncludes('posts', 'permissions')
//     ->where('score', '>', 42)->get(); //


// Selecting fields for a query: /users?fields[users]=id,email

// return $users = QueryBuilder::for(User::class)
//     ->allowedFields(['id', 'email'])
//     ->get();


// GET /users?filter[name]=john&filter[email]=gmail
// GET /users?filter[name]=seb,freek
// $users will contain all users that contain "seb" OR "freek" in their name

// return $users = QueryBuilder::for(User::class)
//     ->allowedFilters(['name', 'email'])
//     ->get();
// $users will contain all users with "john" in their name AND "gmail" in their email address

// #
// Disallowed filters

// Finally, when trying to filter on properties that have not been allowed using allowedFilters() an InvalidFilterQuery exception will be thrown along with a list of allowed filters.
// #
// #
// Disable InvalidFilterQuery exception

// You can set in configuration file to not throw an InvalidFilterQuery exception when a filter is not set in allowedFilter method. This does not allow using any filter, it just disables the exception.

// 'disable_invalid_filter_query_exception' => true



// GET /users?filter[name]=John%20Doe
// return $users = QueryBuilder::for(User::class)
//     ->allowedFilters([AllowedFilter::exact('name')])
//     ->get();

// only users with the exact name "John Doe"


// Exact or partial filters for related properties

// You can also add filters for a relationship property using the dot-notation: AllowedFilter::exact('posts.title'). This works for exact and partial filters. Under the hood we'll add a whereHas statement for the posts that filters for the given title property as well.

// In some cases you'll want to disable this behaviour and just pass the raw filter-property value to the query. For example, when using a joined table's value for filtering. By passing false as the third parameter to AllowedFilter::exact() or AllowedFilter::partial() this behaviour can be disabled:

// $addRelationConstraint = false;

// return QueryBuilder::for(User::class)
//     ->join('posts', 'posts.user_id', 'users.id')
//     ->allowedFilters(AllowedFilter::exact('posts.name', null, $addRelationConstraint))->get();



// QueryBuilder::for(Event::class)
//     ->allowedFilters([
//         AllowedFilter::scope('starts_before'),
//     ])
//     ->get();

// The following filter will now add the startsBefore scope to the underlying query:

// GET /events?filter[starts_before]=2018-01-01

// Trashed filters

// When using Laravel's soft delete feature you can use the AllowedFilter::trashed() filter to query these models.

// The FiltersTrashed filter responds to particular values:

//     with: include soft-deleted records to the result set
//     only: return only 'trashed' records at the result set
//     any other value: return only records without that are not soft-deleted in the result set

// For example:

// QueryBuilder::for(Booking::class)
//     ->allowedFilters([
//         AllowedFilter::trashed(),
//     ]);

// // GET /bookings?filter[trashed]=only will only return soft deleted models




// Callback filters

// If you want to define a tiny custom filter, you can use a callback filter. Using AllowedFilter::callback(string $name, callable $filter) you can specify a callable that will be executed when the filter is requested.

// The filter callback will receive the following parameters: Builder $query, mixed $value, string $name. You can modify the Builder object to add your own query constraints.

// For example:

// QueryBuilder::for(User::class)
//     ->allowedFilters([
//         AllowedFilter::callback('has_posts', function (Builder $query, $value) {
//             $query->whereHas('posts');
//         }),
//     ]);


// Custom filters

// You can specify custom filters using the AllowedFilter::custom() method. Custom filters are instances of invokable classes that implement the \Spatie\QueryBuilder\Filters\Filter interface. The __invoke method will receive the current query builder instance and the filter name/value. This way you can build any query your heart desires.

// For example:

// use Spatie\QueryBuilder\Filters\Filter;
// use Illuminate\Database\Eloquent\Builder;

// class FiltersUserPermission implements Filter
// {
//     public function __invoke(Builder $query, $value, string $property)
//     {
//         $query->whereHas('permissions', function (Builder $query) use ($value) {
//             $query->where('name', $value);
//         });
//     }
// }

// // In your controller for the following request:
// // GET /users?filter[permission]=createPosts

// $users = QueryBuilder::for(User::class)
//     ->allowedFilters([
//         AllowedFilter::custom('permission', new FiltersUserPermission),
//     ])
//     ->get();
// Filter aliases

// It can be useful to specify an alias for a filter to avoid exposing database column names. For example, your users table might have a user_passport_full_name column, which is a horrible name for a filter. Using aliases you can specify a new, shorter name for this filter:

// use Spatie\QueryBuilder\AllowedFilter;

// // GET /users?filter[name]=John

// $users = QueryBuilder::for(User::class)
//     ->allowedFilters(AllowedFilter::exact('name', 'user_passport_full_name')) // will filter by the `user_passport_full_name` column
//     ->get();

// #
// #
// Ignored filters values

// You can specify a set of ignored values for every filter. This allows you to not apply a filter when these values are submitted.

// QueryBuilder::for(User::class)
//     ->allowedFilters([
//         AllowedFilter::exact('name')->ignore(null),
//     ])
//     ->get();

// The ignore() method takes one or more values, where each may be an array of ignored values. Each of the following calls are valid:

//     ignore('should_be_ignored')
//     ignore(null, '-1')
//     ignore([null, 'ignore_me', 'also_ignored'])

// Given an array of values to filter for, only the subset of non-ignored values get passed to the filter. If all values are ignored, the filter does not get applied.

// // GET /user?filter[name]=forbidden,John%20Doe

// QueryBuilder::for(User::class)
//     ->allowedFilters([
//         AllowedFilter::exact('name')->ignore('forbidden'),
//     ])
//     ->get();
// // Returns only users where name matches 'John Doe'

// // GET /user?filter[name]=ignored,ignored_too

// QueryBuilder::for(User::class)
//     ->allowedFilters([
//         AllowedFilter::exact('name')->ignore(['ignored', 'ignored_too']),
//     ])
//     ->get();
// // Filter does not get applied because all requested values are ignored.

// #
// #
// Default Filter Values

// You can specify a default value for a filter if a value for the filter was not present on the request. This is especially useful for boolean filters.

// QueryBuilder::for(User::class)
//     ->allowedFilters([
//         AllowedFilter::exact('name')->default('Joe'),
//         AllowedFilter::scope('deleted')->default(false),
//         AllowedFilter::scope('permission')->default(null),
//     ])
//     ->get();

// #
// #
// Nullable Filter

// You can mark a filter nullable if you want to retrieve entries whose filtered value is null. This way you can apply the filter with an empty value, as shown in the example.

// // GET /user?filter[name]=&filter[permission]=

// QueryBuilder::for(User::class)
//     ->allowedFilters([
//         AllowedFilter::exact('name')->nullable(),
//         AllowedFilter::scope('permission')->nullable(),
//     ])
//     ->get();



















// ==================Sorting
// // GET /users?sort=-name

// $users = QueryBuilder::for(User::class)
//     ->allowedSorts('name')
//     ->get();

// // $users will be sorted by name and descending (Z -> A)








// Scopes are usually not named with query filters in mind. Use filter aliases to alias them to something more appropriate:

// QueryBuilder::for(User::class)
//     ->allowedFilters([
//         AllowedFilter::scope('unconfirmed', 'whereHasUnconfirmedEmail'),
//         // `?filter[unconfirmed]=1` will now add the `scopeWhereHasUnconfirmedEmail` to your query
//     ]);






return User::jsonPaginate();

});
