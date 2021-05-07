<template>
    <header class="iande-navbar">
        <div class="iande-container iande-navbar__row">
            <div class="iande-navbar__site-name">
                <img :src="`${$iande.siteUrl}/wp-content/plugins/iande/assets/img/iande-logo.png`" alt="Iandé"> + {{ __($iande.siteName, 'iande') }}
            </div>
            <a v-if="isLoggedIn" class="iande-navbar__toggle" href="javascript:void(0)" role="button" tabindex="0" :aria-label="showMenu ? __('Ocultar menu', 'iande') : __('Exibir menu', 'iande')" @click="toggleMenu">
                <Icon icon="bars"/>
            </a>
            <nav :class="showMenu || 'hidden'" v-if="isLoggedIn">
                <ul>
                    <li class="iande-navbar__dropdown" v-if="userIsAdmin">
                        <a href="javascript:void(0)" role="button" tabindex="0">
                            <span>{{ __('Agendamento', 'iande') }}</span>
                            <Icon icon="caret-down"/>
                        </a>
                        <ul>
                            <li><a :href="$iandeUrl('group/list')">{{ __('Calendário geral', 'iande') }}</a></li>
                            <li><a :href="$iandeUrl('group/agenda')">{{ __('Minha agenda', 'iande') }}</a></li>
                        </ul>
                    </li>
                    <li v-else><a :href="$iandeUrl('appointment/list')">{{ __('Agendamentos', 'iande') }}</a></li>
                    <li><a :href="$iandeUrl('institution/list')">{{ __('Instituições', 'iande') }}</a></li>
                    <li class="iande-navbar__dropdown">
                        <a href="javascript:void(0)" role="button" tabindex="0" :aria-label="__('Usuário', 'iande')">
                            <Icon icon="user"/>
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
</template>

<script>
    import { sync } from 'vuex-pathify'

    import { api } from '@utils'

    export default {
        name: 'Navbar',
        data () {
            return {
                isLoggedIn: false,
                showMenu: false,
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
        async beforeMount () {
            try {
                if (await api.post('user/is_logged_in')) {
                    const user = await api.post('user/get_logged_in')
                    this.isLoggedIn = true
                    this.user = user
                }
                const exhibitions = await api.post('exhibition/list')
                this.exhibitions = exhibitions
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
            }
        }
    }
</script>