(() => {
    const root = document.getElementById('availability-app');
    const plan = JSON.parse(document.getElementById('availability-data').textContent);
    const el = (id) => document.getElementById(id);
    plan.availability ||= [];
    plan.availability_submitted ||= [];
    let windows = [];

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>'"]/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[char]));
    }

    function inputDateTime(value) {
        const date = value ? new Date(value) : null;
        if (!date || !Number.isFinite(date.getTime())) return '';
        return new Date(date.getTime() - date.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
    }

    function formatClock(date) {
        return new Intl.DateTimeFormat('en-GB', { weekday: 'short', day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit', hour12: false }).format(date);
    }

    function raceWindow() {
        const start = new Date(`${plan.race_date}T${plan.start_time}:00`);
        const end = new Date(start.getTime() + Number(plan.race_mins || 0) * 60000);
        return { from: inputDateTime(start), to: inputDateTime(end) };
    }

    function selectedDriver() { return el('driver-select').value; }

    function loadDriver() {
        const driver = selectedDriver();
        windows = plan.availability.filter((window) => window.driver === driver).map((window) => ({ from: inputDateTime(window.from), to: inputDateTime(window.to) }));
        renderWindows();
        const submitted = plan.availability_submitted.includes(driver);
        el('response-state').textContent = submitted ? `Saved response · ${windows.length ? `${windows.length} available window${windows.length === 1 ? '' : 's'}` : 'not available for this race'}` : 'No response submitted yet';
        el('response-state').className = `response-state ${submitted ? 'saved' : ''}`;
        setMessage('Your response is shared with the race planner after you save.');
    }

    function renderWindows() {
        el('window-list').innerHTML = windows.length ? windows.map((window, index) => `
            <div class="window-row">
                <label>Available from<input type="datetime-local" data-window-index="${index}" data-window-field="from" value="${escapeHtml(window.from)}"></label>
                <label>Available until<input type="datetime-local" data-window-index="${index}" data-window-field="to" value="${escapeHtml(window.to)}"></label>
                <button class="remove-window" type="button" data-remove-window="${index}" aria-label="Remove this time window">&times;</button>
            </div>`).join('') : '<div class="empty-windows">No available time windows. Saving this response tells the planner that you cannot drive this race.</div>';
    }

    function setMessage(message, type = '') {
        el('save-message').textContent = message;
        el('save-message').className = `save-message ${type}`.trim();
    }

    async function save() {
        const mapped = windows.map((window) => {
            const from = new Date(window.from);
            const to = new Date(window.to);
            if (!window.from || !window.to || !Number.isFinite(from.getTime()) || !Number.isFinite(to.getTime()) || to <= from) {
                throw new Error('Check that every window has a valid start and later finish time.');
            }
            return { from: from.toISOString(), to: to.toISOString(), from_label: formatClock(from), to_label: formatClock(to) };
        });
        const button = el('save-availability');
        button.disabled = true;
        setMessage('Saving your response...');
        try {
            const response = await fetch(root.dataset.saveUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ driver: selectedDriver(), windows: mapped }),
            });
            const result = await response.json().catch(() => ({}));
            if (!response.ok) throw new Error(result.message || 'Your response could not be saved.');
            plan.availability = result.availability || [];
            plan.availability_submitted = result.availability_submitted || [];
            loadDriver();
            setMessage(result.message || 'Your availability has been saved.', 'success');
        } finally {
            button.disabled = false;
        }
    }

    el('driver-select').innerHTML = (plan.drivers || []).map((driver) => `<option value="${escapeHtml(driver)}">${escapeHtml(driver)}</option>`).join('');
    el('driver-select').addEventListener('change', loadDriver);
    el('window-list').addEventListener('input', (event) => {
        const index = Number(event.target.dataset.windowIndex);
        const field = event.target.dataset.windowField;
        if (Number.isInteger(index) && field && windows[index]) windows[index][field] = event.target.value;
    });
    el('window-list').addEventListener('click', (event) => {
        const index = Number(event.target.dataset.removeWindow);
        if (!Number.isInteger(index)) return;
        windows.splice(index, 1); renderWindows();
    });
    el('whole-race').addEventListener('click', () => { windows = [raceWindow()]; renderWindows(); });
    el('add-window').addEventListener('click', () => { if (windows.length < 12) { windows.push(raceWindow()); renderWindows(); } });
    el('unavailable').addEventListener('click', () => { windows = []; renderWindows(); });
    el('save-availability').addEventListener('click', async () => { try { await save(); } catch (error) { setMessage(error.message, 'error'); } });
    loadDriver();
})();
