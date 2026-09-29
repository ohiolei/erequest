import { computed, ref } from 'vue';

export const THEMES = [
    {
        id: 'purple',
        label: 'Purple',
        swatch: '#9333ea',
        colors: {
            50: '250 245 255',
            100: '243 232 255',
            200: '233 213 255',
            300: '216 180 254',
            400: '192 132 252',
            500: '168 85 247',
            600: '147 51 234',
            700: '126 34 206',
            800: '107 33 168',
            900: '88 28 135',
        },
    },
    {
        id: 'blue',
        label: 'Blue',
        swatch: '#2563eb',
        colors: {
            50: '239 246 255',
            100: '219 234 254',
            200: '191 219 254',
            300: '147 197 253',
            400: '96 165 250',
            500: '59 130 246',
            600: '37 99 235',
            700: '29 78 216',
            800: '30 64 175',
            900: '30 58 138',
        },
    },
    {
        id: 'emerald',
        label: 'Emerald',
        swatch: '#059669',
        colors: {
            50: '236 253 245',
            100: '209 250 229',
            200: '167 243 208',
            300: '110 231 183',
            400: '52 211 153',
            500: '16 185 129',
            600: '5 150 105',
            700: '4 120 87',
            800: '6 95 70',
            900: '6 78 59',
        },
    },
    {
        id: 'rose',
        label: 'Rose',
        swatch: '#e11d48',
        colors: {
            50: '255 241 242',
            100: '255 228 230',
            200: '254 205 211',
            300: '253 164 175',
            400: '251 113 133',
            500: '244 63 94',
            600: '225 29 72',
            700: '190 18 60',
            800: '159 18 57',
            900: '136 19 55',
        },
    },
    {
        id: 'amber',
        label: 'Amber',
        swatch: '#d97706',
        colors: {
            50: '255 251 235',
            100: '254 243 199',
            200: '253 230 138',
            300: '252 211 77',
            400: '251 191 36',
            500: '245 158 11',
            600: '217 119 6',
            700: '180 83 9',
            800: '146 64 14',
            900: '120 53 15',
        },
    },
    {
        id: 'teal',
        label: 'Teal',
        swatch: '#0d9488',
        colors: {
            50: '240 253 250',
            100: '204 251 241',
            200: '153 246 228',
            300: '94 234 212',
            400: '45 212 191',
            500: '20 184 166',
            600: '13 148 136',
            700: '15 118 110',
            800: '17 94 89',
            900: '19 78 74',
        },
    },
    {
        id: 'indigo',
        label: 'Indigo',
        swatch: '#4f46e5',
        colors: {
            50: '238 242 255',
            100: '224 231 255',
            200: '199 210 254',
            300: '165 180 252',
            400: '129 140 248',
            500: '99 102 241',
            600: '79 70 229',
            700: '67 56 202',
            800: '55 48 163',
            900: '49 46 129',
        },
    },
];

export const COLOR_MODES = [
    { id: 'light', label: 'Light' },
    { id: 'dark', label: 'Dark' },
];

const THEME_STORAGE_KEY = 'app-theme';
const MODE_STORAGE_KEY = 'app-color-mode';
const DEFAULT_THEME = 'purple';
const DEFAULT_MODE = 'light';

const themeId = ref(DEFAULT_THEME);
const colorMode = ref(DEFAULT_MODE);

const getTheme = (id) => THEMES.find((theme) => theme.id === id) ?? THEMES[0];

const applyTheme = (id) => {
    const theme = getTheme(id);
    const root = document.documentElement;

    root.setAttribute('data-theme', theme.id);

    Object.entries(theme.colors).forEach(([shade, value]) => {
        root.style.setProperty(`--color-primary-${shade}`, value);
    });
};

const applyColorMode = (mode) => {
    const root = document.documentElement;
    const nextMode = mode === 'dark' ? 'dark' : 'light';

    root.classList.toggle('dark', nextMode === 'dark');
    root.setAttribute('data-color-mode', nextMode);
};

export const initTheme = () => {
    const savedTheme = localStorage.getItem(THEME_STORAGE_KEY);
    const id = THEMES.some((theme) => theme.id === savedTheme) ? savedTheme : DEFAULT_THEME;
    themeId.value = id;
    applyTheme(id);

    const savedMode = localStorage.getItem(MODE_STORAGE_KEY);
    const mode = COLOR_MODES.some((item) => item.id === savedMode) ? savedMode : DEFAULT_MODE;
    colorMode.value = mode;
    applyColorMode(mode);
};

export function useTheme() {
    const currentTheme = computed(() => getTheme(themeId.value));
    const isDark = computed(() => colorMode.value === 'dark');

    const setTheme = (id) => {
        if (!THEMES.some((theme) => theme.id === id)) {
            return;
        }

        themeId.value = id;
        localStorage.setItem(THEME_STORAGE_KEY, id);
        applyTheme(id);
    };

    const setColorMode = (mode) => {
        if (!COLOR_MODES.some((item) => item.id === mode)) {
            return;
        }

        colorMode.value = mode;
        localStorage.setItem(MODE_STORAGE_KEY, mode);
        applyColorMode(mode);
    };

    return {
        themes: THEMES,
        colorModes: COLOR_MODES,
        themeId,
        colorMode,
        currentTheme,
        isDark,
        setTheme,
        setColorMode,
    };
}
