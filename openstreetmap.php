<?php
// Openstreetmap extension, https://github.com/GiovanniSalmeri/yellow-openstreetmap

class YellowOpenstreetmap {
    const VERSION = "0.8.20";
    public $yellow;         //access to API

    // Handle initialisation
    public function onLoad($yellow) {
        $this->yellow = $yellow;
        $this->yellow->system->setDefault("openstreetmapDirectory", "media/openstreetmap/");
        $this->yellow->system->setDefault("openstreetmapZoom", "14");
        $this->yellow->system->setDefault("openstreetmapStyle", "flexible");
        $this->yellow->system->setDefault("openstreetmapLayer", "standard+marker");
        $this->yellow->system->setDefault("openstreetmapTransportApiKey", "");
    }

    // Handle page content parsing of custom block
    public function onParseContentShortcut($page, $name, $text, $type) {
        $output = null;
        if ($name=="openstreetmap" && ($type=="block" || $type=="inline")) {
            $layers = [
                "standard"=>"mapnik",
                "transport"=>"transportmap",
                "cycle"=>"cyclemap",
                "humanitarian"=>"hot"
            ];
            list($address, $zoom, $style, $width, $height, $layer) = $this->yellow->toolbox->getTextArguments($text);
            if (empty($zoom)) $zoom = $this->yellow->system->get("openstreetmapZoom");
            if (empty($style)) $style = $this->yellow->system->get("openstreetmapStyle");
            if (empty($layer)) $layer = $this->yellow->system->get("openstreetmapLayer");
            list($layer, $marker) = $this->yellow->toolbox->getTextList($layer, "+", 2);

            if (strtolower(substr($address, -4))==".csv") { // leaflet
                $divContent = $this->parseMarkers($address);
                if ($divContent==false) return;
                $output = "<div class=\"".htmlspecialchars($style)." openstreetmap-markers\"";
                $output .= " data-layer=\"".$layer."\" data-marker=\"".(int)($marker=="marker")."\"";
                $output .= " data-zoom=\"".$zoom."\"";
                if ($width && $height) {
                    if (is_numeric($width)) $width .= "px";
                    if (is_numeric($height)) $height .= "px";
                    $output .= " style=\"width:".htmlspecialchars($width).";height:".htmlspecialchars($height)."\">\n";
                }
                $output .= htmlspecialchars($divContent)."\n";
                $output .= "</div>\n";
            } else { // non leaflet
                list($lat, $lon) = $this->addressToCoordinates($address);
                $layer = $layers[$layer];
                $bbox = $this->coordinatesToBbox($lat, $lon, $zoom, (is_numeric($width) ? $width : 1), (int)$height);
                $output = "<div class=\"".htmlspecialchars($style)." openstreetmap\">\n";
                $output .= "<iframe src=\"https://www.openstreetmap.org/export/embed.html?bbox=".rawurlencode($bbox)."&amp;layer=".$layer;
                if ($marker=="marker") $output .= "&amp;marker=".rawurlencode("$lat,$lon");
                $output .= "\" frameborder=\"0\"";
                if ($width && $height) $output .= " width=\"".htmlspecialchars($width)."\" height=\"".htmlspecialchars($height)."\"";
                $output .= "></iframe>\n";
                $output .= "</div>\n";
            }
        }
        return $output;
    }

    // Return JSONified markers
    private function parseMarkers($address) {
        $output = null;
        $lines = @file($this->yellow->system->get("openstreetmapDirectory").$address);
        if ($lines==false) return null;
        $data = [];
        foreach ($lines as $line) {
            list($address, $title, $description) = array_pad(str_getcsv($line), 3, null);
            list($lat, $lon) = $this->addressToCoordinates($address);
            $data[] = [ $lat, $lon, $title, $description ];
        }
        return json_encode($data);
    }


    private function addressToCoordinates($address) {
        if (substr($address, 0, 4)=="geo:") $address = substr($address, 4); // undocumented
        list($lat, $lon) = $this->yellow->toolbox->getTextList($address, ",", 2);
        $lat = trim($lat); $lon = trim($lon);
        if (!is_numeric($lat) || !is_numeric($lon)) list($lat, $lon) = $this->geolocation($address);
        return [ $lat, $lon ];
    }

    // https://wiki.openstreetmap.org/wiki/Slippy_map_tilenames
    private function getTileNumber($lat, $lon, $zoom) {
       $xtile = (($lon + 180) / 360) * (2 ** $zoom);
       $ytile = (1 - log(tan(deg2rad($lat)) + 1 / cos(deg2rad($lat))) / M_PI) /2 * (2 ** $zoom);
       return [ $xtile, $ytile ];
    }

    private function getCoordinates($xtile, $ytile, $zoom) {
       $n = 2 ** $zoom;
       $lat_deg = rad2deg(atan(sinh(M_PI * (1 - 2 * $ytile / $n))));
       $lon_deg = $xtile / $n * 360 - 180;
       return [ $lat_deg, $lon_deg ];
    }

    private function coordinatesToBbox($lat, $lon, $zoom, $width, $height) {
       $tileSize = 256;
       list($xtile, $ytile) = $this->getTileNumber($lat, $lon, $zoom);
       $xtile_s = ($xtile * $tileSize - $width/2) / $tileSize;
       $ytile_s = ($ytile * $tileSize - $height/2) / $tileSize;
       $xtile_e = ($xtile * $tileSize + $width/2) / $tileSize;
       $ytile_e = ($ytile * $tileSize + $height/2) / $tileSize;
       list($lat_s, $lon_s) = $this->getCoordinates($xtile_s, $ytile_s, $zoom);
       list($lat_e, $lon_e) = $this->getCoordinates($xtile_e, $ytile_e, $zoom);
       return "$lon_s,$lat_s,$lon_e,$lat_e";
    }

    // Get coordinates from physical address (https://nominatim.org/release-docs/develop/api/Search/)
    private function nominatim($address) {
        $ua = ini_set("user_agent", "Yellow Openstreetmap extension ". $this::VERSION);
        $nominatim = json_decode(@file_get_contents("https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&q=".rawurlencode($address)), true);
        ini_set("user_agent", $ua);
        if ($nominatim) {
            return [ (float)$nominatim[0]["lat"], (float)$nominatim[0]["lon"] ];
        } else {
            return [ null, null ];
        }
    }

    // Get geolocation
    private function geolocation($address) {
        $cache = [];
        $fileName = $this->yellow->system->get("coreExtensionDirectory")."openstreetmap.csv";
        $fileHandle = @fopen($fileName, "r");
        if ($fileHandle) {
            while ($data = fgetcsv($fileHandle)) {
                $cache[$data[0]] = [ $data[1], $data[2] ];
            }
            fclose($fileHandle);
        }
        if (!isset($cache[$address])) {
            $cache[$address] = $this->nominatim($address);
            if ($cache[$address]!==[ null, null ]) {
                $fileHandle = @fopen($fileName, "w");
                if ($fileHandle) {
                    if (flock($fileHandle, LOCK_EX)) {
                        foreach ($cache as $addr=>$coord) {
                            fputcsv($fileHandle, [ $addr, $coord[0], $coord[1] ]);
                        }
                        flock($fileHandle, LOCK_UN);
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
            $openstreetmapTransportApiKey = $this->yellow->system->get("openstreetmapTransportApiKey");
            $output .= "<script>var openstreetmapTransportApiKey = ".json_encode($openstreetmapTransportApiKey)."</script>\n";
            $output .= "<script type=\"text/javascript\" defer=\"defer\" src=\"{$extensionLocation}openstreetmap.js\"></script>\n";
        }
        return $output;
    }
}
