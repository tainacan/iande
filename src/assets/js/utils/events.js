const EVENT_NAME = 'iande'

export function dispatchIandeEvent (type, data) {
    const event = new CustomEvent(EVENT_NAME, {
        detail: { type, data },
    })
    window.dispatchEvent(event)
}

export function onIandeEvent (callback) {
    const listener = (event) => {
        const { type, data } = event.detail
        callback(type, data, event)
    }
    window.addEventListener(EVENT_NAME, listener)
    return () => window.removeEventListener(EVENT_NAME, listener)
}
