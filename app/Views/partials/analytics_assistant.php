<?php
/**
 * Analytics Assistant (the chat widget of the main dashboard) for any page.
 * It talks to the same endpoints (dashboard/assistantAsk, assistantHistory, assistantChat*), so chats and history are shared.
 *
 * Usage: <?= view('partials/analytics_assistant', ['examples' => ['Question one?', 'Question two?']]) ?>
 * Needs the Tailwind CDN config of the dashboard pages (primary / primary-dark / surface-*) and Material Symbols.
 */
$asstExamples = $examples ?? ['Who is currently on-site?', 'How many visitors are expected today?', 'Show active security alerts.'];
?>
<style>
        #analytics-assistant-panel {
            transition: width .2s ease, height .2s ease, inset .2s ease, border-radius .2s ease, transform .2s ease;
        }
        #analytics-assistant-panel.analytics-assistant-expanded {
            inset: 0.75rem !important;
            width: auto !important;
            height: auto !important;
            max-width: none !important;
            max-height: none !important;
            border-radius: 0.75rem !important;
        }
        #analytics-assistant-panel.analytics-assistant-minimized {
            transform: translateY(calc(100% + 2rem));
            pointer-events: none;
        }
        @media (max-width: 640px) {
            #analytics-assistant-launcher {
                left: 1rem;
                right: 1rem;
                width: auto;
                justify-content: center;
            }
            #analytics-assistant-panel {
                left: 0.75rem !important;
                right: 0.75rem !important;
                bottom: 0.75rem !important;
                width: auto !important;
                height: min(78vh, 620px) !important;
            }
        }
</style>

<!-- Analytics Assistant -->
<button id="analytics-assistant-launcher" type="button" onclick="openAnalyticsAssistant()" class="fixed bottom-6 right-6 z-[90] inline-flex items-center gap-3 rounded-full bg-primary hover:bg-primary-dark text-white px-5 py-3 shadow-2xl transition-all">
    <span class="size-8 rounded-full bg-white/15 flex items-center justify-center">
        <span class="material-symbols-outlined text-[20px]">monitoring</span>
    </span>
    <span class="text-sm font-bold">Analytics Assistant</span>
    <span class="size-3 rounded-full bg-white ring-4 ring-white/20"></span>
</button>

