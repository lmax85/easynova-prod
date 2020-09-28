import api from './../../api'

const state = {
	files: [],
}

// getters
const getters = {
}

// actions
const actions = {
	async getUserFiles({ state, commit }) {
		const response = await api.files.getUserFiles()
		console.debug('store -> files -> actions -> getUserFiles -> response = ', response)
		if (!response.error) {
			if (response.data && response.data.files) {
				 commit('setUserFiles', response.data.files)
			}
		}
	},
	async updateFile({ state, commit }, file) {
		return await api.files.updateUserFile(file)
	},
}

// mutations
const mutations = {
	setUserFiles(state, payload) {
		state.files = payload
	},
}

export default {
	namespaced: true,
	state,
	getters,
	actions,
	mutations,
}
