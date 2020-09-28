<template>
	<Content :class="{'icon-loading': loading}" app-name="easynova">
		<AppContent>
			<article>
				<Files />
			</article>
		</AppContent>
	</Content>
</template>

<script>
import Content from '@nextcloud/vue/dist/Components/Content'
import AppContent from '@nextcloud/vue/dist/Components/AppContent'
import Files from './components/Files'

export default {
	name: 'App',
	components: {
		Content,
		AppContent,
		Files,
	},
	data() {
		return {
			loading: false,
		}
	},
	mounted() {
		console.debug('vue mounted, lets call apis...')
	},
	methods: {
		log(e) {
			console.debug(e)
		},
	},
}
</script>
