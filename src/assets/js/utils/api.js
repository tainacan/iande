import bent from 'bent'

const baseUrl = `${window.location.origin}/iande/`

export default {
    get: bent('GET', baseUrl, 'json'),
    post: bent('POST', baseUrl, 'json')
}
