<script type="text/javascript" language="javascript">
    // This script fills in the 3 divs
    $(document).ready(function() {
        // Fill in the filter view widget
        mapWidget = new MapViewWidget(nav_baseUrl, "div#map-widget");
        mapWidget.RenderView();

        // Fill in the terms monitor widget
        termsToMonitorWidget = new TermsToMonitorWidget(nav_baseUrl, "div#terms-monitor-widget");
        termsToMonitorWidget.RenderView();

        // Fill in the identified sources widget
        identifiedSourcesWidget = new IdentifiedSourcesWidget(nav_baseUrl, "div#identified-sources-widget");
        identifiedSourcesWidget.RenderView();
    });
</script>

<div id="map-widget">
</div>

<div class="widget-seperator"></div>

<div id="terms-monitor-widget">
</div>

<div class="widget-seperator"></div>

<div id="identified-sources-widget">
</div>