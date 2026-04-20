import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Siswa\ReturnController::request
 * @see app/Http/Controllers/Siswa/ReturnController.php:54
 * @route '/siswa/returns/{borrowing}/request'
 */
export const request = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: request.url(args, options),
    method: 'post',
})

request.definition = {
    methods: ["post"],
    url: '/siswa/returns/{borrowing}/request',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Siswa\ReturnController::request
 * @see app/Http/Controllers/Siswa/ReturnController.php:54
 * @route '/siswa/returns/{borrowing}/request'
 */
request.url = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { borrowing: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { borrowing: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    borrowing: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        borrowing: typeof args.borrowing === 'object'
                ? args.borrowing.id
                : args.borrowing,
                }

    return request.definition.url
            .replace('{borrowing}', parsedArgs.borrowing.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Siswa\ReturnController::request
 * @see app/Http/Controllers/Siswa/ReturnController.php:54
 * @route '/siswa/returns/{borrowing}/request'
 */
request.post = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: request.url(args, options),
    method: 'post',
})
const returns = {
    request: Object.assign(request, request),
}

export default returns