const EVENT_NAME = 'iande'

export function dispatchIandeEvent (type, payload = null) {
    const event = new CustomEvent(EVENT_NAME, {
        detail: { type, payload },
    })
    window.dispatchEvent(event)
}

export function onIandeEvent (callback) {
    const listener = (event) => {
        const { type, payload } = event.detail
        callback(type, payload, event)
    }
    window.addEventListener(EVENT_NAME, listener)
    return () => window.removeEventListener(EVENT_NAME, listener)
}
