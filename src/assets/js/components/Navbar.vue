<template>
    <div>
        <ChangeViewBanner v-model="viewMode" v-if="userIsAdmin"/>
        <header class="iande-navbar">
            <div class="iande-container iande-navbar__row">
                <a class="iande-navbar__site-name" :href="$iandeUrl('user/welcome')">
                    <template v-if="tainacanBranded">
                        <img :src="`${$iande.iandePath}assets/img/iande-logo_short.png`" alt="Iandé" title="Iandé">
                        & <img :src="`${$iande.iandePath}assets/img/tainacan-logo_short.png`" alt="Tainacan" title="Tainacan">
                        + {{ __($iande.siteName, 'iande') }}
                    </template>
                    <template v-else>
                        <img :src="`${$iande.iandePath}assets/img/iande-logo.png`" alt="Iandé">
                        + {{ __($iande.siteName, 'iande') }}
                    </template>
                </a>
                <a v-if="isLoggedIn" class="iande-navbar__toggle" href="javascript:void(0)" role="button" tabindex="0" :aria-label="showMenu ? __('Ocultar menu', 'iande') : __('Exibir menu', 'iande')" @click="toggleMenu">
                    <Icon icon="bars"/>
                </a>
                <nav :class="showMenu || 'hidden'" v-if="isLoggedIn">
                    <ul>
                        <template v-if="userIsAdmin && viewMode === 'educator'">
                            <li class="iande-navbar__dropdown">
                                <a href="javascript:void(0)" role="button" tabindex="0">
                                    <span>{{ __('Agendamento', 'iande') }}</span>
                                    <Icon icon="caret-down"/>
                                </a>
                                <ul>
                                    <li><a :href="$iandeUrl('group/list')">{{ __('Calendário geral', 'iande') }}</a></li>
                                    <li><a :href="$iandeUrl('group/agenda')">{{ __('Minha agenda', 'iande') }}</a></li>
                                </ul>
                            </li>
                            <li><a :href="$iandeUrl('itinerary/list')" v-if="$iande.tainacanActive">{{ __('Roteiros', 'iande') }}</a></li>
                            <li class="iande-navbar__icon-link">
                                <a :href="$iande.adminUrl" target="_blank">
                                    <Icon :icon="['fab', 'wordpress-simple']"/>
                                    <span>{{ __('Voltar ao admin', 'iande') }}</span>
                                </a>
                            </li>
                        </template>
                        <template v-else>
                            <li><a :href="$iandeUrl('appointment/list')">{{ __('Agendamentos', 'iande') }}</a></li>
                            <li><a :href="$iandeUrl('itinerary/list')" v-if="$iande.tainacanActive">{{ __('Roteiros', 'iande') }}</a></li>
                            <li><a :href="$iandeUrl('institution/list')">{{ __('Instituições', 'iande') }}</a></li>
                        </template>
                        <li class="iande-navbar__dropdown">
                            <a href="javascript:void(0)" role="button" tabindex="0" :aria-label="__('Usuário', 'iande')">
                                <Icon icon="user-large"/>
                            </a>
                            <ul>
                                <li><a :href="$iandeUrl('user/edit')">{{ __('Editar usuário', 'iande') }}</a></li>
                                <li><a :href="$iandeUrl('user/change-password')">{{ __('Alterar senha', 'iande') }}</a></li>
                                <li><a href="javascript:void(0)" role="button" tabindex="0" @click="logout">{{ __('Logout', 'iande') }}</a></li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </header>
    </div>
</template>

<script>
    import { sync } from 'vuex-pathify'

    import ChangeViewBanner from '@components/ChangeViewBanner.vue'
    import { api } from '@utils'

    export default {
        name: 'Navbar',
        components: {
            ChangeViewBanner,
        },
        data () {
            return {
                isLoggedIn: false,
                showMenu: false,
                viewMode: 'visitor',
            }
        },
        computed: {
            exhibitions: sync('exhibitions/list'),
            tainacanBranded () {
                return window.location.pathname.includes('iande/itinerary')
            },
            user: sync('users/current'),
            userIsAdmin () {
                if (!this.user) {
                    return false
                }
                const adminRoles = ['administrator', 'iande_admin', 'iande_educator']
                return this.user.roles.some(role => adminRoles.includes(role))
            },
        },
        async created () {
            try {
                if (await api.post('user/is_logged_in')) {
                    const [user, exhibitions] = await Promise.all([
                        api.post('user/get_logged_in'),
                        api.post('exhibition/list'),
                    ])
                    this.isLoggedIn = true
                    this.user = user
                    this.exhibitions = exhibitions
                }
            } catch (err) {
                console.error(err)
            }
        },
        methods: {
            async logout () {
                try {
                    await api.post('user/logout')
                    window.location.assign(this.$iandeUrl('user/login'))
                } catch (err) {
                    console.error(err)
                }
            },
            toggleMenu () {
                this.showMenu = !this.showMenu
            },
        }
    }
</script>
