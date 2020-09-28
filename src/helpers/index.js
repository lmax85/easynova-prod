/**
 * find error status & return error message
 * @param {any} error [description]
 * @returns {string}
 */
export const parseAxiosError = (error) => {
	let errorStatus = null

	if (error.status) {
		errorStatus = error.status
	} else if (error.response) {
		errorStatus = error.response.status
	} else if (error.request) {
		errorStatus = error.request.status
	}

	return prepareErrorMessage(errorStatus)
}

const prepareErrorMessage = status => {
	switch (status) {
	case 400:
		return 'Error.'
	case 401:
		return 'You are not allowed for this action.'
	case 422:
		return 'Wrong request.'
	case 404:
		return 'Resource not found.'
	case 405:
		return 'Method not allowed.'
	case 500:
	case 501:
		return 'Server error.'
	default:
		return 'Something went wrong.'
	}
}

export const getEndpoint = () => {
	let endpoint = ''
	console.debug('process.env.NODE_ENV from helpers... = ', process.env.NODE_ENV)

	switch (process.env.NODE_ENV) {
	case 'production':
		endpoint = 'https://portal.easynova.de/apps/easynova/'
		break
	case 'development':
		endpoint = 'http://localhost:8080/apps/easynova/'
		break
	default:
		endpoint = 'http://localhost:8080/apps/easynova/'
		break
	}

	return endpoint
}

/**
 * Search meta tags (usually in header section)
 * @param {string} metaName [description]
 * @returns {string}
 */
export const getMeta = (metaName) => {
	const metas = (document !== undefined) ? document.getElementsByTagName('meta') : []

	for (let i = 0; i < metas.length; i++) {
		if (metas[i].getAttribute('name') === metaName) {
			return metas[i].getAttribute('content')
		}
	}

	return ''
}
/**
 * return human bytes string
 * @param {int} bytes [description]
 * @param {string|symbol} separator [description]
 * @param {string|symbol} postFix [description]
 * @returns {string}
 */
export const prettySize = (bytes, separator = '', postFix = '') => {
	if (bytes) {
		const sizes = ['Байт', 'Кб', 'Мб', 'Гб', 'Тб']
		const i = Math.min(parseInt(Math.floor(Math.log(bytes) / Math.log(1024)), 10), sizes.length - 1)
		return `${(bytes / (1024 ** i)).toFixed(i ? 1 : 0)}${separator}${sizes[i]}${postFix}`
	}
	return 'n/a'
}

/**
 * @param {string} str [description]
 * @returns {string} stripped string (remove html tags)
 */
export const stripString = (str) => {
	return str.replace(/<\/?[^>]+(>|$)/g, '')
}
