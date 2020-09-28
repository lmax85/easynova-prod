import axios from 'axios'
import { getEndpoint } from './../../helpers'

const endpoint = getEndpoint()
const http = axios.create({
	timeout: 15000,
	headers: {
		'Content-Type': 'application/json',
		'Access-Control-Allow-Origin': '*',
	},
})

/**
 * use X-CSRF-TOKEN for every request
 *
 */
http.interceptors.request.use(
	function(config) {
		// store.commit('ERRORS_RESET');
		// config.headers['X-CSRF-TOKEN'] = getMeta('csrf-token');
		return config
	},
	function(error) {
		return Promise.reject(error)
	}
)

/**
 * catch every error and notify vuex ...
 */
http.interceptors.response.use(function(response) {
	return response
}, function(error) {
	console.debug('axios interceptors response catch error...', error)
	// store.commit('ERRORS_SET', parseAxiosError(error)) // just taking some guesses here
	return Promise.reject(error) // this is the important part
})

export { endpoint, http }
