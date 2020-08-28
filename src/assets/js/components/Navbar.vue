<template>
    <header class="iande-navbar">
        <div class="iande-container iande-navbar__row">
            <div class="iande-navbar__site-name">
                <img :src="`${pluginDir}/assets/img/iande-logo.png`" alt="Iandé"> + {{ siteName }}
            </div>
            <a v-if="isLoggedIn" class="iande-navbar__toggle" href="javascript:void(0)" role="button" tabindex="0" :aria-label="showMenu ? 'Ocultar menu' : 'Exibir menu'" @click="toggleMenu">
                <Icon icon="bars"/>
            </a>
            <nav :class="showMenu || 'hidden'" v-if="isLoggedIn">
                <ul>
                    <li><a :href="`${iandeUrl}/appointment/list`">Seus agendamentos</a></li>
                    <li><a :href="`${iandeUrl}/institution/list`">Suas instituições</a></li>
                    <li class="iande-navbar__dropdown">
                        <a href="javascript:void(0)" role="button" tabindex="0" aria-label="Usuário">
                            <Icon icon="user"/>
                        </a>
                        <ul>
                            <li><a :href="`${iandeUrl}/user/edit`">Editar usuário</a></li>
                            <li><a href="javascript:void(0)" role="button" tabindex="0" @click="logout">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import { sync } from 'vuex-pathify'

    import { api, constant } from '../utils'

    export default {
        name: 'Navbar',
        components: {
            Icon: FontAwesomeIcon,
        },
        data () {
            return {
                isLoggedIn: false,
                showMenu: false,
            }
        },
        computed: {
            iandeUrl: constant(window.IandeSettings.iandeUrl),
            siteName: constant(window.IandeSettings.siteName),
            pluginDir: constant(`${window.IandeSettings.siteUrl}/wp-content/plugins/iande/`),
            user: sync('user/user'),
        },
        async beforeMount () {
            try {
                if (await api.post('user/is_logged_in')) {
                    const user = await api.post('user/get_logged_in')
                    this.isLoggedIn = true
                    this.user = user
                }
            } catch (err) {
                console.error(err)
            }
        },
        methods: {
            async logout () {
                try {
                    await api.post('user/logout')
                    window.location.assign(`${window.IandeSettings.iandeUrl}/user/login`)
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