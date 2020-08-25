<template>
    <section class="iande-appointment">
        <div class="iande-appointment__summary" :class="showDetails || 'collapsed'">
            <div>
                <div class="iande-appointment__date">
                    <div class="iande-appointment__day">{{ day }}</div>
                    <div class="iande-appointment__month">{{ month }}</div>
                </div>
                <div class="iande-appointment__summary-main">
                    <h2>{{ name }}</h2>
                    <div class="iande-appointment__info">
                        <Icon icon="map-marker-alt"/>
                        <span>{{ siteName }} / #{{ appointment.ID }}</span>
                    </div>
                    <div class="iande-appointment__info">
                        <Icon :icon="['far', 'clock']"/>
                        <span>{{ hour }}</span>
                    </div>
                </div>
            </div>
            <div>
                <StepsIndicator inline :step="Number(appointment.step)" :status="appointment.post_status" :reason="appointment.reason_cancel"/>
                <div class="iande-appointment__toggle" :aria-label="showDetails ? 'Ocultar detalhes' : 'Exibir detalhes'" role="button" tabindex="0" @click="toggleDetails" @keypress.enter="toggleDetails">
                    <Icon :icon="showDetails ? 'minus-circle' : 'plus-circle'"/>
                </div>
            </div>
        </div>
        <div class="iande-appointment__details" v-if="showDetails">
            <div class="iande-appointment__boxes">
                <div class="iande-appointment__box">
                    <div class="iande-appointment__box-title">
                        <h3><Icon :icon="['far', 'calendar']"/>Evento</h3>
                        <div class="iande-appointment__edit" v-if="editable">
                            <a class="iande-appointment__edit-link" :href="gotoScreen(1)">Editar</a>
                            <Icon icon="pencil-alt"/>
                        </div>
                    </div>
                    <div>{{ appointment.purpose }}</div>
                    <div>{{ date }}</div>
                    <div>{{ hour }}</div>
                </div>

                <div class="iande-appointment__box">
                    <div class="iande-appointment__box-title">
                        <h3><Icon icon="user"/>Responsável pela visita</h3>
                        <div class="iande-appointment__edit" v-if="editable">
                            <a class="iande-appointment__edit-link" :href="gotoScreen(2)">Editar</a>
                            <Icon icon="pencil-alt"/>
                        </div>
                    </div>
                    <div>
                        <div>{{ appointment.responsible_first_name }} {{ appointment.responsible_last_name }}</div>
                        <div>{{ appointment.responsible_role }}</div>
                    </div>
                    <div>
                        <div>{{ appointment.responsible_email }}</div>
                        <div>{{ formatPhone(appointment.responsible_phone) }}</div>
                    </div>
                </div>

                <div class="iande-appointment__box" v-if="institution">
                    <div class="iande-appointment__box-title">
                        <h3><Icon icon="university"/>Instituição</h3>
                        <div class="iande-appointment__edit" v-if="editable">
                            <a class="iande-appointment__edit-link" :href="gotoScreen(3)">Editar</a>
                            <Icon icon="pencil-alt"/>
                        </div>
                    </div>
                    <div>
                        <div>{{ institution.name }}</div>
                        <div>{{ formatPhone(institution.phone) }}</div>
                    </div>
                    <div>
                        <div>
                            {{ institution.address }}, {{ institution.address_number }}
                            <template v-if="institution.complement">{{ institution.complement }}</template>
                            - {{ institution.district }}
                        </div>
                        <div>{{ city }} - {{ institution.state }}</div>
                        <div>CEP {{ formatCep(institution.zip_code) }}</div>
                    </div>
                </div>

                <div class="iande-appointment__box" v-for="group in groups" :key="group.id">
                    <div class="iande-appointment__box-title">
                        <h3><Icon icon="users"/>{{ groupName(group) }}</h3>
                        <div class="iande-appointment__edit" v-if="editable">
                            <a class="iande-appointment__edit-link" :href="gotoScreen(5)">Editar</a>
                            <Icon icon="pencil-alt"/>
                        </div>
                    </div>
                    <div>Previsão de {{ group.num_people }} visitantes</div>
                    <div>{{ group.num_responsible == 1 ? '1 responsável' : `${group.num_responsible} responsáveis` }}</div>
                    <div>{{ group.scholarity }}</div>
                    <div>Pessoas com deficiência: {{ groupDisabilities(group.disabilities) }}</div>
                    <div>Idiomas: {{ groupLanguages(group.languages) }}</div>
                </div>

                <div class="iande-appointment__box" v-if="appointment.step > 2">
                    <div class="iande-appointment__box-title">
                        <h3><Icon :icon="['far', 'address-card']"/>Dados adicionais</h3>
                        <div class="iande-appointment__edit" v-if="editable">
                            <a class="iande-appointment__edit-link" :href="gotoScreen(6)">Editar</a>
                            <Icon icon="pencil-alt"/>
                        </div>
                    </div>
                    <div>Você já visitou o museu antes: {{ appointment.has_visited_previously === 'yes' ? 'Sim' : 'Não' }}</div>
                    <div>Preparação: {{ appointment.has_prepared_visity === 'yes' ? 'Sim' : 'Não' }}</div>
                    <div v-if="appointment.additional_comment">Comentários: {{ appointment.additional_comments }}</div>
                </div>
            </div>
            <div class="iande-appointment__buttons">
                <button class="iande-button solid" @click="cancelAppointment" v-if="editable">
                    Cancelar reserva
                    <Icon icon="times"/>
                </button>
                <a class="iande-button primary" :href="`${iandeUrl}/appointment/confirm?ID=${appointment.ID}`" v-if="editable && appointment.step == 2">
                    Confirmar reserva
                    <Icon icon="check"/>
                </a>
                <button class="iande-button primary" v-else-if="editable && appointment.step == 3">
                    Finalizar reserva
                    <Icon icon="check"/>
                </button>
                <button class="iande-button disabled" v-if="appointment.post_status === 'pending'" disabled>
                    Aguardando confirmação
                    <Icon icon="spinner" spin/>
                </button>
            </div>
        </div>
        <Modal ref="modal">
            <div class="iande-stack">
                <h1>Agendamento enviado com sucesso!</h1>
                <p>Os dados do seu agendamento foram enviados para o museu. Assim que a sua visita for confirmada, você receberá um email com todos os detalhes.</p>
                <button class="iande-button solid" @click="closeModal">Voltar aos agendamentos</button>
            </div>
        </Modal>
    </section>
