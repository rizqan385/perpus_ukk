import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Siswa\AuthController::email
 * @see app/Http/Controllers/Siswa/AuthController.php:131
 * @route '/siswa/resend-otp-email'
 */
export const email = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: email.url(options),
    method: 'post',
})

email.definition = {
    methods: ["post"],
    url: '/siswa/resend-otp-email',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Siswa\AuthController::email
 * @see app/Http/Controllers/Siswa/AuthController.php:131
 * @route '/siswa/resend-otp-email'
 */
email.url = (options?: RouteQueryOptions) => {
    return email.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Siswa\AuthController::email
 * @see app/Http/Controllers/Siswa/AuthController.php:131
 * @route '/siswa/resend-otp-email'
 */
email.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: email.url(options),
    method: 'post',
})
const resend = {
    email: Object.assign(email, email),
}

export default resend