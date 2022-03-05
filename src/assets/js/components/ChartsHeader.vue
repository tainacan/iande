<template>
    <div class="iande-charts-header">
        <div class="iande-chart-box">
            <div class="iande-chart-box__title">{{ __('Visitantes', 'iande') }}</div>
            <div class="iande-chart-box__content">
                <span>{{ formatNumber(visitors) }}</span>
                <Icon icon="chart-column"/>
            </div>
        </div>
        <div class="iande-chart-box">
            <div class="iande-chart-box__title">{{ __('Grupos agendados', 'iande') }}</div>
            <div class="iande-chart-box__content">
                <span>{{ formatNumber(groupsByStatus.publish.length) }}</span>
                <Icon icon="chart-column"/>
            </div>
        </div>
        <div class="iande-chart-box">
            <div class="iande-chart-box__title">{{ __('Exposições', 'iande') }}</div>
            <div class="iande-chart-box__content">
                <span>{{ formatNumber(exhibitions) }}</span>
                <Icon icon="chart-column"/>
            </div>
        </div>
        <div class="iande-chart-box">
            <div class="iande-chart-box__title">{{ __('Instituições', 'iande') }}</div>
            <div class="iande-chart-box__content">
                <span>{{ formatNumber(institutions) }}</span>
                <Icon icon="chart-column"/>
            </div>
        </div>
        <div class="iande-chart-box">
            <div class="iande-chart-box__title">{{ __('Grupos cancelados', 'iande') }}</div>
            <div class="iande-chart-box__content">
                <span>{{ formatNumber(groupsByStatus.canceled.length) }}</span>
                <Icon icon="chart-column"/>
            </div>
        </div>
    </div>
</template>

<script>
    import { formatNumber } from '@utils'

    export default {
        name: 'ChartsHeader',
        props: {
            data: { type: Object, required: true },
            from: { type: String, required: true },
            groups: { type: Array, required: true },
            to: { type: String, required: true },
        },
        computed: {
            groupsByStatus () {
                const statuses = { publish: [], canceled: [] }

                for (const group of this.groups) {
                    const status = group.post_status

                    if (!statuses[status]) {
                        statuses[status] = [group]
                    } else {
                        statuses[status].push(group)
                    }
                }

                return statuses
            },
            exhibitions () {
                let count = 0

                for (const exhibition of this.data.exhibitions) {
                    if (exhibition.post_status === 'publish' && exhibition.post_date >= this.from && exhibition.post_date <= this.to) {
                        count++
                    }
                }

                return count
            },
            institutions () {
                let count = 0

                for (const institution of this.data.institutions) {
                    if (institution.post_status === 'publish' && institution.post_date >= this.from && institution.post_date <= this.to) {
                        count++
                    }
                }

                return count
            },
            visitors () {
                let count = 0

                for (const group of this.groupsByStatus.publish) {
                    count += this.getNumPeople(group)
                }

                return count
            },
        },
        methods: {
            formatNumber,
            getNumPeople (group) {
                if (group.checkin_num_people === 'no') {
                    return parseInt(group.checkin_num_people_actual || group.num_people) || 0
                } else {
                    return parseInt(group.num_people) || 0
                }
            },
        }
    }
</script>
