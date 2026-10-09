        </div>
    </main>

    <script>
        // Shared JSON POST helper for every staff pipeline page.
        window.staffPost = function (url, payload, btn) {
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            let original = null;
            if (btn) { btn.disabled = true; original = btn.innerHTML; btn.innerHTML = 'Please wait…'; }
            const isForm = payload instanceof FormData;
            return fetch(url, {
                method: 'POST',
                headers: Object.assign({ 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
                    isForm ? {} : { 'Content-Type': 'application/json' }),
                body: isForm ? payload : JSON.stringify(payload || {}),
            })
                .then(r => { if (!r.ok) throw new Error('http_' + r.status); return r.json(); })
                .then(data => {
                    if (btn) { btn.disabled = false; btn.innerHTML = original; }
                    return data;
                })
                .catch(err => {
                    if (btn) { btn.disabled = false; btn.innerHTML = original; }
                    alert(String(err.message).startsWith('http_')
                        ? 'Something went wrong on the server (' + err.message.replace('http_', '') + '). If this keeps happening, check that all migrations have been run.'
                        : 'Could not reach the server. Please check your connection and try again.');
                    return { success: false, message: null };
                });
        };
        window.staffPostReload = function (url, payload, btn) {
            return staffPost(url, payload, btn).then(d => {
                if (d.message) alert(d.message);
                if (d.success) location.reload();
                return d;
            });
        };
        document.querySelectorAll('[data-per-page]').forEach(sel => sel.addEventListener('change', function () {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', this.value);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }));
    </script>
