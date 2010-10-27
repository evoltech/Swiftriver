<style>
    #left-bar-map {
        width: 80%;
        height: 150px;
        border: 1px solid black;
    }
</style>
<div style="text-align:center !important;">
    <div id='left-bar-map'></div>
    <script src='http://openlayers.org/api/OpenLayers.js'></script>
    <script type="text/javascript">
        var map = new OpenLayers.Map('left-bar-map',{maxResolution: 0.703125} );
        var wmscURL = [
            "http://wmsc1.terrapages.net/getmap?",
            "http://wmsc2.terrapages.net/getmap?",
            "http://wmsc3.terrapages.net/getmap?",
            "http://wmsc4.terrapages.net/getmap?"
        ];
        var terrapagesStreetLayer = new OpenLayers.Layer.WMS( 'TerraPages Street',wmscURL, {layers: 'UnprojectedStreet', format: 'image/jpeg' }, {buffer: 1, isBaseLayer: true} );
        map.addLayer(terrapagesStreetLayer);
        map.zoomToMaxExtent();
    </script>
</div>