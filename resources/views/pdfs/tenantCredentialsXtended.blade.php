@extends('pdfs.layout')

@section('title')
    {{ __("tenant.tenant_credentials") }}
@endsection

@section('body')
<table>
  <tr>
    <td class="logo"><img src="{{ asset('images/logo3.png') }}" /></td>
    <td class="contact">
        <p>{{ $re->name }}</p>
        <p>{{ $re->address->street_nr }}, {{ $re->address->street }}<br />
        {{ $re->address->city }}, {{ $re->address->state->name }}</p>
        <p>T:{{ $re->phone }}; E:{{ $re->email }}</p>
    </td>
  </tr>
  <tr>
    <td colspan=2><hr class="separator" /></td>
  </tr>
</table>

<table>
  <tr><th><h2>{{ __("tenant.tenancy_details") }}</h2></th></tr>
  @if ($tenant->unit)
  <tr>
    <td class="detail-key">{{ __("tenant.unit") }}</td>
    <td>{{ $tenant->unit->name }}</td>
  </tr>
  <tr>
    <td class="detail-key">{{ __("tenant.floor") }}</td>
    <td>{{ $tenant->unit->floor }}</td>
  </tr>
  @endif
  @if ($tenant->building)
  <tr>
    <td class="detail-key">{{ __("tenant.building") }}</td>
    <td>{{ $tenant->building->name }}</td>
  </tr>
  @endif
  @if ($tenant->address)
  <tr>
    <td class="detail-key">{{ __("tenant.address") }}</td>
    <td>{{ $tenant->address->street_nr }}, {{ $tenant->address->street }} {{ $tenant->address->city }}</td>
  </tr>
  @endif
  @if ($tenant->rent_start)
  <tr>
    <td class="detail-key">{{ __("tenant.rent_start") }}</td>
    <td>{{ $tenant->rent_start->format('Y-m-d')}}</td>
  </tr>
  @endif
</table>

<table>
  <tr><th><h2>{{ __("tenant.login_credentials") }}</h2></th></tr>
  <tr>
    <td class="detail-key">{{ __("tenant.website") }}</td>
    <td>{{ url('/') }}</td>
  </tr>
  <tr>
    <td class="detail-key">{{ __("tenant.username") }}</td>
    <td>{{ $tenant->user->email }}</td>
  </tr>
  <tr>
    <td class="detail-key">{{ __("tenant.url") }}</td>
    <td>{{ $url }}</td>
  </tr>
</table>
@endsection
