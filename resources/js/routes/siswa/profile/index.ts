import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Siswa\ProfileController::edit
 * @see app/Http/Controllers/Siswa/ProfileController.php:17
 * @route '/siswa/profile'
 */
export const edit = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/siswa/profile',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Siswa\ProfileController::edit
 * @see app/Http/Controllers/Siswa/ProfileController.php:17
 * @route '/siswa/profile'
 */
edit.url = (options?: RouteQueryOptions) => {
    return edit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Siswa\ProfileController::edit
 * @see app/Http/Controllers/Siswa/ProfileController.php:17
 * @route '/siswa/profile'
 */
edit.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Siswa\ProfileController::edit
 * @see app/Http/Controllers/Siswa/ProfileController.php:17
 * @route '/siswa/profile'
 */
edit.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Siswa\ProfileController::update
 * @see app/Http/Controllers/Siswa/ProfileController.php:40
 * @route '/siswa/profile'
 */
export const update = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

update.definition = {
    methods: ["post"],
    url: '/siswa/profile',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Siswa\ProfileController::update
 * @see app/Http/Controllers/Siswa/ProfileController.php:40
 * @route '/siswa/profile'
 */
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Siswa\ProfileController::update
 * @see app/Http/Controllers/Siswa/ProfileController.php:40
 * @route '/siswa/profile'
 */
update.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Siswa\ProfileController::success
 * @see app/Http/Controllers/Siswa/ProfileController.php:83
 * @route '/siswa/success-update'
 */
export const success = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: success.url(options),
    method: 'get',
})

success.definition = {
    methods: ["get","head"],
    url: '/siswa/success-update',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Siswa\ProfileController::success
 * @see app/Http/Controllers/Siswa/ProfileController.php:83
 * @route '/siswa/success-update'
 */
success.url = (options?: RouteQueryOptions) => {
    return success.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Siswa\ProfileController::success
 * @see app/Http/Controllers/Siswa/ProfileController.php:83
 * @route '/siswa/success-update'
 */
success.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: success.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Siswa\ProfileController::success
 * @see app/Http/Controllers/Siswa/ProfileController.php:83
 * @route '/siswa/success-update'
 */
success.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: success.url(options),
    method: 'head',
})
const profile = {
    edit: Object.assign(edit, edit),
update: Object.assign(update, update),
success: Object.assign(success, success),
}

export default profile