<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Follow-up | KFHI-CMS</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f4f8f5; color: #1e2922; font-family: Arial, sans-serif; }
        .page-shell { display: flex; min-height: 100vh; }
        main { width: min(1050px, 100%); margin: 0 auto; padding: 34px clamp(18px, 4vw, 54px); }
        h1 { margin: 0 0 6px; font-size: 25px; } h2 { margin: 0 0 16px; font-size: 16px; }
        p, .muted { color: #68766d; font-size: 13px; }
        .top { margin-bottom: 22px; } .top a { color: #2e7d32; font-size: 13px; text-decoration: none; }
        .section { margin-bottom: 15px; padding: 20px; border: 1px solid #dce5de; border-radius: 8px; background: #fff; }
        .fields { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
        .field.full { grid-column: 1 / -1; }
        label { display: block; margin-bottom: 6px; color: #35443a; font-size: 12px; font-weight: 700; }
        input, select, textarea { width: 100%; min-height: 39px; border: 1px solid #d5dfd8; border-radius: 6px; padding: 9px 11px; background: #fff; color: #1e2922; font: inherit; font-size: 13px; }
        textarea { min-height: 90px; resize: vertical; }
        .event-meta { min-height: 40px; padding: 10px 12px; border-radius: 6px; background: #f2f7f3; color: #53645a; font-size: 12px; }
        .actions { display: flex; justify-content: flex-end; gap: 9px; }
        .button { display: inline-flex; align-items: center; justify-content: center; min-height: 39px; padding: 0 15px; border: 1px solid #2e7d32; border-radius: 7px; background: #2e7d32; color: #fff; font-size: 13px; font-weight: 700; text-decoration: none; cursor: pointer; }
        .button.secondary { background: #fff; color: #2e7d32; }
        .error { margin-top: 5px; color: #b44949; font-size: 12px; }
        @media (max-width: 620px) { .fields { grid-template-columns: 1fr; } .field.full { grid-column: auto; } .section { padding: 16px; } }
    </style>
</head>
<body>
<div class="page-shell">
    @include('components.child-officer-sidebar')
    <main>
        <div class="top"><a href="{{ route('child_officer.follow-ups.index') }}">← Follow-ups</a><h1 style="margin-top:14px">New Follow-up</h1><p>Create an event-based task and assign an authorized staff member.</p></div>
        <form method="POST" action="{{ route('child_officer.follow-ups.store') }}">
            @csrf
            <section class="section">
                <h2>Event selection</h2>
                <div class="fields">
                    <div class="field full"><label for="event_id">Existing event *</label>
                        <select id="event_id" name="event_id" required>
                            <option value="">Select an event</option>
                            @foreach ($events as $event)
                                <option value="{{ $event->id }}" data-date="{{ $event->event_date?->format('d M Y') }}" data-code="{{ $event->event_code }}" data-location="{{ $event->location }}" @selected(old('event_id') == $event->id)>{{ $event->title }} · {{ $event->event_code }}</option>
                            @endforeach
                        </select>
                        <div class="error">@error('event_id'){{ $message }}@enderror</div>
                    </div>
                    <div class="field full"><div class="event-meta" id="event-meta">Select an event to view its date, reference and location.</div></div>
                </div>
            </section>
            <section class="section">
                <h2>Task details</h2>
                <div class="fields">
                    <div class="field full"><label for="title">Title *</label><input id="title" name="title" value="{{ old('title') }}" required maxlength="255"><div class="error">@error('title'){{ $message }}@enderror</div></div>
                    <div class="field full"><label for="description">Description</label><textarea id="description" name="description">{{ old('description') }}</textarea><div class="error">@error('description'){{ $message }}@enderror</div></div>
                    <div class="field full"><label for="reason">Reason for follow-up *</label><textarea id="reason" name="reason" required>{{ old('reason') }}</textarea><div class="error">@error('reason'){{ $message }}@enderror</div></div>
                </div>
            </section>
            <section class="section">
                <h2>Assignment and scheduling</h2>
                <div class="fields">
                    <div class="field"><label for="assigned_to">Responsible officer *</label><select id="assigned_to" name="assigned_to" required><option value="">Select staff</option>@foreach ($staff as $member)<option value="{{ $member->id }}" @selected(old('assigned_to', auth()->id()) == $member->id)>{{ $member->name }}</option>@endforeach</select><div class="error">@error('assigned_to'){{ $message }}@enderror</div></div>
                    <div class="field"><label for="due_date">Due date *</label><input type="date" id="due_date" name="due_date" value="{{ old('due_date') }}" required><div class="error">@error('due_date'){{ $message }}@enderror</div></div>
                    <div class="field"><label for="reminder_at">Reminder</label><input type="datetime-local" id="reminder_at" name="reminder_at" value="{{ old('reminder_at') }}"><div class="error">@error('reminder_at'){{ $message }}@enderror</div></div>
                    <div class="field"><label for="priority">Priority *</label><select id="priority" name="priority" required>@foreach (['low', 'normal', 'high', 'urgent'] as $priority)<option value="{{ $priority }}" @selected(old('priority', 'normal') === $priority)>{{ ucfirst($priority) }}</option>@endforeach</select></div>
                    <div class="field"><label for="status">Tracking status *</label><select id="status" name="status" required>@foreach (['open', 'in_progress', 'completed', 'cancelled'] as $status)<option value="{{ $status }}" @selected(old('status', 'open') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>@endforeach</select></div>
                    <div class="field"><label>Created date</label><div class="event-meta">{{ now()->format('d M Y') }} · Set automatically</div></div>
                    <div class="field full" id="outcome-field" hidden><label for="outcome">Completion outcome *</label><textarea id="outcome" name="outcome">{{ old('outcome') }}</textarea><div class="error">@error('outcome'){{ $message }}@enderror</div></div>
                    <div class="field full"><label for="initial_notes">Initial notes</label><textarea id="initial_notes" name="initial_notes" placeholder="Background and expected outcome">{{ old('initial_notes') }}</textarea><div class="error">@error('initial_notes'){{ $message }}@enderror</div></div>
                </div>
            </section>
            <div class="actions"><a class="button secondary" href="{{ route('child_officer.follow-ups.index') }}">Cancel</a><button class="button" type="submit">Create Follow-up</button></div>
        </form>
    </main>
</div>
<script>
    const eventSelect = document.getElementById('event_id');
    const eventMeta = document.getElementById('event-meta');
    const statusSelect = document.getElementById('status');
    const outcomeField = document.getElementById('outcome-field');
    const outcomeInput = document.getElementById('outcome');
    function updateEventMeta() {
        const option = eventSelect.selectedOptions[0];
        if (!option || !option.value) {
            eventMeta.textContent = 'Select an event to view its date, reference and location.';
            return;
        }
        eventMeta.textContent = `${option.dataset.code} · ${option.dataset.date || 'Date not set'} · ${option.dataset.location || 'Location not set'}`;
    }
    eventSelect.addEventListener('change', updateEventMeta);
    updateEventMeta();
    function updateOutcomeField() {
        const completed = statusSelect.value === 'completed';
        outcomeField.hidden = !completed;
        outcomeInput.required = completed;
    }
    statusSelect.addEventListener('change', updateOutcomeField);
    updateOutcomeField();
</script>
</body>
</html>