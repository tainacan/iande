<template>
    <div id="iande-visit-date" class="iande-stack stack-lg">
        <h1>{{ __('Sobre a visita', 'iande') }}</h1>
        <div class="iande-form-error" v-if="userIncomplete">
            {{ __('Seu perfil está incompleto. Para completá-lo,', 'iande') }} <a :href="$iandeUrl('user/edit')">{{ __('clique aqui', 'iande') }}</a>
        </div>
        <div>
            <Label for="purpose">{{ __('Qual o objetivo da visita?', 'iande') }}</Label>
            <Select id="purpose" v-model="purpose" :v="$v.purpose" :options="$iande.purposes"/>
        </div>
        <div v-if="isOther(purpose)">
            <Label for="purposeOther">{{ __('Especifique o objetivo da visita', 'iande') }}</Label>
            <Input id="purposeOther" type="text" v-model="purposeOther" :v="$v.purposeOther"/>
        </div>
        <div>
            <Label for="name" :side="__('(opcional)', 'iande')">{{ __('Dê um nome à visita', 'iande') }}</Label>
            <Input id="name" type="text" :placeholder="__('Ex: Nome da instituição', 'iande')" v-model="name" :v="$v.name"/>
        </div>
        <div>
            <Label for="exhibitionId">{{ __('Qual exposição será visitada?', 'iande') }}</Label>
            <Select id="exhibitionId" v-model="exhibitionId" :v="$v.exhibitionId" :options="exhibitionOptions"/>
            <p class="iande-exhibition-description" v-if="exhibition && exhibition.description">
                {{ __(exhibition.description, 'iande') }}
            </p>
        </div>
        <div>
            <Label for="numPeople">{{ __('Quantidade prevista de pessoas', 'iande') }}</Label>
            <Input id="numPeople" type="number" :min="minPeople" :placeholder="sprintf(__('Mínimo de %s pessoas', 'iande'), minPeople)" :disabled="groupsCreated" v-model.number="numPeople" :v="$v.numPeople"/>
            <p class="text-sm">{{ __('Caso seu grupo seja maior do que a capacidade de atendimento do museu, mais grupos serão criados automaticamente', 'iande') }}</p>
        </div>
    </div>
</template>

<script>
    import { integer, minValue, required } from 'vuelidate/lib/validators'
    import { get, sync } from 'vuex-pathify'

    import Input from '@components/Input.vue'
    import Label from '@components/Label.vue'
    import Select from '@components/Select.vue'
    import { isOther, watchForOther } from '@utils'
    import { falsy } from '@utils/validators'

    export default {
        name: 'SelectExhibition',
        components: {
            Input,
            Label,
            Select,
        },
        computed: {
            ...sync('appointments/current@', {
                exhibitionId: 'exhibition_id',
                name: 'name',
                numPeople: 'num_people',
                purpose: 'purpose',
                purposeOther: 'purpose_other',
            }),
            exhibition: get('appointments/exhibition'),
            exhibitionOptions () {
                const entries = this.exhibitions.map(exhibition => [exhibition.title, exhibition.ID])
                return Object.fromEntries(entries)
            },
            exhibitions: get('exhibitions/list'),
            groups: get('appointments/current@groups'),
            groupsCreated () {
                return this.groups.length > 0
            },
            minPeople () {
                return this.exhibition?.min_group_size ? Number(this.exhibition.min_group_size) : 5
            },
            user: get('users/current'),
            userIncomplete () {
                const user = this.user
                if (!user) {
                    return false
                }
                return !(user.first_name && user.last_name && user.user_email && user.phone)
            },
        },
        validations () {
            return {
                exhibitionId: { required },
                name: { },
                numPeople: { integer, minValue: minValue(this.minPeople), required },
                purpose: { required },
                purposeOther: { },
                userIncomplete: { falsy },
            }
        },
        watch: {
            exhibitions: {
                handler () {
                    if (this.exhibitions.length === 1) {
                        this.exhibitionId = this.exhibitions[0].ID
                    }
                },
                immediate: true,
            },
            purpose: watchForOther('purpose', 'purposeOther'),
        },
        methods: {
            isOther,
        }
    }
</script>
