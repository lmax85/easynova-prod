<template>
	<div>
		<h2>files here:</h2>
		<el-table
			:loading="loading"
			:data="files"
			style="width: 100%">
			<el-table-column
				label="Id"
				prop="id" />
			<el-table-column
				label="File name"
				prop="file_name" />
			<el-table-column
				label="Created at"
				prop="created_at" />
			<el-table-column label="Need paper duplicate doc?">
				<template slot-scope="scope">
					<el-checkbox :value="scope.row.paper_flag ? true : false" @change="updateHandler($event, scope.row)"> Need paper doc
					</el-checkbox>
				</template>
			</el-table-column>
		</el-table>
		<div v-if="!loading && !files.length">
			You are haven't any files from Easynova yet.
		</div>
	</div>
</template>

<script>

import { mapState } from 'vuex'

export default {
	name: 'Files',
	components: {
	},
	data() {
		return {
			loading: false,
		}
	},
	computed: {
		...mapState({
			files: state => state.files.files,
		}),
	},
	async mounted() {
		this.loading = true
		await this.$store.dispatch('files/getUserFiles')
		this.loading = false
	},
	methods: {
		async updateHandler(paperFlag, file) {
			try {
				file.paper_flag = paperFlag

				// send update to server
				const response = await this.$store.dispatch('files/updateFile', file)

				if (!response.error) {
					this.$notify({
						title: 'Success',
						message: 'This is a success message',
					})
				}
			} catch (e) {
				console.debug('Files.vue -> updateHandler -> e', e)
				this.$notify({
					title: 'Error',
					message: 'Something went wrong...',
				})
			}
		},
		log(e) {
			console.debug(e)
		},
	},
}
</script>
