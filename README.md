Openstreetmap 0.8.20
====================
Embed OpenStreetMap maps.

<p align="center"><img src="openstreetmap-screenshot.png?raw=true" alt="Screenshot"></p>

## How to install an extension

[Download ZIP file](https://github.com/GiovanniSalmeri/yellow-openstreetmap/archive/main.zip) and copy it into your `system/extensions` folder. [Learn more about extensions](https://github.com/annaesvensson/yellow-update).

## How to embed a map

Create an `[openstreetmap]` shortcut.

The following arguments are available, all but the first argument are optional:

`Address` = address or GPS coordinates, wrap multiple words into quotes  
`Zoom` = zoom value, from 0 to 19  
`Style` = map style, e.g. `left`, `center`, `right`, `flexible`  
`Width` = map width, pixel or percent  
`Height` = map height, pixel  
`Layer` = map layer, e.g. `standard`, `cycle`, `transport`, `humanitarian` and [others](https://wiki.openstreetmap.org/wiki/Browsing#Layers)

You can append `+marker` to the layers to show a marker. To get GPS coordinates, go to [OpenStreetMap](https://www.openstreetmap.org/) and enter the address. For more precision, select the "Share" icon on the right, then "Include marker", and drag the marker to the desidered location. The GPS coordinates are the last numbers of the URL shown in the browser. If the URL shown is `https://www.openstreetmap.org/#map=17/41.85181/12.62127`, then the GPS coordinates are `41.85181, 12.62127`.

## How to embed a map with points of interest

Create an `[openstreetmap]` shortcut.

The following arguments are available, all but the first argument are optional:

`Name` = file name of the points of interest file  
`Zoom` = zoom value, from 0 to 19  
`Style` = map style, e.g. `left`, `center`, `right`, `flexible`  
`Width` = map width, pixel or percent  
`Height` = map height, pixel  
`Layer` = map layer, e.g. `standard`, `cycle`, `transport`, `humanitarian` and [others](https://wiki.openstreetmap.org/wiki/Browsing#Layers)

You can append `+marker` to the layers to show the marker(s). Put the file into the `media/openstreetmap/` folder with the extension `.csv`. Each point of interest is a line in a comma-separated values file, in one of these two formats:

```
address,city,name,description
latitude,longitude,name,description
```

Fields that cointain commas must be enclosed in quotes. The third and fourth field are optional, if provided they will be used for a popup. An [API key](https://www.thunderforest.com/pricing/) is required for a map with points of interest using the `transport` layer.

## Examples

Embedding a map, different addresses:

    [openstreetmap "Roma"]
    [openstreetmap "Via Columbia 1, Roma" 17 center 100% 400 standard+marker]
    [openstreetmap "Bredgatan 1, Lund, Sweden"  17 center 100% 400 standard+marker]

Embedding a map, different GPS coordinates:

    [openstreetmap "41.85181, 12.62127"]
    [openstreetmap "41.85181, 12.62127" 17 center 100% 400 standard+marker]
    [openstreetmap "55.70647, 13.19246" 17 center 100% 400 standard+marker]

Embedding a map with points of interest:

    [openstreetmap rome.csv]
    [openstreetmap rome.csv 17 center 100% 400 standard+marker]
    [openstreetmap rome.csv 17 center 100% 400 transport+marker]

Configuring points of interest in a CSV file:

```
Piazza di Spagna,Roma,Piazza di Spagna,"At the bottom of the Spanish Steps, one of the most famous squares in Rome"
Piazza Colonna,Roma,Piazza Colonna,"Named for the marble Column of Marcus Aurelius, which has stood there since AD 193"
41.89772,12.47231,Statua di Pasquino,"The first talking statue of Rome: he spoke out about the people's dissatisfaction, denounced injustice, and assaulted misgovernment"
Piazza della Rotonda,Roma,Piazza della Rotonda,"On the north side of the Pantheon, the square gets its name from its informal title as the church of Santa Maria Rotonda"
```

## Settings

The following settings can be configured in file `system/extensions/yellow-system.ini`:

`OpenstreetmapDirectory` = directory for points of interest file  
`OpenstreetmapZoom` = zoom value  
`OpenstreetmapStyle` = map style, e.g. `flexible`  
`OpenstreetmapLayer` = map layer, e.g. `standard+marker`  
`OpenstreetmapTransportApiKey` = your API key  

## Acknowledgements

This extension uses [OpenStreetMap](https://wiki.openstreetmap.org/wiki/Main_Page) and [Nominatim](https://wiki.openstreetmap.org/wiki/Nominatim). Thank you for the free service. The extension includes [Leaflet 1.8.0](https://github.com/Leaflet/Leaflet) by Volodymyr Agafonkin for maps with points of interest. Thank you for the good work.

## Developer

Giovanni Salmeri. [Get help](https://datenstrom.se/yellow/help/)
