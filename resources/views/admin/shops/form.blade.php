@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ isset($shop) ? 'Edit Shop' : 'Add New Shop' }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ isset($shop) ? route('admin.shops.update', $shop) : route('admin.shops.store') }}" 
                          method="POST">
                        @csrf
                        @if(isset($shop))
                            @method('PUT')
                        @endif

                        <div class="form-group">
                            <label for="name">Shop Name</label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $shop->name ?? '') }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" 
                                   class="form-control @error('address') is-invalid @enderror" 
                                   id="address" 
                                   name="address" 
                                   value="{{ old('address', $shop->address ?? '') }}" 
                                   required>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Location</label>
                            <div id="map" style="height: 400px;"></div>
                        </div>

                        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $shop->latitude ?? '') }}">
                        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $shop->longitude ?? '') }}">

                        <div class="form-group mt-3">
                            <div class="custom-control custom-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $shop->is_active ?? true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@php
    $defaultLat = old('latitude', $shop->latitude ?? '2.188168');  // Malaysia's approximate latitude
    $defaultLng = old('longitude', $shop->longitude ?? '102.2501');  // Malaysia's approximate longitude
@endphp
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places"></script>
<script>
let map, marker;

function initMap() {
    const defaultLocation = {
        lat: parseFloat("{{ $defaultLat }}"),
        lng: parseFloat("{{ $defaultLng }}")
    };

    map = new google.maps.Map(document.getElementById('map'), {
        center: defaultLocation,
        zoom: 13
    });

    marker = new google.maps.Marker({
        position: defaultLocation,
        map: map,
        draggable: true
    });

    // Update coordinates when marker is dragged
    google.maps.event.addListener(marker, 'dragend', function(event) {
        document.getElementById('latitude').value = event.latLng.lat();
        document.getElementById('longitude').value = event.latLng.lng();
    });

    // Initialize Places Autocomplete
    const input = document.getElementById('address');
    const autocomplete = new google.maps.places.Autocomplete(input);

    autocomplete.addListener('place_changed', function() {
        const place = autocomplete.getPlace();
        if (!place.geometry) return;

        map.setCenter(place.geometry.location);
        marker.setPosition(place.geometry.location);

        document.getElementById('latitude').value = place.geometry.location.lat();
        document.getElementById('longitude').value = place.geometry.location.lng();
    });
}

document.addEventListener('DOMContentLoaded', initMap);
</script>
@endpush