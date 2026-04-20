import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
 * @see routes/web.php:32
 * @route '/siswa/masuk'
 */
export const page = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: page.url(options),
    method: 'get',
})

page.definition = {
    methods: ["get","head"],
    url: '/siswa/masuk',
} satisfies RouteDefinition<["get","head"]>

/**
 * @see routes/web.php:32
 * @route '/siswa/masuk'
 */
page.url = (options?: RouteQueryOptions) => {
    return page.definition.url + queryParams(options)
}

/**
 * @see routes/web.php:32
 * @route '/siswa/masuk'
 */
page.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: page.url(options),
    method: 'get',
})
/**
 * @see routes/web.php:32
 * @route '/siswa/masuk'
 */
page.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: page.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Siswa\AuthController::submit
 * @see app/Http/Controllers/Siswa/AuthController.php:16
 * @route '/siswa/masuk'
 */
export const submit = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(options),
    method: 'post',
})

submit.definition = {
    methods: ["post"],
    url: '/siswa/masuk',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Siswa\AuthController::submit
 * @see app/Http/Controllers/Siswa/AuthController.php:16
 * @route '/siswa/masuk'
 */
submit.url = (options?: RouteQueryOptions) => {
    return submit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Siswa\AuthController::submit
 * @see app/Http/Controllers/Siswa/AuthController.php:16
 * @route '/siswa/masuk'
 */
submit.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(options),
    method: 'post',
})
const login = {
    page: Object.assign(page, page),
submit: Object.assign(submit, submit),
}

export default login