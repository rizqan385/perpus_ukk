import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
 * @see routes/web.php:60
 * @route '/siswa/lupa-password'
 */
export const request = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: request.url(options),
    method: 'get',
})

request.definition = {
    methods: ["get","head"],
    url: '/siswa/lupa-password',
} satisfies RouteDefinition<["get","head"]>

/**
 * @see routes/web.php:60
 * @route '/siswa/lupa-password'
 */
request.url = (options?: RouteQueryOptions) => {
    return request.definition.url + queryParams(options)
}

/**
 * @see routes/web.php:60
 * @route '/siswa/lupa-password'
 */
request.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: request.url(options),
    method: 'get',
})
/**
 * @see routes/web.php:60
 * @route '/siswa/lupa-password'
 */
request.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: request.url(options),
    method: 'head',
})
const password = {
    request: Object.assign(request, request),
}

export default password