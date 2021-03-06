<script type="text/javascript" language="javascript">
    // This script fills in the 3 divs
    $(document).ready(function() {
        var nav_baseUrl = "<?php echo(url::base()); ?>";
        
        // Fill in the map widget
        filterViewWidget = new FilterViewWidget(nav_baseUrl, "div#filter-view-widget");
        filterViewWidget.RenderView();
        $("div#filter-view-widget").html("Filter view");

        // Fill in the trending keywords widget
        trendingKeywordsWidget = new TrendingKeywordsWidget(nav_baseUrl, "div#trending-keywords-widget");
        trendingKeywordsWidget.RenderView();

        // Fill in the active sources widget
        activeSourcesWidget = new ActiveSourcesWidget(nav_baseUrl, "div#active-sources-widget");
        activeSourcesWidget.RenderView();
    });
</script>

<div id="filter-view-widget">
</div>

<div class="widget-seperator"></div>

<div id="trending-keywords-widget">
</div>

<div class="widget-seperator"></div>

<div id="active-sources-widget">
</div>