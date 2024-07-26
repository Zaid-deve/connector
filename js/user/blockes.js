async function unblockUser(peer, node) {
    node.disabled = true
    if (await blockFriend(peer)) {
        node.closest('.block_te').remove()
    } else {
        throwErr('Failed to unblock user')
    }
    node.disabled = false
}