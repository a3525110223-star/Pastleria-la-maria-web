<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<aside class="sidebar" id="sidebar">
    <div class="sidebar_brand">
        <div class="sidebar_brand-mark" aria-hidden="true">LM</div>
        <div class="sidebar_brand-text">
            <span class="sidebar_brand-name">La María</span>
            <span class="sidebar_brand-caption">Pastelería</span>
        </div>
    </div>
    <div class="sidebar_section-label">MENÚ PRINCIPAL</div>
    <nav class="sidebar_nav" aria-label="Navegación principal">
        <a href="{{ route('dashboard') }}"
           class="sidebar_link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}"
           @if(request()->routeIs('dashboard')) aria-current="page" @endif>
            <i class="fa-solid fa-chart-line sidebar_icon" aria-hidden="true"></i>
            <span>Panel principal</span>
        </a>
        <a href="{{ route('productos.index') }}"
           class="sidebar_link {{ request()->routeIs('productos.*') ? 'is-active' : '' }}"
           @if(request()->routeIs('productos.*')) aria-current="page" @endif>
            <i class="fa-solid fa-cake-candles sidebar_icon" aria-hidden="true"></i>
            <span>Productos</span>
        </a>
        <a href="{{ route('categorias.index') }}"
           class="sidebar_link {{ request()->routeIs('categorias.*') ? 'is-active' : '' }}"
           @if(request()->routeIs('categorias.*')) aria-current="page" @endif>
            <i class="fa-solid fa-tags sidebar_icon" aria-hidden="true"></i>
            <span>Categorías</span>
        </a>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('users.index') }}"
               class="sidebar_link {{ request()->routeIs('users.*') ? 'is-active' : '' }}"
               @if(request()->routeIs('users.*')) aria-current="page" @endif>
                <i class="fa-solid fa-users sidebar_icon" aria-hidden="true"></i>
                <span>Usuarios</span>
            </a>
            <a href="{{ route('roles.index') }}"
               class="sidebar_link {{ request()->routeIs('roles.*') ? 'is-active' : '' }}"
               @if(request()->routeIs('roles.*')) aria-current="page" @endif>
                <i class="fa-solid fa-user-shield sidebar_icon" aria-hidden="true"></i>
                <span>Roles</span>
            </a>
            <div class="sidebar_divider"></div>
            <a href="{{ route('empresa.edit') }}"
               class="sidebar_link {{ request()->routeIs('empresa.*') ? 'is-active' : '' }}"
               @if(request()->routeIs('empresa.*')) aria-current="page" @endif>
                <i class="fa-solid fa-store sidebar_icon" aria-hidden="true"></i>
                <span>Empresa</span>
            </a>
        @endif
    </nav>
    <div class="sidebar_footer">
        <span class="sidebar_footer-dot" aria-hidden="true"></span>
        <span>Panel administrativo</span>
    </div>
    <div class="sidebar-logout">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión
            </button>
        </form>
    </div>
</aside>
