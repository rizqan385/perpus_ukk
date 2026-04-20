import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\FineController::index
 * @see app/Http/Controllers/Admin/FineController.php:18
 * @route '/admin/fines'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/fines',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\FineController::index
 * @see app/Http/Controllers/Admin/FineController.php:18
 * @route '/admin/fines'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\FineController::index
 * @see app/Http/Controllers/Admin/FineController.php:18
 * @route '/admin/fines'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Admin\FineController::index
 * @see app/Http/Controllers/Admin/FineController.php:18
 * @route '/admin/fines'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\FineController::confirm
 * @see app/Http/Controllers/Admin/FineController.php:91
 * @route '/admin/fines/{borrowing}/confirm'
 */
export const confirm = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: confirm.url(args, options),
    method: 'post',
})

confirm.definition = {
    methods: ["post"],
    url: '/admin/fines/{borrowing}/confirm',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\FineController::confirm
 * @see app/Http/Controllers/Admin/FineController.php:91
 * @route '/admin/fines/{borrowing}/confirm'
 */
confirm.url = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
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

    return confirm.definition.url
            .replace('{borrowing}', parsedArgs.borrowing.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\FineController::confirm
 * @see app/Http/Controllers/Admin/FineController.php:91
 * @route '/admin/fines/{borrowing}/confirm'
 */
confirm.post = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: confirm.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\FineController::remind
 * @see app/Http/Controllers/Admin/FineController.php:112
 * @route '/admin/fines/{borrowing}/remind'
 */
export const remind = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: remind.url(args, options),
    method: 'post',
})

remind.definition = {
    methods: ["post"],
    url: '/admin/fines/{borrowing}/remind',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\FineController::remind
 * @see app/Http/Controllers/Admin/FineController.php:112
 * @route '/admin/fines/{borrowing}/remind'
 */
remind.url = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
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

    return remind.definition.url
            .replace('{borrowing}', parsedArgs.borrowing.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\FineController::remind
 * @see app/Http/Controllers/Admin/FineController.php:112
 * @route '/admin/fines/{borrowing}/remind'
 */
remind.post = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: remind.url(args, options),
    method: 'post',
})
const fines = {
    index: Object.assign(index, index),
confirm: Object.assign(confirm, confirm),
remind: Object.assign(remind, remind),
}

export default fines