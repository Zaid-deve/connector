async function toggleStar(peer, node) {
    node.disabled = true
    if (await addStarFriend(peer)) {
        node.closest('.star_te').remove()
    } else {
        throwErr('Failed to mark star user')
    }
    node.disabled = false
}