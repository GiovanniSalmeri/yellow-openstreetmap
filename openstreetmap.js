// Openstreetmap extension, https://github.com/GiovanniSalmeri/yellow-openstreetmap

"use strict";
document.addEventListener("DOMContentLoaded", function() {
    // https://github.com/kylelam/kylelam.github.io/blob/master/iframe.html
    document.querySelectorAll("div.openstreemap").forEach(function(map) {
        map.firstChild.style.pointerEvents = "none";
        map.addEventListener("mousedown", function(e) { e.target.firstChild.style.pointerEvents = "auto"}, false);
        map.addEventListener("mouseleave", function(e) { e.target.firstChild.style.pointerEvents = "none"}, false);
    });
});
