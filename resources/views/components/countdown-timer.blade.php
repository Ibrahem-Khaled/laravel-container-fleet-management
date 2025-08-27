<style>
    .countdown-timer {
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 0.9rem;
        color: #fff;
        background: #cb0c9f;
        border-radius: 5px;
        padding: 5px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .countdown-timer .time-unit {
        margin: 0 5px;
        text-align: center;
        position: relative;
        height: 60px;
    }

    .countdown-timer .time-unit span {
        font-size: 1.2rem;
        font-weight: bold;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .finished {
        font-size: 1.2rem;
        color: #ff4d4d;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .loading-spinner {
        border: 4px solid #f3f3f3;
        border-radius: 50%;
        border-top: 4px solid #cb0c9f;
        width: 20px;
        height: 20px;
        animation: spin 2s linear infinite;
        margin: 0 auto;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

@props(['id', 'transfer_date', 'date_empty', 'type' => null])

<div id="loading-{{ $id }}" class="loading-spinner"></div>
@if ($date_empty == null)
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editDateModal-{{ $id }}">
        {{ $type === 'custom' ? 'اضافة تاريخ أرضية الجمرك' : 'اضافة تاريخ ارضية الفارغ' }}
    </button>
@else
    <div id="countdown-{{ $id }}" class="countdown-timer d-none" data-toggle="modal"
        data-target="#editDateModal-{{ $id }}"></div>
@endif

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var createdAt = new Date("{{ $transfer_date }}").getTime();

        // ضبط نهاية اليوم للوقت بحيث يكون التاريخ حتى الساعة 23:59:59
        var dateEmpty = new Date("{{ $date_empty }}");
        dateEmpty.setHours(23, 59, 59, 999); // تعيين نهاية اليوم

        var countdownElement = document.getElementById("countdown-{{ $id }}");
        var loadingElement = document.getElementById("loading-{{ $id }}");

        // إظهار المؤقت بعد فترة قصيرة وإخفاء دائرة التحميل
        setTimeout(function() {
            loadingElement.classList.add('d-none');
            countdownElement.classList.remove('d-none');
        }, 1000);

        var countdownInterval = setInterval(function() {
            var now = new Date().getTime();
            var distance = dateEmpty.getTime() - now;

            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            countdownElement.innerHTML =
                "<div class='time-unit'><span>" + seconds + "</span><br>ثانية</div>" +
                "<div class='time-unit'><span>" + minutes + "</span><br>دقيقة</div>" +
                "<div class='time-unit'><span>" + hours + "</span><br>ساعة</div>" +
                "<div class='time-unit'><span>" + days + "</span><br>يوم</div>";

            if (distance < 0) {
                clearInterval(countdownInterval);
                countdownElement.innerHTML = "<div class='finished'>انتهى الوقت</div>";
            }
        }, 1000);
    });
</script>
