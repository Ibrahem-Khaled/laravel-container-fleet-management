<style>
    .modal-premium {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 16px;
        border: none;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(106, 27, 154, 0.3);
        transform: scale(0.95);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .modal-premium.show {
        transform: scale(1);
    }

    .modal-premium-header {
        background: linear-gradient(135deg, #6a1b9a 0%, #9c27b0 100%);
        color: white;
        padding: 25px;
        border-bottom: 4px solid rgba(255, 255, 255, 0.2);
        position: relative;
    }

    .modal-premium-header h5 {
        font-weight: 700;
        font-size: 1.8rem;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        letter-spacing: 0.5px;
    }

    .modal-premium-body {
        padding: 30px;
        background-color: rgba(255, 255, 255, 0.9);
    }

    .modal-premium-footer {
        background: linear-gradient(to right, #f5f5f5, #e9ecef);
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        padding: 20px;
    }

    .detail-item {
        display: flex;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px dashed #e0e0e0;
        transition: all 0.3s ease;
    }

    .detail-item:hover {
        transform: translateX(5px);
        border-bottom-color: #9c27b0;
    }

    .detail-label {
        flex: 0 0 40%;
        font-weight: 600;
        color: #6a1b9a;
        position: relative;
        padding-left: 15px;
    }

    .detail-label:after {
        content: ":";
        position: absolute;
        left: 0;
        top: 0;
        color: #6a1b9a;
    }

    .detail-value {
        flex: 0 0 60%;
        color: #555;
    }

    .btn-premium {
        background: linear-gradient(135deg, #6a1b9a 0%, #9c27b0 100%);
        border: none;
        border-radius: 50px;
        padding: 10px 25px;
        color: white;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(106, 27, 154, 0.4);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(106, 27, 154, 0.6);
        color: white;
    }

    .btn-premium:before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: 0.5s;
    }

    .btn-premium:hover:before {
        left: 100%;
    }

    .close-btn-premium {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.2);
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        color: white;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .close-btn-premium:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-50%) rotate(90deg);
    }

    .modal-premium-icon {
        position: absolute;
        top: -20px;
        right: -20px;
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: white;
    }

    @media (max-width: 768px) {
        .detail-item {
            flex-direction: column;
        }

        .detail-label,
        .detail-value {
            flex: 1;
            width: 100%;
        }

        .detail-label {
            padding-left: 0;
            margin-bottom: 5px;
        }

        .detail-label:after {
            display: none;
        }
    }
</style>

<div class="modal fade" id="containerModal{{ $item->id }}" tabindex="-1" role="dialog"
    aria-labelledby="containerModalLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content modal-premium">
            <div class="modal-header modal-premium-header">
                <div class="modal-premium-icon">
                    <i class="fas fa-box-open"></i>
                </div>
                <h5 class="modal-title" id="containerModalLabel{{ $item->id }}">
                    <i class="fas fa-info-circle mr-2"></i> تفاصيل الحاوية
                </h5>
                <button type="button" class="close-btn-premium" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body modal-premium-body">
                <div class="detail-item">
                    <div class="detail-label">مكتب التخليص</div>
                    <div class="detail-value">{{ $item->client->name }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">العميل</div>
                    <div class="detail-value">{{ $item->customs->importer_name }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">رقم البيان</div>
                    <div class="detail-value">{{ $item->customs->statement_number }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">رقم الحاوية</div>
                    <div class="detail-value">
                        <span class="badge badge-primary p-2">{{ $item->number }}</span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">حجم الحاوية</div>
                    <div class="detail-value">
                        <span class="badge badge-info p-2">{{ $item->size }}</span>
                    </div>
                </div>

                @if ($item->rent_id == null)
                    <div class="detail-item">
                        <div class="detail-label">السائق</div>
                        <div class="detail-value">{{ $item->driver->name ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">السيارة</div>
                        <div class="detail-value">{{ $item->car->number ?? 'N/A' }}</div>
                    </div>
                    @if ($item->tipsEmpty()->exists())
                        <div class="detail-item">
                            <div class="detail-label">سائق السيارة الفارغ</div>
                            <div class="detail-value">{{ $item->tipsEmpty()->first()?->user->name ?? 'N/A' }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">رقم السيارة الفارغ</div>
                            <div class="detail-value">{{ $item->tipsEmpty()->first()?->car->number ?? 'N/A' }}</div>
                        </div>
                    @endif
                @else
                    <div class="detail-item">
                        <div class="detail-label">شركة الإيجار</div>
                        <div class="detail-value">{{ $item->rent->name }}</div>
                    </div>
                @endif
                <div class="detail-item">
                    <div class="detail-label">تاريخ التحميل</div>
                    <div class="detail-value">
                        <span class="badge badge-secondary p-2">{{ $item->transfer_date }}</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer modal-premium-footer">
                <button type="button" class="btn btn-premium" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i> إغلاق
                </button>
            </div>
        </div>
    </div>
</div>