<section id="analytics-assistant-panel" class="hidden fixed bottom-6 right-6 z-[95] w-[min(420px,calc(100vw-2rem))] h-[min(640px,calc(100vh-2rem))] bg-surface-light dark:bg-surface-dark text-slate-900 dark:text-white rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col">
    <header class="h-16 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between px-4 flex-shrink-0">
        <div class="flex items-center gap-3 min-w-0">
            <span class="size-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-[20px]">monitoring</span>
            </span>
            <div class="min-w-0">
                <p class="text-sm font-bold leading-tight truncate text-slate-900 dark:text-white">Analytics Assistant</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-tight">Ask about VMS database records</p>
            </div>
        </div>
        <div class="flex items-center gap-1">
            <button type="button" onclick="minimizeAnalyticsAssistant()" class="size-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors" title="Minimize">
                <span class="material-symbols-outlined text-[18px]">remove</span>
            </button>
            <button type="button" onclick="toggleAnalyticsAssistantSize()" class="size-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors" title="Widen">
                <span id="analytics-assistant-size-icon" class="material-symbols-outlined text-[18px]">fullscreen</span>
            </button>
            <button type="button" onclick="closeAnalyticsAssistant()" class="size-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors" title="Close">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
    </header>

    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 flex items-start justify-between gap-4 flex-shrink-0">
        <div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Analytics Assistant</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Ask read-only questions across the VMS database.</p>
        </div>
        <span id="analytics-assistant-status" class="inline-flex items-center gap-2 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 border border-amber-100 dark:border-amber-800 px-3 py-2 text-xs font-semibold whitespace-nowrap">
            <span class="size-2 rounded-full bg-amber-400"></span>
            Ready
        </span>
    </div>

    <div class="flex-1 min-h-0 flex bg-slate-50 dark:bg-slate-950">
        <button id="analytics-history-toggle" type="button" onclick="toggleAnalyticsHistory()" class="hidden sm:flex w-9 bg-primary text-white items-center justify-center [writing-mode:vertical-rl] text-xs font-bold gap-2" title="Chat History">
            <span id="analytics-history-toggle-icon" class="material-symbols-outlined text-[18px] rotate-90">keyboard_double_arrow_right</span>
            Chat History
        </button>
        <aside id="analytics-history-panel" class="hidden w-72 flex-shrink-0 border-r border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 overflow-hidden">
            <div class="h-full flex flex-col">
                <div class="p-4 border-b border-slate-200 dark:border-slate-700 space-y-3">
                    <button type="button" onclick="newAnalyticsChat()" class="w-full h-10 rounded-lg bg-primary hover:bg-primary-dark text-white text-sm font-bold flex items-center justify-center gap-2 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        New Chat
                    </button>
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white">Chat History</h4>
                            <p class="text-xs text-slate-500 mt-0.5">This session only</p>
                        </div>
                        <button type="button" onclick="clearAnalyticsHistory()" class="text-[11px] font-semibold text-slate-400 hover:text-primary transition-colors">Clear</button>
                    </div>
                </div>
                <div id="analytics-history-list" class="flex-1 overflow-y-auto p-3 space-y-2">
                    <div id="analytics-history-empty" class="hidden rounded-lg border border-dashed border-slate-200 dark:border-slate-700 p-3 text-xs text-slate-400">
                        No chats yet.
                    </div>
                </div>
                <div id="analytics-history-menu" class="hidden fixed z-[10080] w-40 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-xl p-1">
                    <button type="button" onclick="renameAnalyticsChatFromMenu()" class="w-full flex items-center gap-2 px-3 py-2 rounded-md text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
                        <span class="material-symbols-outlined text-[18px]">edit_square</span>
                        Rename
                    </button>
                    <button type="button" onclick="deleteAnalyticsChatFromMenu()" class="w-full flex items-center gap-2 px-3 py-2 rounded-md text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                        Delete
                    </button>
                </div>
            </div>
        </aside>
        <div class="flex-1 min-w-0 min-h-0 flex flex-col">
            <div id="analytics-assistant-messages" class="flex-1 overflow-y-auto p-5 space-y-4">
                <div class="flex items-start gap-3">
                    <div class="size-9 rounded-full bg-primary flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[20px]">monitoring</span>
                    </div>
                    <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 text-sm text-slate-600 dark:text-slate-300 max-w-[90%] shadow-sm">
                        <p class="font-bold text-slate-900 dark:text-white mb-2">Hello! I&apos;m your Analytics Assistant</p>
                        <p>Ask me anything about VMS database records. Examples:</p>
                        <ul class="list-disc pl-5 mt-3 space-y-1 text-slate-500 dark:text-slate-400">
                            <?php foreach ($asstExamples as $ex): ?><li><?= esc($ex) ?></li><?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <form id="analytics-assistant-form" class="border-t border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 flex gap-3 flex-shrink-0" onsubmit="sendAnalyticsAssistantMessage(event)">
                <input id="analytics-assistant-input" type="text" autocomplete="off" maxlength="1200" placeholder="Ask anything about your data..." class="flex-1 min-w-0 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder:text-slate-400 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                <button id="analytics-assistant-send" type="submit" class="rounded-lg bg-primary hover:bg-primary-dark text-white px-5 py-3 text-sm font-bold flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                    Send
                    <span class="material-symbols-outlined text-[18px]">send</span>
                </button>
            </form>
            <p class="px-4 pb-4 -mt-2 bg-white dark:bg-slate-900 text-[11px] text-slate-400 flex-shrink-0">
                Smart AI can query VMS records in read-only mode.
            </p>
        </div>
    </div>
</section>

<script>
const ASST_BASE = '<?= rtrim(base_url(), '/') ?>';
const ASST_EXAMPLES = <?= json_encode(array_values($asstExamples)) ?>;
function asstEsc(s) { const d = document.createElement('div'); d.textContent = s == null ? '' : String(s); return d.innerHTML; }
let analyticsChats = [];
let activeAnalyticsChatId = '';
let analyticsHistoryMenuChatId = '';

