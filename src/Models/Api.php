<?php

namespace LaravelBuilder\VisualBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Api extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'model',
        'type',
        'fields',
        'relationships',
        'validation_rules',
        'endpoints',
        'documentation',
        'is_active',
        'is_public',
        'auth_type',
        'auth_providers',
        'auth_config',
        'rate_limit',
        'middleware',
        'ai_suggestions',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'fields' => 'array',
        'relationships' => 'array',
        'validation_rules' => 'array',
        'endpoints' => 'array',
        'documentation' => 'array',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'rate_limit' => 'array',
        'middleware' => 'array',
        'ai_suggestions' => 'array',
        'auth_providers' => 'array',
        'auth_config' => 'array',
    ];

    // API Types
    const TYPE_REST = 'rest';
    const TYPE_CURL = 'curl';
    const TYPE_GRAPHQL = 'graphql';
    const TYPE_SOAP = 'soap';
    const TYPE_GRPC = 'grpc';
    const TYPE_JSON_API = 'json_api';
    const TYPE_HYPERMEDIA = 'hypermedia';

    // Auth Types
    const AUTH_NONE = 'none';
    const AUTH_TOKEN = 'token';
    const AUTH_OAUTH = 'oauth';
    const AUTH_BASIC = 'basic';
    const AUTH_JWT = 'jwt';
    const AUTH_SOCIAL = 'social';

    // OAuth Providers
    const OAUTH_GOOGLE = 'google';
    const OAUTH_FACEBOOK = 'facebook';
    const OAUTH_GITHUB = 'github';
    const OAUTH_TWITTER = 'twitter';
    const OAUTH_LINKEDIN = 'linkedin';
    const OAUTH_GITLAB = 'gitlab';
    const OAUTH_BITBUCKET = 'bitbucket';

    // Builder Types
    const BUILDER_FORM = 'form';
    const BUILDER_TABLE = 'table';
    const BUILDER_UI = 'ui';
    const BUILDER_PAGE = 'page';
    const BUILDER_MENU = 'menu';
    const BUILDER_ROUTE = 'route';
    const BUILDER_MODEL = 'model';
    const BUILDER_CONTROLLER = 'controller';
    const BUILDER_API = 'api';
    const BUILDER_AUTH = 'auth';
    const BUILDER_COMPONENT = 'component';
    const BUILDER_THEME = 'theme';

    // Form Field Types
    const FIELD_TEXT = 'text';
    const FIELD_EMAIL = 'email';
    const FIELD_PASSWORD = 'password';
    const FIELD_OTP = 'otp';
    const FIELD_SELECT = 'select';
    const FIELD_MULTI_SELECT = 'multi_select';
    const FIELD_COMBOBOX = 'combobox';
    const FIELD_CHECKBOX = 'checkbox';
    const FIELD_RADIO = 'radio';
    const FIELD_SWITCH = 'switch';
    const FIELD_TOGGLE = 'toggle';
    const FIELD_DATE = 'date';
    const FIELD_TIME = 'time';
    const FIELD_FILE = 'file';
    const FIELD_IMAGE = 'image';
    const FIELD_SLIDER = 'slider';
    const FIELD_RATING = 'rating';
    const FIELD_COLOR = 'color';

    // Table Features
    const TABLE_SORT = 'sort';
    const TABLE_FILTER = 'filter';
    const TABLE_SEARCH = 'search';
    const TABLE_PAGINATION = 'pagination';
    const TABLE_EXPORT = 'export';
    const TABLE_BULK_ACTIONS = 'bulk_actions';
    const TABLE_ROW_SELECTION = 'row_selection';
    const TABLE_INLINE_EDIT = 'inline_edit';

    // UI Components
    const UI_ACCORDION = 'accordion';
    const UI_ALERT = 'alert';
    const UI_ALERT_DIALOG = 'alert_dialog';
    const UI_ASPECT_RATIO = 'aspect_ratio';
    const UI_AVATAR = 'avatar';
    const UI_BADGE = 'badge';
    const UI_BREADCRUMB = 'breadcrumb';
    const UI_BUTTON = 'button';
    const UI_CALENDAR = 'calendar';
    const UI_CARD = 'card';
    const UI_CAROUSEL = 'carousel';
    const UI_CHART = 'chart';
    const UI_CHECKBOX = 'checkbox';
    const UI_COLLAPSIBLE = 'collapsible';
    const UI_COMBOBOX = 'combobox';
    const UI_COMMAND_MENU = 'command_menu';
    const UI_CONTEXT_MENU = 'context_menu';
    const UI_DATA_TABLE = 'data_table';
    const UI_DATE_PICKER = 'date_picker';
    const UI_DIALOG = 'dialog';
    const UI_DRAWER = 'drawer';
    const UI_DROPDOWN_MENU = 'dropdown_menu';
    const UI_FORM_LAYOUT = 'form_layout';
    const UI_HOVER_CARD = 'hover_card';
    const UI_INPUT = 'input';
    const UI_INPUT_OTP = 'input_otp';
    const UI_LABEL = 'label';
    const UI_MENUBAR = 'menubar';
    const UI_NAVIGATION_MENU = 'navigation_menu';
    const UI_PAGINATION = 'pagination';
    const UI_POPOVER = 'popover';
    const UI_PROGRESS_BAR = 'progress_bar';
    const UI_RADIO_GROUP = 'radio_group';
    const UI_RESIZABLE_PANELS = 'resizable_panels';
    const UI_SCROLL_AREA = 'scroll_area';
    const UI_SELECT = 'select';
    const UI_SEPARATOR = 'separator';
    const UI_SHEET = 'sheet';
    const UI_SIDEBAR = 'sidebar';
    const UI_SKELETON_LOADER = 'skeleton_loader';
    const UI_SLIDER = 'slider';
    const UI_SONNER = 'sonner';
    const UI_SWITCH = 'switch';
    const UI_TABS = 'tabs';
    const UI_TEXTAREA = 'textarea';
    const UI_TOAST = 'toast';
    const UI_TOGGLE = 'toggle';
    const UI_TOGGLE_GROUP = 'toggle_group';
    const UI_TOOLTIP = 'tooltip';
    const UI_TYPOGRAPHY = 'typography';

    // Available API Types
    public static function getAvailableTypes(): array
    {
        return [
            self::TYPE_REST => [
                'name' => 'REST API',
                'description' => 'Representational State Transfer API',
                'features' => [
                    'CRUD operations',
                    'Resource-based endpoints',
                    'HTTP methods (GET, POST, PUT, DELETE)',
                    'JSON/XML responses',
                    'HATEOAS support',
                    'Versioning',
                ],
            ],
            self::TYPE_CURL => [
                'name' => 'cURL API',
                'description' => 'Command-line tool for transferring data',
                'features' => [
                    'Direct HTTP requests',
                    'File uploads',
                    'Custom headers',
                    'Authentication support',
                    'SSL/TLS support',
                    'Proxy support',
                ],
            ],
            self::TYPE_GRAPHQL => [
                'name' => 'GraphQL API',
                'description' => 'Query language for APIs',
                'features' => [
                    'Single endpoint',
                    'Client-specified queries',
                    'Real-time subscriptions',
                    'Schema introspection',
                    'Batched queries',
                    'Persisted queries',
                ],
            ],
            self::TYPE_SOAP => [
                'name' => 'SOAP API',
                'description' => 'Simple Object Access Protocol',
                'features' => [
                    'XML-based messaging',
                    'WSDL definitions',
                    'Built-in security',
                    'Stateful operations',
                    'WS-Security',
                    'WS-Addressing',
                ],
            ],
            self::TYPE_GRPC => [
                'name' => 'gRPC API',
                'description' => 'Google Remote Procedure Call',
                'features' => [
                    'High performance',
                    'Protocol Buffers',
                    'Bidirectional streaming',
                    'Cross-platform support',
                    'Load balancing',
                    'Health checking',
                ],
            ],
            self::TYPE_JSON_API => [
                'name' => 'JSON:API',
                'description' => 'JSON API Specification',
                'features' => [
                    'Standardized JSON responses',
                    'Sparse fieldsets',
                    'Compound documents',
                    'Filtering and sorting',
                    'Pagination',
                    'Error handling',
                ],
            ],
            self::TYPE_HYPERMEDIA => [
                'name' => 'Hypermedia API',
                'description' => 'Hypermedia-driven API',
                'features' => [
                    'HATEOAS compliance',
                    'Resource linking',
                    'State transitions',
                    'Self-documenting',
                    'Versioning support',
                    'Content negotiation',
                ],
            ],
        ];
    }

    // Available Auth Types
    public static function getAvailableAuthTypes(): array
    {
        return [
            self::AUTH_NONE => [
                'name' => 'No Authentication',
                'description' => 'Public API without authentication',
                'features' => [
                    'No authentication required',
                    'Public access',
                    'No user tracking',
                ],
            ],
            self::AUTH_TOKEN => [
                'name' => 'Token Authentication',
                'description' => 'API token-based authentication',
                'features' => [
                    'Laravel Sanctum support',
                    'Token generation',
                    'Token revocation',
                    'Expiration control',
                ],
            ],
            self::AUTH_OAUTH => [
                'name' => 'OAuth Authentication',
                'description' => 'OAuth 2.0 authentication',
                'features' => [
                    'OAuth 2.0 protocol',
                    'Access tokens',
                    'Refresh tokens',
                    'Token expiration',
                ],
            ],
            self::AUTH_BASIC => [
                'name' => 'Basic Authentication',
                'description' => 'HTTP Basic authentication',
                'features' => [
                    'Username/password',
                    'Base64 encoding',
                    'HTTPS required',
                ],
            ],
            self::AUTH_JWT => [
                'name' => 'JWT Authentication',
                'description' => 'JSON Web Token authentication',
                'features' => [
                    'Stateless authentication',
                    'Token signing',
                    'Token verification',
                    'Claims support',
                ],
            ],
            self::AUTH_SOCIAL => [
                'name' => 'Social Authentication',
                'description' => 'Social media login integration',
                'features' => [
                    'Multiple providers',
                    'User profile sync',
                    'Avatar import',
                    'Email verification',
                ],
            ],
        ];
    }

    // Available OAuth Providers
    public static function getAvailableOAuthProviders(): array
    {
        return [
            self::OAUTH_GOOGLE => [
                'name' => 'Google',
                'description' => 'Google OAuth 2.0',
                'scopes' => [
                    'profile',
                    'email',
                    'openid',
                ],
                'icon' => 'fab fa-google',
            ],
            self::OAUTH_FACEBOOK => [
                'name' => 'Facebook',
                'description' => 'Facebook OAuth 2.0',
                'scopes' => [
                    'email',
                    'public_profile',
                ],
                'icon' => 'fab fa-facebook',
            ],
            self::OAUTH_GITHUB => [
                'name' => 'GitHub',
                'description' => 'GitHub OAuth 2.0',
                'scopes' => [
                    'user:email',
                    'read:user',
                ],
                'icon' => 'fab fa-github',
            ],
            self::OAUTH_TWITTER => [
                'name' => 'Twitter',
                'description' => 'Twitter OAuth 2.0',
                'scopes' => [
                    'tweet.read',
                    'users.read',
                ],
                'icon' => 'fab fa-twitter',
            ],
            self::OAUTH_LINKEDIN => [
                'name' => 'LinkedIn',
                'description' => 'LinkedIn OAuth 2.0',
                'scopes' => [
                    'r_liteprofile',
                    'r_emailaddress',
                ],
                'icon' => 'fab fa-linkedin',
            ],
            self::OAUTH_GITLAB => [
                'name' => 'GitLab',
                'description' => 'GitLab OAuth 2.0',
                'scopes' => [
                    'read_user',
                    'profile',
                ],
                'icon' => 'fab fa-gitlab',
            ],
            self::OAUTH_BITBUCKET => [
                'name' => 'Bitbucket',
                'description' => 'Bitbucket OAuth 2.0',
                'scopes' => [
                    'account',
                    'email',
                ],
                'icon' => 'fab fa-bitbucket',
            ],
        ];
    }

    // Available Builder Types
    public static function getAvailableBuilderTypes(): array
    {
        return [
            self::BUILDER_FORM => [
                'name' => 'Form Builder',
                'description' => 'AI-powered form designer with drag-and-drop interface',
                'features' => [
                    'Multi-step forms',
                    'Inline forms',
                    'Modal forms',
                    'Validation rules',
                    'AI suggestions',
                    'Field types',
                    'API binding',
                    'Role-based access',
                ],
            ],
            self::BUILDER_TABLE => [
                'name' => 'Table Builder',
                'description' => 'Dynamic table generator with AI suggestions',
                'features' => [
                    'Column selection',
                    'Filtering',
                    'Search',
                    'Pagination',
                    'Export options',
                    'Bulk actions',
                    'Row selection',
                    'Dynamic relations',
                ],
            ],
            self::BUILDER_UI => [
                'name' => 'UI Pattern Builder',
                'description' => 'Component generator with prebuilt patterns',
                'features' => [
                    'Drag-and-drop interface',
                    'Tailwind styling',
                    'Dark mode support',
                    'Component library',
                    'Custom patterns',
                    'Export options',
                ],
            ],
            self::BUILDER_PAGE => [
                'name' => 'Page Builder',
                'description' => 'Visual page designer',
                'features' => [
                    'Layout templates',
                    'Component library',
                    'Responsive design',
                    'SEO optimization',
                ],
            ],
            self::BUILDER_MENU => [
                'name' => 'Menu Builder',
                'description' => 'Navigation menu designer',
                'features' => [
                    'Menu types',
                    'Dropdown support',
                    'Mega menu',
                    'Mobile menu',
                ],
            ],
            self::BUILDER_ROUTE => [
                'name' => 'Route Builder',
                'description' => 'Route configuration tool',
                'features' => [
                    'Route groups',
                    'Middleware',
                    'Parameters',
                    'Constraints',
                ],
            ],
            self::BUILDER_MODEL => [
                'name' => 'Model Builder',
                'description' => 'Database model generator',
                'features' => [
                    'Relationships',
                    'Attributes',
                    'Scopes',
                    'Events',
                ],
            ],
            self::BUILDER_CONTROLLER => [
                'name' => 'Controller Builder',
                'description' => 'Controller generator',
                'features' => [
                    'CRUD operations',
                    'Resource methods',
                    'Validation',
                    'Authorization',
                ],
            ],
            self::BUILDER_API => [
                'name' => 'API Builder',
                'description' => 'API endpoint generator',
                'features' => [
                    'REST endpoints',
                    'GraphQL schema',
                    'Documentation',
                    'Testing',
                ],
            ],
            self::BUILDER_AUTH => [
                'name' => 'Auth Builder',
                'description' => 'Authentication system generator',
                'features' => [
                    'Social login',
                    'Role management',
                    'Permissions',
                    'Policies',
                ],
            ],
            self::BUILDER_COMPONENT => [
                'name' => 'Component Builder',
                'description' => 'UI component generator',
                'features' => [
                    'Livewire components',
                    'Vue components',
                    'Blade components',
                    'Props',
                ],
            ],
            self::BUILDER_THEME => [
                'name' => 'Theme Builder',
                'description' => 'Theme and style generator',
                'features' => [
                    'Color schemes',
                    'Typography',
                    'Dark mode',
                    'Custom CSS',
                ],
            ],
        ];
    }

    // Available Form Field Types
    public static function getAvailableFormFieldTypes(): array
    {
        return [
            self::FIELD_TEXT => [
                'name' => 'Text Input',
                'description' => 'Single line text input',
                'validation' => ['string', 'max'],
            ],
            self::FIELD_EMAIL => [
                'name' => 'Email Input',
                'description' => 'Email address input',
                'validation' => ['email'],
            ],
            self::FIELD_PASSWORD => [
                'name' => 'Password Input',
                'description' => 'Password input with masking',
                'validation' => ['string', 'min'],
            ],
            self::FIELD_OTP => [
                'name' => 'OTP Input',
                'description' => 'One-time password input',
                'validation' => ['string', 'size'],
            ],
            self::FIELD_SELECT => [
                'name' => 'Select',
                'description' => 'Single select dropdown',
                'validation' => ['string', 'in'],
            ],
            self::FIELD_MULTI_SELECT => [
                'name' => 'Multi Select',
                'description' => 'Multiple select dropdown',
                'validation' => ['array'],
            ],
            self::FIELD_COMBOBOX => [
                'name' => 'Combobox',
                'description' => 'Searchable select input',
                'validation' => ['string'],
            ],
            self::FIELD_CHECKBOX => [
                'name' => 'Checkbox',
                'description' => 'Boolean checkbox input',
                'validation' => ['boolean'],
            ],
            self::FIELD_RADIO => [
                'name' => 'Radio',
                'description' => 'Radio button group',
                'validation' => ['string', 'in'],
            ],
            self::FIELD_SWITCH => [
                'name' => 'Switch',
                'description' => 'Toggle switch input',
                'validation' => ['boolean'],
            ],
            self::FIELD_TOGGLE => [
                'name' => 'Toggle',
                'description' => 'Toggle button input',
                'validation' => ['boolean'],
            ],
            self::FIELD_DATE => [
                'name' => 'Date Picker',
                'description' => 'Date selection input',
                'validation' => ['date'],
            ],
            self::FIELD_TIME => [
                'name' => 'Time Picker',
                'description' => 'Time selection input',
                'validation' => ['date_format'],
            ],
            self::FIELD_FILE => [
                'name' => 'File Upload',
                'description' => 'File upload input',
                'validation' => ['file', 'mimes', 'max'],
            ],
            self::FIELD_IMAGE => [
                'name' => 'Image Upload',
                'description' => 'Image upload input',
                'validation' => ['image', 'mimes', 'max'],
            ],
            self::FIELD_SLIDER => [
                'name' => 'Slider',
                'description' => 'Range slider input',
                'validation' => ['numeric', 'min', 'max'],
            ],
            self::FIELD_RATING => [
                'name' => 'Rating',
                'description' => 'Star rating input',
                'validation' => ['numeric', 'min', 'max'],
            ],
            self::FIELD_COLOR => [
                'name' => 'Color Picker',
                'description' => 'Color selection input',
                'validation' => ['string', 'regex'],
            ],
        ];
    }

    // Available Table Features
    public static function getAvailableTableFeatures(): array
    {
        return [
            self::TABLE_SORT => [
                'name' => 'Sorting',
                'description' => 'Column sorting functionality',
            ],
            self::TABLE_FILTER => [
                'name' => 'Filtering',
                'description' => 'Data filtering options',
            ],
            self::TABLE_SEARCH => [
                'name' => 'Search',
                'description' => 'Global search functionality',
            ],
            self::TABLE_PAGINATION => [
                'name' => 'Pagination',
                'description' => 'Page navigation controls',
            ],
            self::TABLE_EXPORT => [
                'name' => 'Export',
                'description' => 'Data export options',
            ],
            self::TABLE_BULK_ACTIONS => [
                'name' => 'Bulk Actions',
                'description' => 'Mass action operations',
            ],
            self::TABLE_ROW_SELECTION => [
                'name' => 'Row Selection',
                'description' => 'Row selection functionality',
            ],
            self::TABLE_INLINE_EDIT => [
                'name' => 'Inline Editing',
                'description' => 'Direct cell editing',
            ],
        ];
    }

    // Available UI Components
    public static function getAvailableUIComponents(): array
    {
        return [
            self::UI_ACCORDION => [
                'name' => 'Accordion',
                'description' => 'Collapsible content panels',
            ],
            self::UI_ALERT => [
                'name' => 'Alert',
                'description' => 'Notification messages',
            ],
            self::UI_ALERT_DIALOG => [
                'name' => 'Alert Dialog',
                'description' => 'Confirmation dialogs',
            ],
            self::UI_ASPECT_RATIO => [
                'name' => 'Aspect Ratio',
                'description' => 'Maintain element proportions',
            ],
            self::UI_AVATAR => [
                'name' => 'Avatar',
                'description' => 'User profile images',
            ],
            self::UI_BADGE => [
                'name' => 'Badge',
                'description' => 'Status indicators',
            ],
            self::UI_BREADCRUMB => [
                'name' => 'Breadcrumb',
                'description' => 'Navigation hierarchy',
            ],
            self::UI_BUTTON => [
                'name' => 'Button',
                'description' => 'Action triggers',
            ],
            self::UI_CALENDAR => [
                'name' => 'Calendar',
                'description' => 'Date visualization',
            ],
            self::UI_CARD => [
                'name' => 'Card',
                'description' => 'Content containers',
            ],
            self::UI_CAROUSEL => [
                'name' => 'Carousel',
                'description' => 'Image sliders',
            ],
            self::UI_CHART => [
                'name' => 'Chart',
                'description' => 'Data visualization',
            ],
            self::UI_CHECKBOX => [
                'name' => 'Checkbox',
                'description' => 'Boolean inputs',
            ],
            self::UI_COLLAPSIBLE => [
                'name' => 'Collapsible',
                'description' => 'Expandable content',
            ],
            self::UI_COMBOBOX => [
                'name' => 'Combobox',
                'description' => 'Searchable selects',
            ],
            self::UI_COMMAND_MENU => [
                'name' => 'Command Menu',
                'description' => 'Command palette',
            ],
            self::UI_CONTEXT_MENU => [
                'name' => 'Context Menu',
                'description' => 'Right-click menus',
            ],
            self::UI_DATA_TABLE => [
                'name' => 'Data Table',
                'description' => 'Tabular data display',
            ],
            self::UI_DATE_PICKER => [
                'name' => 'Date Picker',
                'description' => 'Date selection',
            ],
            self::UI_DIALOG => [
                'name' => 'Dialog',
                'description' => 'Modal windows',
            ],
            self::UI_DRAWER => [
                'name' => 'Drawer',
                'description' => 'Side panels',
            ],
            self::UI_DROPDOWN_MENU => [
                'name' => 'Dropdown Menu',
                'description' => 'Context menus',
            ],
            self::UI_FORM_LAYOUT => [
                'name' => 'Form Layout',
                'description' => 'Form structure',
            ],
            self::UI_HOVER_CARD => [
                'name' => 'Hover Card',
                'description' => 'Tooltip cards',
            ],
            self::UI_INPUT => [
                'name' => 'Input',
                'description' => 'Text inputs',
            ],
            self::UI_INPUT_OTP => [
                'name' => 'Input OTP',
                'description' => 'One-time password',
            ],
            self::UI_LABEL => [
                'name' => 'Label',
                'description' => 'Form labels',
            ],
            self::UI_MENUBAR => [
                'name' => 'Menubar',
                'description' => 'Menu containers',
            ],
            self::UI_NAVIGATION_MENU => [
                'name' => 'Navigation Menu',
                'description' => 'Site navigation',
            ],
            self::UI_PAGINATION => [
                'name' => 'Pagination',
                'description' => 'Page navigation',
            ],
            self::UI_POPOVER => [
                'name' => 'Popover',
                'description' => 'Floating content',
            ],
            self::UI_PROGRESS_BAR => [
                'name' => 'Progress Bar',
                'description' => 'Progress indicators',
            ],
            self::UI_RADIO_GROUP => [
                'name' => 'Radio Group',
                'description' => 'Radio inputs',
            ],
            self::UI_RESIZABLE_PANELS => [
                'name' => 'Resizable Panels',
                'description' => 'Adjustable layouts',
            ],
            self::UI_SCROLL_AREA => [
                'name' => 'Scroll Area',
                'description' => 'Scrollable content',
            ],
            self::UI_SELECT => [
                'name' => 'Select',
                'description' => 'Dropdown selects',
            ],
            self::UI_SEPARATOR => [
                'name' => 'Separator',
                'description' => 'Visual dividers',
            ],
            self::UI_SHEET => [
                'name' => 'Sheet',
                'description' => 'Bottom sheets',
            ],
            self::UI_SIDEBAR => [
                'name' => 'Sidebar',
                'description' => 'Side navigation',
            ],
            self::UI_SKELETON_LOADER => [
                'name' => 'Skeleton Loader',
                'description' => 'Loading states',
            ],
            self::UI_SLIDER => [
                'name' => 'Slider',
                'description' => 'Range inputs',
            ],
            self::UI_SONNER => [
                'name' => 'Sonner',
                'description' => 'Toast notifications',
            ],
            self::UI_SWITCH => [
                'name' => 'Switch',
                'description' => 'Toggle switches',
            ],
            self::UI_TABS => [
                'name' => 'Tabs',
                'description' => 'Tabbed content',
            ],
            self::UI_TEXTAREA => [
                'name' => 'Textarea',
                'description' => 'Multi-line text',
            ],
            self::UI_TOAST => [
                'name' => 'Toast',
                'description' => 'Notifications',
            ],
            self::UI_TOGGLE => [
                'name' => 'Toggle',
                'description' => 'Toggle buttons',
            ],
            self::UI_TOGGLE_GROUP => [
                'name' => 'Toggle Group',
                'description' => 'Toggle sets',
            ],
            self::UI_TOOLTIP => [
                'name' => 'Tooltip',
                'description' => 'Hover hints',
            ],
            self::UI_TYPOGRAPHY => [
                'name' => 'Typography',
                'description' => 'Text styles',
            ],
        ];
    }

    // Relationships
    public function versions(): HasMany
    {
        return $this->hasMany(ApiVersion::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'updated_by');
    }

    // Helper Methods
    public function getField(string $key, $default = null)
    {
        return $this->fields[$key] ?? $default;
    }

    public function addField(string $key, array $field)
    {
        $fields = $this->fields ?? [];
        $fields[$key] = $field;
        $this->fields = $fields;
        return $this;
    }

    public function removeField(string $key)
    {
        $fields = $this->fields ?? [];
        unset($fields[$key]);
        $this->fields = $fields;
        return $this;
    }

    public function getRelationship(string $key, $default = null)
    {
        return $this->relationships[$key] ?? $default;
    }

    public function addRelationship(string $key, array $relationship)
    {
        $relationships = $this->relationships ?? [];
        $relationships[$key] = $relationship;
        $this->relationships = $relationships;
        return $this;
    }

    public function removeRelationship(string $key)
    {
        $relationships = $this->relationships ?? [];
        unset($relationships[$key]);
        $this->relationships = $relationships;
        return $this;
    }

    public function getEndpoint(string $key, $default = null)
    {
        return $this->endpoints[$key] ?? $default;
    }

    public function addEndpoint(string $key, array $endpoint)
    {
        $endpoints = $this->endpoints ?? [];
        $endpoints[$key] = $endpoint;
        $this->endpoints = $endpoints;
        return $this;
    }

    public function removeEndpoint(string $key)
    {
        $endpoints = $this->endpoints ?? [];
        unset($endpoints[$key]);
        $this->endpoints = $endpoints;
        return $this;
    }

    public function getValidationRules(): array
    {
        return $this->validation_rules ?? [];
    }

    public function generateDocumentation(): array
    {
        $documentation = [
            'name' => $this->name,
            'type' => $this->type,
            'model' => $this->model,
            'endpoints' => $this->endpoints,
            'fields' => $this->fields,
            'relationships' => $this->relationships,
            'validation_rules' => $this->validation_rules,
            'auth_type' => $this->auth_type,
            'auth_providers' => $this->auth_providers,
            'auth_config' => $this->auth_config,
            'rate_limit' => $this->rate_limit,
            'middleware' => $this->middleware,
            'ai_suggestions' => $this->ai_suggestions,
        ];

        $this->documentation = $documentation;
        $this->save();

        return $documentation;
    }

    public function toArray(): array
    {
        $array = parent::toArray();
        $array['type_info'] = self::getAvailableTypes()[$this->type] ?? null;
        $array['auth_info'] = self::getAvailableAuthTypes()[$this->auth_type] ?? null;
        return $array;
    }
} 