import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Siswa\PaymentController::notification
 * @see app/Http/Controllers/Siswa/PaymentController.php:69
 * @route '/payment/midtrans/notification'
 */
export const notification = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: notification.url(options),
    method: 'post',
})

notification.definition = {
    methods: ["post"],
    url: '/payment/midtrans/notification',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Siswa\PaymentController::notification
 * @see app/Http/Controllers/Siswa/PaymentController.php:69
 * @route '/payment/midtrans/notification'
 */
notification.url = (options?: RouteQueryOptions) => {
    return notification.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Siswa\PaymentController::notification
 * @see app/Http/Controllers/Siswa/PaymentController.php:69
 * @route '/payment/midtrans/notification'
 */
notification.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: notification.url(options),
    method: 'post',
})
const payment = {
    notification: Object.assign(notification, notification),
}

export default payment