<div class="content-item clearfix" id="<?php echo($content->id); ?>">
    <div class="left-column">
        <a href="javascript:MarkContentAsAccurate('<?php echo($content->id); ?>')" title="Mark this content as acurate" class="logo">
            <?php echo(Html::image("media/images/logo-image-med.png")); ?>
        </a>
        <div class="actions">
            <a href="javascript:MarkContentAsInaccurate('<?php echo($content->id); ?>')" title="Mark this content as inaccurate"><?php echo(Html::image("media/images/button-markas-inaccurate.png")); ?></a>
            <a href="javascript:MarkContentAsCrossTalk('<?php echo($content->id); ?>')" title="Mark this content as cross talk"><?php echo(Html::image("media/images/button-markas-crosstalk.png")); ?></a>
            <a href="<?php echo($content->link); ?>" title="View original content" target="_blank"><?php echo(Html::image("media/images/button-view-article.png")); ?></a>
        </div>
        <div class="veracity">
            <p class="<?php echo($content->source->id); ?>">
                <?php echo($content->source->score == "null")? "not rated" : $content->source->score."&#37;";?>
            </p>
        </div>
    </div>
    <div class="right-column">
        <div class="system-details">
            <p class="id"><?php echo($content->id); ?></p>
        </div>
        <div class="languages">
            <?php for($i=0; $i<count($content->text); $i++): ?>
                <div class="language <?php echo($content->text[$i]->languageCode); ?> <?php if($i>0) { echo ('more'); } ?>">
                    <p class="title"><?php echo($content->text[$i]->title); ?></p>
                    <div class="text">
                        <?php for($j=0; $j<count($content->text[$i]); $j++): ?>
                            <p class="<?php if($j>0) { echo ('more'); } ?>">
                                <?php echo($content->text[$i]->text[$j]); ?>
                            </p>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
        <div class="meta">
            <p class="date">Written on <?php echo(date('r', $content->date)); ?></p>
            <p class="source">From <?php echo($content->source->name); ?></p>
        </div>
        <div class="tags">
            <?php foreach($content->tags as $type => $tags) : ?>
                <?php if(is_array($tags) && count($tags) > 0) : ?>
                    <div class="tag-group clearfix">
                        <p class="tag-type"><?php echo($type); ?></p>
                        <div class="tag-list">
                            <?php foreach($tags as $key => $tag) : ?>
                                <p><?php echo($tag); ?></p>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

