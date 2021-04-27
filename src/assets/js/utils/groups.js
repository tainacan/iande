export function assignmentStatus (group, userId) {
    if (!group.educator_id) {
        return 'unassigned'
    }
    if (group.educator_id == userId) {
        return 'assigned-self'
    } else {
        return 'assigned-other'
    }
}
