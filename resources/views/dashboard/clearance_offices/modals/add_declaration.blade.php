@php
    // Fetch clients once to use in the modal
    $clientRole = \App\Models\Role::where('name', 'client')->first();
    $clients = $clientRole ? \App\Models\User::where('role_id', $clientRole->id)->where('is_active', true)->get(['id', 'name']) : collect();
@endphp

<div class="modal fade" id="addDeclarationModal" tabindex="-1" role="dialog" aria-labelledby="addDeclarationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form id="declarationForm" action="{{ route('customs-declarations.store') }}" method="POST">
                @csrf
                <input type="hidden" name="clearance_office_id" id="modal_clearance_office_id">

                <div class="modal-header">
                    <h5 class="modal-title" id="addDeclarationModalLabel">إضافة بيان جمركي لـ: <span id="modal_office_name" class="font-weight-bold text-primary"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Step 1: Declaration Details -->
                    <div id="step1">
                        <h6 class="font-weight-bold text-primary">الخطوة 1: تفاصيل البيان</h6>
                        <hr class="mt-0">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label>العميل <span class="text-danger">*</span></label>
                                <div class="custom-control custom-radio d-inline-block mr-3">
                                    <input type="radio" id="existingClientRadio" name="client_selection_method" class="custom-control-input" value="existing" checked>
                                    <label class="custom-control-label" for="existingClientRadio">اختيار عميل حالي</label>
                                </div>
                                <div class="custom-control custom-radio d-inline-block">
                                    <input type="radio" id="newClientRadio" name="client_selection_method" class="custom-control-input" value="new">
                                    <label class="custom-control-label" for="newClientRadio">إضافة عميل جديد</label>
                                </div>
                            </div>
                        </div>

                        <div id="existing-client-section" class="row">
                            <div class="col-md-12 form-group">
                                <select name="client_id" class="form-control" required>
                                    <option value="">-- اختر العميل --</option>
                                    @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="new-client-section" style="display: none;" class="row">
                            <div class="col-md-6 form-group">
                                <input type="text" name="new_client_name" class="form-control" placeholder="اسم العميل الجديد">
                            </div>
                            <div class="col-md-6 form-group">
                                <input type="text" name="new_client_phone" class="form-control" placeholder="رقم هاتف العميل (اختياري)">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="statement_number">رقم البيان <span class="text-danger">*</span></label>
                                <input type="number" name="statement_number" class="form-control" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="expire_date">تاريخ ارضية الجمرك</label>
                                <input type="date" name="expire_date" class="form-control">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="weight">وزن الحاويات</label>
                                <input type="number" name="weight" class="form-control" step="0.01" min="0">
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row align-items-center">
                            <label for="container-count" class="col-sm-4 col-form-label">كم عدد الحاويات؟</label>
                            <div class="col-sm-8">
                                <input type="number" id="container-count" class="form-control" min="1" value="1">
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Container Details -->
                    <div id="step2" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center">
                             <h6 class="font-weight-bold text-primary">الخطوة 2: تفاصيل الحاويات</h6>
                             <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-back">العودة</button>
                        </div>
                        <hr class="mt-0">
                        <div id="container-fields">
                            <!-- Dynamic fields will be inserted here -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-primary" id="btn-next">التالي <i class="fas fa-arrow-left ml-1"></i></button>
                    <button type="submit" class="btn btn-success" id="btn-save" style="display: none;">حفظ البيان <i class="fas fa-check ml-1"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
