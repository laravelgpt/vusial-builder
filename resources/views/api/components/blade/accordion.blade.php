<div class="space-y-2">
    @foreach($component['items'] as $item)
        <div class="border rounded-lg">
            <button class="w-full px-4 py-2 text-left flex justify-between items-center hover:bg-gray-50"
                    onclick="toggleAccordion('{{ $item['id'] }}')">
                <span>{{ $item['title'] }}</span>
                <svg class="w-5 h-5 transform transition-transform" id="icon-{{ $item['id'] }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>
            <div class="hidden px-4 py-2" id="content-{{ $item['id'] }}">
                {!! $item['content'] !!}
            </div>
        </div>
    @endforeach
</div>

<script>
function toggleAccordion(id) {
    const content = document.getElementById(`content-${id}`);
    const icon = document.getElementById(`icon-${id}`);
    content.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
}
</script> 