function openAnalyticsAssistant() {
    const panel = document.getElementById('analytics-assistant-panel');
    const launcher = document.getElementById('analytics-assistant-launcher');
    panel.classList.remove('hidden', 'analytics-assistant-minimized');
    launcher.classList.add('hidden');
    setTimeout(() => {
        scrollAnalyticsToBottom();
        document.getElementById('analytics-assistant-input')?.focus();
    }, 80);
}

function minimizeAnalyticsAssistant() {
    const panel = document.getElementById('analytics-assistant-panel');
    const launcher = document.getElementById('analytics-assistant-launcher');
    panel.classList.add('analytics-assistant-minimized');
    launcher.classList.remove('hidden');
    setTimeout(() => panel.classList.add('hidden'), 180);
}

function closeAnalyticsAssistant() {
    const panel = document.getElementById('analytics-assistant-panel');
    const launcher = document.getElementById('analytics-assistant-launcher');
    panel.classList.add('hidden');
    panel.classList.remove('analytics-assistant-expanded', 'analytics-assistant-minimized');
    document.getElementById('analytics-assistant-size-icon').textContent = 'fullscreen';
    launcher.classList.remove('hidden');
}

function toggleAnalyticsAssistantSize() {
    const panel = document.getElementById('analytics-assistant-panel');
    const icon = document.getElementById('analytics-assistant-size-icon');
    const expanded = panel.classList.toggle('analytics-assistant-expanded');
    icon.textContent = expanded ? 'fullscreen_exit' : 'fullscreen';
}

function postAssistantAction(url, data = {}) {
    const body = new URLSearchParams();
    Object.keys(data).forEach(key => body.append(key, data[key]));
    return fetch(ASST_BASE + url, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: body.toString()
    }).then(r => r.json());
}

function loadAnalyticsChats() {
    return fetch(ASST_BASE + '/dashboard/assistantHistory', {
        headers: {'X-Requested-With': 'XMLHttpRequest'}
    })
    .then(r => r.json())
    .then(d => {
        analyticsChats = d.success && Array.isArray(d.chats) ? d.chats : [];
        activeAnalyticsChatId = analyticsChats[0]?.id || '';
        if (!activeAnalyticsChatId) createAnalyticsChat(false);
        renderActiveAnalyticsChat();
        renderAnalyticsHistory();
    })
    .catch(() => {
        if (!activeAnalyticsChatId) createAnalyticsChat(false);
        renderActiveAnalyticsChat();
        renderAnalyticsHistory();
    });
}

function analyticsWelcomeHtml() {
    return '<div class="flex items-start gap-3">'
        + '<div class="size-9 rounded-full bg-primary flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-[20px]">monitoring</span></div>'
        + '<div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 text-sm text-slate-600 dark:text-slate-300 max-w-[90%] shadow-sm">'
        + '<p class="font-bold text-slate-900 dark:text-white mb-2">Hello! I&apos;m your Analytics Assistant</p>'
        + '<p>Ask me anything about VMS database records. Examples:</p>'
        + '<ul class="list-disc pl-5 mt-3 space-y-1 text-slate-500 dark:text-slate-400">'
        + ASST_EXAMPLES.map(function (x) { return '<li>' + asstEsc(x) + '</li>'; }).join('')
        + '</ul></div></div>';
}

function ensureActiveAnalyticsChat() {
    if (activeAnalyticsChatId && analyticsChats.some(chat => chat.id === activeAnalyticsChatId)) return;
    createAnalyticsChat(false);
}

function createAnalyticsChat(render = true) {
    const now = new Date();
    const chat = {
        id: 'chat-' + now.getTime() + '-' + Math.random().toString(16).slice(2),
        title: 'New Chat',
        date: now.toLocaleDateString('en-US', {month: 'short', day: 'numeric'}),
        messages: []
    };
    analyticsChats.unshift(chat);
    activeAnalyticsChatId = chat.id;
    if (render) {
        renderActiveAnalyticsChat();
        renderAnalyticsHistory();
    }
}

function activeAnalyticsChat() {
    ensureActiveAnalyticsChat();
    return analyticsChats.find(chat => chat.id === activeAnalyticsChatId);
}

