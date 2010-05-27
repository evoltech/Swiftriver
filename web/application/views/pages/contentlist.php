<?php ?>
<script type="text/javascript" language="javascript">
    var nav_state = '<?php echo(isset($_SESSION["nav_state"]) ? $_SESSION["nav_state"] : "new_content"); ?>';
    var nav_minVeracity = <?php echo(isset($_SESSION["nav_minVeracity"]) ? $_SESSION["nav_minVeracity"] : "'null'"); ?>;
    var nav_maxVeracity = <?php echo(isset($_SESSION["nav_maxVeracity"]) ? $_SESSION["nav_maxVeracity"] : "'null'"); ?>;
    var nav_type = '<?php echo(isset($_SESSION["nav_type"]) ? $_SESSION["nav_type"] : "null"); ?>';
    var nav_subType = '<?php echo(isset($_SESSION["nav_subType"]) ? $_SESSION["nav_subType"] : "null"); ?>';
    var nav_source = '<?php echo(isset($_SESSION["nav_source"]) ? $_SESSION["nav_source"] : "null"); ?>';
    var nav_pageSize = <?php echo(isset($_SESSION["nav_pageSize"]) ? $_SESSION["nav_pageSize"] : "20"); ?>;
    var nav_pageStart = <?php echo(isset($_SESSION["nav_pageStart"]) ? $_SESSION["nav_pageStart"] : "0"); ?>;
    var nav_orderBy = '<?php echo(isset($_SESSION["nav_orderBy"]) ? $_SESSION["nav_orderBy"] : "null"); ?>';
    var nav_baseUrl = "<?php echo(url::base()); ?>";
    var render_firstload = true;

    $(document).ready(function(){
        setInterval("Update()", 10000);

        RepaintChannelTree();

        listController = new ListController(nav_baseUrl, "div#content-list ul");
        listController.NavigationStateChange(new NavigationState(nav_state, nav_minVeracity, nav_maxVeracity, nav_type, nav_subType, nav_source, nav_pageSize, nav_pageStart, nav_orderBy));

        //Show the loading message
        //$("div#content-list").append("<div class='loading'>loading</div>");

        //Make the call to the main API function to list the
        //AddContent(20, new Array());
    });

    function Update() {
        $.post("<?php echo(str_replace("/web", "", url::base())); ?>core/ServiceAPI/ChannelServices/RunNextChannel.php",{ key : "swiftriver_dev" });
        listController.RenderList();
    }

    function ShowAddChannelModal(type, subType) {
        $.get("<?php echo(url::base()); ?>parts/addchannel/" + type + "/" + subType, function(data) {
            Shadowbox.open({
                content : data,
                player : "html",
                height : 450,
                width : 500
            });
        });
    }

    function DeleteChannel(id) {
        $.getJSON("<?php echo(url::base()); ?>api/channels/deletechannel/"+id, function(data){
            RepaintChannelTree();
        });
    }

    function TreeViewChannelTree() {
        $("div#channel-tree ul").treeview({
            animated: "fast",
            persist: "cookie"
        });
        $("div#channel-tree").show("fast");
    }

    function RepaintChannelTree() {
        $.get("<?php echo(url::base()); ?>parts/channeltree/render", function(data){
            var treeContainer = $("div#channel-tree-container");
            var child = treeContainer.children("div#channel-tree");
            $(child).remove();
            $(treeContainer).prepend(data);
            TreeViewChannelTree();
        });
    }

    function ConfigureTheme() {
        $.get("<?php echo(url::base()); ?>config/themes", function(data) {
            Shadowbox.open({
                content : data,
                player : "html",
                height : 450,
                width : 500
            });
        });
    }

    function ConfigureTurbines() {
        $.get("<?php echo(url::base()); ?>config/turbines", function(data) {
            Shadowbox.open({
                content : data,
                player : "html",
                height : 450,
                width : 500
            });
        });
    }

    function FilterByType(type) {
        nav_type = type;
        nav_subType = "null";
        nav_source = "null";
        ClearList();
        render_firstload = true;
        AddContent(new Array());
    }

    function FilterBySubType(subType) {
        nav_type = "null";
        nav_subType = subType;
        nav_source = "null";
        ClearList();
        render_firstload = true;
        AddContent(new Array());
    }

    function FilterBySource(source) {
        nav_type = "null";
        nav_subType = "null";
        nav_source = source;
        ClearList();
        render_firstload = true;
        AddContent(new Array());
    }

    function ClearList() {
        $("div#content-list ul li").each(function(){
            $(this).remove();
        })
    }

</script>
<div id="content-list">
    <div class="pagination">
        <p class="total-count"></p>
    </div>
    <ul>
    </ul>
</div>
