const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

function setMessage(text, isError = false) {
    const el = document.getElementById('hotel-message');
    if (!el) return;
    el.textContent = text;
    el.classList.toggle('hotel-message--error', isError);
}

async function requestJson(url, options = {}) {
    const headers = {
        Accept: 'application/json',
        'X-CSRF-TOKEN': csrf,
        'X-Requested-With': 'XMLHttpRequest',
        ...(options.headers || {}),
    };
    const res = await fetch(url, { ...options, headers });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
        const msg = data.message || res.statusText || 'Request failed';
        throw new Error(msg);
    }
    return data;
}

function renderRows(rows) {
    const root = document.getElementById('hotel-rows');
    if (!root) return;
    root.replaceChildren();
    rows.forEach((row) => {
        const rowEl = document.createElement('div');
        rowEl.className = 'hotel-row';
        rowEl.dataset.floor = String(row.floor);
        row.rooms.forEach((cell) => {
            const div = document.createElement('button');
            div.type = 'button';
            div.className = `hotel-cell hotel-cell--${cell.status}`;
            div.textContent = String(cell.number);
            div.title = `Room ${cell.number} · ${cell.status}`;
            div.dataset.number = String(cell.number);
            rowEl.appendChild(div);
        });
        root.appendChild(rowEl);
    });
}

async function refresh() {
    const data = await requestJson('/hotel/state');
    renderRows(data.rooms || []);
}

async function book() {
    const input = document.getElementById('room-count');
    const n = Number.parseInt(String(input?.value ?? '1'), 10);
    setMessage('');
    try {
        const data = await requestJson('/hotel/book', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ n }),
        });
        renderRows(data.rooms || []);
        const rooms = (data.booked || []).join(', ');
        setMessage(`Booked ${data.booked?.length ?? n} room(s): ${rooms}. Travel diameter ≈ ${data.diameter ?? 0} min.`);
    } catch (e) {
        setMessage(e.message || 'Could not book', true);
    }
}

async function reset() {
    setMessage('');
    try {
        const data = await requestJson('/hotel/reset', { method: 'POST' });
        renderRows(data.rooms || []);
        setMessage('All rooms cleared.');
    } catch (e) {
        setMessage(e.message || 'Reset failed', true);
    }
}

async function randomize() {
    setMessage('');
    try {
        const data = await requestJson('/hotel/random', { method: 'POST' });
        renderRows(data.rooms || []);
        setMessage('Random occupancy applied.');
    } catch (e) {
        setMessage(e.message || 'Random failed', true);
    }
}

document.getElementById('btn-book')?.addEventListener('click', () => {
    void book();
});
document.getElementById('btn-reset')?.addEventListener('click', () => {
    void reset();
});
document.getElementById('btn-random')?.addEventListener('click', () => {
    void randomize();
});

void refresh().catch((e) => setMessage(e.message || 'Load failed', true));
