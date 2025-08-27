<div class="row mb-5 align-items-center">
    <!-- معلومات الشركة واللوجو -->
    <div class="col-md-6">
        <div class="d-flex justify-content-between align-items-start">

            <!-- معلومات الشركة -->
            <div class="invoice-company-info text-right">
                <h2 class="text-primary font-weight-bold mb-3" style="font-size: 2rem;">{{ $companyName }}</h2>
                <p class="mb-2">
                    <i class="fas fa-map-marker-alt ml-2"></i>
                    {{ $companyAddress }}
                </p>
                <p class="mb-2">
                    <i class="fas fa-phone ml-2"></i>
                    {{ $companyPhone }}
                </p>
                <p class="mb-0">
                    <i class="fas fa-envelope ml-2"></i>
                    {{ $companyEmail }}
                </p>
            </div>

            <!-- اللوجو -->
            <div class="company-logo mr-3">
                <img src="{{ asset('assets/img/logo.png') }}" alt="لوجو الشركة" style="max-height: 200px;">
            </div>
        </div>
    </div>

    <!-- معلومات العميل -->
    <div class="col-md-6">
        <div class="d-flex justify-content-between align-items-center">
            <div class="client-info-box p-3 bg-light rounded text-right" style="flex-grow: 1;">
                <h1 class="text-success font-weight-bold mb-3" style="font-size: 2rem;">{{ $title }}</h1>
                <h3 class="text-primary mb-2">{{ $clientName }}</h3>
                <p class="mb-0 font-weight-bold">
                    <span class="text-muted">كشف حساب شهر :</span>
                    {{ $monthName }}
                </p>
            </div>
        </div>
    </div>
</div>
