import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\MemberController::cards
 * @see app/Http/Controllers/Admin/MemberController.php:218
 * @route '/admin/members/cards'
 */
export const cards = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: cards.url(options),
    method: 'get',
})

cards.definition = {
    methods: ["get","head"],
    url: '/admin/members/cards',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\MemberController::cards
 * @see app/Http/Controllers/Admin/MemberController.php:218
 * @route '/admin/members/cards'
 */
cards.url = (options?: RouteQueryOptions) => {
    return cards.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MemberController::cards
 * @see app/Http/Controllers/Admin/MemberController.php:218
 * @route '/admin/members/cards'
 */
cards.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: cards.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Admin\MemberController::cards
 * @see app/Http/Controllers/Admin/MemberController.php:218
 * @route '/admin/members/cards'
 */
cards.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: cards.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\MemberController::index
 * @see app/Http/Controllers/Admin/MemberController.php:20
 * @route '/admin/members'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/members',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\MemberController::index
 * @see app/Http/Controllers/Admin/MemberController.php:20
 * @route '/admin/members'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MemberController::index
 * @see app/Http/Controllers/Admin/MemberController.php:20
 * @route '/admin/members'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Admin\MemberController::index
 * @see app/Http/Controllers/Admin/MemberController.php:20
 * @route '/admin/members'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\MemberController::create
 * @see app/Http/Controllers/Admin/MemberController.php:82
 * @route '/admin/members/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/members/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\MemberController::create
 * @see app/Http/Controllers/Admin/MemberController.php:82
 * @route '/admin/members/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MemberController::create
 * @see app/Http/Controllers/Admin/MemberController.php:82
 * @route '/admin/members/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Admin\MemberController::create
 * @see app/Http/Controllers/Admin/MemberController.php:82
 * @route '/admin/members/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\MemberController::store
 * @see app/Http/Controllers/Admin/MemberController.php:90
 * @route '/admin/members'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/members',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\MemberController::store
 * @see app/Http/Controllers/Admin/MemberController.php:90
 * @route '/admin/members'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MemberController::store
 * @see app/Http/Controllers/Admin/MemberController.php:90
 * @route '/admin/members'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\MemberController::show
 * @see app/Http/Controllers/Admin/MemberController.php:136
 * @route '/admin/members/{member}'
 */
export const show = (args: { member: string | number | { id: string | number } } | [member: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/admin/members/{member}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\MemberController::show
 * @see app/Http/Controllers/Admin/MemberController.php:136
 * @route '/admin/members/{member}'
 */
show.url = (args: { member: string | number | { id: string | number } } | [member: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { member: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { member: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    member: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        member: typeof args.member === 'object'
                ? args.member.id
                : args.member,
                }

    return show.definition.url
            .replace('{member}', parsedArgs.member.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MemberController::show
 * @see app/Http/Controllers/Admin/MemberController.php:136
 * @route '/admin/members/{member}'
 */
show.get = (args: { member: string | number | { id: string | number } } | [member: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Admin\MemberController::show
 * @see app/Http/Controllers/Admin/MemberController.php:136
 * @route '/admin/members/{member}'
 */
show.head = (args: { member: string | number | { id: string | number } } | [member: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\MemberController::edit
 * @see app/Http/Controllers/Admin/MemberController.php:148
 * @route '/admin/members/{member}/edit'
 */
export const edit = (args: { member: string | number | { id: string | number } } | [member: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/members/{member}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\MemberController::edit
 * @see app/Http/Controllers/Admin/MemberController.php:148
 * @route '/admin/members/{member}/edit'
 */
edit.url = (args: { member: string | number | { id: string | number } } | [member: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { member: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { member: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    member: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        member: typeof args.member === 'object'
                ? args.member.id
                : args.member,
                }

    return edit.definition.url
            .replace('{member}', parsedArgs.member.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MemberController::edit
 * @see app/Http/Controllers/Admin/MemberController.php:148
 * @route '/admin/members/{member}/edit'
 */
edit.get = (args: { member: string | number | { id: string | number } } | [member: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Admin\MemberController::edit
 * @see app/Http/Controllers/Admin/MemberController.php:148
 * @route '/admin/members/{member}/edit'
 */
edit.head = (args: { member: string | number | { id: string | number } } | [member: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\MemberController::update
 * @see app/Http/Controllers/Admin/MemberController.php:160
 * @route '/admin/members/{member}'
 */
export const update = (args: { member: string | number | { id: string | number } } | [member: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/members/{member}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Admin\MemberController::update
 * @see app/Http/Controllers/Admin/MemberController.php:160
 * @route '/admin/members/{member}'
 */
update.url = (args: { member: string | number | { id: string | number } } | [member: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { member: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { member: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    member: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        member: typeof args.member === 'object'
                ? args.member.id
                : args.member,
                }

    return update.definition.url
            .replace('{member}', parsedArgs.member.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MemberController::update
 * @see app/Http/Controllers/Admin/MemberController.php:160
 * @route '/admin/members/{member}'
 */
update.put = (args: { member: string | number | { id: string | number } } | [member: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\Admin\MemberController::update
 * @see app/Http/Controllers/Admin/MemberController.php:160
 * @route '/admin/members/{member}'
 */
update.patch = (args: { member: string | number | { id: string | number } } | [member: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\MemberController::destroy
 * @see app/Http/Controllers/Admin/MemberController.php:256
 * @route '/admin/members/{member}'
 */
export const destroy = (args: { member: string | number | { id: string | number } } | [member: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/members/{member}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\MemberController::destroy
 * @see app/Http/Controllers/Admin/MemberController.php:256
 * @route '/admin/members/{member}'
 */
destroy.url = (args: { member: string | number | { id: string | number } } | [member: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { member: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { member: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    member: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        member: typeof args.member === 'object'
                ? args.member.id
                : args.member,
                }

    return destroy.definition.url
            .replace('{member}', parsedArgs.member.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MemberController::destroy
 * @see app/Http/Controllers/Admin/MemberController.php:256
 * @route '/admin/members/{member}'
 */
destroy.delete = (args: { member: string | number | { id: string | number } } | [member: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Admin\MemberController::card
 * @see app/Http/Controllers/Admin/MemberController.php:206
 * @route '/admin/members/{member}/card'
 */
export const card = (args: { member: string | number | { id: string | number } } | [member: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: card.url(args, options),
    method: 'get',
})

card.definition = {
    methods: ["get","head"],
    url: '/admin/members/{member}/card',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\MemberController::card
 * @see app/Http/Controllers/Admin/MemberController.php:206
 * @route '/admin/members/{member}/card'
 */
card.url = (args: { member: string | number | { id: string | number } } | [member: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { member: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { member: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    member: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        member: typeof args.member === 'object'
                ? args.member.id
                : args.member,
                }

    return card.definition.url
            .replace('{member}', parsedArgs.member.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MemberController::card
 * @see app/Http/Controllers/Admin/MemberController.php:206
 * @route '/admin/members/{member}/card'
 */
card.get = (args: { member: string | number | { id: string | number } } | [member: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: card.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Admin\MemberController::card
 * @see app/Http/Controllers/Admin/MemberController.php:206
 * @route '/admin/members/{member}/card'
 */
card.head = (args: { member: string | number | { id: string | number } } | [member: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: card.url(args, options),
    method: 'head',
})
const members = {
    cards: Object.assign(cards, cards),
index: Object.assign(index, index),
create: Object.assign(create, create),
store: Object.assign(store, store),
show: Object.assign(show, show),
edit: Object.assign(edit, edit),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
card: Object.assign(card, card),
}

export default members