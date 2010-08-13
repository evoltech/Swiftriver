    <div class="left-column">
        <a href="javascript:listController.MarkContentAsAccurate('<?php echo($content->id); ?>')" title="Mark this content as acurate" class="logo">
            <?php echo(Html::image("media/images/logo-image-med.png")); ?>
        </a>
        <div class="actions">
            <a href="javascript:listController.MarkContentAsInaccurate('<?php echo($content->id); ?>')" title="Mark this content as inaccurate"><?php echo(Html::image("media/images/button-markas-inaccurate.png")); ?></a>
            <a href="javascript:listController.MarkContentAsCrossTalk('<?php echo($content->id); ?>')" title="Mark this content as cross talk"><?php echo(Html::image("media/images/button-markas-crosstalk.png")); ?></a>
            <a href="javascript:listController.MarkContentAsIrrelevant('<?php echo($content->id); ?>')" title="Mark this content as irrelevant"><?php echo(Html::image("media/images/button-markas-irrelevant.png")); ?></a>
            <a href="<?php echo($content->link); ?>" title="View original content" target="_blank"><?php echo(Html::image("media/images/button-view-article.png")); ?></a>
        </div>
        <div class="veracity">
            <p class="<?php echo($content->source->id); ?>">
                <?php echo($content->source->score == "null")? "not rated" : $content->source->score."&#37;";?>
            </p>
        </div>
    </div>
