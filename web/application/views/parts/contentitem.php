<!-- Begin Sweeper Panel -->

<div class="content-item clearfix" id="<?php echo($content->id); ?>">
	
<!-- Content Item Left -->
    <?php echo($actions); ?>
<!-- Content Item Right -->
    <div class="right-column">
        <div class="system-details">
            <p class="id"><?php echo($content->id); ?></p>
        </div>
        <div class="languages">
            <?php for($i=0; $i<count($content->text); $i++): ?>
                <div class="language <?php echo($content->text[$i]->languageCode); ?> <?php if($i>0) { echo ('more'); } ?>">
                    <p class="title"><?php echo($content->text[$i]->title); ?></p>
                    <div class="text">
                        <?php if(isset($content->text[$i]->text)) : ?>
                            <?php for($j=0; $j<count($content->text[$i]->text); $j++): ?>
                                <p class="<?php if($j>0) { echo ('more'); } ?>">
                                    <?php echo($content->text[$i]->text[$j]); ?>
                                </p>
                            <?php endfor; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
        <div class="meta">
            <p class="date"><strong>Delivered:</strong> <?php echo(date('r', $content->date)); ?> <strong>Source:</strong> <?php echo($content->source->name); ?></p>
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

<!-- End Sweeper Panel -->