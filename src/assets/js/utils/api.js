import { __ } from '@plugins/wp-i18n'

const baseUrl = window.IandeSettings.iandeUrl + '/'

export function searchParams (object) {
    const search = new URLSearchParams()
    for (const [key, value] of Object.entries(object)) {
        if (Array.isArray(value)) {
            for (const item of value) {
                search.append(key, item)
            }
        } else {
            search.set(key, value)
        }
    }
    return search
}

async function client (method, relativeUrl, body, headers) {
    const url = relativeUrl.startsWith('http') ? new URL(relativeUrl) : new URL(relativeUrl, baseUrl)
    if (body instanceof URLSearchParams) {
        for (const [key, value] of body.entries()) {
            url.searchParams.set(key, value)
        }
    }
    const request = !body || body instanceof URLSearchParams
        ? { method, headers: { ...headers, 'Accept': 'application/json' } }
        : { method, body: JSON.stringify(body), headers: { ...headers, 'Content-Type': 'application/json' } }
    try {
        const response = await window.fetch(url, request)
        const data = await response.json()
        if (response.ok) {
            return data
        } else {
            return Promise.reject(data)
        }
    } catch {
        return Promise.reject(__('Erro inesperado do servidor', 'iande'))
    }
}

export default {
    get (url, body = {}, headers = {}) {
        return client('GET', url, searchParams(body), headers)
    },
    post (url, body = null, headers = {}) {
        return client('POST', url, body, headers)
    },
}
