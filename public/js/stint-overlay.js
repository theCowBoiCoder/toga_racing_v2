(() => {
    const root = document.getElementById('overlay');
    const endpoint = root.dataset.endpoint;
    const preview = new URLSearchParams(location.search).get('preview') === '1';
    let plan;
    let lastUpdated;
    const el = (id) => document.getElementById(id);

    async function refresh() {
        try {
            const response = await fetch(endpoint, { headers: { Accept: 'application/json' }, cache: 'no-store' });
            if (!response.ok) throw new Error('Plan unavailable');
            const payload = await response.json();
            plan = payload.plan; lastUpdated = payload.updated_at; render();
            el('sync-state').textContent = 'LIVE · AUTO REFRESH';
        } catch (_) {
            el('sync-state').textContent = 'PLAN OFFLINE';
        }
    }

    function render() {
        if (!plan?.schedule?.length) return;
        const now = preview ? new Date(new Date(plan.schedule[0].start).getTime() + 10 * 60000) : new Date();
        const first = new Date(plan.schedule[0].start);
        const last = new Date(plan.schedule.at(-1).end);
        let currentIndex = plan.schedule.findIndex((stint) => now >= new Date(stint.start) && now < new Date(stint.end));
        let label = 'CURRENT DRIVER';
        if (currentIndex < 0 && now < first) { currentIndex = 0; label = 'FIRST DRIVER'; }
        else if (currentIndex < 0 && now > last) { currentIndex = plan.schedule.length - 1; label = 'FINAL DRIVER'; }
        else if (currentIndex < 0) { currentIndex = Math.max(0, plan.schedule.findIndex((stint) => now < new Date(stint.start))); label = 'NEXT DRIVER · PIT WINDOW'; }

        const stint = plan.schedule[currentIndex];
        el('overlay-event').textContent = plan.event;
        el('overlay-meta').textContent = `${plan.sim} · ${plan.car} · ${plan.track}`;
        el('current-index').textContent = String(stint.stint).padStart(2, '0');
        el('current-label').textContent = label;
        el('current-driver').textContent = stint.driver || 'DRIVER TBC';
        el('current-time').textContent = `${stint.start_label || clock(stint.start)} – ${stint.end_label || clock(stint.end)}${stint.standby ? ` · Stand-by ${stint.standby}` : ''}`;
        el('current-laps').textContent = stint.laps;
        el('current-fuel').textContent = `${number(stint.start_target)} ${plan.fuel_unit}`;

        const upcoming = plan.schedule.slice(Math.min(currentIndex + 1, plan.schedule.length), currentIndex + 6);
        el('upcoming-stints').innerHTML = upcoming.length ? upcoming.map((item) => `<div class="upcoming-row"><span>${String(item.stint).padStart(2, '0')}</span><b>${escapeHtml(item.driver || 'TBC')}</b><i>${escapeHtml(item.start_label || clock(item.start))}</i></div>`).join('') : '<div class="upcoming-row"><b>Race plan complete</b></div>';

        if (now < first) { el('clock-label').textContent = 'STARTS IN'; el('race-clock').textContent = duration(first - now); }
        else if (now <= last) { el('clock-label').textContent = 'RACE TIME LEFT'; el('race-clock').textContent = duration(last - now); }
        else { el('clock-label').textContent = 'RACE STATUS'; el('race-clock').textContent = 'FINISHED'; }
        el('sync-state').title = lastUpdated || '';
    }

    function duration(milliseconds) {
        const seconds = Math.max(0, Math.floor(milliseconds / 1000));
        const h = Math.floor(seconds / 3600); const m = Math.floor((seconds % 3600) / 60); const s = seconds % 60;
        return [h, m, s].map((value) => String(value).padStart(2, '0')).join(':');
    }
    function clock(value) { return new Intl.DateTimeFormat('en-GB', { weekday: 'short', hour: '2-digit', minute: '2-digit', hour12: false }).format(new Date(value)); }
    function number(value) { return Number(value).toLocaleString('en-GB', { maximumFractionDigits: 1 }); }
    function escapeHtml(value) { return String(value).replace(/[&<>'"]/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[char])); }

    refresh(); setInterval(refresh, 10000); setInterval(() => { if (plan) render(); }, 1000);
})();
