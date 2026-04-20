import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Siswa\FavoriteController::toggle
 * @see app/Http/Controllers/Siswa/FavoriteController.php:52
 * @route '/siswa/favorites/{book}/toggle'
 */
export const toggle = (args: { book: string | number | { id: string | number } } | [book: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggle.url(args, options),
    method: 'post',
})

toggle.definition = {
    methods: ["post"],
    url: '/siswa/favorites/{book}/toggle',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Siswa\FavoriteController::toggle
 * @see app/Http/Controllers/Siswa/FavoriteController.php:52
 * @route '/siswa/favorites/{book}/toggle'
 */
toggle.url = (args: { book: string | number | { id: string | number } } | [book: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { book: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { book: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    book: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        book: typeof args.book === 'object'
                ? args.book.id
                : args.book,
                }

    return toggle.definition.url
            .replace('{book}', parsedArgs.book.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Siswa\FavoriteController::toggle
 * @see app/Http/Controllers/Siswa/FavoriteController.php:52
 * @route '/siswa/favorites/{book}/toggle'
 */
toggle.post = (args: { book: string | number | { id: string | number } } | [book: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: toggle.url(args, options),
    method: 'post',
})
const favorites = {
    toggle: Object.assign(toggle, toggle),
}

export default favorites