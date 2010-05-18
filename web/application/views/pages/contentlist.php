<?php ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function(){
        //setInterval("UpdateContent()", 5000);

        RepaintChannelTree();

        //Show the loading message
        $("div#content-list").append("<div class='loading'>loading</div>");

        //variables used to work out when the loading has finished
        var total = 0; var loaded = 0;

        //Make the call to the main API function to list the
        $.getJSON("<?php echo(url::base()); ?>api/contentselection/get/<?php echo($state);?>/0/20/0/100", function(data) {
            if(!data.message) {
                total = data.totalcount;
                if(total > 0) {
                    for(var i=0; i<data.contentitems.length; i++) {
                        $.post("<?php echo(url::base()); ?>parts/content/render",
                               { content : data.contentitems[i] },
                               function(contentTemplate) {
                                   $("div#content-list ul").append(
                                        "<li>" + contentTemplate + "</li>"
                                   );
                                   loaded++;
                                   if(loaded == total) {
                                       $("div#content-list div.loading").remove();
                                   }
                                }
                        );
                    }
                }else {
                    $("div#content-list div.loading").remove();
                    $.get("<?php echo(url::base()); ?>parts/nocontent", function(data){
                        $("div#content-list ul").append(
                            "<li>" + data + "</li>"
                        );
                    });
                }
            }
        });
    });

    function MarkContentAsAccurate(contentId) {
        $("div#"+contentId).parent().slideUp("normal", function(){
            $(this).remove();
        });
        $.getJSON("<?php echo(url::base()); ?>api/contentcuration/markasaccurate/" + contentId, function(data) {
            UpdateSourceScores(data.sourceId, data.sourceScore);
        });
    }

    function MarkContentAsInaccurate(contentId) {
        $("div#"+contentId).parent().slideUp("normal", function(){
                $(this).remove();
        });
        $.getJSON("<?php echo(url::base()); ?>api/contentcuration/markasinaccurate/" + contentId, function(data) {
            
            UpdateSourceScores(data.sourceId, data.sourceScore);
        });
    }

    function MarkContentAsCrossTalk(contentId) {
        $("div#"+contentId).parent().slideUp("normal", function(){
                $(this).remove();
        });
        $.getJSON("<?php echo(url::base()); ?>api/contentcuration/markascrosstalk/" + contentId, function(data) {
            UpdateSourceScores(data.sourceId, data.sourceScore);
        });
    }

    function UpdateSourceScores(sourceId, sourceScore) {
        $("p."+sourceId).each(function(){
            $(this).html(sourceScore + "&#37;");
        });
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
            collapsed: true,
            unique: true,
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

    function UpdateContent() {
        $.post(
            "<?php echo(str_replace("/web", "", url::base())); ?>core/ServiceAPI/ChannelServices/RunNextChannel.php",
            { key : "swiftriver_dev" }
        );
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

</script>
<div id="content-list">
    <ul>
    </ul>
</div>
