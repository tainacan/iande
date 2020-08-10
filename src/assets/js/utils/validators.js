import { helpers } from 'vuelidate/lib/validators'

export const phone = helpers.regex('phone', /^\d{10,11}$/)
