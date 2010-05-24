<script type="text/javascript" language="javascript">
    $(document).ready(function(){
        $("div#channel-tree").corner("top 11px");
        $("div#channel-tree h2").corner("top");
    })
</script>
<div id="channel-tree" style="display:none;">
    <h2>Channels</h2>
    <ul>
        <?php foreach($channels->channelTypes as $channelType) : ?>
            <li>
                <a href="javascript:FilterByType('<?php echo($channelType->type); ?>')"><?php echo($channelType->type); ?></a>
                <ul>
                    <?php foreach($channelType->subTypes as $subType) : ?>
                        <li>
                            <a href="javascript:FilterBySubType('<?php echo($subType->type); ?>')"><?php echo($subType->type); ?></a>
                            <ul>
                                <li>
                                    <a href="javascript:ShowAddChannelModal('<?php echo($channelType->type); ?>', '<?php echo($subType->type); ?>');">Add new <?php echo($subType->type); ?>?</a>
                                </li>
                                <?php if(count($subType->sources) > 0 ) : ?>
                                    <?php foreach($subType->sources as $source) : ?>
                                        <li>
                                            <a href="javascript:FilterBySource('<?php echo($source->id); ?>')"><?php echo($source->name); ?></a><a href="javascript:DeleteChannel('<?php echo($source->id); ?>')"><?php echo(Html::image("media/images/button-markas-inaccurate.png")); ?></a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                        <li>
                                            No feeds of this type yet.
                                        </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                </ul>
        <?php endforeach; ?>
    </ul>
</div>
<script type="text/javascript" language="javascript">
    $(doucument).ready(function(){
        TreeViewchannelTree();
    });
</script>
