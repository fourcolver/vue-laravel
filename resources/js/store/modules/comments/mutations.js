export default {
    set (state, {
        id,
        data,
        parent_id,
        commentable
    }) {
        data.data = data.data.map(comment => {
            comment.children = {data: [], total: 0}

            return comment
        }).reverse()

        let props = Object.keys(data).filter(prop => prop !== 'data')

        if (state[commentable][id] && !parent_id) {
            for (let prop of props) {
                if (state[commentable][id][prop] !== data[prop]) {
                    state[commentable][id][prop] = data[prop]
                }
            }

            state[commentable][id].data = data.data.concat(state[commentable][id].data)
        } else if (id && parent_id) {
            let comment = state[commentable][id].data.find(({id}) => id == parent_id)

            if (comment) {
                if (!comment.children.data.length) {
                    comment.children = data
                } else {
                    for (let prop of props) {
                        if (comment.children[prop] !== data[prop]) {
                            comment.children[prop] = data[prop]
                        }
                    }

                    comment.children.data = data.data.concat(comment.children.data)
                }
            }
        } else {
            state[commentable][id] = {...state[commentable][id], ...data}
        }
    },
    create (state, {
        id,
        data,
        parent_id,
        commentable
    }) {
        data.new = true

        if (id && parent_id) {
            let comment = state[commentable][id].data.find(({id}) => id === parent_id)

            if (comment) {
                comment.children_count++
                comment.children.total++
                comment.children.data.push(data)
            }

        } else {
            data.children = {data: [], total: 0}

            state[commentable][id].total++
            state[commentable][id].data.push(data)
        }
    },
    update (state, {
        id,
        data,
        parent_id,
        child_id,
        commentable
    }) {
        const comment = state[commentable][id].data.find(c => c.id === parent_id)

        if (comment) {
            if (child_id) {
                const childComment = comment.children.data.find(({id}) => id === child_id)

                if (childComment) {
                    Object.assign(childComment, data)
                }
            } else {
                Object.assign(comment, data)
            }
        }
    },

    delete (state, {
        id,
        data,
        child_id,
        parent_id,
        commentable
    }) {
        const idx = state[commentable][id].data.findIndex(c => c.id === parent_id)

        if (idx > -1) {
            if (child_id) {
                const childIdx = state[commentable][id].data[idx].children.data.findIndex(cc => cc.id === child_id)
                
                if (childIdx > -1) {
                    state[commentable][id].data[idx].children.data.splice(childIdx, 1)
                    state[commentable][id].data[idx].children_count--
                }
            } else if (data.comment) {
                state[commentable][id].data.splice(idx, 1)
            } else {
                state[commentable][id].data[idx].comment = data.comment
            }
        }
    },

    clear (state, {commentable}) {
        state[commentable] = {}
    }
}