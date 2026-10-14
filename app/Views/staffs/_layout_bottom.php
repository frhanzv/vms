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
        // Upload File popup (Staff List / Closed List): send the Excel with fetch()
        // so the page never navigates away. Success -> close the popup and reload
        // the list (the server leaves the "N imported" message for the reload).
        // Failure -> keep the popup open and list the errors inside it.
        document.addEventListener('submit', function (e) {
            const form = e.target.closest('form[data-staff-import]');
            if (!form) return;
            e.preventDefault();

            const box = form.querySelector('[data-import-result]');
            const btn = form.querySelector('[data-import-submit]');
            const show = function (ok, message, errors) {
                box.className = 'mt-3 max-h-48 overflow-y-auto rounded border px-3 py-2 text-xs ' +
                    (ok ? 'border-green-200 bg-green-50 text-green-800' : 'border-red-200 bg-red-50 text-red-700');
                box.innerHTML = '';
                const p = document.createElement('p');
                p.className = 'font-semibold';
                p.textContent = message;
                box.appendChild(p);
                if (errors && errors.length) {
                    const ul = document.createElement('ul');
                    ul.className = 'list-disc pl-4 mt-1 space-y-0.5';
                    errors.forEach(function (err) { const li = document.createElement('li'); li.textContent = err; ul.appendChild(li); });
                    box.appendChild(ul);
                }
            };

            const original = btn ? btn.innerHTML : '';
            if (btn) { btn.disabled = true; btn.innerHTML = 'Importing…'; }
            box.classList.add('hidden');

            fetch(form.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: new FormData(form),
                credentials: 'same-origin',
            })
                .then(function (r) {
                    const type = r.headers.get('Content-Type') || '';
                    if (!type.includes('application/json')) {
                        // e.g. the login page after the session expired, or a PHP error page.
                        throw new Error(r.redirected ? 'session' : 'http_' + r.status);
                    }
                    return r.json();
                })
                .then(function (d) {
                    if (d.success) {
                        show(true, d.message, d.errors);
                        const modal = form.closest('#uploadModal');
                        if (modal) modal.classList.add('hidden');
                        window.location.reload();
                        return;
                    }
                    show(false, d.message || 'Import failed.', d.errors);
                    if (btn) { btn.disabled = false; btn.innerHTML = original; }
                })
                .catch(function (err) {
                    show(false, err.message === 'session'
                        ? 'Your session has expired. Please log in again, then import the file.'
                        : 'The server could not process the file (' + err.message.replace('http_', 'HTTP ') + '). Check writable/logs for details.');
                    if (btn) { btn.disabled = false; btn.innerHTML = original; }
                });
        });
        // Choosing another file clears the previous result.
        document.addEventListener('change', function (e) {
            const form = e.target.closest && e.target.closest('form[data-staff-import]');
            if (form && e.target.type === 'file') form.querySelector('[data-import-result]').classList.add('hidden');
        });

        document.querySelectorAll('[data-per-page]').forEach(sel => sel.addEventListener('change', function () {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', this.value);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }));
    </script>
