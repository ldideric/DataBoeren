import * as L from 'leaflet';

/**
 * Leaflet map used as the "Kaart" view of the campsite search.
 *
 * The map itself lives inside a `wire:ignore` element so Livewire never
 * morphs it away. Marker data is handed in once on render and afterwards
 * refreshed through the `map-markers-updated` Livewire event, so the map
 * always mirrors the same availability filters as the list view.
 */
document.addEventListener('alpine:init', () => {
    window.Alpine.data('campsiteMap', (initialMarkers, config) => ({
        config,
        markers: initialMarkers ?? [],
        map: null,
        layer: null,
        bounds: null,

        init() {
            this.renderMap();
            this.$nextTick(() => this.map?.invalidateSize());

            window.Livewire.on('map-markers-updated', ({ markers }) => {
                this.markers = markers ?? [];
                this.drawMarkers();
            });

            // The map starts hidden in list view; recalculate its size once shown.
            window.addEventListener('map-shown', () => {
                if (!this.map) return;
                this.map.invalidateSize();
                this.fitToImage();
            });
        },

        renderMap() {
            this.map = L.map(this.$refs.map, {
                crs: L.CRS.Simple,
                minZoom: -2.6,
                maxZoom: -0.5,
                zoomControl: false,
            });
            L.control.zoom({ position: 'topright' }).addTo(this.map);
            this.layer = L.layerGroup().addTo(this.map);

            const img = new Image();
            img.onload = () => {
                this.bounds = [[0, 0], [img.naturalHeight, img.naturalWidth]];
                L.imageOverlay(this.config.image, this.bounds).addTo(this.map);
                this.map.setMaxBounds(this.bounds);
                this.map.options.maxBoundsViscosity = 1.0;
                this.fitToImage();
                this.drawMarkers();
            };
            img.src = this.config.image;
        },

        fitToImage() {
            if (!this.bounds) return;
            this.map.fitBounds(this.bounds);
            this.map.setZoom(this.map.getZoom() - 0.5);
        },

        drawMarkers() {
            if (!this.layer) return;
            this.layer.clearLayers();

            this.markers.forEach((marker) => {
                L.marker([marker.lat, marker.lng], { icon: this.iconFor(marker.type) })
                    .on('click', () => window.dispatchEvent(
                        new CustomEvent('open-campsite', { detail: marker })
                    ))
                    .addTo(this.layer);
            });
        },

        iconFor(type) {
            const url = this.config.icons[type] ?? this.config.icons.default;
            const size = type === 'Varkensveld' ? [50, 50] : [42, 42];

            return L.icon({
                iconUrl: url,
                iconSize: size,
                iconAnchor: [size[0] / 2, size[1] / 2],
            });
        },
    }));
});