function newAnalyticsChat() {
    closeAnalyticsHistoryMenu();
    postAssistantAction('/dashboard/assistantChatCreate').then(d => {
        if (d.success && d.chat) {
            analyticsChats.unshift(d.chat);
            activeAnalyticsChatId = d.chat.id;
        } else {
            createAnalyticsChat(false);
        }
        renderActiveAnalyticsChat();
        renderAnalyticsHistory();
        document.getElementById('analytics-assistant-input')?.focus();
    });
}

function toggleAnalyticsHistory() {
    const panel = document.getElementById('analytics-history-panel');
    const icon = document.getElementById('analytics-history-toggle-icon');
    const isOpening = panel.classList.contains('hidden');
    panel.classList.toggle('hidden', !isOpening);
    icon.textContent = isOpening ? 'keyboard_double_arrow_left' : 'keyboard_double_arrow_right';
    if (isOpening) renderAnalyticsHistory();
    else closeAnalyticsHistoryMenu();
}

function updateActiveChatTitle(question) {
    const chat = activeAnalyticsChat();
    if (!chat || (chat.title !== 'New Chat' && chat.messages.filter(m => m.role === 'user').length > 1)) return;
    chat.title = question.length > 34 ? question.slice(0, 31) + '...' : question;
    renderAnalyticsHistory();
}

