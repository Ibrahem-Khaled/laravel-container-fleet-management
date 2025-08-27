<div {{ $attributes->merge(['class' => 'col-xl-3 col-md-6 mb-4']) }}>
    <div class="card border-left-{{ $color }} shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xl font-weight-bold text-{{ $color }} text-uppercase mb-1 text-right">
                        {{ $title }}
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800 text-right">
                        {{ $count ?? $value ?? 0 }}
                    </div>
                </div>
                <div class="col-auto mr-3">
                    <i class="fas fa-{{ $icon }} fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
</div>
