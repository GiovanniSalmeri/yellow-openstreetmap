Openstreetmap 0.8.10
====================
Embed OpenStreetMap maps.

<p align="center"><img src="openstreetmap-screenshot.png?raw=true" alt="Screenshot"></p>

## How to show a map

Create a `[openstreetmap]` shortcut.

The following arguments are available, all but the first argument are optional:

`Address` = textual address (wrap multiple words into quotes) or GPS coordinates or [geo URI](https://en.wikipedia.org/wiki/Geo_URI_scheme)  
`Zoom` = zoom value from 0 to 19  
`Style` = map style, e.g. `left`, `center`, `right`, `flexible`  
`Width` = map width, pixel or percent  
`Height` = map height  
`Layer` = map layer: you can choose between `standard`, `cycle`, `transport`, `humanitarian` (see [explication](https://wiki.openstreetmap.org/wiki/Browsing#Layers)); append `+marker` to add the marker  

GPS coordinates and geo URIs allow a greater precision. For getting the exact coordinates, go to [openstreetmap.org](https://www.openstreetmap.org/) and enter the address; the coordinates are the last numbers in the URL shown in the browser (if the URL `https://www.openstreetmap.org/#map=17/41.85181/12.62127` the coordinates are `41.85181, 12.62127`). To be as precise as possible, select the *Share* icon on the right, select *Include marker*, and drag the marker onto the point that will be the center of the embedded map.

## Examples

Showing a map:

    [openstreetmap "Via Columbia 1 Roma"]
    [openstreetmap "Via Columbia 1 Roma" 17 center 600 400 standard+marker]

Showing a map, GPS coordinates:

    [openstreetmap "41.85181, 12.62127"]
    [openstreetmap "41.85181, 12.62127" 17 center 600 400 standard+marker]

Showing a map, geo URIs:

    [openstreetmap geo:41.85181,12.62127]
    [openstreetmap geo:41.85181,12.62127 17 center 600 400 standard+marker]

## Settings

The following settings can be configured in file `system/extensions/yellow-system.ini`:

`OpenstreetmapZoom` (default:  `14`) = default zoom  
`OpenstreetmapStyle` (default:  `flexible`) = default style  
`OpenstreetmapWidth` (default:  `300`) = default width  
`OpenstreetmapHeight` (default:  `150`) = default height  
`OpenstreetmapLayer` (default:  `standard+marker`) = default layer  

The following files can be configured:

`system/extensions/openstreetmap.csv` = cached addresses and coordinates  

## Installation

[Download extension](https://github.com/GiovanniSalmeri/yellow-openstreetmap/archive/master.zip) and copy zip file into your `system/extensions` folder. Right click if you use Safari.

This extension uses [OSM's Nominatim](https://wiki.openstreetmap.org/wiki/Nominatim) service for address lookup.

## Developer

Giovanni Salmeri. [Get help](https://github.com/GiovanniSalmeri/yellow-openstreetmap/issues).
