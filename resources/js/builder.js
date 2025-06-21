class VisualBuilder {
    constructor(options) {
        this.canvas = options.canvas;
        this.propertiesPanel = options.propertiesPanel;
        this.components = new Map();
        this.selectedComponent = null;
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // Canvas click event for component selection
        this.canvas.addEventListener('click', (e) => {
            const component = e.target.closest('.builder-component');
            if (component) {
                this.selectComponent(component);
            } else {
                this.deselectComponent();
            }
        });

        // Keyboard events for component manipulation
        document.addEventListener('keydown', (e) => {
            if (this.selectedComponent) {
                switch (e.key) {
                    case 'Delete':
                        this.deleteComponent(this.selectedComponent);
                        break;
                    case 'Escape':
                        this.deselectComponent();
                        break;
                }
            }
        });
    }

    addComponent(type, position) {
        const component = this.createComponent(type, position);
        this.canvas.appendChild(component);
        this.components.set(component.id, component);
        this.selectComponent(component);
    }

    createComponent(type, position) {
        const component = document.createElement('div');
        component.id = `component-${Date.now()}`;
        component.className = 'builder-component';
        component.style.position = 'absolute';
        component.style.left = `${position.x}px`;
        component.style.top = `${position.y}px`;
        component.dataset.type = type;

        // Add component content based on type
        component.innerHTML = this.getComponentTemplate(type);

        // Make component draggable
        this.makeDraggable(component);

        return component;
    }

    getComponentTemplate(type) {
        const templates = {
            text: '<div class="p-2 border border-gray-300 rounded">Text Component</div>',
            image: '<div class="p-2 border border-gray-300 rounded"><img src="placeholder.jpg" alt="Image" class="w-32 h-32 object-cover"></div>',
            button: '<button class="px-4 py-2 bg-blue-500 text-white rounded">Button</button>',
            input: '<input type="text" class="border border-gray-300 rounded px-2 py-1" placeholder="Input">',
            textarea: '<textarea class="border border-gray-300 rounded px-2 py-1" placeholder="Textarea"></textarea>',
            select: '<select class="border border-gray-300 rounded px-2 py-1"><option>Option 1</option><option>Option 2</option></select>',
            checkbox: '<label class="flex items-center"><input type="checkbox" class="mr-2"> Checkbox</label>',
            radio: '<label class="flex items-center"><input type="radio" class="mr-2"> Radio</label>',
            container: '<div class="p-4 border border-gray-300 rounded min-h-[100px]">Container</div>',
            row: '<div class="flex border border-gray-300 rounded p-2">Row</div>',
            column: '<div class="border border-gray-300 rounded p-2">Column</div>',
            card: '<div class="border border-gray-300 rounded p-4 shadow">Card</div>',
            modal: '<div class="border border-gray-300 rounded p-4 bg-white">Modal</div>',
            tabs: '<div class="border border-gray-300 rounded"><div class="flex border-b"><div class="px-4 py-2 border-r">Tab 1</div><div class="px-4 py-2">Tab 2</div></div><div class="p-4">Content</div></div>',
            accordion: '<div class="border border-gray-300 rounded"><div class="p-2 border-b">Accordion Item 1</div><div class="p-2">Accordion Item 2</div></div>',
            navbar: '<nav class="bg-gray-800 text-white p-4">Navbar</nav>',
            sidebar: '<aside class="w-48 bg-gray-100 p-4">Sidebar</aside>',
            breadcrumb: '<nav class="flex p-2"><span class="text-gray-500">Home</span><span class="mx-2">/</span><span>Page</span></nav>',
            pagination: '<div class="flex space-x-2"><button class="px-3 py-1 border rounded">1</button><button class="px-3 py-1 border rounded">2</button><button class="px-3 py-1 border rounded">3</button></div>',
            menu: '<ul class="border border-gray-300 rounded"><li class="p-2 border-b">Menu Item 1</li><li class="p-2">Menu Item 2</li></ul>',
            table: '<table class="border border-gray-300"><thead><tr><th class="border p-2">Header 1</th><th class="border p-2">Header 2</th></tr></thead><tbody><tr><td class="border p-2">Data 1</td><td class="border p-2">Data 2</td></tr></tbody></table>',
            list: '<ul class="border border-gray-300 rounded"><li class="p-2 border-b">List Item 1</li><li class="p-2">List Item 2</li></ul>',
            grid: '<div class="grid grid-cols-2 gap-4 p-4 border border-gray-300 rounded"><div>Grid Item 1</div><div>Grid Item 2</div></div>',
            chart: '<div class="border border-gray-300 rounded p-4">Chart Placeholder</div>',
            calendar: '<div class="border border-gray-300 rounded p-4">Calendar Placeholder</div>',
            form: '<form class="space-y-4 p-4 border border-gray-300 rounded"><input type="text" class="w-full border rounded px-2 py-1" placeholder="Form Field"><button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Submit</button></form>',
            fieldset: '<fieldset class="border border-gray-300 rounded p-4"><legend class="px-2">Fieldset</legend><div class="space-y-2"><input type="text" class="w-full border rounded px-2 py-1"></div></fieldset>',
            'file-upload': '<div class="border border-gray-300 rounded p-4"><input type="file" class="w-full"></div>',
            'date-picker': '<input type="date" class="border border-gray-300 rounded px-2 py-1">',
            'time-picker': '<input type="time" class="border border-gray-300 rounded px-2 py-1">',
            'color-picker': '<input type="color" class="border border-gray-300 rounded">',
        };

        return templates[type] || '<div class="p-2 border border-gray-300 rounded">Unknown Component</div>';
    }

    makeDraggable(element) {
        let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
        element.onmousedown = dragMouseDown;

        function dragMouseDown(e) {
            e.preventDefault();
            pos3 = e.clientX;
            pos4 = e.clientY;
            document.onmouseup = closeDragElement;
            document.onmousemove = elementDrag;
        }

        function elementDrag(e) {
            e.preventDefault();
            pos1 = pos3 - e.clientX;
            pos2 = pos4 - e.clientY;
            pos3 = e.clientX;
            pos4 = e.clientY;
            element.style.top = (element.offsetTop - pos2) + "px";
            element.style.left = (element.offsetLeft - pos1) + "px";
        }

        function closeDragElement() {
            document.onmouseup = null;
            document.onmousemove = null;
        }
    }

    selectComponent(component) {
        this.deselectComponent();
        this.selectedComponent = component;
        component.classList.add('selected');
        this.updatePropertiesPanel(component);
    }

    deselectComponent() {
        if (this.selectedComponent) {
            this.selectedComponent.classList.remove('selected');
            this.selectedComponent = null;
            this.clearPropertiesPanel();
        }
    }

    deleteComponent(component) {
        if (component && this.components.has(component.id)) {
            this.components.delete(component.id);
            component.remove();
            this.deselectComponent();
        }
    }

    updatePropertiesPanel(component) {
        const type = component.dataset.type;
        const properties = this.getComponentProperties(type);
        
        let html = '<div class="space-y-4">';
        for (const [key, value] of Object.entries(properties)) {
            html += this.createPropertyInput(key, value, component);
        }
        html += '</div>';

        this.propertiesPanel.innerHTML = html;
        this.initializePropertyListeners();
    }

    getComponentProperties(type) {
        const commonProperties = {
            id: { type: 'text', label: 'ID' },
            class: { type: 'text', label: 'Class' },
            style: { type: 'text', label: 'Style' },
        };

        const specificProperties = {
            text: {
                content: { type: 'textarea', label: 'Content' },
                fontSize: { type: 'number', label: 'Font Size' },
                color: { type: 'color', label: 'Color' },
            },
            image: {
                src: { type: 'text', label: 'Source' },
                alt: { type: 'text', label: 'Alt Text' },
                width: { type: 'number', label: 'Width' },
                height: { type: 'number', label: 'Height' },
            },
            button: {
                text: { type: 'text', label: 'Text' },
                type: { type: 'select', label: 'Type', options: ['button', 'submit', 'reset'] },
                variant: { type: 'select', label: 'Variant', options: ['primary', 'secondary', 'danger'] },
            },
            // Add more component-specific properties as needed
        };

        return { ...commonProperties, ...(specificProperties[type] || {}) };
    }

    createPropertyInput(key, config, component) {
        const value = component.dataset[key] || '';
        let input = '';

        switch (config.type) {
            case 'text':
            case 'number':
            case 'color':
                input = `<input type="${config.type}" class="w-full border rounded px-2 py-1" value="${value}" data-property="${key}">`;
                break;
            case 'textarea':
                input = `<textarea class="w-full border rounded px-2 py-1" data-property="${key}">${value}</textarea>`;
                break;
            case 'select':
                const options = config.options.map(opt => `<option value="${opt}" ${value === opt ? 'selected' : ''}>${opt}</option>`).join('');
                input = `<select class="w-full border rounded px-2 py-1" data-property="${key}">${options}</select>`;
                break;
        }

        return `
            <div class="property-group">
                <label class="block text-sm font-medium mb-1">${config.label}</label>
                ${input}
            </div>
        `;
    }

    initializePropertyListeners() {
        this.propertiesPanel.querySelectorAll('[data-property]').forEach(input => {
            input.addEventListener('change', (e) => {
                if (this.selectedComponent) {
                    const property = e.target.dataset.property;
                    const value = e.target.value;
                    this.updateComponentProperty(property, value);
                }
            });
        });
    }

    updateComponentProperty(property, value) {
        if (this.selectedComponent) {
            this.selectedComponent.dataset[property] = value;
            // Update the component's appearance based on the new property value
            this.updateComponentAppearance(property, value);
        }
    }

    updateComponentAppearance(property, value) {
        const component = this.selectedComponent;
        switch (property) {
            case 'class':
                component.className = `builder-component ${value}`;
                break;
            case 'style':
                component.style.cssText = value;
                break;
            case 'content':
                component.querySelector('.content')?.textContent = value;
                break;
            // Add more property-specific updates as needed
        }
    }

    clearPropertiesPanel() {
        this.propertiesPanel.innerHTML = '<p class="text-gray-500">Select a component to edit its properties</p>';
    }

    getComponentData() {
        const data = [];
        this.components.forEach(component => {
            data.push({
                id: component.id,
                type: component.dataset.type,
                position: {
                    x: parseInt(component.style.left),
                    y: parseInt(component.style.top),
                },
                properties: Object.fromEntries(
                    Object.entries(component.dataset).map(([key, value]) => [key, value])
                ),
            });
        });
        return data;
    }

    loadComponentData(data) {
        data.forEach(item => {
            const component = this.createComponent(item.type, item.position);
            Object.entries(item.properties).forEach(([key, value]) => {
                component.dataset[key] = value;
            });
            this.canvas.appendChild(component);
            this.components.set(component.id, component);
        });
    }
}

// Export the builder class
export default VisualBuilder; 