</template>

<script>
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import { get } from 'vuex-pathify'

    import Modal from './Modal'
    import StepsIndicator from './StepsIndicator'
    import { api, constant, formatCep, formatPhone } from '../utils'
    import '../utils/ibge'

    const months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']

    export default {
        name: 'AppointmentDetails',
        components: {
            Icon: FontAwesomeIcon,
            Modal,
            StepsIndicator,
        },
        props: {
            appointment: { type: Object, required: true },
            n: { type: Number, default: 1 },
        },
        data () {
            return {
                showDetails: false,
            }
        },
        computed: {
            city () {
                const cityId = this.institution.city
                return Object.entries(window.municipios).find(([key]) => key === cityId)[1]
            },
            date () {
                const parts = this.appointment.date.split('-')
                return parts.reverse().join('/')
            },
            day () {
                const parts = this.appointment.date.split('-')
                return parts[2]
            },
            editable () {
                return this.appointment.status === 'draft'
            },
            groups () {
                if (this.appointment.group_list) {
                    return this.appointment.group_list.groups || []
                }
                return []
            },
            hour () {
                const parts = this.appointment.hour.split(':')
                return parts.join('h')
            },
            iandeUrl: constant(window.IandeSettings.iandeUrl),
            institution () {
                return this.institutions.find(institution => institution.ID == this.appointment.institution_id)
            },
            institutions: get('institutions/list'),
            month() {
                const parts = this.appointment.date.split('-')
                return months[parseInt(parts[1]) - 1]
            },
            name () {
                if (this.appointment.name) {
                    return this.appointment.name
                } else {
                    return `Agendamento ${this.n}`
                }
            },
            siteName: constant(window.IandeSettings.siteName),
        },
        methods: {
            async cancelAppointment () {
                try {
                    await api.post('appointment/cancel', { ID: this.appointment.ID })
                    window.location.reload()
                } catch (err) {
                    console.error(err)
                }
            },
            closeModal () {
                this.$refs.modal.close()
                window.location.reload()
            },
            formatCep,
            formatPhone,
            gotoScreen (screen) {
                return `${window.IandeSettings.iandeUrl}/appointment/edit?ID=${this.appointment.ID}&screen=${screen}`
            },
            groupDisabilities (disabilities) {
                if (disabilities.length === 0) {
                    return 'não'
                } else {
                    return disabilities
                        .map(disability => `${disability.type} (${disability.count})`)
                        .join(', ')
                }
            },
            groupLanguages (languages) {
                return ['Português', ...languages].join(', ')
            },
            groupName (group) {
                if (group.name) {
                    return `Grupo: ${group.name}`
                } else {
                    return `Grupo ${group.id}`
                }
            },
            async sendConfirmation () {
                this.$refs.modal.open()
                //await api.post('appointment/set_status', { ID: this.appointment.ID, post_status: 'pending' })
            },
            toggleDetails () {
                this.showDetails = !this.showDetails
            },
        }
    }
</script>