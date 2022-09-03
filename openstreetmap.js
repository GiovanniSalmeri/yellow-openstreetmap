// Openstreetmap extension, https://github.com/GiovanniSalmeri/yellow-openstreetmap

"use strict";
document.addEventListener("DOMContentLoaded", function() {
    // https://github.com/kylelam/kylelam.github.io/blob/master/iframe.html
    document.querySelectorAll("div.openstreetmap").forEach(function(map) {
        map.firstElementChild.style.pointerEvents = "none";
        map.addEventListener("mousedown", function(e) { e.target.firstElementChild.style.pointerEvents = "auto"; }, false);
        map.addEventListener("mouseleave", function(e) { e.target.firstElementChild.style.pointerEvents = "none"; }, false);
    });

    var divs = document.querySelectorAll("div.openstreetmap-markers");
    if (divs.length) {
        var head = document.querySelector("head");
        var leafletCss = document.createElement("link");
        leafletCss.rel = "stylesheet";
        leafletCss.type = "text/css";
        leafletCss.href = "https://unpkg.com/leaflet@1.8.0/dist/leaflet.css";
        head.appendChild(leafletCss);
        var leafletScript = document.createElement("script");
        leafletScript.type = "text/javascript";
        leafletScript.src = "https://unpkg.com/leaflet@1.8.0/dist/leaflet.js";
        head.appendChild(leafletScript);
        const tileUrls = {
            standard: "https://tile.openstreetmap.org/{z}/{x}/{y}.png",
            transport: `https://tile.thunderforest.com/transport/{z}/{x}/{y}.png?apikey=${openstreetmapTransportApiKey}`,
            cycle: "https://a.tile-cyclosm.openstreetmap.fr/cyclosm/{z}/{x}/{y}.png",
            humanitarian: "https://a.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png"
        };
        leafletScript.addEventListener("load", function() {
            var leaflet = L.noConflict();
            divs.forEach(function(div) {
                var layerType = div.dataset.layer;
                var markers = JSON.parse(div.textContent);
                div.textContent = null;
                var map = leaflet.map(div, { scrollWheelZoom: false });
                map.on("focus", function() { map.scrollWheelZoom.enable(); });
                map.on("blur", function() { map.scrollWheelZoom.disable(); });
                leaflet.tileLayer(tileUrls[layerType], {
                    maxZoom: 19,
                    attribution: "© <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors"
                }).addTo(map);
                var minX = Infinity, maxX = -Infinity;
                var minY = Infinity, maxY = -Infinity;
                markers.forEach(function(marker) {
                    if (marker[0] < minX) minX = marker[0];
                    if (marker[0] > maxX) maxX = marker[0];
                    if (marker[1] < minY) minY = marker[1];
                    if (marker[1] > maxY) maxY = marker[1];
                    if (div.dataset.marker=="1") {
                        var markerOnMap = leaflet.marker([ marker[0], marker[1] ]).addTo(map);
                        if (marker[2] || marker[3]) {
                            var label = [];
                            if (marker[2]) label.push("<b>"+marker[2]+"</b>");
                            if (marker[3]) label.push(marker[3]);
                            markerOnMap.bindPopup(label.join("<br />"));
                        }
                    }
                });
                map.fitBounds([ [ minX, minY ], [ maxX, maxY ] ], { 
                    maxZoom: div.dataset.zoom,
                    padding: [ 30, 30 ]
                });
            });
        });
    }
});
