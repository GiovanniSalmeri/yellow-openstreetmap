Openstreetmap 0.8.20
====================
Embed OpenStreetMap maps.

<p align="center"><img src="openstreetmap-screenshot.png?raw=true" alt="Screenshot"></p>

## How to embed a map

Create an `[openstreetmap]` shortcut.

The following arguments are available, all but the first argument are optional:

`Address` = GPS coordinates, or textual address (wrap multiple words into quotes), or name of POI (points of interest) file  
`Zoom` = zoom value, from 0 to 19  
`Style` = map style, e.g. `left`, `center`, `right`, `flexible`  
`Width` = map width, pixel or percent  
`Height` = map height, pixel  
`Layer` = [map layer](https://wiki.openstreetmap.org/wiki/Browsing#Layers): `standard`, `cycle`, `transport`, or `humanitarian`; append `+marker` to show the marker(s)  

To get the coordinates, go to [openstreetmap.org](https://www.openstreetmap.org/) and enter the address; for more precision, select the "Share" icon on the right, then "Include marker", and drag the marker to the desidered location. The coordinates are the last numbers of the URL shown in the browser (if the URL `https://www.openstreetmap.org/#map=17/41.85181/12.62127` the coordinates are `41.85181, 12.62127`).

## How to create a POI (points of interest) file

Put the file into `media/openstreetmap/` with the extension `.csv`. Each point of interest is a line in CSV (comma-separated values) format in one of these two forms:

```
latitude,longitude,name,description
textual address,city,name,description
```

Fields that cointain commas must be enclosed in quotes. The third and fourth field are optional, if provided they will be used for a popup.

## Examples

Embedding a map:

    [openstreetmap "Via Columbia 1, Roma"]
    [openstreetmap "Via Columbia 1, Roma" 17 center 100% 400 standard+marker]

Embedding a map, GPS coordinates:

    [openstreetmap "41.85181, 12.62127"]
    [openstreetmap "41.85181, 12.62127" 17 center 100% 400 cycle]

Embedding a map, multiple points of interest:

    [openstreetmap rome.csv 17 center 100% 400 transport+marker]

POI file:

```
Piazza di Spagna,Roma,Piazza di Spagna,"At the bottom of the Spanish Steps, one of the most famous squares in Rome"
Piazza Colonna,Roma,Piazza Colonna,"Named for the marble Column of Marcus Aurelius, which has stood there since AD 193"
41.89772,12.47231,Statua di Pasquino,"The first talking statue of Rome: he spoke out about the people's dissatisfaction, denounced injustice, and assaulted misgovernment"
Piazza della Rotonda,Roma,Piazza della Rotonda,"On the north side of the Pantheon, the square gets its name from its informal title as the church of Santa Maria Rotonda"
```

## Settings

The following settings can be configured in file `system/extensions/yellow-system.ini`:

`OpenstreetmapDirectory` (default: `media/openstreetmap/`) = directory for POI files
`OpenstreetmapZoom` (default:  `14`) = default zoom  
`OpenstreetmapStyle` (default:  `flexible`) = default style  
`OpenstreetmapLayer` (default:  `standard+marker`) = default layer  
`OpenstreetmapTransportApiKey` (default: none) = [API key](https://www.thunderforest.com/pricing/): needed only for maps with points of interest using the `transport` layer  

## Installation

[Download extension](https://github.com/GiovanniSalmeri/yellow-openstreetmap/archive/master.zip) and copy zip file into your `system/extensions` folder. Right click if you use Safari.

This extension uses [OpenStreetMap](https://wiki.openstreetmap.org/wiki/Main_Page) for maps, [Nominatim](https://wiki.openstreetmap.org/wiki/Nominatim) for address lookup, and [Leaflet](https://leafletjs.com/) by Volodymyr Agafonkin for maps with points of interest.

## Developer

Giovanni Salmeri. [Get help](https://github.com/GiovanniSalmeri/yellow-openstreetmap/issues).