function renderAnalyticsHistory() {
    const list = document.getElementById('analytics-history-list');
    const empty = document.getElementById('analytics-history-empty');
    if (!list) return;

    list.querySelectorAll('[data-history-entry]').forEach(el => el.remove());
    if (empty) empty.classList.toggle('hidden', analyticsChats.length > 0);

    analyticsChats.forEach((chat) => {
        const item = document.createElement('div');
        item.dataset.historyEntry = '1';
        item.className = 'group relative rounded-lg border transition-colors '
            + (chat.id === activeAnalyticsChatId
                ? 'border-primary/30 bg-blue-50 dark:bg-slate-800'
                : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 hover:border-primary/40');
        item.innerHTML = '<button type="button" class="w-full text-left p-3 pr-10" onclick="switchAnalyticsChat(\'' + asstEsc(chat.id) + '\')">'
            + '<p class="text-xs font-bold text-slate-900 dark:text-white truncate">' + asstEsc(chat.title) + '</p>'
            + '<p class="text-[11px] text-slate-400 mt-1">' + asstEsc(chat.date) + '</p>'
            + '</button>'
            + '<button type="button" class="absolute top-2 right-2 size-7 rounded-md flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-white dark:hover:bg-slate-700" onclick="openAnalyticsHistoryMenu(event, \'' + asstEsc(chat.id) + '\')" title="Chat options">'
            + '<span class="material-symbols-outlined text-[18px]">more_vert</span>'
            + '</button>';
        list.appendChild(item);
    });
}

function clearAnalyticsHistory() {
    closeAnalyticsHistoryMenu();
    if (!confirm('Are you sure you want to clear all chat history? This action cannot be undone.')) return;
    postAssistantAction('/dashboard/assistantChatClear').finally(() => {
        analyticsChats = [];
        activeAnalyticsChatId = '';
        createAnalyticsChat(true);
    });
}

function switchAnalyticsChat(chatId) {
    if (!analyticsChats.some(chat => chat.id === chatId)) return;
    activeAnalyticsChatId = chatId;
    closeAnalyticsHistoryMenu();
    renderActiveAnalyticsChat();
    renderAnalyticsHistory();
    scrollAnalyticsToBottom();
}

function openAnalyticsHistoryMenu(event, chatId) {
    event.stopPropagation();
    const menu = document.getElementById('analytics-history-menu');
    const rect = event.currentTarget.getBoundingClientRect();
    analyticsHistoryMenuChatId = chatId;
    menu.style.left = Math.min(rect.right + 4, window.innerWidth - 176) + 'px';
    menu.style.top = rect.top + 'px';
    menu.classList.remove('hidden');
}

function closeAnalyticsHistoryMenu() {
    const menu = document.getElementById('analytics-history-menu');
    if (menu) menu.classList.add('hidden');
    analyticsHistoryMenuChatId = '';
}

function renameAnalyticsChatFromMenu() {
    const chat = analyticsChats.find(item => item.id === analyticsHistoryMenuChatId);
    if (!chat) return;
    const next = prompt('Rename chat', chat.title);
    if (next === null) return;
    const title = next.trim();
    if (title) {
        chat.title = title.length > 40 ? title.slice(0, 37) + '...' : title;
        postAssistantAction('/dashboard/assistantChatRename', {chat_id: chat.id, title: chat.title});
    }
    closeAnalyticsHistoryMenu();
    renderAnalyticsHistory();
}

function deleteAnalyticsChatFromMenu() {
    const chatId = analyticsHistoryMenuChatId;
    if (!chatId) return;
    postAssistantAction('/dashboard/assistantChatDelete', {chat_id: chatId});
    analyticsChats = analyticsChats.filter(chat => chat.id !== chatId);
    closeAnalyticsHistoryMenu();
    if (activeAnalyticsChatId === chatId) {
        activeAnalyticsChatId = analyticsChats[0]?.id || '';
        if (!activeAnalyticsChatId) createAnalyticsChat(false);
        renderActiveAnalyticsChat();
    }
    renderAnalyticsHistory();
}

document.addEventListener('click', function (event) {
    const target = event.target;
    if (target && target.closest && (target.closest('#analytics-history-menu') || target.closest('[onclick^="openAnalyticsHistoryMenu"]'))) {
        return;
    }
    closeAnalyticsHistoryMenu();
});

function scrollAnalyticsToBottom() {
    const wrap = document.getElementById('analytics-assistant-messages');
    if (!wrap) return;
    requestAnimationFrame(() => requestAnimationFrame(() => { wrap.scrollTop = wrap.scrollHeight; }));
}

function renderActiveAnalyticsChat() {
    const wrap = document.getElementById('analytics-assistant-messages');
    const chat = activeAnalyticsChat();
    wrap.innerHTML = analyticsWelcomeHtml();
    chat.messages.forEach(message => renderAssistantMessage(message.role, message.text, false));
    scrollAnalyticsToBottom();
}

function renderAssistantMessage(role, text, isLoading = false) {
    const wrap = document.getElementById('analytics-assistant-messages');
    const row = document.createElement('div');
    const rowId = 'assistant-row-' + Date.now() + '-' + Math.random().toString(16).slice(2);
    row.dataset.assistantRowId = rowId;
    row.className = role === 'user' ? 'flex items-start justify-end gap-3' : 'flex items-start gap-3';

    const bubble = document.createElement('div');
    bubble.className = role === 'user'
        ? 'rounded-lg bg-primary text-white p-4 text-sm max-w-[88%] shadow-sm'
        : 'rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-4 text-sm text-slate-600 dark:text-slate-300 max-w-[90%] shadow-sm';
    bubble.innerHTML = isLoading
        ? '<span class="inline-flex items-center gap-2 text-slate-500 dark:text-slate-400"><span class="size-2 rounded-full bg-primary animate-pulse"></span>Thinking...</span>'
        : assistantFormatText(text);

    if (role === 'user') {
        row.appendChild(bubble);
    } else {
        const avatar = document.createElement('div');
        avatar.className = 'size-9 rounded-full bg-primary flex items-center justify-center flex-shrink-0';
        avatar.innerHTML = '<span class="material-symbols-outlined text-[20px]">monitoring</span>';
        row.appendChild(avatar);
        row.appendChild(bubble);
    }

    if (isLoading) row.dataset.loading = '1';
    wrap.appendChild(row);
    scrollAnalyticsToBottom();
    return {row, rowId};
}

function appendAssistantMessage(role, text, isLoading = false) {
    const rendered = renderAssistantMessage(role, text, isLoading);
    if (!isLoading) {
        activeAnalyticsChat().messages.push({role, text});
    }
    return rendered;
}

function assistantInlineFormat(text) {
    return asstEsc(text).replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
}

function assistantIsMarkdownTableStart(lines, index) {
    if (index + 1 >= lines.length) return false;
    const header = lines[index] || '';
    const separator = lines[index + 1] || '';
    return /^\s*\|.*\|\s*$/.test(header)
        && /^\s*\|?\s*:?-{3,}:?\s*(\|\s*:?-{3,}:?\s*)+\|?\s*$/.test(separator);
}

function assistantRenderMarkdownTable(tableLines) {
    const rows = tableLines.map(line => line.trim().replace(/^\|/, '').replace(/\|$/, '').split('|').map(cell => assistantInlineFormat(cell.trim())));
    const headers = rows[0] || [];
    const bodyRows = rows.slice(2);
    let html = '<div class="my-2 overflow-x-auto rounded-lg border border-slate-200 dark:border-slate-700">';
    html += '<table class="min-w-full text-left text-xs">';
    html += '<thead class="bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200"><tr>';
    headers.forEach(header => { html += '<th class="px-3 py-2 font-bold">' + header + '</th>'; });
    html += '</tr></thead><tbody class="divide-y divide-slate-100 dark:divide-slate-700">';
    bodyRows.forEach(row => {
        html += '<tr>';
        headers.forEach((_, idx) => { html += '<td class="px-3 py-2 align-top">' + (row[idx] || '') + '</td>'; });
        html += '</tr>';
    });
    html += '</tbody></table></div>';
    return html;
}

function assistantFormatText(text) {
    const lines = String(text || '').split(/\r?\n/);
    const parts = [];

    for (let i = 0; i < lines.length; i++) {
        if (assistantIsMarkdownTableStart(lines, i)) {
            const tableLines = [lines[i], lines[i + 1]];
            i += 2;
            while (i < lines.length && /^\s*\|.*\|\s*$/.test(lines[i])) {
                tableLines.push(lines[i]);
                i++;
            }
            i--;
            parts.push(assistantRenderMarkdownTable(tableLines));
            continue;
        }

        parts.push(assistantInlineFormat(lines[i]));
    }

    return parts.join('<br>');
}

function setAssistantBusy(isBusy) {
    const input = document.getElementById('analytics-assistant-input');
    const send = document.getElementById('analytics-assistant-send');
    const status = document.getElementById('analytics-assistant-status');
    input.disabled = isBusy;
    send.disabled = isBusy;
    status.className = isBusy
        ? 'inline-flex items-center gap-2 rounded-lg bg-primary/10 text-primary border border-primary/20 px-3 py-2 text-xs font-semibold whitespace-nowrap'
        : 'inline-flex items-center gap-2 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 border border-amber-100 dark:border-amber-800 px-3 py-2 text-xs font-semibold whitespace-nowrap';
    status.innerHTML = isBusy
        ? '<span class="size-2 rounded-full bg-primary animate-pulse"></span> Thinking'
        : '<span class="size-2 rounded-full bg-amber-400"></span> Ready';
}

function sendAnalyticsAssistantMessage(event) {
    event.preventDefault();
    const input = document.getElementById('analytics-assistant-input');
    const message = input.value.trim();
    if (!message) return;

    const chat = activeAnalyticsChat();
    const history = chat.messages.slice(-10).map(item => ({
        role: item.role,
        content: item.text
    }));

    appendAssistantMessage('user', message);
    updateActiveChatTitle(message);
    input.value = '';
    setAssistantBusy(true);
    const loading = appendAssistantMessage('assistant', '', true);

    fetch(ASST_BASE + '/dashboard/assistantAsk', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: 'message=' + encodeURIComponent(message) + '&chat_id=' + encodeURIComponent(chat.id) + '&history=' + encodeURIComponent(JSON.stringify(history))
    })
    .then(r => r.json())
    .then(d => {
        loading.row.remove();
        if (!d.success) {
            if (d.chat_id) activeAnalyticsChatId = String(d.chat_id);
            appendAssistantMessage('assistant', d.message || 'Analytics Assistant is unavailable.');
            return;
        }
        if (d.chat_id) {
            chat.id = String(d.chat_id);
            activeAnalyticsChatId = String(d.chat_id);
        }
        if (d.title) {
            chat.title = d.title;
            renderAnalyticsHistory();
        }
        appendAssistantMessage('assistant', d.answer || 'No answer was returned.');
    })
    .catch(() => {
        loading.row.remove();
        appendAssistantMessage('assistant', 'I could not reach the Analytics Assistant service. Please try again.');
    })
    .finally(() => {
        setAssistantBusy(false);
        input.focus();
    });
}

document.addEventListener('DOMContentLoaded', function () { loadAnalyticsChats(); });
</script>
