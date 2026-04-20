import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Siswa\FineController::pay
 * @see app/Http/Controllers/Siswa/FineController.php:72
 * @route '/siswa/fines/{borrowing}/pay'
 */
export const pay = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: pay.url(args, options),
    method: 'post',
})

pay.definition = {
    methods: ["post"],
    url: '/siswa/fines/{borrowing}/pay',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Siswa\FineController::pay
 * @see app/Http/Controllers/Siswa/FineController.php:72
 * @route '/siswa/fines/{borrowing}/pay'
 */
pay.url = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
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

    return pay.definition.url
            .replace('{borrowing}', parsedArgs.borrowing.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Siswa\FineController::pay
 * @see app/Http/Controllers/Siswa/FineController.php:72
 * @route '/siswa/fines/{borrowing}/pay'
 */
pay.post = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: pay.url(args, options),
    method: 'post',
})
const fines = {
    pay: Object.assign(pay, pay),
}

export default fines