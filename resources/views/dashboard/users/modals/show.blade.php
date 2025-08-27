<div class="modal fade" id="showUserModal{{ $user->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تفاصيل المستخدم</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://placehold.co/120x120/EBF4FF/76859A?text=' . mb_substr($user->name, 0, 1) }}"
                    class="rounded-circle mb-3" width="120" height="120" alt="{{ $user->name }}">
                <h3 class="font-weight-bold">{{ $user->name }}</h3>
                <p class="text-muted">{{ $user->role->description }}</p>
                <hr>
                <div class="text-right">
                    <p><strong><i class="fas fa-envelope fa-fw mr-2"></i>البريد:</strong>
                        {{ $user->email ?? 'لم يحدد' }}</p>
                    <p><strong><i class="fas fa-phone fa-fw mr-2"></i>الهاتف:</strong> {{ $user->phone ?? 'لم يحدد' }}
                    </p>
                    <p><strong><i class="fas fa-user-check fa-fw mr-2"></i>الحالة:</strong>
                        <span class="badge badge-{{ $user->is_active ? 'success' : 'danger' }}">
                            {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </p>
                    <p><strong><i class="fas fa-calendar-alt fa-fw mr-2"></i>تاريخ الانضمام:</strong>
                        {{ $user?->created_at?->format('d M, Y') }}</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>
