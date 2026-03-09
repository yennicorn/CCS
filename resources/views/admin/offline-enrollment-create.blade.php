@extends('layouts.admin')

@section('page_title', 'Assisted Enrollment')
@section('page_subtitle', 'Admin encoding for parent/student physical enrollment forms')

@section('content')
<section class="panel print-hide">
    <div class="panel-head">
        <h3>Assisted Enrollment Guide</h3>
        <p class="muted">Encode the physical form as-is and save once complete.</p>
    </div>
    <p class="muted">
        For parents/students who cannot submit online, fill this page using their hardcopy form details.
    </p>
    <ul class="assisted-hint-list">
        <li>Account linking is handled automatically by the system.</li>
        <li>Use CAPITAL letters for text fields.</li>
        <li>Choose Yes/No options before saving to avoid validation errors.</li>
    </ul>
</section>

<form method="POST" action="{{ route('admin.monitoring.hardcopy.store') }}" class="js-assisted-enrollment-form" data-readonly="0">
    @csrf
    <input type="hidden" name="account_user_id" value="{{ old('account_user_id', optional($accounts->first())->id) }}">

    <section class="enrollment-paper-wrap">
        <article class="enrollment-paper">
            @include('enduser.partials.enrollment-paper-head', [
                'application' => null,
                'activeSchoolYear' => $activeSchoolYear,
                'receivedDate' => now()->format('m/d/Y'),
            ])

            @include('enduser.partials.enrollment-form-fields', [
                'application' => null,
                'readonly' => false,
            ])

            <div class="enrollment-actions print-hide">
                <button class="btn" type="submit" id="assistedEnrollmentSaveBtn">Save Assisted Enrollment</button>
            </div>
        </article>
    </section>
</form>

<script>
(() => {
    const form = document.querySelector('.js-assisted-enrollment-form');
    if (!form || form.dataset.readonly === '1') {
        return;
    }

    const bindConditionalField = (radioName, targetSelector) => {
        const radios = form.querySelectorAll(`input[name="${radioName}"]`);
        const targets = form.querySelectorAll(targetSelector);

        if (!radios.length || !targets.length) {
            return;
        }

        const refresh = () => {
            const checked = form.querySelector(`input[name="${radioName}"]:checked`);
            const enabled = checked && checked.value === '1';

            targets.forEach((target) => {
                target.disabled = !enabled;
                const shouldRequire = enabled && (
                    target.name === 'lrn' ||
                    target.name === 'ip_affiliation' ||
                    target.name === 'four_ps_household_id'
                );
                target.required = shouldRequire;

                if (!enabled) {
                    if (target.type === 'checkbox' || target.type === 'radio') {
                        target.checked = false;
                    } else {
                        target.value = '';
                    }
                }
            });
        };

        radios.forEach((radio) => radio.addEventListener('change', refresh));
        refresh();
    };

    form.querySelectorAll('input[type="text"], textarea').forEach((field) => {
        field.addEventListener('input', () => {
            const cursorPos = field.selectionStart;
            field.value = field.value.toUpperCase();
            if (typeof cursorPos === 'number') {
                field.setSelectionRange(cursorPos, cursorPos);
            }
        });
    });

    bindConditionalField('with_lrn', 'input[name="lrn"]');
    bindConditionalField('has_ip_affiliation', 'input[name="ip_affiliation"]');
    bindConditionalField('is_4ps_beneficiary', 'input[name="four_ps_household_id"]');
    bindConditionalField('is_lwd', 'input[name="disability_types[]"]');

    form.addEventListener('submit', () => {
        const saveBtn = document.getElementById('assistedEnrollmentSaveBtn');
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.textContent = 'Saving...';
        }
    });
})();
</script>
@endsection
