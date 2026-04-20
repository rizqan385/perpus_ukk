import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Siswa\PaymentController::token
 * @see app/Http/Controllers/Siswa/PaymentController.php:25
 * @route '/siswa/payment/{borrowing}/token'
 */
export const token = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: token.url(args, options),
    method: 'post',
})

token.definition = {
    methods: ["post"],
    url: '/siswa/payment/{borrowing}/token',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Siswa\PaymentController::token
 * @see app/Http/Controllers/Siswa/PaymentController.php:25
 * @route '/siswa/payment/{borrowing}/token'
 */
token.url = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
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

    return token.definition.url
            .replace('{borrowing}', parsedArgs.borrowing.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Siswa\PaymentController::token
 * @see app/Http/Controllers/Siswa/PaymentController.php:25
 * @route '/siswa/payment/{borrowing}/token'
 */
token.post = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: token.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Siswa\PaymentController::success
 * @see app/Http/Controllers/Siswa/PaymentController.php:106
 * @route '/siswa/payment/{borrowing}/success'
 */
export const success = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: success.url(args, options),
    method: 'get',
})

success.definition = {
    methods: ["get","head"],
    url: '/siswa/payment/{borrowing}/success',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Siswa\PaymentController::success
 * @see app/Http/Controllers/Siswa/PaymentController.php:106
 * @route '/siswa/payment/{borrowing}/success'
 */
success.url = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
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

    return success.definition.url
            .replace('{borrowing}', parsedArgs.borrowing.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Siswa\PaymentController::success
 * @see app/Http/Controllers/Siswa/PaymentController.php:106
 * @route '/siswa/payment/{borrowing}/success'
 */
success.get = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: success.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Siswa\PaymentController::success
 * @see app/Http/Controllers/Siswa/PaymentController.php:106
 * @route '/siswa/payment/{borrowing}/success'
 */
success.head = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: success.url(args, options),
    method: 'head',
})
const payment = {
    token: Object.assign(token, token),
success: Object.assign(success, success),
}

export default payment