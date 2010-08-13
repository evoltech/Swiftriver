    <div class="left-column">
        <?php echo(Html::image("media/images/logo-image-med.png")); ?>
        <div class="actions">
            <a href="<?php echo($content->link); ?>" title="View original content" target="_blank"><?php echo(Html::image("media/images/button-view-article.png")); ?></a>
        </div>
        <div class="veracity">
            <p class="<?php echo($content->source->id); ?>">
                <?php echo($content->source->score == "null")? "not rated" : $content->source->score."&#37;";?>
            </p>
        </div>
    </div>
