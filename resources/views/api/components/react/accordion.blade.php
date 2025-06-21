<div id="accordion-{{ $component['id'] }}" class="space-y-2"></div>

@push('scripts')
<script>
const AccordionItem = ({ item, isOpen, onToggle }) => (
    <div className="border rounded-lg">
        <button
            onClick={() => onToggle(item.id)}
            className="w-full px-4 py-2 text-left flex justify-between items-center hover:bg-gray-50"
        >
            <span>{item.title}</span>
            <svg
                className={`w-5 h-5 transform transition-transform ${isOpen ? 'rotate-180' : ''}`}
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                strokeWidth="1.5"
                stroke="currentColor"
            >
                <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                />
            </svg>
        </button>
        {isOpen && (
            <div className="px-4 py-2" dangerouslySetInnerHTML={{ __html: item.content }} />
        )}
    </div>
);

const Accordion = ({ items }) => {
    const [openItems, setOpenItems] = React.useState({});

    const toggleAccordion = (id) => {
        setOpenItems(prev => ({
            ...prev,
            [id]: !prev[id]
        }));
    };

    return (
        <div className="space-y-2">
            {items.map(item => (
                <AccordionItem
                    key={item.id}
                    item={item}
                    isOpen={openItems[item.id] || false}
                    onToggle={toggleAccordion}
                />
            ))}
        </div>
    );
};

const items = @json($component['items']);

ReactDOM.render(
    <Accordion items={items} />,
    document.getElementById('accordion-{{ $component['id'] }}')
);
</script>
@endpush 