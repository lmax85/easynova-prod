import Vue from 'vue'
import { translate, translatePlural } from '@nextcloud/l10n'
import { Checkbox, Button, Switch, Table, TableColumn, Notification } from 'element-ui'
import store from './store'
import App from './App'
import lang from 'element-ui/lib/locale/lang/de'
import locale from 'element-ui/lib/locale'

// configure language
locale.use(lang)

Vue.component(Button.name, Button)
Vue.component(Checkbox.name, Checkbox)
Vue.component(Switch.name, Switch)
Vue.component(Table.name, Table)
Vue.component(TableColumn.name, TableColumn)
Vue.component(Notification.name, Notification)

Vue.prototype.$notify = Notification
Vue.prototype.t = translate
Vue.prototype.n = translatePlural

export default new Vue({
	el: '#content',
	store,
	render: h => h(App),
})
