import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Siswa\BorrowingController::store
 * @see app/Http/Controllers/Siswa/BorrowingController.php:54
 * @route '/siswa/borrow'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/siswa/borrow',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Siswa\BorrowingController::store
 * @see app/Http/Controllers/Siswa/BorrowingController.php:54
 * @route '/siswa/borrow'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Siswa\BorrowingController::store
 * @see app/Http/Controllers/Siswa/BorrowingController.php:54
 * @route '/siswa/borrow'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})
const borrow = {
    store: Object.assign(store, store),
}

export default borrow