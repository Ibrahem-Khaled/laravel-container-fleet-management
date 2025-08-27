@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        {{-- Page Heading & Breadcrumb --}}
        <h1 class="h3 mb-2 text-gray-800">إدارة مكاتب التخليص</h1>
        <p class="mb-4">هنا يمكنك التحكم في جميع مكاتب التخليص الجمركي المسجلة في النظام.</p>

        @include('components.alerts')

        {{-- Stats Cards --}}
        <div class="row mb-4">
            <x-stats-card icon="fas fa-building" title="إجمالي المكاتب" :value="$stats['total_offices']" color="primary" />
            <x-stats-card icon="fas fa-user-check" title="المكاتب النشطة" :value="$stats['active_offices']" color="success" />
            <x-stats-card icon="fas fa-user-times" title="المكاتب غير النشطة" :value="$stats['inactive_offices']" color="warning" />
        </div>

        {{-- Offices List Card --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">قائمة المكاتب</h6>
                <button class="btn btn-primary" data-toggle="modal" data-target="#createOfficeModal">
                    <i class="fas fa-plus fa-sm"></i> إضافة مكتب جديد
                </button>
            </div>
            <div class="card-body">
                {{-- Search Form --}}
                <form action="{{ route('clearance-offices.index') }}" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control"
                            placeholder="ابحث بالاسم، البريد الإلكتروني، أو الهاتف..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search fa-sm"></i></button>
                        </div>
                    </div>
                </form>

                {{-- Offices Table --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>الاسم</th>
                                <th>البريد الإلكتروني</th>
                                <th>الهاتف</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($offices as $office)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $office->avatar ? asset('storage/' . $office->avatar) : asset('img/default-avatar.png') }}"
                                                alt="{{ $office->name }}" class="rounded-circle mr-2" width="40"
                                                height="40">
                                            {{ $office->name }}
                                        </div>
                                    </td>
                                    <td>{{ $office->email ?? '-' }}</td>
                                    <td>{{ $office->phone ?? '-' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $office->is_active ? 'success' : 'danger' }}">
                                            {{ $office->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                    <td>
                                        <!-- View Details Button -->
                                        <a href="{{ route('clearance-offices.show', $office->id) }}"
                                            class="btn btn-info btn-circle btn-sm" title="عرض التفاصيل والبيانات">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <!-- Add Declaration Button -->
                                        <button class="btn btn-success btn-circle btn-sm" data-toggle="modal"
                                            data-target="#addDeclarationModal" data-office-id="{{ $office->id }}"
                                            data-office-name="{{ $office->name }}" title="إضافة بيان جمركي">
                                            <i class="fas fa-plus"></i>
                                        </button>

                                        <!-- Edit Button -->
                                        <button class="btn btn-primary btn-circle btn-sm" data-toggle="modal"
                                            data-target="#editOfficeModal{{ $office->id }}" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <!-- Delete Button -->
                                        <button class="btn btn-danger btn-circle btn-sm" data-toggle="modal"
                                            data-target="#deleteOfficeModal{{ $office->id }}" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        @include('dashboard.clearance_offices.modals.edit', [
                                            'office' => $office,
                                        ])
                                        @include('dashboard.clearance_offices.modals.delete', [
                                            'office' => $office,
                                        ])
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">لا توجد مكاتب تخليص لعرضها.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center">
                    {{ $offices->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Include necessary modals --}}
    @include('dashboard.clearance_offices.modals.create')
    @include('dashboard.clearance_offices.modals.add_declaration')
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Logic for Add Declaration Modal ---
            const modal = $('#addDeclarationModal');
            const step1 = modal.find('#step1');
            const step2 = modal.find('#step2');
            const btnNext = modal.find('#btn-next');
            const btnBack = modal.find('#btn-back');
            const btnSave = modal.find('#btn-save');
            const containerCountInput = modal.find('#container-count');
            const containerFieldsDiv = modal.find('#container-fields');
            const declarationForm = modal.find('#declarationForm');

            // Client creation elements
            const existingClientSection = modal.find('#existing-client-section');
            const newClientSection = modal.find('#new-client-section');
            const clientSelectionRadios = modal.find('input[name="client_selection_method"]');
            const existingClientSelect = existingClientSection.find('select');
            const newClientNameInput = newClientSection.find('input[name="new_client_name"]');

            // Set office ID and name when modal is opened
            modal.on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var officeId = button.data('office-id');
                var officeName = button.data('office-name');

                modal.find('#modal_clearance_office_id').val(officeId);
                modal.find('#modal_office_name').text(officeName);

                // Reset to step 1 and default client view every time modal opens
                step1.show();
                step2.hide();
                btnNext.show();
                btnSave.hide();
                btnBack.hide();
                declarationForm[0].reset();
                modal.find('.is-invalid').removeClass('is-invalid');
                modal.find('#existingClientRadio').prop('checked', true).trigger('change');
            });

            // Handle showing/hiding new client fields
            clientSelectionRadios.on('change', function() {
                if ($(this).val() === 'new') {
                    existingClientSection.hide();
                    newClientSection.show();
                    // **THE FIX**: Disable the select so it doesn't get submitted
                    existingClientSelect.prop('disabled', true);
                    newClientNameInput.prop('disabled', false);
                    newClientNameInput.prop('required', true);
                    existingClientSelect.prop('required', false);
                } else {
                    existingClientSection.show();
                    newClientSection.hide();
                    // **THE FIX**: Enable the select for submission
                    existingClientSelect.prop('disabled', false);
                    newClientNameInput.prop('disabled', true);
                    existingClientSelect.prop('required', true);
                    newClientNameInput.prop('required', false);
                }
            });

            // Go to Step 2
            btnNext.on('click', function() {
                let isValid = true;
                step1.find('.is-invalid').removeClass('is-invalid');

                step1.find('input:not(:disabled)[required], select:not(:disabled)[required]').each(
                    function() {
                        if (!$(this).val()) {
                            isValid = false;
                            $(this).addClass('is-invalid');
                        }
                    });

                if (isValid) {
                    step1.hide();
                    step2.show();
                    btnNext.hide();
                    btnSave.show();
                    btnBack.show();
                    generateContainerFields();
                }
            });

            // Go back to Step 1
            btnBack.on('click', function() {
                step2.hide();
                step1.show();
                btnSave.hide();
                btnNext.show();
                btnBack.hide();
            });

            function generateContainerFields() {
                const count = parseInt(containerCountInput.val(), 10) || 1;
                let html = '';
                for (let i = 0; i < count; i++) {
                    html += `
                <div class="border rounded p-3 mb-3 bg-light">
                    <h6 class="font-weight-bold">حاوية #${i + 1}</h6>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>رقم الحاوية <span class="text-danger">*</span></label>
                            <input type="text" name="containers[${i}][number]" class="form-control" required>
                        </div>
                        <div class="col-md-3 form-group">
                            <label>المقاس <span class="text-danger">*</span></label>
                            <select name="containers[${i}][size]" class="form-control" required>
                                <option value="20">20</option>
                                <option value="40">40</option>
                                <option value="box">Box</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group align-self-center pt-3">
                            <div class="custom-control custom-switch">
                              <input type="hidden" name="containers[${i}][is_rent]" value="0">
                              <input type="checkbox" class="custom-control-input" id="is_rent_${i}" name="containers[${i}][is_rent]" value="1">
                              <label class="custom-control-label" for="is_rent_${i}">إيجار؟</label>
                            </div>
                        </div>
                    </div>
                </div>
            `;
                }
                containerFieldsDiv.html(html);
            }
        });
    </script>
@endpush
