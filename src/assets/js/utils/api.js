const baseUrl = `${window.location.origin}/iande/`

async function client (method, url, body = null, headers = {}) {
    const resource = baseUrl + url
    const request = body
        ? { method, body: JSON.stringify(body), headers: { ...headers, 'content-type': 'application/json' } }
        : { method, headers }
    const response = await window.fetch(resource, request)
    const data = await response.json()
    if (response.ok) {
        return data
    } else {
        return Promise.reject(data)
    }
}

export default {
    get (url, body, headers) {
        return client('GET', url, body, headers)
    },
    post (url, body, headers) {
        return client('POST', url, body, headers)
    }
}
