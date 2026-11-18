(function () {
    if (window.labsBrushEditorLoaded) {
        return;
    }

    window.labsBrushEditorLoaded = true;

    const originalSources = new WeakMap();

    const ICONS = {
        erase: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7 21-4.3-4.3c-1-1-1-2.5 0-3.4l9.6-9.6c1-1 2.5-1 3.4 0l5.6 5.6c1 1 1 2.5 0 3.4L13 21"/><path d="M22 21H7"/><path d="m5 11 9 9"/></svg>',
        restore: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>',
        undo: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7v6h6"/><path d="M21 17a9 9 0 0 0-9-9 9 9 0 0 0-6 2.3L3 13"/></svg>',
        reset: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg>',
        brush: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9.06 11.9 8.07-8.06a2.85 2.85 0 1 1 4.03 4.03l-8.06 8.08"/><path d="M7.07 14.94c-1.66 0-3 1.35-3 3.02 0 1.33-2.5 1.52-2 2.02 1.08 1.1 2.49 2.02 4 2.02 2.2 0 4-1.8 4-4.04a3.01 3.01 0 0 0-3-3.02z"/></svg>',
        auto: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3Z"/></svg>',
    };

    const STYLES = `
        .labs-bg-panel { display:flex; flex-direction:column; gap:10px; padding:14px 16px; border-bottom:1px solid #e5e7eb; background:#fff; font-size:12px; color:#374151; }
        .labs-bg-title { margin:0; font-size:10px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#6b7280; }
        .labs-bg-modes { display:grid; grid-template-columns:1fr 1fr; gap:8px; }
        .labs-bg-mode { display:flex; align-items:center; justify-content:center; gap:6px; padding:8px; border:1px solid #d1d5db; border-radius:8px; background:#fff; color:#374151; font-weight:600; font-size:12px; cursor:pointer; }
        .labs-bg-mode svg { width:15px; height:15px; }
        .labs-bg-mode[data-mode="erase"][data-active="1"] { background:#dc2626; border-color:#dc2626; color:#fff; }
        .labs-bg-mode[data-mode="restore"][data-active="1"] { background:#2563eb; border-color:#2563eb; color:#fff; }
        .labs-bg-size { display:flex; align-items:center; gap:8px; color:#4b5563; }
        .labs-bg-size input { flex:1; accent-color:#111827; }
        .labs-bg-size-value { width:22px; text-align:right; font-variant-numeric:tabular-nums; }
        .labs-bg-open { display:flex; align-items:center; justify-content:center; gap:6px; padding:9px; border:0; border-radius:8px; background:#111827; color:#fff; font-size:12px; font-weight:600; cursor:pointer; }
        .labs-bg-open svg { width:15px; height:15px; }
        .labs-bg-auto { display:flex; align-items:center; justify-content:center; gap:6px; padding:9px; border:1px solid #d1d5db; border-radius:8px; background:#fff; color:#111827; font-size:12px; font-weight:600; cursor:pointer; }
        .labs-bg-auto svg { width:15px; height:15px; }
        .labs-bg-auto[disabled] { opacity:.6; cursor:progress; }
        .labs-bg-status { margin:0; font-size:11px; color:#6b7280; }
        .labs-brush-overlay { position:fixed; inset:0; z-index:9999; display:flex; align-items:center; justify-content:center; padding:16px; background:rgba(17,24,39,.7); }
        .labs-brush-card { display:flex; flex-direction:column; width:min(1040px,100%); max-height:94vh; overflow:hidden; border-radius:14px; background:#fff; box-shadow:0 25px 60px rgba(0,0,0,.35); }
        .labs-brush-head { display:flex; flex-wrap:wrap; align-items:center; gap:10px; padding:12px 16px; border-bottom:1px solid #e5e7eb; }
        .labs-brush-title { margin-right:auto; font-size:13px; font-weight:700; color:#111827; }
        .labs-brush-wrap { flex:1; overflow:auto; display:flex; align-items:center; justify-content:center; padding:16px; background-color:#f9fafb; background-image:repeating-conic-gradient(#eef0f3 0% 25%, #ffffff 0% 50%); background-size:18px 18px; }
        .labs-brush-canvas { max-width:100%; max-height:62vh; cursor:crosshair; touch-action:none; border-radius:4px; box-shadow:0 0 0 1px rgba(17,24,39,.12); }
        .labs-brush-foot { display:flex; align-items:center; gap:10px; padding:12px 16px; border-top:1px solid #e5e7eb; }
        .labs-brush-status { margin:0; margin-right:auto; font-size:12px; color:#6b7280; }
        .labs-btn { padding:8px 14px; border:1px solid #d1d5db; border-radius:8px; background:#fff; color:#374151; font-size:12px; font-weight:600; cursor:pointer; }
        .labs-btn-primary { border-color:#111827; background:#111827; color:#fff; }
        .labs-btn[disabled] { opacity:.5; cursor:not-allowed; }
        .labs-brush-tool { display:inline-flex; align-items:center; gap:6px; }
        .labs-brush-tool svg { width:15px; height:15px; }
        .labs-brush-mode[data-mode="erase"][data-active="1"] { background:#dc2626; border-color:#dc2626; color:#fff; }
        .labs-brush-mode[data-mode="restore"][data-active="1"] { background:#2563eb; border-color:#2563eb; color:#fff; }
        .labs-brush-size { display:flex; align-items:center; gap:6px; font-size:12px; color:#4b5563; }
        .labs-brush-size input { width:110px; accent-color:#111827; }
        @media (max-width:640px) { .labs-brush-head { gap:6px; } .labs-brush-title { width:100%; } }
    `;

    function injectStyles() {
        if (document.getElementById('labs-brush-styles')) {
            return;
        }

        const style = document.createElement('style');
        style.id = 'labs-brush-styles';
        style.textContent = STYLES;
        document.head.appendChild(style);
    }

    function findPanel(overlay) {
        return overlay.querySelector('[class*="lg:max-w-xs"]');
    }

    function editorFor(element) {
        const data = window.Alpine ? window.Alpine.$data(element) : null;

        return data && data.editor ? data.editor : null;
    }

    function injectPanel(overlay) {
        if (overlay.dataset.labsBrush === '1') {
            return;
        }

        const panel = findPanel(overlay);

        if (!panel) {
            return;
        }

        overlay.dataset.labsBrush = '1';
        injectStyles();

        const state = { mode: 'erase', size: 32 };
        const box = document.createElement('div');
        box.className = 'labs-bg-panel';
        box.innerHTML = `
            <p class="labs-bg-title">Editor background</p>
            <div class="labs-bg-modes">
                <button type="button" class="labs-bg-mode" data-mode="erase" data-active="1">${ICONS.erase}Hapus BG</button>
                <button type="button" class="labs-bg-mode" data-mode="restore" data-active="0">${ICONS.restore}Restore BG</button>
            </div>
            <label class="labs-bg-size">Kuas
                <input type="range" min="6" max="140" step="2" value="32" class="labs-bg-size-input">
                <span class="labs-bg-size-value">32</span>
            </label>
            <button type="button" class="labs-bg-auto">${ICONS.auto}Hapus BG otomatis</button>
            <button type="button" class="labs-bg-open">${ICONS.brush}Buka kuas</button>
            <p class="labs-bg-status">Klik "Hapus BG otomatis", atau rapikan manual dengan kuas.</p>`;

        panel.prepend(box);

        const status = box.querySelector('.labs-bg-status');
        const buttons = Array.from(box.querySelectorAll('.labs-bg-mode'));

        buttons.forEach((button) => {
            button.addEventListener('click', () => {
                state.mode = button.dataset.mode;
                buttons.forEach((item) => {
                    item.dataset.active = item === button ? '1' : '0';
                });
            });
        });

        const sizeInput = box.querySelector('.labs-bg-size-input');
        const sizeValue = box.querySelector('.labs-bg-size-value');

        sizeInput.addEventListener('input', () => {
            state.size = Number(sizeInput.value);
            sizeValue.textContent = sizeInput.value;
        });

        box.querySelector('.labs-bg-auto').addEventListener('click', async () => {
            const autoButton = box.querySelector('.labs-bg-auto');
            const editor = editorFor(overlay);

            if (!editor) {
                status.textContent = 'Editor gambar belum siap.';

                return;
            }

            if (!window.labsBgRemoveUrl) {
                status.textContent = 'Layanan hapus background tidak tersedia.';

                return;
            }

            let canvas = null;

            try {
                canvas = editor.getCroppedCanvas({ fillColor: 'transparent' });
            } catch (error) {
                canvas = null;
            }

            if (!canvas) {
                status.textContent = 'Gambar belum selesai dimuat.';

                return;
            }

            autoButton.disabled = true;
            status.textContent = 'Menghapus background...';

            try {
                const response = await fetch(window.labsBgRemoveUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.labsCsrf || '',
                    },
                    body: JSON.stringify({
                        image: canvas.toDataURL('image/png'),
                        tolerance: 32,
                    }),
                });

                if (! response.ok) {
                    throw new Error('Gagal menghapus background.');
                }

                const result = await response.json();

                if (! originalSources.has(editor)) {
                    originalSources.set(editor, cloneCanvas(canvas));
                }

                editor.replace(result.image);
                status.textContent = 'Background dihapus otomatis. Rapikan dengan kuas bila perlu.';
            } catch (error) {
                status.textContent = error.message;
            } finally {
                autoButton.disabled = false;
            }
        });

        box.querySelector('.labs-bg-open').addEventListener('click', () => {
            const editor = editorFor(overlay);

            if (!editor) {
                status.textContent = 'Editor gambar belum siap.';

                return;
            }

            openBrush({
                editor,
                mode: state.mode,
                size: state.size,
                container: overlay,
                original: originalSources.get(editor) || null,
                onSaved: (message) => {
                    status.textContent = message;
                },
            });
        });
    }

    function cloneCanvas(source) {
        const copy = document.createElement('canvas');
        copy.width = source.width;
        copy.height = source.height;
        copy.getContext('2d').drawImage(source, 0, 0);

        return copy;
    }

    function baseCanvas(editor) {
        try {
            const cropped = editor.getCroppedCanvas({
                fillColor: 'transparent',
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            if (cropped) {
                return cropped;
            }
        } catch (error) {
            // Fall back to the raw editor image below.
        }

        const image = editor.image;

        if (!image || !image.naturalWidth) {
            return null;
        }

        const canvas = document.createElement('canvas');
        canvas.width = image.naturalWidth;
        canvas.height = image.naturalHeight;
        canvas.getContext('2d').drawImage(image, 0, 0);

        return canvas;
    }

    function openBrush({ editor, mode, size, onSaved, container, original }) {
        const source = baseCanvas(editor);

        if (!source) {
            onSaved('Gambar belum siap. Coba lagi setelah gambar tampil.');

            return;
        }

        const scale = Math.min(1, 1400 / Math.max(source.width, source.height));
        const width = Math.max(1, Math.round(source.width * scale));
        const height = Math.max(1, Math.round(source.height * scale));
        const originalSource = original || originalSources.get(editor) || null;

        if (! originalSources.has(editor)) {
            originalSources.set(editor, cloneCanvas(source));
        }

        const state = { mode, size, drawing: false, last: null, history: [] };

        const overlay = document.createElement('div');
        overlay.className = 'labs-brush-overlay';
        overlay.innerHTML = `
            <div class="labs-brush-card" role="dialog" aria-modal="true" aria-label="Editor background">
                <div class="labs-brush-head">
                    <span class="labs-brush-title">Editor background</span>
                    <button type="button" class="labs-btn labs-brush-tool labs-brush-mode" data-mode="erase">${ICONS.erase}Hapus BG</button>
                    <button type="button" class="labs-btn labs-brush-tool labs-brush-mode" data-mode="restore">${ICONS.restore}Restore BG</button>
                    <label class="labs-brush-size">Kuas
                        <input type="range" min="6" max="140" step="2" value="${size}" class="labs-brush-size-input">
                        <span class="labs-brush-size-value">${size}</span>
                    </label>
                    <button type="button" class="labs-btn labs-brush-tool labs-brush-undo">${ICONS.undo}Undo</button>
                    <button type="button" class="labs-btn labs-brush-tool labs-brush-reset">${ICONS.reset}Reset</button>
                </div>
                <div class="labs-brush-wrap">
                    <canvas class="labs-brush-canvas" width="${width}" height="${height}"></canvas>
                </div>
                <div class="labs-brush-foot">
                    <p class="labs-brush-status">Drag pada gambar untuk menghapus background. Mode restore mengembalikan bagian yang terhapus.</p>
                    <button type="button" class="labs-btn labs-brush-cancel">Batal</button>
                    <button type="button" class="labs-btn labs-btn-primary labs-brush-apply">Terapkan</button>
                </div>
            </div>`;

        (container || document.body).appendChild(overlay);

        const canvas = overlay.querySelector('.labs-brush-canvas');
        const ctx = canvas.getContext('2d');
        const status = overlay.querySelector('.labs-brush-status');
        const modeButtons = Array.from(overlay.querySelectorAll('.labs-brush-mode'));
        const sizeInput = overlay.querySelector('.labs-brush-size-input');
        const sizeValue = overlay.querySelector('.labs-brush-size-value');
        const undoButton = overlay.querySelector('.labs-brush-undo');

        const baseline = document.createElement('canvas');
        baseline.width = width;
        baseline.height = height;
        baseline.getContext('2d').drawImage(source, 0, 0, width, height);
        ctx.drawImage(baseline, 0, 0);

        const restoreLayer = document.createElement('canvas');
        restoreLayer.width = width;
        restoreLayer.height = height;
        restoreLayer.getContext('2d').drawImage(originalSource || source, 0, 0, width, height);

        if (originalSource) {
            status.textContent = 'Mode Restore BG memakai gambar asli sebelum background dihapus otomatis.';
        }

        function pointAt(event) {
            const rect = canvas.getBoundingClientRect();

            return {
                x: (event.clientX - rect.left) * (canvas.width / Math.max(1, rect.width)),
                y: (event.clientY - rect.top) * (canvas.height / Math.max(1, rect.height)),
            };
        }

        function radiusAt() {
            const rect = canvas.getBoundingClientRect();

            return Math.max(2, (state.size / 2) * (canvas.width / Math.max(1, rect.width)));
        }

        function stamp(x, y, radius) {
            if (state.mode === 'restore') {
                ctx.save();
                ctx.beginPath();
                ctx.arc(x, y, radius, 0, Math.PI * 2);
                ctx.clip();
                ctx.drawImage(restoreLayer, 0, 0);
                ctx.restore();

                return;
            }

            ctx.save();
            ctx.globalCompositeOperation = 'destination-out';
            ctx.beginPath();
            ctx.arc(x, y, radius, 0, Math.PI * 2);
            ctx.fill();
            ctx.restore();
        }

        function stroke(from, to, radius) {
            const distance = Math.hypot(to.x - from.x, to.y - from.y);
            const steps = Math.max(1, Math.ceil(distance / Math.max(1, radius / 3)));

            for (let step = 0; step <= steps; step++) {
                const ratio = step / steps;
                stamp(
                    from.x + (to.x - from.x) * ratio,
                    from.y + (to.y - from.y) * ratio,
                    radius,
                );
            }
        }

        function syncModes() {
            modeButtons.forEach((button) => {
                button.dataset.active = button.dataset.mode === state.mode ? '1' : '0';
            });
        }

        function syncUndo() {
            undoButton.disabled = state.history.length === 0;
        }

        function snapshot() {
            const copy = document.createElement('canvas');
            copy.width = canvas.width;
            copy.height = canvas.height;
            copy.getContext('2d').drawImage(canvas, 0, 0);

            state.history.push(copy);

            if (state.history.length > 12) {
                state.history.shift();
            }

            syncUndo();
        }

        modeButtons.forEach((button) => {
            button.addEventListener('click', () => {
                state.mode = button.dataset.mode;
                syncModes();
            });
        });

        sizeInput.addEventListener('input', () => {
            state.size = Number(sizeInput.value);
            sizeValue.textContent = sizeInput.value;
        });

        undoButton.addEventListener('click', () => {
            const previous = state.history.pop();

            if (!previous) {
                return;
            }

            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(previous, 0, 0);
            syncUndo();
        });

        overlay.querySelector('.labs-brush-reset').addEventListener('click', () => {
            snapshot();
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(baseline, 0, 0);
            status.textContent = 'Gambar dikembalikan ke kondisi awal.';
        });

        canvas.addEventListener('pointerdown', (event) => {
            event.preventDefault();
            canvas.setPointerCapture(event.pointerId);
            state.drawing = true;
            snapshot();
            state.last = pointAt(event);
            stamp(state.last.x, state.last.y, radiusAt());
        });

        canvas.addEventListener('pointermove', (event) => {
            if (!state.drawing) {
                return;
            }

            const next = pointAt(event);
            stroke(state.last, next, radiusAt());
            state.last = next;
        });

        ['pointerup', 'pointercancel'].forEach((type) => {
            canvas.addEventListener(type, () => {
                state.drawing = false;
            });
        });

        window.addEventListener('pointerup', () => {
            state.drawing = false;
        });

        function close() {
            overlay.remove();
            document.removeEventListener('keydown', onKey, true);
        }

        function onKey(event) {
            if (event.key !== 'Escape') {
                return;
            }

            event.preventDefault();
            event.stopPropagation();
            close();
        }

        document.addEventListener('keydown', onKey, true);

        overlay.querySelector('.labs-brush-cancel').addEventListener('click', close);

        overlay.addEventListener('click', (event) => {
            if (event.target === overlay) {
                close();
            }
        });

        overlay.querySelector('.labs-brush-apply').addEventListener('click', () => {
            const dataUrl = canvas.toDataURL('image/webp', 0.92);

            editor.replace(dataUrl);
            close();
            onSaved('Background sudah diedit. Atur crop, lalu klik Save.');
        });

        syncModes();
        syncUndo();
    }

    function scan() {
        document.querySelectorAll('.fi-fo-file-upload [x-show="isEditorOpen"]').forEach((overlay) => {
            if (!overlay.classList.contains('fixed')) {
                return;
            }

            if (window.getComputedStyle(overlay).display === 'none') {
                return;
            }

            injectPanel(overlay);
        });
    }

    let queued = false;

    function scheduleScan() {
        if (queued) {
            return;
        }

        queued = true;

        window.requestAnimationFrame(() => {
            queued = false;
            scan();
        });
    }

    new MutationObserver(scheduleScan).observe(document.body, {
        subtree: true,
        childList: true,
        attributes: true,
        attributeFilter: ['style', 'class'],
    });

    document.addEventListener('DOMContentLoaded', scheduleScan);
    document.addEventListener('livewire:navigated', scheduleScan);
})();
