<!-- Leaderboard Card -->
<div class="col-lg-4 col-md-6 mb-4">
    <div class="card">
        <div class="card-header p-3 pt-2">
            <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                <i class="fas fa-trophy opacity-10"></i>
            </div>
            <div class="text-end pt-1">
                <p class="text-sm mb-0 text-capitalize">Peringkat Anda</p>
                <h4 class="mb-0">{{ $currentUserRank->rank ?? 'N/A' }}</h4>
            </div>
        </div>
        <hr class="dark horizontal my-0">
        <div class="card-footer p-3">
            <p class="mb-0">
                @if(isset($currentUserRank))
                <span class="level-badge level-{{ $currentUserRank->badge_color }}">{{ $currentUserRank->badge }}</span>
                @endif
                <a href="{{ route('mahasiswa.leaderboard') }}" class="text-primary float-end">
                    Lihat Leaderboard <i class="fas fa-arrow-right text-xs ms-1"></i>
                </a>
            </p>
        </div>
    </div>
</div> 