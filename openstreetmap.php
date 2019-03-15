<?php
// OpenStreetMap extension
// Copyright (c) 2019 Giovanni Salmeri
// This file may be used and distributed under the terms of the public license.

class YellowOpenStreetMap {
    const VERSION = "0.8.2";
    const TYPE = "feature";
    public $yellow;         //access to API
   
    // Handle initialisation
    public function onLoad($yellow) {
        $this->yellow = $yellow;
        $this->yellow->system->setDefault("openstreetmapZoom", "15");
        $this->yellow->system->setDefault("openstreetmapStyle", "center");
        $this->yellow->system->setDefault("openstreetmapWidth", "450");
        $this->yellow->system->setDefault("openstreetmapHeight", "450");
        $this->yellow->system->setDefault("openstreetmapLayer", "standard");
    }
    
    // Handle page content parsing of custom block
    public function onParseContentShortcut($page, $name, $text, $type) {
        $output = null;
        if ($name=="openstreetmap" && ($type=="block" || $type=="inline")) {
            $layers = [
                'standard' => 'mapnik',
                'transport' => 'transportmap',
                'cycle' => 'cyclemap',
                'humanitarian' => 'hot',
            ];
            list($address, $zoom, $style, $width, $height, $layer) = $this->yellow->toolbox->getTextArgs($text);
            if (empty($width)) $width = $this->yellow->system->get("openstreetmapWidth");
            if (empty($height)) $height = $this->yellow->system->get("openstreetmapHeight");
            if (empty($zoom)) $zoom = $this->yellow->system->get("openstreetmapZoom");
            if (empty($style)) $style = $this->yellow->system->get("openstreetmapStyle");
            if (empty($layer)) $style = $this->yellow->system->get("openstreetmapLayer");

            if (substr($address, 0, 4) == "geo:") $address = (substr($address, 4));
            list($lat, $lon) = explode(",", explode(";", $address)[0]);
            if (!is_numeric($lat) || !is_numeric($lon)) list($lat, $lon) = $this->geolocation($address);

            list($layer, $marker) = explode("+", $layer);
            $layer = $layers[$layer];

            $bbox = $this->coordToBbox($lat, $lon, $zoom, (is_numeric($width) ? $width : 1), $height);
            $output = "<div class=\"".htmlspecialchars($style)."\">";
            $output .= "<iframe src=\"https://www.openstreetmap.org/export/embed.html?bbox=".rawurlencode($bbox)."&amp;layer=".$layer;
            if ($marker == "marker") $output .= "&amp;marker=".rawurlencode("$lat,$lon");
            $output .= "\" frameborder=\"0\"";
            if ($width && $height) $output .= " width=\"".htmlspecialchars($width)."\" height=\"".htmlspecialchars($height)."\"";
            $output .= "></iframe>";
            $output .= "</div>";
        }
        return $output;
    }

    // https://wiki.openstreetmap.org/wiki/Slippy_map_tilenames
    function getTileNumber($lat, $lon, $zoom) {
       $xtile = (($lon + 180) / 360) * pow(2, $zoom); // no rounding with floor()
       $ytile = (1 - log(tan(deg2rad($lat)) + 1 / cos(deg2rad($lat))) / pi()) /2 * pow(2, $zoom);
       return array($xtile, $ytile);
    }
    function getCoord($xtile, $ytile, $zoom) {
       $n = pow(2, $zoom);
       $lat_deg = rad2deg(atan(sinh(pi() * (1 - 2 * $ytile / $n))));
       $lon_deg = $xtile / $n * 360 - 180;
       return array($lat_deg, $lon_deg);
    }
    function coordToBbox($lat, $lon, $zoom, $width, $height) {
       define(TILE_SIZE, 256);
       list($xtile, $ytile) = $this->getTileNumber($lat, $lon, $zoom);
       $xtile_s = ($xtile * TILE_SIZE - $width/2) / TILE_SIZE;
       $ytile_s = ($ytile * TILE_SIZE - $height/2) / TILE_SIZE;
       $xtile_e = ($xtile * TILE_SIZE + $width/2) / TILE_SIZE;
       $ytile_e = ($ytile * TILE_SIZE + $height/2) / TILE_SIZE;
       list($lat_s, $lon_s) = $this->getCoord($xtile_s, $ytile_s, $zoom);
       list($lat_e, $lon_e) = $this->getCoord($xtile_e, $ytile_e, $zoom);
       return "$lon_s,$lat_s,$lon_e,$lat_e";
    }
    // https://wiki.openstreetmap.org/wiki/Nominatim#Address_lookup
    function geolocation($address) {
        $ua = ini_set("user_agent", "Yellow OpenStreetMap extension ". $this::VERSION);
        $nominatim = simplexml_load_file("https://nominatim.openstreetmap.org/search?format=xml&q=$address");
        ini_set("user_agent", $ua);
        if ($nominatim) {
            $lat = $nominatim->place["lat"];
            $lon = $nominatim->place["lon"];
            return array((float)$lat, (float)$lon);
        }
    }
}