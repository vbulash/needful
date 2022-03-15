<!-- User Dropdown -->
<div class="dropdown d-inline-block">
    <button type="button" class="btn btn-alt-secondary" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-fw fa-user d-sm-none"></i>
        <span class="d-none d-sm-inline-block">{{ Illuminate\Support\Facades\Auth::user()->name }}</span>
        <i class="fa fa-fw fa-angle-down opacity-50 ms-1 d-none d-sm-inline-block"></i>
    </button>
    <div class="dropdown-menu dropdown-menu-end p-0" aria-labelledby="page-header-user-dropdown">
{{--        <div class="bg-primary-dark rounded-top fw-semibold text-white text-center p-3">--}}
{{--            User Options--}}
{{--        </div>--}}
        <div class="p-2">
            <a class="dropdown-item" href="javascript:void(0)">
                <i class="far fa-fw fa-user me-1"></i> Профиль
            </a>

            <div role="separator" class="dropdown-divider"></div>
            <a class="dropdown-item" href="{{ route('logout') }}">
                <i class="far fa-fw fa-arrow-alt-circle-left me-1"></i> Выход
            </a>
        </div>
    </div>
</div>
<!-- END User Dropdown -->
