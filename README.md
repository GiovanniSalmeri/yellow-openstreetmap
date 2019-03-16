Openstreetmap 0.8.2
================
Embed OpenStreetMap maps.

<p align="center"><img src="openstreetmap-screenshot.png?raw=true" alt="Screenshot"></p>

## How to install extension

1. [Download and install Datenstrom Yellow](https://github.com/datenstrom/yellow/).
2. [Download extension](../../archive/master.zip). If you are using Safari, right click and select 'Download file as'.
3. Copy `openstreetmap.zip` into your `system/extensions` folder.

To uninstall delete the [extension files](extension.ini).

## How to embed a map

Create a `[openstreetmap]` shortcut.

The following arguments are available, all but the first argument are optional:

`Address` = textual address (wrap multiple words into quotes) or GPS coordinates or [geo URI](https://en.wikipedia.org/wiki/Geo_URI_scheme)  
`Zoom` = zoom value from 0 to 19, the default zoom is 15  
`Style` = map style, e.g. `left`, `center`, `right`; the default style is `center`  
`Width` = map width, pixel or percent; the deafult is 450  
`Height` = map height, pixel; the default is 450  
`Layer` = map layer: you can choose between `standard`, `cycle`, `transport`, `humanitarian` (see [explication](https://wiki.openstreetmap.org/wiki/Browsing#Layers)); append `+marker` (e.g. `standard+marker`) to add the marker; the default is `standard`  

Textual addresses use [OSM's Nominatim](https://wiki.openstreetmap.org/wiki/Nominatim) service and results are cached in `system/extensions/openstreetmap.csv` (cache can be safely deleted).

GPS coordinates and geo URIs allow a greater precision. For getting the exact coordinates, go to [openstreetmap.org](https://www.openstreetmap.org/) and enter the address; the coordinates are the last numbers in the URL shown in the browser (if the URL `https://www.openstreetmap.org/#map=17/41.85181/12.62127` the coordinates are `41.85181, 12.62127`). To be as precise as possible, select the *Share* icon on the right, select *Include marker*, and drag the marker onto the point that will be the center of the embedded map.

## How to configure OpenStreetMap

The following settings can be configured in file `system/settings/system.ini`.

`openstreetmapZoom` (default:  `15`) = default zoom  
`openstreetmapStyle` (default:  `center`) = default style  
`openstreetmapWidth` (default:  `450`) = default width  
`openstreetmapHeight` (default:  `450`) = default height  
`openstreetmapLayer` (default:  `standard`) = default layer  

## Example

Embedding a map:

    [openstreetmap "Via Columbia 1 Roma"]
    [openstreetmap "Via Columbia 1 Roma" 17 center 600 400 standard+marker]

Embedding a map, GPS coordinates:

    [openstreetmap "41.85181, 12.62127"]
    [openstreetmap "41.85181, 12.62127" 17 center 600 400 standard+marker]

Embedding a map, geo URIs:

    [openstreetmap geo:41.85181,12.62127]
    [openstreetmap geo:41.85181,12.62127 17 center 600 400 standard+marker]


## Developer

Giovanni Salmeri.
