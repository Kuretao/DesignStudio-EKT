(function () {
    const config = window.AdminImageGallery || {};
    const endpoint = config.endpoint || '/admin/image-gallery';
    const state = {
        images: null,
        activeInput: null,
        query: '',
        directory: 'all',
    };

    function ready(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback, { once: true });
            return;
        }

        callback();
    }

    function isGalleryInput(input) {
        if (!(input instanceof HTMLInputElement) || input.type !== 'file') {
            return false;
        }

        const accept = (input.getAttribute('accept') || '').toLowerCase();

        return ['image/', '.jpg', '.jpeg', '.png', '.webp', '.avif', '.gif', '.svg'].some((part) => accept.includes(part));
    }

    function isGalleryTextarea(input) {
        if (!(input instanceof HTMLTextAreaElement)) {
            return false;
        }

        if (input.dataset.galleryLines === '1') {
            return true;
        }

        const name = input.getAttribute('name') || '';

        return [
            'image',
            'logo',
            'hero_images',
            'deliverable_images',
            'before_image',
            'after_image',
        ].includes(name);
    }

    function hiddenNameFor(input) {
        const name = input.getAttribute('name') || '';

        if (name === '') {
            return '';
        }

        return name.replace(/([^\[\]]+)(\]?)$/, 'hidden_$1$2');
    }

    function formatSize(bytes) {
        if (!bytes) {
            return '';
        }

        if (bytes < 1024 * 1024) {
            return `${Math.max(1, Math.round(bytes / 1024))} КБ`;
        }

        return `${(bytes / 1024 / 1024).toFixed(1)} МБ`;
    }

    function ensureModal() {
        let modal = document.querySelector('[data-admin-image-gallery-modal]');

        if (modal) {
            return modal;
        }

        modal = document.createElement('div');
        modal.className = 'admin-image-gallery';
        modal.dataset.adminImageGalleryModal = '1';
        modal.innerHTML = `
            <div class="admin-image-gallery__backdrop" data-gallery-close></div>
            <section class="admin-image-gallery__dialog" role="dialog" aria-modal="true" aria-label="Галерея изображений">
                <header class="admin-image-gallery__header">
                    <div>
                        <p>Медиафайлы</p>
                        <h2>Выберите изображение</h2>
                    </div>
                    <button type="button" class="admin-image-gallery__close" data-gallery-close aria-label="Закрыть">×</button>
                </header>
                <div class="admin-image-gallery__tools">
                    <input type="search" class="admin-image-gallery__search" placeholder="Поиск по названию или папке" data-gallery-search>
                    <div class="admin-image-gallery__directories" data-gallery-directories></div>
                </div>
                <div class="admin-image-gallery__body" data-gallery-body>
                    <div class="admin-image-gallery__empty">Загрузка изображений...</div>
                </div>
            </section>
        `;

        modal.addEventListener('click', (event) => {
            const target = event.target;

            if (target instanceof HTMLElement && target.hasAttribute('data-gallery-close')) {
                closeModal();
            }
        });

        modal.querySelector('[data-gallery-search]').addEventListener('input', (event) => {
            state.query = event.target.value.trim().toLowerCase();
            renderModal();
        });

        document.body.appendChild(modal);

        return modal;
    }

    function openModal(input) {
        state.activeInput = input;
        const modal = ensureModal();
        modal.classList.add('is-open');
        document.body.classList.add('admin-image-gallery-open');
        loadImages().then(renderModal).catch(() => {
            const body = modal.querySelector('[data-gallery-body]');
            body.innerHTML = '<div class="admin-image-gallery__empty">Не удалось загрузить галерею.</div>';
        });
    }

    function closeModal() {
        const modal = ensureModal();
        modal.classList.remove('is-open');
        document.body.classList.remove('admin-image-gallery-open');
        state.activeInput = null;
    }

    async function loadImages() {
        if (Array.isArray(state.images)) {
            return state.images;
        }

        const response = await fetch(endpoint, {
            headers: {
                Accept: 'application/json',
            },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            throw new Error('Gallery request failed');
        }

        const payload = await response.json();
        state.images = Array.isArray(payload.images) ? payload.images : [];

        return state.images;
    }

    function directories(images) {
        const result = Array.from(new Set(images.map((image) => image.directory || 'Без папки')));
        result.sort((a, b) => a.localeCompare(b, 'ru'));

        return ['all', ...result];
    }

    function filteredImages() {
        const images = Array.isArray(state.images) ? state.images : [];

        return images.filter((image) => {
            const directory = image.directory || 'Без папки';
            const haystack = `${image.name || ''} ${directory} ${image.path || ''}`.toLowerCase();
            const directoryMatches = state.directory === 'all' || directory === state.directory;
            const queryMatches = state.query === '' || haystack.includes(state.query);

            return directoryMatches && queryMatches;
        });
    }

    function renderModal() {
        const modal = ensureModal();
        const imageList = Array.isArray(state.images) ? state.images : [];
        const body = modal.querySelector('[data-gallery-body]');
        const dirRoot = modal.querySelector('[data-gallery-directories]');
        const dirs = directories(imageList);

        dirRoot.innerHTML = dirs.map((directory) => {
            const label = directory === 'all' ? 'Все' : directory;
            const active = state.directory === directory ? ' is-active' : '';

            return `<button type="button" class="admin-image-gallery__dir${active}" data-gallery-dir="${escapeHtml(directory)}">${escapeHtml(label)}</button>`;
        }).join('');

        dirRoot.querySelectorAll('[data-gallery-dir]').forEach((button) => {
            button.addEventListener('click', () => {
                state.directory = button.dataset.galleryDir || 'all';
                renderModal();
            });
        });

        const images = filteredImages();

        if (images.length === 0) {
            body.innerHTML = '<div class="admin-image-gallery__empty">Изображений не найдено.</div>';
            return;
        }

        body.innerHTML = `
            <div class="admin-image-gallery__grid">
                ${images.map((image, index) => `
                    <button type="button" class="admin-image-gallery__item" data-gallery-index="${index}">
                        <span class="admin-image-gallery__thumb"><img src="${escapeAttr(image.url)}" alt=""></span>
                        <span class="admin-image-gallery__meta">
                            <strong>${escapeHtml(image.name || '')}</strong>
                            <span>${escapeHtml(image.directory || 'Без папки')} · ${escapeHtml(formatSize(image.size))}</span>
                        </span>
                    </button>
                `).join('')}
            </div>
        `;

        body.querySelectorAll('[data-gallery-index]').forEach((button) => {
            button.addEventListener('click', () => {
                const image = images[Number(button.dataset.galleryIndex)];

                if (image && state.activeInput) {
                    selectImage(state.activeInput, image);
                    closeModal();
                }
            });
        });
    }

    function imagePath(image) {
        return image.path || image.url || '';
    }

    function insertTextareaImage(input, image) {
        const path = imagePath(image);

        if (!path) {
            return;
        }

        const start = input.selectionStart ?? input.value.length;
        const end = input.selectionEnd ?? input.value.length;
        const before = input.value.slice(0, start);
        const after = input.value.slice(end);
        const prefix = before.length > 0 && !before.endsWith('\n') ? '\n' : '';
        const suffix = after.length > 0 && !after.startsWith('\n') ? '\n' : '';

        input.value = `${before}${prefix}${path}${suffix}${after}`;
        input.focus();
        input.selectionStart = input.selectionEnd = before.length + prefix.length + path.length;
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function selectImage(input, image) {
        if (input instanceof HTMLTextAreaElement) {
            insertTextareaImage(input, image);
            return;
        }

        const group = input.closest('.form-group-dropzone');
        const hiddenName = hiddenNameFor(input);

        if (!group || hiddenName === '') {
            return;
        }

        input.value = '';
        group.querySelectorAll('.dropzone, .admin-gallery-selected').forEach((node) => node.remove());
        group.querySelectorAll('input[type="hidden"]').forEach((node) => {
            if (node.getAttribute('name') === hiddenName) {
                node.remove();
            }
        });

        const selected = document.createElement('div');
        selected.className = 'admin-gallery-selected';
        selected.innerHTML = `
            <input type="hidden" name="${escapeAttr(hiddenName)}" data-name="${escapeAttr(hiddenName)}" value="${escapeAttr(image.path || '')}">
            <div class="admin-gallery-selected__preview">
                <img src="${escapeAttr(image.url || '')}" alt="">
            </div>
            <div class="admin-gallery-selected__meta">
                <strong>${escapeHtml(image.name || '')}</strong>
                <span>${escapeHtml(image.path || '')}</span>
            </div>
            <button type="button" class="admin-gallery-selected__remove" aria-label="Убрать выбранное изображение">×</button>
        `;

        selected.querySelector('.admin-gallery-selected__remove').addEventListener('click', () => selected.remove());
        group.appendChild(selected);
    }

    function addButtons(root) {
        root.querySelectorAll('.form-group-dropzone input[type="file"]').forEach((input) => {
            if (!isGalleryInput(input) || input.dataset.galleryButtonReady === '1') {
                return;
            }

            input.dataset.galleryButtonReady = '1';

            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'admin-gallery-trigger';
            button.textContent = 'Выбрать из галереи';
            button.addEventListener('click', () => openModal(input));

            input.insertAdjacentElement('afterend', button);
        });

        root.querySelectorAll('textarea').forEach((input) => {
            if (!isGalleryTextarea(input) || input.dataset.galleryButtonReady === '1') {
                return;
            }

            input.dataset.galleryButtonReady = '1';

            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'admin-gallery-trigger';
            button.textContent = 'Вставить из галереи';
            button.addEventListener('click', () => openModal(input));

            input.insertAdjacentElement('afterend', button);
        });
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

    ready(() => {
        addButtons(document);

        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node instanceof HTMLElement) {
                        addButtons(node);
                    }
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && document.querySelector('.admin-image-gallery.is-open')) {
                closeModal();
            }
        });
    });
}());
