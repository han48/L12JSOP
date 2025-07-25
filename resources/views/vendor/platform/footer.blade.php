@guest
    <ul class="nav justify-content-center mb-5">
        <li class="nav-item">
            <a href="#" target="_blank" rel="noopener" class="nav-link px-2 text-muted">
                {{ \App\Application::name() }} {{ \App\Application::version() }}
            </a>
        </li>
    </ul>
@else
    <div class="text-center user-select-none my-4 d-none d-lg-block">
        <p class="small mb-0">
            <a href="#" target="_blank" rel="noopener">
                {{ \App\Application::name() }} {{ \App\Application::version() }}
            </a>
        </p>
    </div>
@endguest
