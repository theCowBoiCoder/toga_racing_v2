(() => {
    const storageKey = 'toga-racing-stint-planner-v1';
    const planTokenKey = 'toga-racing-stint-plan-token-v1';
    const plannerRoot = document.getElementById('planner-app');
    const lmuSource = 'Official LMU Spa 2026 BoP reference';
    const iracingSource = 'Official iRacing car/manual reference';
    const cars = [
        { sim: 'LMU', name: 'Alpine A424', class: 'Hypercar', capacity: 913, unit: 'MJ', source: lmuSource },
        { sim: 'LMU', name: 'Aston Martin Valkyrie AMR-LMH', class: 'Hypercar', capacity: 894, unit: 'MJ', source: lmuSource },
        { sim: 'LMU', name: 'BMW M Hybrid V8', class: 'Hypercar', capacity: 905, unit: 'MJ', source: lmuSource },
        { sim: 'LMU', name: 'Cadillac V-Series.R', class: 'Hypercar', capacity: 904, unit: 'MJ', source: lmuSource },
        { sim: 'LMU', name: 'Ferrari 499P', class: 'Hypercar', capacity: 890, unit: 'MJ', source: lmuSource },
        { sim: 'LMU', name: 'Genesis GMR-001', class: 'Hypercar', capacity: 913, unit: 'MJ', source: lmuSource },
        { sim: 'LMU', name: 'Glickenhaus SCG 007', class: 'Hypercar', capacity: 913, unit: 'MJ', source: lmuSource },
        { sim: 'LMU', name: 'Isotta Fraschini Tipo 6', class: 'Hypercar', capacity: 923, unit: 'MJ', source: lmuSource },
        { sim: 'LMU', name: 'Lamborghini SC63', class: 'Hypercar', capacity: 908, unit: 'MJ', source: lmuSource },
        { sim: 'LMU', name: 'Peugeot 9X8', class: 'Hypercar', capacity: 894, unit: 'MJ', source: lmuSource },
        { sim: 'LMU', name: 'Peugeot 9X8 Evo', class: 'Hypercar', capacity: 885, unit: 'MJ', source: lmuSource },
        { sim: 'LMU', name: 'Porsche 963', class: 'Hypercar', capacity: 910, unit: 'MJ', source: lmuSource },
        { sim: 'LMU', name: 'Toyota GR010-Hybrid', class: 'Hypercar', capacity: 900, unit: 'MJ', source: lmuSource },
        { sim: 'LMU', name: 'Vanwall Vandervell 680', class: 'Hypercar', capacity: 920, unit: 'MJ', source: lmuSource },
        { sim: 'LMU', name: 'Aston Martin Vantage AMR LMGT3', class: 'LMGT3', capacity: 675, unit: 'MJ', source: lmuSource },
        { sim: 'LMU', name: 'BMW M4 LMGT3', class: 'LMGT3', capacity: 668, unit: 'MJ', source: lmuSource },
        { sim: 'LMU', name: 'Chevrolet Corvette Z06 LMGT3.R', class: 'LMGT3', capacity: 703, unit: 'MJ', source: lmuSource },
        { sim: 'LMU', name: 'Ferrari 296 LMGT3', class: 'LMGT3', capacity: 672, unit: 'MJ', source: lmuSource },
        { sim: 'LMU', name: 'Ford Mustang LMGT3', class: 'LMGT3', capacity: 711, unit: 'MJ', source: lmuSource },
        { sim: 'LMU', name: 'Lamborghini Huracan LMGT3 Evo2', class: 'LMGT3', capacity: 684, unit: 'MJ', source: lmuSource },
        { sim: 'LMU', name: 'Lexus RC F LMGT3', class: 'LMGT3', capacity: 666, unit: 'MJ', source: lmuSource },
        { sim: 'LMU', name: 'McLaren 720S LMGT3 Evo', class: 'LMGT3', capacity: 673, unit: 'MJ', source: lmuSource },
        { sim: 'LMU', name: 'Mercedes-AMG LMGT3', class: 'LMGT3', capacity: 666, unit: 'MJ', source: lmuSource },
        { sim: 'LMU', name: 'Porsche 911 GT3 R LMGT3', class: 'LMGT3', capacity: 671, unit: 'MJ', source: lmuSource },
        { sim: 'LMU', name: 'Oreca 07 Gibson (WEC)', class: 'LMP2', capacity: 63, unit: 'L', source: lmuSource },
        { sim: 'LMU', name: 'Oreca 07 Gibson (ELMS)', class: 'LMP2', capacity: 75, unit: 'L', source: lmuSource },
        { sim: 'LMU', name: 'Aston Martin Vantage GTE', class: 'GTE', capacity: 97, unit: 'L', source: lmuSource },
        { sim: 'LMU', name: 'Chevrolet Corvette C8.R', class: 'GTE', capacity: 91, unit: 'L', source: lmuSource },
        { sim: 'LMU', name: 'Ferrari 488 GTE Evo', class: 'GTE', capacity: 84, unit: 'L', source: lmuSource },
        { sim: 'LMU', name: 'Porsche 911 RSR-19', class: 'GTE', capacity: 99, unit: 'L', source: lmuSource },
        { sim: 'iRacing', name: 'Acura NSX GT3 EVO 22', class: 'GT3', capacity: null, unit: 'L', source: iracingSource },
        { sim: 'iRacing', name: 'Aston Martin Vantage GT3 EVO', class: 'GT3', capacity: null, unit: 'L', source: iracingSource },
        { sim: 'iRacing', name: 'Audi R8 LMS EVO II GT3', class: 'GT3', capacity: null, unit: 'L', source: iracingSource },
        { sim: 'iRacing', name: 'BMW M4 GT3 EVO', class: 'GT3', capacity: 120, unit: 'L', source: iracingSource },
        { sim: 'iRacing', name: 'Chevrolet Corvette Z06 GT3.R', class: 'GT3', capacity: null, unit: 'L', source: iracingSource },
        { sim: 'iRacing', name: 'Ferrari 296 GT3', class: 'GT3', capacity: 104, unit: 'L', source: iracingSource },
        { sim: 'iRacing', name: 'Ford Mustang GT3', class: 'GT3', capacity: null, unit: 'L', source: iracingSource },
        { sim: 'iRacing', name: 'Lamborghini Huracan GT3 EVO', class: 'GT3', capacity: 120, unit: 'L', source: iracingSource },
        { sim: 'iRacing', name: 'McLaren 720S GT3 EVO', class: 'GT3', capacity: null, unit: 'L', source: iracingSource },
        { sim: 'iRacing', name: 'Mercedes-AMG GT3 2020', class: 'GT3', capacity: 120, unit: 'L', source: iracingSource },
        { sim: 'iRacing', name: 'Porsche 911 GT3 R (992)', class: 'GT3', capacity: 120, unit: 'L', source: iracingSource },
        { sim: 'iRacing', name: 'Acura ARX-06 GTP', class: 'GTP', capacity: null, unit: 'L', source: iracingSource },
        { sim: 'iRacing', name: 'BMW M Hybrid V8 Evo', class: 'GTP', capacity: null, unit: 'L', source: iracingSource },
        { sim: 'iRacing', name: 'Cadillac V-Series.R GTP', class: 'GTP', capacity: null, unit: 'L', source: iracingSource },
        { sim: 'iRacing', name: 'Ferrari 499P', class: 'GTP', capacity: null, unit: 'L', source: iracingSource },
        { sim: 'iRacing', name: 'Porsche 963 GTP', class: 'GTP', capacity: null, unit: 'L', source: iracingSource },
        { sim: 'iRacing', name: 'Dallara P217 LMP2', class: 'LMP2', capacity: null, unit: 'L', source: iracingSource },
        { sim: 'iRacing', name: 'Ligier JS P320', class: 'LMP3', capacity: null, unit: 'L', source: iracingSource },
    ];

    const defaults = {
        event: '24h Spa', sim: 'iRacing', car: 'Ferrari 296 GT3', track: 'Spa-Francorchamps',
        raceDate: '2026-07-11', startTime: '13:45', raceMins: 1440, lapSecs: 138,
        fuelPerLap: 4, usableCapacity: 101, reserveLaps: 2, maxStint: 56, pitLoss: 60,
        drivers: ['Hayden Sweet', 'Stijn Donckerwolke', 'Mitchell Sterrenberg', 'Lukas James', 'Konrad Wasowicz', 'Lukas Küthe', 'Troy-Fraser McGonigal', 'Jordan McGonigal'],
        assignments: [], availability: [], availabilitySubmitted: [],
    };

    const fields = {
        event: 'event', sim: 'sim', car: 'car', track: 'track', raceDate: 'race-date', startTime: 'start-time',
        raceMins: 'race-mins', lapSecs: 'lap-secs', fuelPerLap: 'fuel-per-lap', usableCapacity: 'usable-capacity',
        reserveLaps: 'reserve-laps', maxStint: 'max-stint', pitLoss: 'pit-loss',
    };
    const numericFields = new Set(['raceMins', 'lapSecs', 'fuelPerLap', 'usableCapacity', 'reserveLaps', 'maxStint', 'pitLoss']);
    const el = (id) => document.getElementById(id);
    let state = loadState();
    let lastSchedule = [];
    let saveTimer;

    function loadState() {
        try {
            const stored = JSON.parse(localStorage.getItem(storageKey));
            return stored ? { ...defaults, ...stored, drivers: stored.drivers?.length ? stored.drivers : defaults.drivers, assignments: stored.assignments || [], availability: stored.availability || [], availabilitySubmitted: stored.availabilitySubmitted || [] } : structuredClone(defaults);
        } catch (_) {
            return structuredClone(defaults);
        }
    }

    function selectedCar() {
        return cars.find((item) => item.sim === state.sim && item.name === state.car) || cars.find((item) => item.sim === state.sim);
    }

    function populateCars() {
        const options = cars.filter((item) => item.sim === state.sim);
        if (!options.some((item) => item.name === state.car)) state.car = options[0]?.name || '';
        el('car').innerHTML = options.map((item) => `<option value="${escapeHtml(item.name)}">${escapeHtml(item.name)}</option>`).join('');
        el('car').value = state.car;
    }

    function syncInputs() {
        Object.entries(fields).forEach(([key, id]) => { el(id).value = state[key] ?? ''; });
        populateCars();
        updateCarReference();
    }

    function readInputs() {
        Object.entries(fields).forEach(([key, id]) => {
            state[key] = numericFields.has(key) ? numberValue(el(id).value) : el(id).value;
        });
    }

    function updateCarReference() {
        const car = selectedCar();
        el('car-class').value = car?.class || '';
        el('reference-capacity').value = car?.capacity == null ? 'Confirm in sim' : `${formatNumber(car.capacity, 1)} ${car.unit}`;
        el('fuel-per-lap-label').textContent = `Fuel per lap (${car?.unit || 'units'})`;
        el('capacity-note').textContent = car?.sim === 'LMU'
            ? 'LMU Hypercar and LMGT3 values are Spa 2026 maximum stint energy references. Confirm the current event BoP and use the override as the planning source of truth.'
            : car?.capacity == null
                ? 'No reliable capacity was found in the official reference used for this car. Enter the session garage value in the usable-capacity override.'
                : 'Published iRacing reference capacity. Hosted-session rules may restrict usable fuel, so confirm the garage value.';
    }

    function numberValue(value) {
        const parsed = Number(value);
        return Number.isFinite(parsed) ? parsed : 0;
    }

    function calculate() {
        const car = selectedCar();
        const capacity = state.usableCapacity > 0 ? state.usableCapacity : (car?.capacity || 0);
        const raceStart = new Date(`${state.raceDate}T${state.startTime}:00`);
        const valid = Number.isFinite(raceStart.getTime()) && state.raceMins > 0 && state.lapSecs > 0 && state.fuelPerLap > 0 && capacity > 0 && state.maxStint > 0;
        if (!valid) return { schedule: [], capacity, car, finish: null, plannedStint: 0, maxLaps: 0 };

        const raceFinish = new Date(raceStart.getTime() + state.raceMins * 60000);
        const maxLaps = Math.max(0, Math.floor(capacity / state.fuelPerLap) - Math.max(0, Math.floor(state.reserveLaps)));
        const fuelStintMins = maxLaps * state.lapSecs / 60;
        const plannedStint = Math.min(state.maxStint, fuelStintMins);
        if (plannedStint <= 0) return { schedule: [], capacity, car, finish: raceFinish, plannedStint, maxLaps };

        const schedule = [];
        let start = new Date(raceStart);
        for (let index = 0; index < 60 && start < raceFinish; index += 1) {
            const remainingMins = (raceFinish - start) / 60000;
            const driveMins = Math.min(plannedStint, remainingMins);
            const end = new Date(start.getTime() + driveMins * 60000);
            const laps = Math.ceil(driveMins * 60 / state.lapSecs);
            const fuel = laps * state.fuelPerLap;
            const startTarget = Math.min(capacity, fuel + state.reserveLaps * state.fuelPerLap);
            const assignment = state.assignments[index] || {};
            schedule.push({ index, start, end, driveMins, laps, fuel, startTarget, driver: assignment.driver || '', standby: assignment.standby || '', notes: assignment.notes || '' });
            start = new Date(end.getTime() + state.pitLoss * 1000);
        }
        return { schedule, capacity, car, finish: raceFinish, plannedStint, maxLaps };
    }

    function render() {
        updateCarReference();
        const result = calculate();
        lastSchedule = result.schedule;
        renderSummary(result);
        renderSchedule(result.schedule, result.car?.unit || 'units');
        renderDriverTotals(result.schedule);
    }

    function renderSummary(result) {
        const totalLaps = result.schedule.reduce((sum, stint) => sum + stint.laps, 0);
        const totalFuel = result.schedule.reduce((sum, stint) => sum + stint.fuel, 0);
        const pitMins = Math.max(0, result.schedule.length - 1) * state.pitLoss / 60;
        el('summary-finish').textContent = result.finish ? formatClock(result.finish) : '—';
        el('summary-duration').textContent = `${formatNumber(state.raceMins / 60, 1)} race hours`;
        el('summary-stint').textContent = result.plannedStint > 0 ? `${formatNumber(result.plannedStint, 1)} min` : '—';
        el('summary-laps-tank').textContent = result.maxLaps > 0 ? `${result.maxLaps} racing laps per tank` : 'Capacity or fuel data needed';
        el('summary-stints').textContent = result.schedule.length || '—';
        el('summary-pit').textContent = `${formatNumber(pitMins, 1)} planned pit minutes`;
        el('summary-fuel').textContent = totalFuel ? `${formatNumber(totalFuel, 1)} ${result.car?.unit || ''}` : '—';
        el('summary-laps').textContent = totalLaps ? `${totalLaps} estimated race laps` : '—';
    }

    function driverOptions(selected, stint = null) {
        const options = [''].concat(state.drivers.filter(Boolean));
        return options.map((name) => {
            const status = name && stint ? availabilityStatus({ ...stint, driver: name }) : null;
            const label = name ? `${name}${status && status.label !== 'Available' ? ` - ${status.label.toLowerCase()}` : ''}` : 'Unassigned';
            return `<option value="${escapeHtml(name)}"${name === selected ? ' selected' : ''}>${escapeHtml(label)}</option>`;
        }).join('');
    }

    function availabilityWindows(driver) {
        return state.availability.filter((window) => window.driver === driver);
    }

    function isDriverAvailable(driver, start, end) {
        if (!driver) return false;
        if (!state.availabilitySubmitted.includes(driver)) return false;
        const windows = availabilityWindows(driver);
        if (!windows.length) return false;
        return windows.some((window) => {
            const from = new Date(window.from);
            const to = new Date(window.to);
            return Number.isFinite(from.getTime()) && Number.isFinite(to.getTime()) && from <= start && to >= end;
        });
    }

    function availabilityStatus(stint) {
        if (!stint.driver) return { label: 'Unassigned', className: 'unassigned' };
        if (!state.availabilitySubmitted.includes(stint.driver)) return { label: 'Pending', className: 'unassigned' };
        if (!availabilityWindows(stint.driver).length) return { label: 'Unavailable', className: 'conflict' };
        return isDriverAvailable(stint.driver, stint.start, stint.end)
            ? { label: 'Available', className: '' }
            : { label: 'Conflict', className: 'conflict' };
    }

    function renderSchedule(schedule, unit) {
        el('empty-state').hidden = schedule.length > 0;
        el('schedule-body').innerHTML = schedule.map((stint) => {
            const status = availabilityStatus(stint);
            const driverConflict = stint.driver && !isDriverAvailable(stint.driver, stint.start, stint.end);
            const standbyConflict = stint.standby && !isDriverAvailable(stint.standby, stint.start, stint.end);
            return `
            <tr${driverConflict ? ' class="availability-conflict"' : ''}>
                <td>${stint.index + 1}</td>
                <td><select${driverConflict ? ' class="availability-conflict"' : ''} data-assignment="driver" data-index="${stint.index}" aria-label="Driver for stint ${stint.index + 1}">${driverOptions(stint.driver, stint)}</select></td>
                <td><select${standbyConflict ? ' class="availability-conflict"' : ''} data-assignment="standby" data-index="${stint.index}" aria-label="Stand-by driver for stint ${stint.index + 1}">${driverOptions(stint.standby, stint)}</select></td>
                <td><span class="availability-badge ${status.className}">${status.label}</span></td>
                <td>${formatClock(stint.start)}</td><td>${formatClock(stint.end)}</td>
                <td>${formatNumber(stint.driveMins, 1)}</td><td>${stint.laps}</td>
                <td>${formatNumber(stint.fuel, 1)} ${escapeHtml(unit)}</td><td>${formatNumber(stint.startTarget, 1)} ${escapeHtml(unit)}</td>
                <td><input class="notes-input" data-assignment="notes" data-index="${stint.index}" value="${escapeHtml(stint.notes)}" aria-label="Notes for stint ${stint.index + 1}"></td>
            </tr>`;
        }).join('');
    }

    function renderRoster() {
        el('roster-list').innerHTML = state.drivers.map((driver, index) => `
            <div class="roster-row"><span>${String(index + 1).padStart(2, '0')}</span><input data-driver-index="${index}" value="${escapeHtml(driver)}" aria-label="Driver ${index + 1} name"><button class="icon-button" data-remove-driver="${index}" type="button" aria-label="Remove ${escapeHtml(driver || `driver ${index + 1}`)}">×</button></div>`).join('');
    }

    function renderAvailability() {
        el('availability-summary').innerHTML = state.drivers.filter(Boolean).map((driver) => {
            const submitted = state.availabilitySubmitted.includes(driver);
            const windows = availabilityWindows(driver);
            const label = !submitted ? 'Waiting for response' : windows.length ? `${windows.length} window${windows.length === 1 ? '' : 's'} received` : 'Not available';
            const className = submitted ? (windows.length ? 'received' : 'unavailable') : '';
            return `<div class="availability-response"><strong>${escapeHtml(driver)}</strong><span class="${className}">${label}</span></div>`;
        }).join('');
    }

    function autoAssignAvailableDrivers() {
        const schedule = calculate().schedule;
        const drivers = state.drivers.filter(Boolean);
        if (!schedule.length || !drivers.length) {
            el('availability-message').textContent = 'Add drivers and complete the race inputs first.';
            el('availability-message').className = 'availability-message warning';
            return;
        }
        let cursor = 0;
        let unassigned = 0;
        let previous = '';
        state.assignments = schedule.map((stint, index) => {
            const available = drivers.filter((driver) => isDriverAvailable(driver, stint.start, stint.end));
            if (!available.length) {
                unassigned += 1;
                return { ...(state.assignments[index] || {}), driver: '', standby: '' };
            }
            const ordered = available.map((_, offset) => available[(cursor + offset) % available.length]);
            const driver = ordered.find((name) => name !== previous) || ordered[0];
            const standby = ordered.find((name) => name !== driver) || '';
            cursor = (drivers.indexOf(driver) + 1) % drivers.length;
            previous = driver;
            return { ...(state.assignments[index] || {}), driver, standby };
        });
        render();
        queueSave();
        el('availability-message').textContent = unassigned
            ? `${unassigned} stint${unassigned === 1 ? '' : 's'} could not be covered by the entered availability.`
            : 'All stints assigned using the current availability windows.';
        el('availability-message').className = `availability-message ${unassigned ? 'warning' : 'success'}`;
    }

    function renderDriverTotals(schedule) {
        el('driver-totals').innerHTML = state.drivers.filter(Boolean).map((driver) => {
            const stints = schedule.filter((stint) => stint.driver === driver);
            const mins = stints.reduce((sum, stint) => sum + stint.driveMins, 0);
            const laps = stints.reduce((sum, stint) => sum + stint.laps, 0);
            return `<article class="driver-total"><strong>${escapeHtml(driver)}</strong><dl><dt>Stints</dt><dd>${stints.length}</dd><dt>Drive</dt><dd>${formatNumber(mins, 1)} min</dd><dt>Laps</dt><dd>${laps}</dd></dl></article>`;
        }).join('');
    }

    function savePlan(message = 'Saved locally') {
        localStorage.setItem(storageKey, JSON.stringify(state));
        el('save-status').textContent = message;
        el('save-detail').textContent = new Intl.DateTimeFormat('en-GB', { hour: '2-digit', minute: '2-digit', second: '2-digit' }).format(new Date());
    }

    function queueSave() {
        clearTimeout(saveTimer);
        el('save-status').textContent = 'Saving…';
        saveTimer = setTimeout(() => savePlan(), 250);
    }

    function download(name, content, type) {
        const url = URL.createObjectURL(new Blob([content], { type }));
        const link = document.createElement('a');
        link.href = url; link.download = name; document.body.appendChild(link); link.click(); link.remove(); URL.revokeObjectURL(url);
    }

    function csvEscape(value) {
        const text = String(value ?? '');
        return /[",\n]/.test(text) ? `"${text.replaceAll('"', '""')}"` : text;
    }

    function exportCsv() {
        if (!lastSchedule.length) return setStatus('Nothing to export', 'Complete the required inputs first');
        const car = selectedCar();
        const rows = [
            ['Event', state.event], ['Simulator', state.sim], ['Car', state.car], ['Track', state.track], ['Race date', state.raceDate], ['Start time', state.startTime], [],
            ['Availability windows'],
            ...state.availability.map((window) => [window.driver, window.from, window.to]), [],
            ['Stint', 'Driver', 'Stand-by', 'Availability', 'Start', 'End', 'Drive mins', 'Estimated laps', `Fuel needed (${car?.unit || 'units'})`, `Start target (${car?.unit || 'units'})`, 'Notes'],
            ...lastSchedule.map((s) => [s.index + 1, s.driver, s.standby, availabilityStatus(s).label, formatClock(s.start), formatClock(s.end), s.driveMins.toFixed(1), s.laps, s.fuel.toFixed(1), s.startTarget.toFixed(1), s.notes]),
        ];
        download(`${safeName(state.event)}-stint-plan.csv`, '\ufeff' + rows.map((row) => row.map(csvEscape).join(',')).join('\r\n'), 'text/csv;charset=utf-8');
        setStatus('Schedule exported', 'CSV opens in Excel and Google Sheets');
    }

    function exportJson() {
        download(`${safeName(state.event)}-stint-plan.json`, JSON.stringify(state, null, 2), 'application/json');
        setStatus('Backup downloaded', 'Keep the JSON file to restore this plan');
    }

    function sharePayload() {
        const result = calculate();
        if (!result.schedule.length) throw new Error('Complete the race, lap, fuel and capacity inputs first.');
        return {
            event: state.event, sim: state.sim, car: state.car, class: result.car?.class || '', track: state.track,
            race_date: state.raceDate, start_time: state.startTime, race_mins: state.raceMins, lap_secs: state.lapSecs,
            fuel_per_lap: state.fuelPerLap, fuel_unit: result.car?.unit || 'units', capacity: result.capacity,
            drivers: state.drivers.filter(Boolean),
            availability: state.availability.filter((window) => window.driver && window.from && window.to).map((window) => ({
                driver: window.driver, from: new Date(window.from).toISOString(), to: new Date(window.to).toISOString(),
                from_label: formatClock(new Date(window.from)), to_label: formatClock(new Date(window.to)),
            })),
            availability_submitted: state.availabilitySubmitted,
            schedule: result.schedule.map((stint) => ({
                stint: stint.index + 1, driver: stint.driver || null, standby: stint.standby || null,
                start: stint.start.toISOString(), end: stint.end.toISOString(), start_label: formatClock(stint.start), end_label: formatClock(stint.end),
                drive_mins: Number(stint.driveMins.toFixed(2)), laps: stint.laps, fuel: Number(stint.fuel.toFixed(2)),
                start_target: Number(stint.startTarget.toFixed(2)), availability_status: availabilityStatus(stint).label, notes: stint.notes || null,
            })),
        };
    }

    async function postJson(url, body) {
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify(body),
        });
        const payload = await response.json().catch(() => ({}));
        const validationMessage = Object.values(payload.errors || {}).flat()[0];
        if (!response.ok) throw new Error(payload.message || validationMessage || `The request could not be completed (${response.status}).`);
        return payload;
    }

    async function publishPlan() {
        const shareKey = el('share-key').value;
        if (plannerRoot.dataset.shareConfigured !== 'true') throw new Error('Publishing is not configured yet. Add STINT_PLANNER_SHARE_KEY to .env and recreate the app container.');
        if (!shareKey) throw new Error('Enter the team share key first.');
        shareStatus('Publishing the latest plan…');
        const result = await postJson(plannerRoot.dataset.publishUrl, {
            share_key: shareKey,
            plan_token: localStorage.getItem(planTokenKey),
            plan: sharePayload(),
        });
        if (!result?.token || !result?.overlays?.compact || !result?.overlays?.schedule || !result?.availability_url) {
            throw new Error('The server returned an incomplete publish response. Refresh the page and try again.');
        }
        localStorage.setItem(planTokenKey, result.token);
        el('compact-overlay-url').value = result.overlays.compact;
        el('schedule-overlay-url').value = result.overlays.schedule;
        el('availability-form-url').value = result.availability_url;
        state.availability = result.availability || [];
        state.availabilitySubmitted = result.availability_submitted || [];
        renderAvailability(); render(); savePlan('Plan published');
        el('overlay-links').hidden = false;
        shareStatus('Overlays published. OBS will refresh them automatically.', 'success');
        return result;
    }

    async function syncAvailability() {
        const shareKey = el('share-key').value;
        const planToken = localStorage.getItem(planTokenKey);
        if (!shareKey) throw new Error('Enter the team share key first.');
        if (!planToken) throw new Error('Publish the plan first to create the driver availability link.');
        const result = await postJson(plannerRoot.dataset.availabilitySyncUrl, { share_key: shareKey, plan_token: planToken });
        state.availability = result.availability || [];
        state.availabilitySubmitted = result.availability_submitted || [];
        el('availability-form-url').value = result.availability_url;
        el('overlay-links').hidden = false;
        renderAvailability(); render(); savePlan('Driver responses refreshed');
        const received = state.availabilitySubmitted.length;
        el('availability-message').textContent = `${received} of ${state.drivers.filter(Boolean).length} drivers have responded.`;
        el('availability-message').className = `availability-message ${received ? 'success' : 'warning'}`;
    }

    async function sendDiscord() {
        const result = await publishPlan();
        shareStatus('Sending the race plan to Discord…');
        const response = await postJson(plannerRoot.dataset.discordUrl, { share_key: el('share-key').value, plan_token: result.token });
        shareStatus(response.message || 'Race plan sent to Discord.', 'success');
    }

    function shareStatus(message, type = '') {
        const messageEl = el('share-message');
        messageEl.textContent = message;
        messageEl.className = `share-message ${type}`.trim();
    }

    function importJson(file) {
        const reader = new FileReader();
        reader.onload = () => {
            try {
                const imported = JSON.parse(reader.result);
                state = { ...defaults, ...imported, drivers: imported.drivers?.length ? imported.drivers : defaults.drivers, assignments: imported.assignments || [], availability: imported.availability || [], availabilitySubmitted: imported.availabilitySubmitted || [] };
                syncInputs(); renderRoster(); renderAvailability(); render(); savePlan('Backup restored');
            } catch (_) {
                setStatus('Restore failed', 'Choose a valid Toga planner JSON backup');
            }
        };
        reader.readAsText(file);
    }

    function formatClock(date) {
        return new Intl.DateTimeFormat('en-GB', { weekday: 'short', day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit', hour12: false }).format(date);
    }
    function dateTimeInputValue(date) {
        const offsetDate = new Date(date.getTime() - date.getTimezoneOffset() * 60000);
        return offsetDate.toISOString().slice(0, 16);
    }
    function formatNumber(value, digits = 0) { return Number(value).toLocaleString('en-GB', { minimumFractionDigits: digits, maximumFractionDigits: digits }); }
    function safeName(value) { return String(value || 'toga-racing').trim().replace(/[^a-z0-9]+/gi, '-').replace(/^-|-$/g, '').toLowerCase() || 'toga-racing'; }
    function escapeHtml(value) { return String(value ?? '').replace(/[&<>'"]/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[char])); }
    function setStatus(title, detail) { el('save-status').textContent = title; el('save-detail').textContent = detail; }

    document.querySelectorAll('#planner-app input:not(#import-file), #planner-app select').forEach((input) => {
        input.addEventListener('input', () => {
            const previousCar = state.car;
            readInputs();
            if (input.id === 'sim') { populateCars(); state.car = el('car').value; const ref = selectedCar(); state.usableCapacity = ref?.capacity || 0; el('usable-capacity').value = state.usableCapacity || ''; }
            if (input.id === 'car' && state.car !== previousCar) { const ref = selectedCar(); state.usableCapacity = ref?.capacity || 0; el('usable-capacity').value = state.usableCapacity || ''; }
            render(); queueSave();
        });
    });

    el('schedule-body').addEventListener('input', (event) => {
        const target = event.target;
        const index = Number(target.dataset.index);
        const key = target.dataset.assignment;
        if (!Number.isInteger(index) || !key) return;
        state.assignments[index] = { ...(state.assignments[index] || {}), [key]: target.value };
        if (key === 'notes') renderDriverTotals(calculate().schedule);
        else render();
        queueSave();
    });

    el('roster-list').addEventListener('input', (event) => {
        const index = Number(event.target.dataset.driverIndex);
        if (!Number.isInteger(index)) return;
        const previousName = state.drivers[index];
        const nextName = event.target.value;
        state.drivers[index] = nextName;
        state.availability.forEach((window) => { if (window.driver === previousName) window.driver = nextName; });
        state.availabilitySubmitted = state.availabilitySubmitted.map((driver) => driver === previousName ? nextName : driver);
        state.assignments.forEach((assignment) => {
            if (assignment.driver === previousName) assignment.driver = nextName;
            if (assignment.standby === previousName) assignment.standby = nextName;
        });
        renderAvailability(); render(); queueSave();
    });
    el('roster-list').addEventListener('click', (event) => {
        const index = Number(event.target.dataset.removeDriver);
        if (!Number.isInteger(index)) return;
        const removedName = state.drivers[index];
        state.drivers.splice(index, 1);
        state.availability = state.availability.filter((window) => window.driver !== removedName);
        state.availabilitySubmitted = state.availabilitySubmitted.filter((driver) => driver !== removedName);
        state.assignments.forEach((assignment) => {
            if (assignment.driver === removedName) assignment.driver = '';
            if (assignment.standby === removedName) assignment.standby = '';
        });
        renderRoster(); renderAvailability(); render(); queueSave();
    });
    el('add-driver').addEventListener('click', () => { if (state.drivers.length < 12) { state.drivers.push(`Driver ${state.drivers.length + 1}`); renderRoster(); renderAvailability(); render(); queueSave(); } });
    el('sync-availability').addEventListener('click', async () => {
        try { await syncAvailability(); } catch (error) {
            el('availability-message').textContent = error.message;
            el('availability-message').className = 'availability-message warning';
        }
    });
    el('auto-assign').addEventListener('click', autoAssignAvailableDrivers);
    el('save-plan').addEventListener('click', () => savePlan());
    el('export-csv').addEventListener('click', exportCsv);
    el('export-json').addEventListener('click', exportJson);
    el('import-json').addEventListener('click', () => el('import-file').click());
    el('import-file').addEventListener('change', (event) => { if (event.target.files[0]) importJson(event.target.files[0]); event.target.value = ''; });
    el('reset-plan').addEventListener('click', () => {
        if (!confirm('Reset the planner and remove the locally saved plan?')) return;
        state = structuredClone(defaults); localStorage.removeItem(storageKey); syncInputs(); renderRoster(); renderAvailability(); render(); savePlan('Plan reset');
    });
    el('publish-plan').addEventListener('click', async () => {
        try { await publishPlan(); } catch (error) { shareStatus(error.message, 'error'); }
    });
    el('send-discord').addEventListener('click', async () => {
        try { await sendDiscord(); } catch (error) { shareStatus(error.message, 'error'); }
    });
    el('overlay-links').addEventListener('click', async (event) => {
        const inputId = event.target.dataset.copyOverlay;
        if (!inputId) return;
        await navigator.clipboard.writeText(el(inputId).value);
        shareStatus('OBS Browser Source URL copied.', 'success');
    });

    const menu = document.querySelector('.menu');
    menu.addEventListener('click', () => { const nav = document.querySelector('.header nav'); const open = nav.classList.toggle('open'); menu.setAttribute('aria-expanded', String(open)); });

    syncInputs(); renderRoster(); renderAvailability(); render(); savePlan(localStorage.getItem(storageKey) ? 'Plan restored' : 'Ready');
})();
