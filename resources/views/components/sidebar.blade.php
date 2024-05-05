<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ url('/') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('template/assets/img/favicon/favicon.png') }}" class="img-fluid" />
            </span>
            <span class="app-brand-text demo menu-text fw-bold">
                Meppo Gen
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        @php
            $menus = App\Models\Menu::menuUser(auth()->id());
        @endphp
        @foreach($menus as $parent)
            <li class="menu-item">
                <a 
                    @if(count($parent['children']) > 0) 
                        href="javascript:void(0);" class="menu-link menu-toggle" 
                    @else 
                        href="{{$parent['parent']->pathmenu}}" class="menu-link" 
                    @endif >
                    <i class="menu-icon {{$parent['parent']->icon}}"></i>
                    <div>{{ $parent['parent']->displayname }}</div>
                </a>
                <ul class="menu-sub">
                @foreach($parent['children'] as $child)
                    <li class="menu-item">
                        <a 
                            @if(count($child['subchildren']) > 0) 
                                href="javascript:void(0);" class="menu-link menu-toggle"
                            @else 
                                href="{{$child['child']->pathmenu}}" class="menu-link"
                            @endif>
                            <div>{{ $child['child']->displayname }}</div>
                        </a>
                        <ul class="menu-sub">
                        @foreach($child['subchildren'] as $subchild)
                            <li class="menu-item">
                                <a href="{{$child['child']->pathmenu}}" class="menu-link">
                                    <div>{{ $subchild->displayname }}</div>
                                </a>
                            </li>
                        @endforeach
                        </ul>
                    </li>
                @endforeach
                </ul>
            </li>
        @endforeach
    </ul>
</aside>
