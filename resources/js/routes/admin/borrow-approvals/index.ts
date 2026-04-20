import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\BorrowApprovalController::index
 * @see app/Http/Controllers/Admin/BorrowApprovalController.php:17
 * @route '/admin/borrow-approvals'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/borrow-approvals',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\BorrowApprovalController::index
 * @see app/Http/Controllers/Admin/BorrowApprovalController.php:17
 * @route '/admin/borrow-approvals'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\BorrowApprovalController::index
 * @see app/Http/Controllers/Admin/BorrowApprovalController.php:17
 * @route '/admin/borrow-approvals'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Admin\BorrowApprovalController::index
 * @see app/Http/Controllers/Admin/BorrowApprovalController.php:17
 * @route '/admin/borrow-approvals'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\BorrowApprovalController::approve
 * @see app/Http/Controllers/Admin/BorrowApprovalController.php:95
 * @route '/admin/borrow-approvals/{borrowing}/approve'
 */
export const approve = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(args, options),
    method: 'post',
})

approve.definition = {
    methods: ["post"],
    url: '/admin/borrow-approvals/{borrowing}/approve',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\BorrowApprovalController::approve
 * @see app/Http/Controllers/Admin/BorrowApprovalController.php:95
 * @route '/admin/borrow-approvals/{borrowing}/approve'
 */
approve.url = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
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

    return approve.definition.url
            .replace('{borrowing}', parsedArgs.borrowing.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\BorrowApprovalController::approve
 * @see app/Http/Controllers/Admin/BorrowApprovalController.php:95
 * @route '/admin/borrow-approvals/{borrowing}/approve'
 */
approve.post = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\BorrowApprovalController::reject
 * @see app/Http/Controllers/Admin/BorrowApprovalController.php:131
 * @route '/admin/borrow-approvals/{borrowing}/reject'
 */
export const reject = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reject.url(args, options),
    method: 'post',
})

reject.definition = {
    methods: ["post"],
    url: '/admin/borrow-approvals/{borrowing}/reject',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\BorrowApprovalController::reject
 * @see app/Http/Controllers/Admin/BorrowApprovalController.php:131
 * @route '/admin/borrow-approvals/{borrowing}/reject'
 */
reject.url = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
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

    return reject.definition.url
            .replace('{borrowing}', parsedArgs.borrowing.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\BorrowApprovalController::reject
 * @see app/Http/Controllers/Admin/BorrowApprovalController.php:131
 * @route '/admin/borrow-approvals/{borrowing}/reject'
 */
reject.post = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reject.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\BorrowApprovalController::approveReturn
 * @see app/Http/Controllers/Admin/BorrowApprovalController.php:158
 * @route '/admin/borrow-approvals/{borrowing}/approve-return'
 */
export const approveReturn = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approveReturn.url(args, options),
    method: 'post',
})

approveReturn.definition = {
    methods: ["post"],
    url: '/admin/borrow-approvals/{borrowing}/approve-return',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\BorrowApprovalController::approveReturn
 * @see app/Http/Controllers/Admin/BorrowApprovalController.php:158
 * @route '/admin/borrow-approvals/{borrowing}/approve-return'
 */
approveReturn.url = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
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

    return approveReturn.definition.url
            .replace('{borrowing}', parsedArgs.borrowing.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\BorrowApprovalController::approveReturn
 * @see app/Http/Controllers/Admin/BorrowApprovalController.php:158
 * @route '/admin/borrow-approvals/{borrowing}/approve-return'
 */
approveReturn.post = (args: { borrowing: string | number | { id: string | number } } | [borrowing: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approveReturn.url(args, options),
    method: 'post',
})
const borrowApprovals = {
    index: Object.assign(index, index),
approve: Object.assign(approve, approve),
reject: Object.assign(reject, reject),
approveReturn: Object.assign(approveReturn, approveReturn),
}

export default borrowApprovals