import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
 * @see routes/web.php:40
 * @route '/siswa/daftar'
 */
export const page = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: page.url(options),
    method: 'get',
})

page.definition = {
    methods: ["get","head"],
    url: '/siswa/daftar',
} satisfies RouteDefinition<["get","head"]>

/**
 * @see routes/web.php:40
 * @route '/siswa/daftar'
 */
page.url = (options?: RouteQueryOptions) => {
    return page.definition.url + queryParams(options)
}

/**
 * @see routes/web.php:40
 * @route '/siswa/daftar'
 */
page.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: page.url(options),
    method: 'get',
})
/**
 * @see routes/web.php:40
 * @route '/siswa/daftar'
 */
page.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: page.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Siswa\AuthController::submit
 * @see app/Http/Controllers/Siswa/AuthController.php:38
 * @route '/siswa/daftar'
 */
export const submit = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(options),
    method: 'post',
})

submit.definition = {
    methods: ["post"],
    url: '/siswa/daftar',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Siswa\AuthController::submit
 * @see app/Http/Controllers/Siswa/AuthController.php:38
 * @route '/siswa/daftar'
 */
submit.url = (options?: RouteQueryOptions) => {
    return submit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Siswa\AuthController::submit
 * @see app/Http/Controllers/Siswa/AuthController.php:38
 * @route '/siswa/daftar'
 */
submit.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Siswa\MemberRegistrationController::store
 * @see app/Http/Controllers/Siswa/MemberRegistrationController.php:33
 * @route '/siswa/register'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/siswa/register',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Siswa\MemberRegistrationController::store
 * @see app/Http/Controllers/Siswa/MemberRegistrationController.php:33
 * @route '/siswa/register'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Siswa\MemberRegistrationController::store
 * @see app/Http/Controllers/Siswa/MemberRegistrationController.php:33
 * @route '/siswa/register'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})
const register = {
    store: Object.assign(store, store),
}

export default register