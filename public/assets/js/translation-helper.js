/**
 * Helper للترجمة في JavaScript
 */
window.TranslationHelper = {
    /**
     * ترجمة حالات الحاويات
     */
    containerStatus: function(status) {
        const translations = {
            'wait': 'في الانتظار',
            'transport': 'في النقل',
            'done': 'مكتملة',
            'rent': 'مؤجرة',
            'storage': 'في التخزين'
        };
        return translations[status] || status;
    },

    /**
     * ترجمة أحجام الحاويات
     */
    containerSize: function(size) {
        const sizes = {
            '20': '20 قدم',
            '40': '40 قدم',
            'box': 'صندوق'
        };
        return sizes[size] || size;
    },

    /**
     * ترجمة أنواع المعاملات
     */
    transactionType: function(type) {
        const types = {
            'income': 'وارد',
            'expense': 'منصرف'
        };
        return types[type] || type;
    },

    /**
     * ترجمة طرق الدفع
     */
    paymentMethod: function(method) {
        const methods = {
            'cash': 'نقدي',
            'bank': 'بنكي',
            'transfer': 'تحويل'
        };
        return methods[method] || method;
    },

    /**
     * ترجمة أدوار المستخدمين
     */
    userRole: function(role) {
        const roles = {
            'admin': 'مدير',
            'super_admin': 'مدير عام',
            'client': 'عميل',
            'clearance_office': 'مكتب تخليص',
            'partner': 'شريك',
            'employee': 'موظف',
            'driver': 'سائق'
        };
        return roles[role] || role;
    },

    /**
     * ترجمة أنواع السيارات
     */
    carType: function(type) {
        const types = {
            'truck': 'شاحنة',
            'van': 'فان',
            'pickup': 'بيك أب',
            'trailer': 'مقطورة'
        };
        return types[type] || type;
    },

    /**
     * ترجمة حالة السيارات
     */
    carStatus: function(status) {
        const statuses = {
            'active': 'نشطة',
            'inactive': 'غير نشطة',
            'maintenance': 'في الصيانة',
            'rented': 'مؤجرة'
        };
        return statuses[status] || status;
    },

    /**
     * ترجمة حالة الحسابات
     */
    accountStatus: function(status) {
        const statuses = {
            'active': 'نشط',
            'inactive': 'غير نشط',
            'suspended': 'معلق',
            'closed': 'مغلق'
        };
        return statuses[status] || status;
    },

    /**
     * ترجمة أنواع التربات
     */
    tipType: function(type) {
        const types = {
            'tip': 'إكرامية',
            'bonus': 'مكافأة',
            'reward': 'مكافأة',
            'gift': 'هدية'
        };
        return types[type] || type;
    },

    /**
     * ترجمة حالة الضرائب
     */
    taxStatus: function(status) {
        const statuses = {
            'active': 'نشط',
            'inactive': 'غير نشط',
            'exempt': 'معفى'
        };
        return statuses[status] || status;
    },

    /**
     * ترجمة حالة الشركاء
     */
    partnerStatus: function(status) {
        const statuses = {
            'active': 'نشط',
            'inactive': 'غير نشط',
            'pending': 'في الانتظار',
            'suspended': 'معلق'
        };
        return statuses[status] || status;
    },

    /**
     * ترجمة حالة الأرباح
     */
    profitStatus: function(status) {
        const statuses = {
            'pending': 'في الانتظار',
            'calculated': 'محسوبة',
            'distributed': 'موزعة',
            'paid': 'مدفوعة'
        };
        return statuses[status] || status;
    },

    /**
     * الحصول على لون الحالة
     */
    getStatusColor: function(status) {
        const colors = {
            'wait': 'warning',
            'transport': 'info',
            'done': 'success',
            'rent': 'primary',
            'storage': 'secondary'
        };
        return colors[status] || 'secondary';
    },

    /**
     * الحصول على أيقونة الحالة
     */
    getStatusIcon: function(status) {
        const icons = {
            'wait': 'fas fa-clock',
            'transport': 'fas fa-truck',
            'done': 'fas fa-check-circle',
            'rent': 'fas fa-hand-holding-usd',
            'storage': 'fas fa-warehouse'
        };
        return icons[status] || 'fas fa-question-circle';
    },

    /**
     * إنشاء badge للحالة
     */
    createStatusBadge: function(status, type = 'container') {
        const text = this[type + 'Status'] ? this[type + 'Status'](status) : status;
        const color = this.getStatusColor(status);
        const icon = this.getStatusIcon(status);

        return `<span class="badge badge-${color}"><i class="${icon}"></i> ${text}</span>`;
    },

    /**
     * ترجمة جميع الحالات في جدول
     */
    translateTableStatuses: function(tableSelector, statusColumnIndex = 0) {
        const table = document.querySelector(tableSelector);
        if (!table) return;

        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells[statusColumnIndex]) {
                const status = cells[statusColumnIndex].textContent.trim();
                const translatedStatus = this.containerStatus(status);
                if (translatedStatus !== status) {
                    cells[statusColumnIndex].innerHTML = this.createStatusBadge(status);
                }
            }
        });
    }
};

// استخدام تلقائي عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    // ترجمة تلقائية للجداول التي تحتوي على حالات
    if (typeof window.autoTranslateStatuses !== 'undefined' && window.autoTranslateStatuses) {
        TranslationHelper.translateTableStatuses('.containers-table', 0);
        TranslationHelper.translateTableStatuses('.transactions-table', 0);
        TranslationHelper.translateTableStatuses('.cars-table', 0);
    }
});
