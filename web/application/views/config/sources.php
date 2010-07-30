<div id="sources">
    <?php $counter = 1; ?>
    <?php foreach($channels->channelTypes as $channelType) : ?>
        <h3><a href="javascript:ShowChannel('<?php echo($counter); ?>')"><?php echo($channelType->type); ?></a></h3>
        <div id="channel-type_<?php echo($counter); ?>" class="channel-container" style="display:none">
            <div class="tree">
                <ul>
                    <?php $innerCounter = 1; ?>
                    <?php foreach($channelType->subTypes as $subType) : ?>
                        <li>
                            <?php echo($subType->type); ?>
                            <ul>
                                <li id="node_<?php echo($counter); ?>_<?php echo($innerCounter); ?>">
                                    Add new <?php echo($subType->type); ?>
                                    <ul>
                                        <form id="form_<?php echo($counter); ?>_<?php echo($innerCounter); ?>">
                                            <fieldset>
                                                <input type="hidden" name="type" value="<?php echo($channelType->type); ?>" />
                                                <input type="hidden" name="subType" value="<?php echo($subType->type); ?>" />
                                                <input type="hidden" name="updatePeriod" value="1" />
                                                <div class="form-row">
                                                    <label for="name">The name of the <?php echo($channelType->type); ?> <?php echo($subType->type); ?>:</label>
                                                    <input type="text" name="name" class="required" />
                                                </div>
                                                <?php foreach($subType->configurationProperties as $key => $properties) : ?>
                                                    <?php if($subType->type == $key) : ?>
                                                        <?php foreach($properties as $property) : ?>
                                                            <div class="form-row">
                                                                <label for="<?php echo(str_replace(" ", "", $property->name)); ?>"><?php echo($property->description); ?></label>
                                                                <input type="text" name="<?php echo(str_replace(" ", "", $property->name)); ?>" class="required" />
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                                <div class="form-row">
                                                    <input type="submit" value="Add to this channel" onclick="SubmitForm('<?php echo($counter); ?>_<?php echo($innerCounter); ?>')" />
                                                </div>
                                            </fieldset>
                                        </form>
                                    </ul>
                                </li>
                                <?php if(count($subType->sources) > 0 ) : ?>
                                    <?php foreach($subType->sources as $source) : ?>
                                        <li id="<?php echo($source->id); ?>">
                                            <?php echo($source->name); ?><a href="javascript:DeleteChannel('<?php echo($source->id); ?>')"><?php echo(Html::image("media/images/button-markas-inaccurate.png")); ?></a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                        <li class="no-feeds">
                                            No feeds of this type yet.
                                        </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php $innerCounter++; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php $counter++; ?>
    <?php endforeach; ?>
</div>