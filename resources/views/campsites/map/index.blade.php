@extends('layouts.app')
@section('header', 'Plattegrond')
@section('content')
    @php
    $dbCampsites = \App\Models\Campsite::all()
        ->keyBy(fn($c) => $c->name . '|' . $c->type->value);
    $campsites = collect(json_decode(file_get_contents(database_path('src/campsites.json')), true))
        ->filter(fn($c) => isset($c['lat'], $c['lng']))
        ->map(fn($c) => array_merge($c, [
            'id' => $dbCampsites[$c['name'] . '|' . strtolower($c['type'])]->id ?? null
        ]))
        ->values();
    @endphp
    <div class="relative min-h-[calc(100vh-6.8rem)] m-0 overflow-hidden px-4">
        <div id="map" class="absolute left-3/4 top-0 bottom-0 w-1/2 max-w-full max h-full -translate-x-1/2 bg-gray-900"></div>
        @include('campsites.partials.modal', [
            'wrapperClass' => 'hidden fixed top-[6.8rem] left-0 bottom-0 w-1/2 items-start justify-start p-4 sm:p-6 overflow-hidden',
            'dialogClass'  => 'bg-tan-300 w-full h-full rounded-none overflow-hidden flex flex-col',
        ])
        <script>
        document.addEventListener('DOMContentLoaded', () => {
            const IMAGE_URL   = @json(asset('img/camping_map.png'));
            const MAP_OPTIONS = { crs: L.CRS.Simple, minZoom: -2.6, maxZoom: -0.5, zoomControl: false };
            const map = L.map('map', MAP_OPTIONS);
            L.control.zoom({ position: 'topright' }).addTo(map);
            
            const campsites   = @json($campsites);

            const ICONS = {
                'Varkensveld': L.icon({ iconUrl: 'img/icon_varken.png', iconSize: [50, 50], iconAnchor: [19, 19] }),
                'Paardenveld': L.icon({ iconUrl: 'img/icon_paard.png', iconSize: [42, 42], iconAnchor: [19, 19] }),
            };
            const DEFAULT_ICON = L.icon({ iconUrl: 'img/icon_default.png', iconSize: [42, 42], iconAnchor: [19, 19] });

            const groups = {};

            campsites.forEach(site => {
                const type = site.type ?? 'Overig';
                if (!groups[type]) groups[type] = L.layerGroup().addTo(map);

                const icon   = ICONS[type] ?? DEFAULT_ICON;
                const marker = L.marker([site.lat, site.lng], { icon });

                marker.on('click', () => {
                    if (typeof openModal !== 'function') return;
                    const el = document.createElement('div');
                    el.dataset.campsiteId   = site.id              ?? '';
                    el.dataset.name         = site.name            ?? '';
                    el.dataset.campsiteType = site.type            ?? '';
                    el.dataset.people       = site.max_people      ?? '';
                    el.dataset.vehicles     = site.max_vehicles    ?? '';
                    el.dataset.electricity  = site.has_electricity ? 'true' : 'false';
                    el.dataset.notes        = site.notes           ?? '';
                    openModal(el);
                });
                marker.addTo(groups[type]);
            });

            const layerControl = L.control.layers(null, groups, { collapsed: false, position: 'topleft' }).addTo(map);
            layerControl.getContainer().style.cssText = `
                margin-top: 2%;
                margin-left: 2%;
                min-width: 115px;
            `;

            const img = new Image();
            img.onload = () => {
                const bounds = [[0, 0], [img.naturalHeight, img.naturalWidth]];
                L.imageOverlay(IMAGE_URL, bounds).addTo(map);
                map.setMaxBounds(bounds);
                map.options.maxBoundsViscosity = 1.0;
                map.fitBounds(bounds);
                map.setZoom(map.getZoom() - 0.5);
            };
            img.src = IMAGE_URL;
        });
        </script>
    </div>
@endsection