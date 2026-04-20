import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
import login from './login'
import register702019 from './register'
import otp from './otp'
import password from './password'
import borrow from './borrow'
import returns4b8e07 from './returns'
import finesB0faef from './fines'
import favoritesC40993 from './favorites'
import profile from './profile'
import payment from './payment'
/**
* @see \App\Http\Controllers\Siswa\DashboardController::dashboard
 * @see app/Http/Controllers/Siswa/DashboardController.php:15
 * @route '/siswa'
 */
export const dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ["get","head"],
    url: '/siswa',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Siswa\DashboardController::dashboard
 * @see app/Http/Controllers/Siswa/DashboardController.php:15
 * @route '/siswa'
 */
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Siswa\DashboardController::dashboard
 * @see app/Http/Controllers/Siswa/DashboardController.php:15
 * @route '/siswa'
 */
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Siswa\DashboardController::dashboard
 * @see app/Http/Controllers/Siswa/DashboardController.php:15
 * @route '/siswa'
 */
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Siswa\MemberRegistrationController::register
 * @see app/Http/Controllers/Siswa/MemberRegistrationController.php:18
 * @route '/siswa/register'
 */
export const register = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: register.url(options),
    method: 'get',
})

register.definition = {
    methods: ["get","head"],
    url: '/siswa/register',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Siswa\MemberRegistrationController::register
 * @see app/Http/Controllers/Siswa/MemberRegistrationController.php:18
 * @route '/siswa/register'
 */
register.url = (options?: RouteQueryOptions) => {
    return register.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Siswa\MemberRegistrationController::register
 * @see app/Http/Controllers/Siswa/MemberRegistrationController.php:18
 * @route '/siswa/register'
 */
register.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: register.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Siswa\MemberRegistrationController::register
 * @see app/Http/Controllers/Siswa/MemberRegistrationController.php:18
 * @route '/siswa/register'
 */
register.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: register.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Siswa\ReturnController::returns
 * @see app/Http/Controllers/Siswa/ReturnController.php:17
 * @route '/siswa/returns'
 */
export const returns = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: returns.url(options),
    method: 'get',
})

returns.definition = {
    methods: ["get","head"],
    url: '/siswa/returns',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Siswa\ReturnController::returns
 * @see app/Http/Controllers/Siswa/ReturnController.php:17
 * @route '/siswa/returns'
 */
returns.url = (options?: RouteQueryOptions) => {
    return returns.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Siswa\ReturnController::returns
 * @see app/Http/Controllers/Siswa/ReturnController.php:17
 * @route '/siswa/returns'
 */
returns.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: returns.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Siswa\ReturnController::returns
 * @see app/Http/Controllers/Siswa/ReturnController.php:17
 * @route '/siswa/returns'
 */
returns.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: returns.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Siswa\FineController::fines
 * @see app/Http/Controllers/Siswa/FineController.php:16
 * @route '/siswa/fines'
 */
export const fines = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: fines.url(options),
    method: 'get',
})

fines.definition = {
    methods: ["get","head"],
    url: '/siswa/fines',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Siswa\FineController::fines
 * @see app/Http/Controllers/Siswa/FineController.php:16
 * @route '/siswa/fines'
 */
fines.url = (options?: RouteQueryOptions) => {
    return fines.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Siswa\FineController::fines
 * @see app/Http/Controllers/Siswa/FineController.php:16
 * @route '/siswa/fines'
 */
fines.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: fines.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Siswa\FineController::fines
 * @see app/Http/Controllers/Siswa/FineController.php:16
 * @route '/siswa/fines'
 */
fines.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: fines.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Siswa\FavoriteController::favorites
 * @see app/Http/Controllers/Siswa/FavoriteController.php:15
 * @route '/siswa/favorites'
 */
export const favorites = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: favorites.url(options),
    method: 'get',
})

favorites.definition = {
    methods: ["get","head"],
    url: '/siswa/favorites',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Siswa\FavoriteController::favorites
 * @see app/Http/Controllers/Siswa/FavoriteController.php:15
 * @route '/siswa/favorites'
 */
favorites.url = (options?: RouteQueryOptions) => {
    return favorites.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Siswa\FavoriteController::favorites
 * @see app/Http/Controllers/Siswa/FavoriteController.php:15
 * @route '/siswa/favorites'
 */
favorites.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: favorites.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Siswa\FavoriteController::favorites
 * @see app/Http/Controllers/Siswa/FavoriteController.php:15
 * @route '/siswa/favorites'
 */
favorites.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: favorites.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Siswa\CardController::card
 * @see app/Http/Controllers/Siswa/CardController.php:14
 * @route '/siswa/kartu'
 */
export const card = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: card.url(options),
    method: 'get',
})

card.definition = {
    methods: ["get","head"],
    url: '/siswa/kartu',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Siswa\CardController::card
 * @see app/Http/Controllers/Siswa/CardController.php:14
 * @route '/siswa/kartu'
 */
card.url = (options?: RouteQueryOptions) => {
    return card.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Siswa\CardController::card
 * @see app/Http/Controllers/Siswa/CardController.php:14
 * @route '/siswa/kartu'
 */
card.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: card.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Siswa\CardController::card
 * @see app/Http/Controllers/Siswa/CardController.php:14
 * @route '/siswa/kartu'
 */
card.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: card.url(options),
    method: 'head',
})
const siswa = {
    login: Object.assign(login, login),
register: Object.assign(register, register702019),
otp: Object.assign(otp, otp),
password: Object.assign(password, password),
dashboard: Object.assign(dashboard, dashboard),
borrow: Object.assign(borrow, borrow),
returns: Object.assign(returns, returns4b8e07),
fines: Object.assign(fines, finesB0faef),
favorites: Object.assign(favorites, favoritesC40993),
card: Object.assign(card, card),
profile: Object.assign(profile, profile),
payment: Object.assign(payment, payment),
}

export default siswa