const root = document.documentElement;
const themeButton = document.querySelector('.theme-toggle');
const systemTheme = matchMedia('(prefers-color-scheme: dark)');
const themeKey = 'brk-landing-theme';
let explicitTheme;

try {
    explicitTheme = localStorage.getItem(themeKey);
} catch {}

function applyTheme(theme) {
    root.dataset.theme = theme;
    themeButton.setAttribute('aria-pressed', String(theme === 'dark'));
}

applyTheme(root.dataset.theme);
themeButton.hidden = false;

themeButton.addEventListener('click', () => {
    explicitTheme = root.dataset.theme === 'dark' ? 'light' : 'dark';
    applyTheme(explicitTheme);
    try {
        localStorage.setItem(themeKey, explicitTheme);
    } catch {}
});

systemTheme.addEventListener('change', ({ matches }) => {
    if (explicitTheme !== 'light' && explicitTheme !== 'dark') {
        applyTheme(matches ? 'dark' : 'light');
    }
});

const message = document.querySelector('#contact-message');
document.querySelectorAll('[data-product]').forEach((link) => {
    link.addEventListener('click', () => {
        if (!message.value.trim()) {
            message.value = `Halo, saya ingin bertanya tentang ${link.dataset.product}.`;
        }
    });
});

document.querySelectorAll('[data-product-image]').forEach((image) => {
    const hideBrokenImage = () => { image.hidden = true; };
    image.addEventListener('error', hideBrokenImage);
    if (image.complete && !image.naturalWidth) {
        hideBrokenImage();
    }
});
