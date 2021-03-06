<?php
// Openstreetmap extension, https://github.com/GiovanniSalmeri/yellow-openstreetmap

class YellowOpenstreetmap {
    const VERSION = "0.8.10";
    public $yellow;         //access to API

    // Handle initialisation
    public function onLoad($yellow) {
        $this->yellow = $yellow;
        $this->yellow->system->setDefault("openstreetmapZoom", "14");
        $this->yellow->system->setDefault("openstreetmapStyle", "flexible");
        $this->yellow->system->setDefault("openstreetmapWidth", "300");
        $this->yellow->system->setDefault("openstreetmapHeight", "150");
        $this->yellow->system->setDefault("openstreetmapLayer", "standard+marker");
    }
    
    // Handle page content parsing of custom block
    public function onParseContentShortcut($page, $name, $text, $type) {
        $output = null;
        if ($name=="openstreetmap" && ($type=="block" || $type=="inline")) {
            $layers = array(
                "standard" => "mapnik",
                "transport" => "transportmap",
                "cycle" => "cyclemap",
                "humanitarian" => "hot");
            list($address, $zoom, $style, $width, $height, $layer) = $this->yellow->toolbox->getTextArguments($text);
            if (empty($width)) $width = $this->yellow->system->get("openstreetmapWidth");
            if (empty($height)) $height = $this->yellow->system->get("openstreetmapHeight");
            if (empty($zoom)) $zoom = $this->yellow->system->get("openstreetmapZoom");
            if (empty($style)) $style = $this->yellow->system->get("openstreetmapStyle");
            if (empty($layer)) $layer = $this->yellow->system->get("openstreetmapLayer");

            if (substr($address, 0, 4) == "geo:") $address = (substr($address, 4));
            list($lat, $lon) = $this->yellow->toolbox->getTextList($address, ",", 2);
            $lat = trim($lat); $lon = trim($lon);
            if (!is_numeric($lat) || !is_numeric($lon)) list($lat, $lon) = $this->geolocation($address);
            list($layer, $marker) = $this->yellow->toolbox->getTextList($layer, "+", 2);
            $layer = $layers[$layer];

            $bbox = $this->coordToBbox($lat, $lon, $zoom, (is_numeric($width) ? $width : 1), $height);
            $output = "<div class=\"".htmlspecialchars($style)." map\">";
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
       $xtile = (($lon + 180) / 360) * pow(2, $zoom);
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
       $tileSize = 256;
       list($xtile, $ytile) = $this->getTileNumber($lat, $lon, $zoom);
       $xtile_s = ($xtile * $tileSize - $width/2) / $tileSize;
       $ytile_s = ($ytile * $tileSize - $height/2) / $tileSize;
       $xtile_e = ($xtile * $tileSize + $width/2) / $tileSize;
       $ytile_e = ($ytile * $tileSize + $height/2) / $tileSize;
       list($lat_s, $lon_s) = $this->getCoord($xtile_s, $ytile_s, $zoom);
       list($lat_e, $lon_e) = $this->getCoord($xtile_e, $ytile_e, $zoom);
       return "$lon_s,$lat_s,$lon_e,$lat_e";
    }
    // https://wiki.openstreetmap.org/wiki/Nominatim#Address_lookup
    function nominatim($address) {
        $ua = ini_set("user_agent", "Yellow OpenStreetMap extension ". $this::VERSION);
        $nominatim = simplexml_load_file("https://nominatim.openstreetmap.org/search?format=xml&q=$address");
        ini_set("user_agent", $ua);
        if ($nominatim) {
            $lat = (float)$nominatim->place["lat"];
            $lon = (float)$nominatim->place["lon"];
            return array($lat, $lon);
        } else {
            return array(0, 0);
        }
    }
    function geolocation($address) {
        $cache = array();
        $fileName = $this->yellow->system->get("coreExtensionDirectory")."openstreetmap.csv";
        $fileHandle = @fopen($fileName, "r");
        if ($fileHandle) {
            while ($data = fgetcsv($fileHandle)) {
                $cache[$data[0]] = array($data[1], $data[2]);
            }
            fclose($fileHandle);
        }
        if (!isset($cache[$address])) {
            $cache[$address] = $this->nominatim($address);
            if (isset($cache[$address][0]) && isset($cache[$address][1])) {
                $fileHandle = @fopen($fileName, "w");
                if ($fileHandle) {
                    foreach ($cache as $addr => $coord) {
                        fputcsv($fileHandle, array($addr, $coord[0], $coord[1]));
                    }
                    fclose($fileHandle);
                } else {
                    $this->yellow->log("error", "Can't write file '$fileName'!");
                }
            }
        }
        return $cache[$address];
    }

    // Handle page extra data
    public function onParsePageExtra($page, $name) {
        $output = null;
        if ($name=="header") {
            $extensionLocation = $this->yellow->system->get("coreServerBase").$this->yellow->system->get("coreExtensionLocation");
            $output .= "<script type=\"text/javascript\" defer=\"defer\" src=\"{$extensionLocation}openstreetmap.js\"></script>\n";
        }
        return $output;
    }

}
