@php

    $breadcrumbs = explode('/', Request::path());
    if (! in_array('home', $breadcrumbs)) { array_unshift($breadcrumbs, 'home'); }
    foreach ($breadcrumbs as $key => $breadcrumb) {
        if (is_numeric($breadcrumb)) {
            $breadcrumbs[$key] = 'view';
            if (in_array('edit', $breadcrumbs)) { unset($breadcrumbs[$key]); }
        }
    }

@endphp

<nav aria-label="breadcrumb">
    <ol class="breadcrumb justify-content-center">
        @foreach($breadcrumbs as $breadcrumb)

            @if ($loop->last)

                <li class="breadcrumb-item active" aria-current="page">{{ucwords(str_replace('-', ' ', $breadcrumb))}}</li>

            @else

                <li class="breadcrumb-item"><a href="/{{$breadcrumb}}">{{ucwords(str_replace('-', ' ', $breadcrumb))}}</a></li>

            @endif

        @endforeach
    </ol>
</nav>

