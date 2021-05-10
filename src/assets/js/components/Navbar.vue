<template>
    <div>
        <ChangeViewBanner v-model="viewMode" v-if="userIsAdmin"/>
        <header class="iande-navbar">
            <div class="iande-container iande-navbar__row">
                <div class="iande-navbar__site-name">
                    <img :src="`${$iande.siteUrl}/wp-content/plugins/iande/assets/img/iande-logo.png`" alt="Iandé"> + {{ $iande.siteName }}
                </div>
                <a v-if="isLoggedIn" class="iande-navbar__toggle" href="javascript:void(0)" role="button" tabindex="0" :aria-label="showMenu ? 'Ocultar menu' : 'Exibir menu'" @click="toggleMenu">
                    <Icon icon="bars"/>
                </a>
                <nav :class="showMenu || 'hidden'" v-if="isLoggedIn">
                    <ul>
                        <template v-if="userIsAdmin && viewMode === 'educator'">
                            <li class="iande-navbar__dropdown">
                                <a href="javascript:void(0)" role="button" tabindex="0">
                                    <span>Agendamentos</span>
                                    <Icon icon="caret-down"/>
                                </a>
                                <ul>
                                    <li><a :href="$iandeUrl('group/list')">Calendário geral</a></li>
                                    <li><a :href="$iandeUrl('group/agenda')">Minha agenda</a></li>
                                </ul>
                            </li>
                        </template>
                        <template v-else>
                            <li><a :href="$iandeUrl('appointment/list')">Agendamentos</a></li>
                            <li><a :href="$iandeUrl('institution/list')">Instituições</a></li>
                        </template>
                        <li class="iande-navbar__dropdown">
                            <a href="javascript:void(0)" role="button" tabindex="0" aria-label="Usuário">
                                <Icon icon="user"/>
                            </a>
                            <ul>
                                <li><a :href="$iandeUrl('user/edit')">Editar usuário</a></li>
                                <li><a :href="$iandeUrl('user/change-password')">Alterar senha</a></li>
                                <li><a href="javascript:void(0)" role="button" tabindex="0" @click="logout">Logout</a></li>
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
                viewMode: 'educator',
            }
        },
        computed: {
            exhibitions: sync('exhibitions/list'),
            user: sync('users/current'),
            userIsAdmin () {
                if (!this.user) {
                    return false
                }
                return this.user.roles.some(role => role === 'administrator' || role === 'iande_admin')
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