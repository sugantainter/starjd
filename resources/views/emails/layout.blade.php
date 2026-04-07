@component('mail::layout')
{{-- Header --}}
<table width="100%" style="background:#0d6efd;color:#ffffff;padding:30px 0;text-align:center;">
    <tr>
        <td>
            <h1 style="margin:0;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">
                {{ config('app.name') }}
            </h1>
        </td>
    </tr>
</table>

{{-- Body --}}
@yield('content')

{{-- Footer --}}
<table width="100%" style="background:#f8f9fa;color:#6c757d;padding:20px;text-align:center;font-size:13px;">
    <tr>
        <td>
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
            <a href="{{ url('/') }}" style="color:#0d6efd;text-decoration:none;">
                Visit our website
            </a>
        </td>
    </tr>
</table>
@endcomponent