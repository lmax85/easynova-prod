import { endpoint, http } from './../../utils/http'

export default {
	getUserFiles(params = {}) {
		return http.get(endpoint + 'api/v1/user/files', { params })
			.then(response => {
				 return response
			})
			.catch(error => {
				return {
					error,
				}
			})
	},
	updateUserFile(file) {
		return http.put(endpoint + 'api/v1/user/files/update/' + file.id, { ...file })
			.then(response => {
				 return response
			})
			.catch(error => {
				return {
					error,
				}
			})
	},
}
