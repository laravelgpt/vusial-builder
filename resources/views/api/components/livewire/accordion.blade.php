<div class="space-y-2">
    @foreach($component['items'] as $item)
        <div class="border rounded-lg">
            <button wire:click="toggleAccordion('{{ $item['id'] }}')"
                    class="w-full px-4 py-2 text-left flex justify-between items-center hover:bg-gray-50">
                <span>{{ $item['title'] }}</span>
                <svg class="w-5 h-5 transform transition-transform {{ $openAccordions[$item['id']] ?? false ? 'rotate-180' : '' }}" 
                     xmlns="http://www.w3.org/2000/svg" 
                     fill="none" 
                     viewBox="0 0 24 24" 
                     stroke-width="1.5" 
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>
            <div class="px-4 py-2 {{ $openAccordions[$item['id']] ?? false ? '' : 'hidden' }}">
                {!! $item['content'] !!}
            </div>
        </div>
    @endforeach
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('accordionToggled', (id) => {
            const content = document.querySelector(`[data-accordion-content="${id}"]`);
            const icon = document.querySelector(`[data-accordion-icon="${id}"]`);
            content.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        });
    });
</script>
@endpush 