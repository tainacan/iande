<template>
    <div class="iande-stack stack-lg">
        <h1>Sobre a instituição</h1>
        <p>Aqui você deve cadastrar dados referentes a instituição responsável pela visita. Cada instituição cadastrada ficará salva no seu perfil. Deste modo você poderá criar novas visitas com mais agilidade e menos campos para preencher.</p>
        <div>
            <label class="iande-label" for="name">Nome da instituição</label>
            <Input id="name" type="text" placeholder="Nome oficial da instituição" v-model="name" :validations="$v.name"/>
        </div>
    </div>
</template>

<script>
    import Input from './Input.vue'

    export default {
        name: 'CreateInstitution',
        components: {
            Input,
        },
        data () {
            return {
                name: ''
            }
        },
        validations: {
            name: { },
        }
    }
</script>