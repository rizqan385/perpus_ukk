import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
import resendE1e4c0 from './resend'
/**
 * @see routes/web.php:45
 * @route '/siswa/verifikasi-otp'
 */
export const page = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: page.url(options),
    method: 'get',
})

page.definition = {
    methods: ["get","head"],
    url: '/siswa/verifikasi-otp',
} satisfies RouteDefinition<["get","head"]>

/**
 * @see routes/web.php:45
 * @route '/siswa/verifikasi-otp'
 */
page.url = (options?: RouteQueryOptions) => {
    return page.definition.url + queryParams(options)
}

/**
 * @see routes/web.php:45
 * @route '/siswa/verifikasi-otp'
 */
page.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: page.url(options),
    method: 'get',
})
/**
 * @see routes/web.php:45
 * @route '/siswa/verifikasi-otp'
 */
page.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: page.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Siswa\AuthController::submit
 * @see app/Http/Controllers/Siswa/AuthController.php:73
 * @route '/siswa/verifikasi-otp'
 */
export const submit = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(options),
    method: 'post',
})

submit.definition = {
    methods: ["post"],
    url: '/siswa/verifikasi-otp',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Siswa\AuthController::submit
 * @see app/Http/Controllers/Siswa/AuthController.php:73
 * @route '/siswa/verifikasi-otp'
 */
submit.url = (options?: RouteQueryOptions) => {
    return submit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Siswa\AuthController::submit
 * @see app/Http/Controllers/Siswa/AuthController.php:73
 * @route '/siswa/verifikasi-otp'
 */
submit.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Siswa\AuthController::resend
 * @see app/Http/Controllers/Siswa/AuthController.php:108
 * @route '/siswa/resend-otp'
 */
export const resend = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resend.url(options),
    method: 'post',
})

resend.definition = {
    methods: ["post"],
    url: '/siswa/resend-otp',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Siswa\AuthController::resend
 * @see app/Http/Controllers/Siswa/AuthController.php:108
 * @route '/siswa/resend-otp'
 */
resend.url = (options?: RouteQueryOptions) => {
    return resend.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Siswa\AuthController::resend
 * @see app/Http/Controllers/Siswa/AuthController.php:108
 * @route '/siswa/resend-otp'
 */
resend.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resend.url(options),
    method: 'post',
})
const otp = {
    page: Object.assign(page, page),
submit: Object.assign(submit, submit),
resend: Object.assign(resend, resendE1e4c0),
}

export default otp