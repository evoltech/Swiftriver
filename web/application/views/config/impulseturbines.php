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
                <?php if(is_array($turbine->configurationProperties) && count($turbine->configurationProperties) > 0) : ?>
                    <div class="config-container clearfix" id="turbine_config_<?php echo($i); ?>">
                        <?php for($j=0; $j<count($turbine->configurationProperties); $j++) : ?>
                            <?php $config = $turbine->configurationProperties[$j]; ?>
                            <div class="config-property-container clearfix">
                                <p class="name"><?php echo($config->name); ?></p>
                                <input name="<?php echo($config->name); ?>" type="text" onchange="SaveConfiguration('<?php echo($turbine->name); ?>', '<?php echo($i); ?>')" class="config-property-value <?php echo($config->type); ?>" id="config_<?php echo($i); ?>_<?php echo($j); ?>" value="<?php echo($config->value); ?>"/>
                                <a href="javascript:ShowConfigurationDescription('<?php echo($i); ?>_<?php echo($j); ?>')" class="show-config-description">?</a>
                                <p class="description" style="display:none;" id="config_description_<?php echo($i); ?>_<?php echo($j); ?>"><?php echo($config->description); ?></p>
                            </div>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
                <p class="active" style="<?php if(!$turbine->active) echo("display:none"); ?>" id="active_<?php echo($i); ?>"><?php echo(Html::image("media/images/button-activate.png", array())); ?>Currently active, <a href="javascript:DeactivateTurbine(<?php echo($i); ?>, '<?php echo($turbine->name); ?>')">deactivate?</a></p>
                <p class="active" style="<?php if($turbine->active) echo("display:none"); ?>" id="inactive_<?php echo($i); ?>"><?php echo(Html::image("media/images/button-deactivate.png", array())); ?>Not currently active, <a href="javascript:ActivateTurbine(<?php echo($i); ?>, '<?php echo($turbine->name); ?>')">actiavte?</a></p>
            </li>
        <?php endfor; ?>
    </ul>
</div>
<script language="javascript" type="text/javascript">
    function SaveConfiguration(name, number) {
        var postData = new Object();
        var inputs = $("div#turbine_config_" + number + " input");
        for(var i=0; i<inputs.length; i++) {
            var input = inputs[i];
            postData[$(input).attr("name")] = $(input).val();
        }
        $.post(
            "<?php echo(url::base()); ?>config/impulseturbines/save",
            { "name" : name, "data" : postData },
            function(data){},
            'json'
        );
    }
    function ShowTurbineDescription(number) {
        $("p#turbinedesctiption_" + number).show("slow");
        setTimeout("HideTurbineDescription("+number+")", 10000);
    }
    function HideTurbineDescription(number) {
        $("p#turbinedesctiption_" + number).hide("slow");
    }
    function ShowConfigurationDescription(number) {
        $("p#config_description_" + number).show("slow");
        setTimeout("HideConfigurationDescription('"+number+"')", 5000);
    }
    function HideConfigurationDescription(number) {
        $("p#config_description_" + number).hide("slow");
    }
    function ActivateTurbine(number, name) {
        $("p#inactive_" + number).hide("fast");
        $("p#active_" + number).show("fast");
        $.post(
            "<?php echo(url::base()); ?>config/impulseturbines/activate",
            { name: name },
            function(data){},
            'json'
        );
    }
    function DeactivateTurbine(number, name) {
        $("p#active_" + number).hide("fast");
        $("p#inactive_" + number).show("fast");
        $.post(
            "<?php echo(url::base()); ?>config/impulseturbines/deactivate",
            { name: name },
            function(data){},
            'json'
        );
    }
</script>
