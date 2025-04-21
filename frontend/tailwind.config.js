/** @type {import('tailwindcss').Config} */
export default {
    content: [],
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                brand: {
                    25: 'var(--brand-25)',
                    50: 'var(--brand-50)',
                    100: 'var(--brand-100)',
                    200: 'var(--brand-200)',
                    300: 'var(--brand-300)',
                    400: 'var(--brand-400)',
                    500: 'var(--brand-500)',
                    600: 'var(--brand-600)',
                    700: 'var(--brand-700)',
                    800: 'var(--brand-800)',
                    900: 'var(--brand-900)',
                    950: 'var(--brand-950)',
                },
                error: {
                    25: 'var(--error-25)',
                    50: 'var(--error-50)',
                    100: 'var(--error-100)',
                    200: 'var(--error-200)',
                    300: 'var(--error-300)',
                    400: 'var(--error-400)',
                    500: 'var(--error-500)',
                    600: 'var(--error-600)',
                    700: 'var(--error-700)',
                    800: 'var(--error-800)',
                    900: 'var(--error-900)',
                    950: 'var(--error-950)',
                },
                warning: {
                    25: 'var(--warning-25)',
                    50: 'var(--warning-50)',
                    100: 'var(--warning-100)',
                    200: 'var(--warning-200)',
                    300: 'var(--warning-300)',
                    400: 'var(--warning-400)',
                    500: 'var(--warning-500)',
                    600: 'var(--warning-600)',
                    700: 'var(--warning-700)',
                    800: 'var(--warning-800)',
                    900: 'var(--warning-900)',
                    950: 'var(--warning-950)',
                },
                success: {
                    25: 'var(--success-25)',
                    50: 'var(--success-50)',
                    100: 'var(--success-100)',
                    200: 'var(--success-200)',
                    300: 'var(--success-300)',
                    400: 'var(--success-400)',
                    500: 'var(--success-500)',
                    600: 'var(--success-600)',
                    700: 'var(--success-700)',
                    800: 'var(--success-800)',
                    900: 'var(--success-900)',
                    950: 'var(--success-950)',
                },
                gray: {
                    25: 'var(--gray-25)',
                    50: 'var(--gray-50)',
                    100: 'var(--gray-100)',
                    200: 'var(--gray-200)',
                    300: 'var(--gray-300)',
                    400: 'var(--gray-400)',
                    500: 'var(--gray-500)',
                    600: 'var(--gray-600)',
                    700: 'var(--gray-700)',
                    800: 'var(--gray-800)',
                    900: 'var(--gray-900)',
                    950: 'var(--gray-950)',
                },
                utility: {
                    gray: {
                        50: 'var(--utility-gray-50)',
                        100: 'var(--utility-gray-100)',
                        200: 'var(--utility-gray-200)',
                        300: 'var(--utility-gray-300)',
                        400: 'var(--utility-gray-400)',
                        500: 'var(--utility-gray-500)',
                        600: 'var(--utility-gray-600)',
                        700: 'var(--utility-gray-700)',
                        800: 'var(--utility-gray-800)',
                        900: 'var(--utility-gray-900)',
                    },
                    brand: {
                        50: 'var(--utility-brand-50)',
                        '50-alt': 'var(--utility-brand-50-alt)',
                        100: 'var(--utility-brand-100)',
                        '100-alt': 'var(--utility-brand-100-alt)',
                        200: 'var(--utility-brand-200)',
                        '200-alt': 'var(--utility-brand-200-alt)',
                        300: 'var(--utility-brand-300)',
                        '300-alt': 'var(--utility-brand-300-alt)',
                        400: 'var(--utility-brand-400)',
                        '400-alt': 'var(--utility-brand-400-alt)',
                        500: 'var(--utility-brand-500)',
                        '500-alt': 'var(--utility-brand-500-alt)',
                        600: 'var(--utility-brand-600)',
                        '600-alt': 'var(--utility-brand-600-alt)',
                        700: 'var(--utility-brand-700)',
                        '700-alt': 'var(--utility-brand-700-alt)',
                        800: 'var(--utility-brand-800)',
                        '800-alt': 'var(--utility-brand-800-alt)',
                        900: 'var(--utility-brand-900)',
                        '900-alt': 'var(--utility-brand-900-alt)',
                    },
                    error: {
                        50: 'var(--utility-error-50)',
                        100: 'var(--utility-error-100)',
                        200: 'var(--utility-error-200)',
                        300: 'var(--utility-error-300)',
                        400: 'var(--utility-error-400)',
                        500: 'var(--utility-error-500)',
                        600: 'var(--utility-error-600)',
                        700: 'var(--utility-error-700)',
                    },
                    warning: {
                        50: 'var(--utility-warning-50)',
                        100: 'var(--utility-warning-100)',
                        200: 'var(--utility-warning-200)',
                        300: 'var(--utility-warning-300)',
                        400: 'var(--utility-warning-400)',
                        500: 'var(--utility-warning-500)',
                        600: 'var(--utility-warning-600)',
                        700: 'var(--utility-warning-700)',
                    },
                    success: {
                        50: 'var(--utility-success-50)',
                        100: 'var(--utility-success-100)',
                        200: 'var(--utility-success-200)',
                        300: 'var(--utility-success-300)',
                        400: 'var(--utility-success-400)',
                        500: 'var(--utility-success-500)',
                        600: 'var(--utility-success-600)',
                        700: 'var(--utility-success-700)',
                    },
                },
            },
            backgroundColor: {
                primary: {
                    DEFAULT: 'var(--bg-primary)',
                    alt: 'var(--bg-primary-alt)',
                    hover: 'var(--bg-primary-hover)',
                    solid: 'var(--bg-primary-solid)',
                },
                secondary: {
                    DEFAULT: 'var(--bg-secondary)',
                    alt: 'var(--bg-secondary-alt)',
                    hover: 'var(--bg-secondary-hover)',
                    subtle: 'var(--bg-secondary-subtle)',
                    solid: 'var(--bg-secondary-solid)',
                },
                tertiary: 'var(--bg-tertiary)',
                quaternary: 'var(--bg-quaternary)',
                active: 'var(--bg-active)',
                disabled: {
                    DEFAULT: 'var(--bg-disabled)',
                    subtle: 'var(--bg-disabled-sublte)',
                },
                overlay: 'var(--bg-overlay)',
                brand: {
                    primary: {
                        DEFAULT: 'var(--bg-brand-primary)',
                        alt: 'var(--bg-brand-primary-alt)',
                    },
                    secondary: 'var(--bg-brand-secondary)',
                    solid: {
                        DEFAULT: 'var(--bg-brand-solid)',
                        hover: 'var(--bg-brand-solid-hover)',
                    },
                    section: {
                        DEFAULT: 'var(--bg-brand-section)',
                        subtle: 'var(--bg-brand-section-subtle)',
                    },
                },
                error: {
                    primary: 'var(--bg-error-primary)',
                    secondary: 'var(--bg-error-secondary)',
                    solid: 'var(--bg-error-solid)',
                },
                warning: {
                    primary: 'var(--bg-warning-primary)',
                    secondary: 'var(--bg-warning-secondary)',
                    solid: 'var(--bg-warning-solid)',
                },
                success: {
                    primary: 'var(--bg-success-primary)',
                    secondary: 'var(--bg-success-secondary)',
                    solid: 'var(--bg-success-solid)',
                },
            },
            textColor: {
                primary: {
                    DEFAULT: 'var(--text-primary)',
                    'on-brand': 'var(--text-primary-on-brand)',
                },
                secondary: {
                    DEFAULT: 'var(--text-secondary)',
                    hover: 'var(--text-secondary-hover)',
                    'on-brand': 'var(--text-secondary-on-brand)',
                },
                tertiary: {
                    DEFAULT: 'var(--text-tertiary)',
                    hover: 'var(--text-tertiary-hover)',
                    'on-brand': 'var(--text-tertiary-on-brand)',
                },
                quaternary: {
                    DEFAULT: 'var(--text-quaternary)',
                    'on-brand': 'var(--text-quaternary-on-brand)',
                },
                disabled: 'var(--text-disabled)',
                placeholder: {
                    DEFAULT: 'var(--text-placeholder)',
                    subtle: 'var(--text-placeholder-subtle)',
                },
                brand: {
                    primary: 'var(--text-brand-primary)',
                    secondary: {
                        DEFAULT: 'var(--text-brand-secondary)',
                        hover: 'var(--text-brand-secondary-hover)',
                    },
                    tertiary: {
                        DEFAULT: 'var(--text-brand-tertiary)',
                        alt: 'var(--text-brand-tertiary-alt)',
                    },
                },
                error: {
                    primary: {
                        DEFAULT: 'var(--text-error-primary)',
                        hover: 'var(--text-error-primary-hover)',
                    },
                },
                warning: {
                    primary: 'var(--text-warning-primary)',
                },
                success: {
                    primary: 'var(--text-success-primary)',
                },
                fg: {
                    primary: 'var(--fg-primary)',
                    secondary: {
                        DEFAULT: 'var(--fg-secondary)',
                        hover: 'var(--fg-secondary-hover)',
                    },
                    tertiary: {
                        DEFAULT: 'var(--fg-tertiary)',
                        hover: 'var(--fg-tertiary-hover)',
                    },
                    quaternary: {
                        DEFAULT: 'var(--fg-quaternary)',
                        hover: 'var(--fg-quaternary-hover)',
                    },
                    disabled: {
                        DEFAULT: 'var(--fg-disabled)',
                        subtle: 'var(--fg-disabled-subtle)',
                    },
                    brand: {
                        primary: {
                            DEFAULT: 'var(--fg-brand-primary)',
                            alt: 'var(--fg-brand-primary-alt)',
                        },
                        secondary: {
                            DEFAULT: 'var(--fg-brand-secondary)',
                            alt: 'var(--fg-brand-secondary-alt)',
                            hover: 'var(--fg-brand-secondary-hover)',
                        },
                    },
                    error: {
                        primary: 'var(--fg-error-primary)',
                        secondary: 'var(--fg-error-secondary)',
                    },
                    warning: {
                        primary: 'var(--fg-warning-primary)',
                        secondary: 'var(--fg-warning-secondary)',
                    },
                    success: {
                        primary: 'var(--fg-success-primary)',
                        secondary: 'var(--fg-success-secondary)',
                    },
                },
                button: {
                    primary: {
                        icon: {
                            DEFAULT: 'var(--button-primary-icon)',
                            hover: 'var(--button-primary-icon-hover)',
                        },
                    },
                    destructive: {
                        primary: {
                            icon: {
                                DEFAULT: 'var(--button-destructive-primary-icon)',
                                hover: 'var(--button-destructive-primary-icon-hover)',
                            },
                        },
                    },
                },
                featured: {
                    icon: {
                        light: {
                            brand: 'var(--featured-icon-light-fg-brand)',
                            gray: 'var(--featured-icon-light-fg-gray)',
                            error: 'var(--featured-icon-light-fg-error)',
                            warning: 'var(--featured-icon-light-fg-warning)',
                            success: 'var(--featured-icon-light-fg-success)',
                        },
                    },
                },
            },
            borderColor: {
                primary: 'var(--border-primary)',
                secondary: {
                    DEFAULT: 'var(--border-secondary)',
                    alt: 'var(--border-secondary-alt)',
                },
                tertiary: 'var(--border-tertiary)',
                disabled: {
                    DEFAULT: 'var(--border-disabled)',
                    subtle: 'var(--border-disabled-subtle)',
                },
                brand: {
                    DEFAULT: 'var(--border-brand)',
                    alt: 'var(--border-brand-alt)',
                },
                error: {
                    DEFAULT: 'var(--border-error)',
                    subtle: 'var(--border-error-sublte)',
                },
                'skeuemorphic-gradient': 'var(--border-skeuemorphic-gradient)',
                fg: {
                    primary: 'var(--fg-primary)',
                    secondary: {
                        DEFAULT: 'var(--fg-secondary)',
                        hover: 'var(--fg-secondary-hover)',
                    },
                    tertiary: {
                        DEFAULT: 'var(--fg-tertiary)',
                        hover: 'var(--fg-tertiary-hover)',
                    },
                    quaternary: {
                        DEFAULT: 'var(--fg-quaternary)',
                        hover: 'var(--fg-quaternary-hover)',
                    },
                    disabled: {
                        DEFAULT: 'var(--fg-disabled)',
                        subtle: 'var(--fg-disabled-subtle)',
                    },
                    brand: {
                        primary: {
                            DEFAULT: 'var(--fg-brand-primary)',
                            alt: 'var(--fg-brand-primary-alt)',
                        },
                        secondary: {
                            DEFAULT: 'var(--fg-brand-secondary)',
                            alt: 'var(--fg-brand-secondary-alt)',
                            hover: 'var(--fg-brand-secondary-hover)',
                        },
                    },
                    error: {
                        primary: 'var(--fg-error-primary)',
                        secondary: 'var(--fg-error-secondary)',
                    },
                    warning: {
                        primary: 'var(--fg-warning-primary)',
                        secondary: 'var(--fg-warning-secondary)',
                    },
                    success: {
                        primary: 'var(--fg-success-primary)',
                        secondary: 'var(--fg-success-secondary)',
                    },
                },
            },
            ringColor: {
                primary: 'var(--focus-ring)',
                error: 'var(--focus-ring-error)',
            },
            fill: {
                primary: 'var(--fg-primary)',
                secondary: {
                    DEFAULT: 'var(--fg-secondary)',
                    hover: 'var(--fg-secondary-hover)',
                },
                tertiary: {
                    DEFAULT: 'var(--fg-tertiary)',
                    hover: 'var(--fg-tertiary-hover)',
                },
                quaternary: {
                    DEFAULT: 'var(--fg-quaternary)',
                    hover: 'var(--fg-quaternary-hover)',
                },
                disabled: {
                    DEFAULT: 'var(--fg-disabled)',
                    subtle: 'var(--fg-disabled-subtle)',
                },
                brand: {
                    primary: {
                        DEFAULT: 'var(--fg-brand-primary)',
                        alt: 'var(--fg-brand-primary-alt)',
                    },
                    secondary: {
                        DEFAULT: 'var(--fg-brand-secondary)',
                        alt: 'var(--fg-brand-secondary-alt)',
                        hover: 'var(--fg-brand-secondary-hover)',
                    },
                },
                error: {
                    primary: 'var(--fg-error-primary)',
                    secondary: 'var(--fg-error-secondary)',
                },
                warning: {
                    primary: 'var(--fg-warning-primary)',
                    secondary: 'var(--fg-warning-secondary)',
                },
                success: {
                    primary: 'var(--fg-success-primary)',
                    secondary: 'var(--fg-success-secondary)',
                },
            },
            spacing: {
                4.5: '1.125rem',
                82: '20.5rem',
            },
        },
    },
    plugins: [],
};
