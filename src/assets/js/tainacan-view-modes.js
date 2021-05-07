import ListViewMode from '@components/ListViewMode.vue'
import MasonryViewMode from '@components/MasonryViewMode.vue'

window.tainacan_extra_components = typeof window.tainacan_extra_components != 'undefined' ? window.tainacan_extra_components : {}
window.tainacan_extra_components['iande-list'] = ListViewMode
window.tainacan_extra_components['iande-masonry'] = MasonryViewMode
