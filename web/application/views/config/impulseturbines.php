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
                                <input name="<?php echo($config->name); ?>" type="text" onchange="SaveConfiguration('<?php echo($turbine->name); ?>', '<?php echo($i); ?>', 'impulse')" class="config-property-value <?php echo($config->type); ?>" id="config_<?php echo($i); ?>_<?php echo($j); ?>" value="<?php echo($config->value); ?>"/>
                                <a href="javascript:ShowConfigurationDescription('<?php echo($i); ?>_<?php echo($j); ?>')" class="show-config-description">?</a>
                                <p class="description" style="display:none;" id="config_description_<?php echo($i); ?>_<?php echo($j); ?>"><?php echo($config->description); ?></p>
                            </div>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
                <p class="active" style="<?php if(!$turbine->active) echo("display:none"); ?>" id="active_<?php echo($i); ?>"><?php echo(Html::image("media/images/button-activate.png", array())); ?>Currently active, <a href="javascript:DeactivateTurbine(<?php echo($i); ?>, '<?php echo($turbine->name); ?>', 'impulse')">deactivate?</a></p>
                <p class="active" style="<?php if($turbine->active) echo("display:none"); ?>" id="inactive_<?php echo($i); ?>"><?php echo(Html::image("media/images/button-deactivate.png", array())); ?>Not currently active, <a href="javascript:ActivateTurbine(<?php echo($i); ?>, '<?php echo($turbine->name); ?>', 'impulse')">actiavte?</a></p>
            </li>
        <?php endfor; ?>
    </ul>
</div>