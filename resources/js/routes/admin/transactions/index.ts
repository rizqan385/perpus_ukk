import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\TransactionController::exportMethod
 * @see app/Http/Controllers/Admin/TransactionController.php:58
 * @route '/admin/transactions/export'
 */
export const exportMethod = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

exportMethod.definition = {
    methods: ["get","head"],
    url: '/admin/transactions/export',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\TransactionController::exportMethod
 * @see app/Http/Controllers/Admin/TransactionController.php:58
 * @route '/admin/transactions/export'
 */
exportMethod.url = (options?: RouteQueryOptions) => {
    return exportMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\TransactionController::exportMethod
 * @see app/Http/Controllers/Admin/TransactionController.php:58
 * @route '/admin/transactions/export'
 */
exportMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Admin\TransactionController::exportMethod
 * @see app/Http/Controllers/Admin/TransactionController.php:58
 * @route '/admin/transactions/export'
 */
exportMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportMethod.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\TransactionController::index
 * @see app/Http/Controllers/Admin/TransactionController.php:19
 * @route '/admin/transactions'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/transactions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\TransactionController::index
 * @see app/Http/Controllers/Admin/TransactionController.php:19
 * @route '/admin/transactions'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\TransactionController::index
 * @see app/Http/Controllers/Admin/TransactionController.php:19
 * @route '/admin/transactions'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Admin\TransactionController::index
 * @see app/Http/Controllers/Admin/TransactionController.php:19
 * @route '/admin/transactions'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\TransactionController::create
 * @see app/Http/Controllers/Admin/TransactionController.php:146
 * @route '/admin/transactions/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/transactions/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\TransactionController::create
 * @see app/Http/Controllers/Admin/TransactionController.php:146
 * @route '/admin/transactions/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\TransactionController::create
 * @see app/Http/Controllers/Admin/TransactionController.php:146
 * @route '/admin/transactions/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Admin\TransactionController::create
 * @see app/Http/Controllers/Admin/TransactionController.php:146
 * @route '/admin/transactions/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\TransactionController::store
 * @see app/Http/Controllers/Admin/TransactionController.php:161
 * @route '/admin/transactions'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/transactions',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\TransactionController::store
 * @see app/Http/Controllers/Admin/TransactionController.php:161
 * @route '/admin/transactions'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\TransactionController::store
 * @see app/Http/Controllers/Admin/TransactionController.php:161
 * @route '/admin/transactions'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\TransactionController::show
 * @see app/Http/Controllers/Admin/TransactionController.php:215
 * @route '/admin/transactions/{transaction}'
 */
export const show = (args: { transaction: string | number | { id: string | number } } | [transaction: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/admin/transactions/{transaction}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\TransactionController::show
 * @see app/Http/Controllers/Admin/TransactionController.php:215
 * @route '/admin/transactions/{transaction}'
 */
show.url = (args: { transaction: string | number | { id: string | number } } | [transaction: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { transaction: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { transaction: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    transaction: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        transaction: typeof args.transaction === 'object'
                ? args.transaction.id
                : args.transaction,
                }

    return show.definition.url
            .replace('{transaction}', parsedArgs.transaction.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\TransactionController::show
 * @see app/Http/Controllers/Admin/TransactionController.php:215
 * @route '/admin/transactions/{transaction}'
 */
show.get = (args: { transaction: string | number | { id: string | number } } | [transaction: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Admin\TransactionController::show
 * @see app/Http/Controllers/Admin/TransactionController.php:215
 * @route '/admin/transactions/{transaction}'
 */
show.head = (args: { transaction: string | number | { id: string | number } } | [transaction: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\TransactionController::returnMethod
 * @see app/Http/Controllers/Admin/TransactionController.php:225
 * @route '/admin/transactions/{transaction}/return'
 */
export const returnMethod = (args: { transaction: string | number | { id: string | number } } | [transaction: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: returnMethod.url(args, options),
    method: 'post',
})

returnMethod.definition = {
    methods: ["post"],
    url: '/admin/transactions/{transaction}/return',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\TransactionController::returnMethod
 * @see app/Http/Controllers/Admin/TransactionController.php:225
 * @route '/admin/transactions/{transaction}/return'
 */
returnMethod.url = (args: { transaction: string | number | { id: string | number } } | [transaction: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { transaction: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { transaction: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    transaction: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        transaction: typeof args.transaction === 'object'
                ? args.transaction.id
                : args.transaction,
                }

    return returnMethod.definition.url
            .replace('{transaction}', parsedArgs.transaction.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\TransactionController::returnMethod
 * @see app/Http/Controllers/Admin/TransactionController.php:225
 * @route '/admin/transactions/{transaction}/return'
 */
returnMethod.post = (args: { transaction: string | number | { id: string | number } } | [transaction: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: returnMethod.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\TransactionController::approveReturn
 * @see app/Http/Controllers/Admin/TransactionController.php:242
 * @route '/admin/transactions/{transaction}/approve-return'
 */
export const approveReturn = (args: { transaction: string | number | { id: string | number } } | [transaction: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approveReturn.url(args, options),
    method: 'post',
})

approveReturn.definition = {
    methods: ["post"],
    url: '/admin/transactions/{transaction}/approve-return',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\TransactionController::approveReturn
 * @see app/Http/Controllers/Admin/TransactionController.php:242
 * @route '/admin/transactions/{transaction}/approve-return'
 */
approveReturn.url = (args: { transaction: string | number | { id: string | number } } | [transaction: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { transaction: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { transaction: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    transaction: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        transaction: typeof args.transaction === 'object'
                ? args.transaction.id
                : args.transaction,
                }

    return approveReturn.definition.url
            .replace('{transaction}', parsedArgs.transaction.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\TransactionController::approveReturn
 * @see app/Http/Controllers/Admin/TransactionController.php:242
 * @route '/admin/transactions/{transaction}/approve-return'
 */
approveReturn.post = (args: { transaction: string | number | { id: string | number } } | [transaction: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approveReturn.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\TransactionController::destroy
 * @see app/Http/Controllers/Admin/TransactionController.php:278
 * @route '/admin/transactions/{transaction}'
 */
export const destroy = (args: { transaction: string | number | { id: string | number } } | [transaction: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/transactions/{transaction}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\TransactionController::destroy
 * @see app/Http/Controllers/Admin/TransactionController.php:278
 * @route '/admin/transactions/{transaction}'
 */
destroy.url = (args: { transaction: string | number | { id: string | number } } | [transaction: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { transaction: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { transaction: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    transaction: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        transaction: typeof args.transaction === 'object'
                ? args.transaction.id
                : args.transaction,
                }

    return destroy.definition.url
            .replace('{transaction}', parsedArgs.transaction.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\TransactionController::destroy
 * @see app/Http/Controllers/Admin/TransactionController.php:278
 * @route '/admin/transactions/{transaction}'
 */
destroy.delete = (args: { transaction: string | number | { id: string | number } } | [transaction: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})
const transactions = {
    export: Object.assign(exportMethod, exportMethod),
index: Object.assign(index, index),
create: Object.assign(create, create),
store: Object.assign(store, store),
show: Object.assign(show, show),
return: Object.assign(returnMethod, returnMethod),
approveReturn: Object.assign(approveReturn, approveReturn),
destroy: Object.assign(destroy, destroy),
}

export default transactions