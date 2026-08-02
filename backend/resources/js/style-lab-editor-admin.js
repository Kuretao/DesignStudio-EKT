(function () {
    const styles = ['minimal', 'classic', 'loft', 'eco'];
    const materials = ['wood', 'stone', 'textile', 'metal'];
    const lights = ['morning', 'evening', 'gallery'];
    const state = {
        style: styles[0],
        material: materials[0],
        light: lights[0],
        source: 'style',
    };

    function ready(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback, { once: true });
            return;
        }

        callback();
    }

    function value(root, key, locale = 'ru') {
        const input =
            root.querySelector(`[data-sl-key="${selectorValue(key)}"][data-sl-locale="${locale}"]`) ||
            root.querySelector(`[data-sl-key="${selectorValue(key)}"][data-sl-locale="value"]`);

        return input ? input.value.trim() : '';
    }

    function lines(text) {
        return text
            .split(/[\n,]+/u)
            .map((item) => item.trim())
            .filter(Boolean);
    }

    function renderButtons(root, target, ids, current, keyPrefix, onClick) {
        const holder = root.querySelector(target);
        if (!holder) return;

        holder.innerHTML = ids.map((id) => {
            const label = value(root, `${keyPrefix}.${id}.label`) || id;
            return `<button type="button" class="${id === current ? 'is-active' : ''}" data-id="${escapeAttr(id)}">${escapeHtml(label)}</button>`;
        }).join('');

        holder.querySelectorAll('button').forEach((button) => {
            button.addEventListener('click', () => onClick(button.dataset.id || ids[0]));
        });
    }

    function render(root) {
        const style = state.style;
        const material = state.material;
        const light = state.light;
        const image =
            state.source === 'light'
                ? imageValue(root, `styleLab.lights.${light}.image`, root)
                : state.source === 'material'
                    ? imageValue(root, `styleLab.materials.${material}.image`, root)
                    : imageValue(root, `styleLab.styles.${style}.image`, root);
        const colors = lines(value(root, `styleLab.styles.${style}.colors`));
        const accent = value(root, `styleLab.materials.${material}.accent`) || '#B78352';
        const lightOverlay = value(root, `styleLab.lights.${light}.overlay`) || 'linear-gradient(120deg, rgba(245,242,236,.22), rgba(214,154,102,.08), rgba(5,5,5,.18))';

        setText(root, '[data-sl-preview-eyebrow]', value(root, 'styleLab.eyebrow'));
        setText(root, '[data-sl-preview-title]', value(root, 'styleLab.title'));
        setText(root, '[data-sl-preview-text]', value(root, 'styleLab.text'));
        setText(root, '[data-sl-preview-style-label]', value(root, `styleLab.styles.${style}.label`));
        setText(root, '[data-sl-preview-headline]', value(root, `styleLab.styles.${style}.headline`));
        setText(
            root,
            '[data-sl-preview-pill]',
            `${value(root, `styleLab.materials.${material}.label`)} · ${value(root, `styleLab.lights.${light}.label`)}`,
        );

        const bg = root.querySelector('[data-sl-preview-bg]');
        if (bg) bg.src = image;

        const lightNode = root.querySelector('[data-sl-preview-light]');
        if (lightNode) lightNode.style.background = lightOverlay;

        const swatches = root.querySelector('[data-sl-preview-swatches]');
        if (swatches) {
            swatches.innerHTML = [...colors, accent]
                .filter(Boolean)
                .map((color) => `<span style="background:${escapeAttr(color)}"></span>`)
                .join('');
        }

        renderButtons(root, '[data-sl-preview-style-buttons]', styles, state.style, 'styleLab.styles', (id) => {
            state.style = id;
            state.source = 'style';
            render(root);
        });
        renderButtons(root, '[data-sl-preview-material-buttons]', materials, state.material, 'styleLab.materials', (id) => {
            state.material = id;
            state.source = 'material';
            render(root);
        });
        renderButtons(root, '[data-sl-preview-light-buttons]', lights, state.light, 'styleLab.lights', (id) => {
            state.light = id;
            state.source = 'light';
            render(root);
        });
    }

    function imageValue(root, key) {
        return value(root, key) || value(root, `styleLab.styles.${state.style}.image`) || '/images/cms/river-park-interior.webp';
    }

    function setText(root, selector, text) {
        const node = root.querySelector(selector);
        if (node) node.textContent = text;
    }

    function escapeHtml(value) {
        return String(value).replace(/[&<>"']/g, (char) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;',
        }[char]));
    }

    function escapeAttr(value) {
        return escapeHtml(value);
    }

    function selectorValue(value) {
        return String(value).replace(/\\/g, '\\\\').replace(/"/g, '\\"');
    }

    ready(() => {
        document.querySelectorAll('[data-style-lab-editor]').forEach((root) => {
            root.addEventListener('input', () => render(root));
            render(root);
        });
    });
}());
