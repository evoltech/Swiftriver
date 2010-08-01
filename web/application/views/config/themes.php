<div id="themes">
    <ul id="theme-list">
        <?php foreach($themes as $theme) : ?>
            <li>
                <div class="theme clearfix">
                    <div class="info">
                        <p class="title">Title: <?php echo($theme->title); ?></p>
                        <p class="description">Description: <?php echo($theme->description); ?></p>
                        <p class="author">Author: <?php echo($theme->author); ?></p>
                        <p class="email">Email: <?php echo($theme->email); ?></p>
                        <p class="url">Url: <?php echo($theme->url); ?></p>
                        <p class="notes">Notes: <?php echo($theme->notes); ?></p>
                    </div>
                    <img src="<?php echo($theme->thumbnail); ?>"/>
                    <div class="action">
                        <form action="<?php echo(url::base()); ?>config/themes/select" method="POST">
                            <input type="hidden" name="cssfile" value="<?php echo($theme->cssFilePath); ?>" />
                            <input type="submit" value="Choose this theme" />
                        </form>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>