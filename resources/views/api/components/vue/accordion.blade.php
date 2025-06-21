<div id="accordion-{{ $component['id'] }}" class="space-y-2">
    <accordion-component :items='@json($component['items'])'></accordion-component>
</div>

@push('scripts')
<script>
Vue.component('accordion-component', {
    props: {
        items: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            openItems: {}
        }
    },
    methods: {
        toggleAccordion(id) {
            this.$set(this.openItems, id, !this.openItems[id]);
        }
    },
    template: `
        <div class="space-y-2">
            <div v-for="item in items" :key="item.id" class="border rounded-lg">
                <button @click="toggleAccordion(item.id)"
                        class="w-full px-4 py-2 text-left flex justify-between items-center hover:bg-gray-50">
                    <span v-text="item.title"></span>
                    <svg class="w-5 h-5 transform transition-transform"
                         :class="{ 'rotate-180': openItems[item.id] }"
                         xmlns="http://www.w3.org/2000/svg"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke-width="1.5"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
                <div class="px-4 py-2" v-show="openItems[item.id]" v-html="item.content"></div>
            </div>
        </div>
    `
});

new Vue({
    el: '#accordion-{{ $component['id'] }}'
});
</script>
@endpush 