<div id="center-notify-stack" class="pointer-events-none fixed inset-0 z-[120] flex items-center justify-center p-4"></div>

<script>
    (function () {
        let notifyTimer = null;
        let fadeTimer = null;

        function escapeHtml(str) {
            if (!str) return '';
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        window.showCenterNotice = function (options) {
            const container = document.getElementById('center-notify-stack');
            if (!container) return;

            if (notifyTimer) clearTimeout(notifyTimer);
            if (fadeTimer) clearTimeout(fadeTimer);

            const title = typeof options === 'string' ? options : (options.title || options.productName || 'Produk');
            const target = options.target || 'wishlist';
            const action = options.action || 'add';

            const safeTitle = escapeHtml(title);
            let messageHtml = '';

            if (options.customMessage) {
                messageHtml = options.customMessage;
            } else if (target === 'cart') {
                messageHtml = `&ldquo;<span class="font-semibold text-slate-900">${safeTitle}</span>&rdquo; has been added to your &ldquo;<span class="font-semibold text-slate-900">Shopping Cart</span>&rdquo;!`;
            } else if (target === 'compare') {
                if (action === 'remove') {
                    messageHtml = `&ldquo;<span class="font-semibold text-slate-900">${safeTitle}</span>&rdquo; has been removed from your &ldquo;<span class="font-semibold text-slate-900">Compare</span>&rdquo; list!`;
                } else {
                    messageHtml = `&ldquo;<span class="font-semibold text-slate-900">${safeTitle}</span>&rdquo; has been added to your &ldquo;<span class="font-semibold text-slate-900">Compare</span>&rdquo; list!`;
                }
            } else {
                if (action === 'remove') {
                    messageHtml = `&ldquo;<span class="font-semibold text-slate-900">${safeTitle}</span>&rdquo; has been removed from your &ldquo;<span class="font-semibold text-slate-900">My wishlist</span>&rdquo; list!`;
                } else {
                    messageHtml = `&ldquo;<span class="font-semibold text-slate-900">${safeTitle}</span>&rdquo; has been added to your &ldquo;<span class="font-semibold text-slate-900">My wishlist</span>&rdquo; list!`;
                }
            }

            container.innerHTML = `
                <div id="center-notify-card"
                     class="pointer-events-auto transform rounded-2xl border border-slate-200/90 bg-white/95 px-8 py-5 text-center shadow-[0_22px_60px_-15px_rgba(0,0,0,0.22)] backdrop-blur-md transition-all duration-300 ease-out max-w-[min(480px,calc(100vw-2rem))] opacity-0 scale-95 translate-y-1">
                    <p class="text-sm sm:text-[15px] font-normal leading-relaxed text-slate-800">
                        ${messageHtml}
                    </p>
                </div>
            `;

            const card = document.getElementById('center-notify-card');
            if (!card) return;

            // Trigger enter transition
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    card.classList.remove('opacity-0', 'scale-95', 'translate-y-1');
                    card.classList.add('opacity-100', 'scale-100', 'translate-y-0');
                });
            });

            // Smooth fade-out before removing
            fadeTimer = setTimeout(() => {
                card.style.transition = 'opacity 500ms ease-out, transform 500ms ease-out';
                card.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
                card.classList.add('opacity-0', 'scale-95', '-translate-y-1');

                notifyTimer = setTimeout(() => {
                    container.innerHTML = '';
                }, 520);
            }, 2400);
        };
    })();
</script>
