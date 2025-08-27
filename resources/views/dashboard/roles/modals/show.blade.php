<!-- Show Role Modal -->
<div class="modal fade" id="showRoleModal{{ $role->id }}" tabindex="-1" role="dialog"
    aria-labelledby="showRoleModalLabel{{ $role->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showRoleModalLabel{{ $role->id }}">تفاصيل الدور: {{ $role->name }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>الاسم:</strong> {{ $role->name }}</p>
                <p><strong>الوصف:</strong> {{ $role->description ?? 'لا يوجد' }}</p>
                <p><strong>عدد المستخدمين:</strong> {{ $role->users_count }}</p>
                <p><strong>تاريخ الإنشاء:</strong> {{ $role->created_at?->diffForHumans() }}</p>
                <p><strong>آخر تحديث:</strong> {{ $role->updated_at?->diffForHumans() }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>
