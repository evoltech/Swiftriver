<div id="turbines">
    <ul id="turbine-list">
        <?php for($i=0; $i<count($turbines); $i++) : ?>
            <?php $turbine = $turbines[$i]; ?>
            <li class="clearfix">
                <div class="name-container clearfix">
                    <p class="name"><?php echo($turbine->name); ?></p>
                    <p class="more"><a href="javascript:ShowTurbineDescription('<?php echo($i); ?>')"><?php echo(Html::image("media/images/button-help.png", array())); ?></a></p>
                </div>
                <p class="description" style="display:none;" id="turbinedesctiption_<?php echo($i); ?>"><?php echo($turbine->description) ?></p>
                <p class="active" style="<?php if(!$turbine->active) echo("display:none"); ?>" id="active_<?php echo($i); ?>"><?php echo(Html::image("media/images/button-activate.png", array())); ?>Currently active, <a href="javascript:DeactivateTurbine(<?php echo($i); ?>, '<?php echo($turbine->name); ?>')">deactivate?</a></p>
                <p class="active" style="<?php if($turbine->active) echo("display:none"); ?>" id="inactive_<?php echo($i); ?>"><?php echo(Html::image("media/images/button-deactivate.png", array())); ?>Not currently active, <a href="javascript:ActivateTurbine(<?php echo($i); ?>, '<?php echo($turbine->name); ?>')">actiavte?</a></p>
            </li>
        <?php endfor; ?>
    </ul>
</div>
<script language="javascript" type="text/javascript">
    function ShowTurbineDescription(number) {
        $("p#turbinedesctiption_" + number).show("slow");
        setTimeout("HideTurbineDescription("+number+")", 10000);
    }
    function HideTurbineDescription(number) {
        $("p#turbinedesctiption_" + number).hide("slow");
    }
    function ActivateTurbine(number, name) {
        $("p#inactive_" + number).hide("fast");
        $("p#active_" + number).show("fast");
        $.post(
            "<?php echo(url::base()); ?>config/turbines/activate",
            { name: name },
            function(data){},
            'json'
        );
    }
    function DeactivateTurbine(number, name) {
        $("p#active_" + number).hide("fast");
        $("p#inactive_" + number).show("fast");
        $.post(
            "<?php echo(url::base()); ?>config/turbines/deactivate",
            { name: name },
            function(data){},
            'json'
        );
    }
</script>
