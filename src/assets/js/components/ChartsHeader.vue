<template>
    <div class="iande-charts-header">
        <div class="iande-chart-box">
            <div class="iande-chart-box__title">{{ __('Visitantes', 'iande') }}</div>
            <div class="iande-chart-box__content">
                <span>1.245</span>
                <Icon icon="chart-bar"/>
            </div>
        </div>
        <div class="iande-chart-box">
            <div class="iande-chart-box__title">{{ __('Grupos agendados', 'iande') }}</div>
            <div class="iande-chart-box__content">
                <span>578</span>
                <Icon icon="chart-bar"/>
            </div>
        </div>
        <div class="iande-chart-box">
            <div class="iande-chart-box__title">{{ __('Exposições', 'iande') }}</div>
            <div class="iande-chart-box__content">
                <span>43</span>
                <Icon icon="chart-bar"/>
            </div>
        </div>
        <div class="iande-chart-box">
            <div class="iande-chart-box__title">{{ __('Instituições', 'iande') }}</div>
            <div class="iande-chart-box__content">
                <span>23</span>
                <Icon icon="chart-bar"/>
            </div>
        </div>
        <div class="iande-chart-box">
            <div class="iande-chart-box__title">{{ __('Grupos cancelados', 'iande') }}</div>
            <div class="iande-chart-box__content">
                <span>12</span>
                <Icon icon="chart-bar"/>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'ChartsHeader',
        props: {
            data: { type: Object, required: true },
        },
    }
</script>
