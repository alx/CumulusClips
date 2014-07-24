<?php View::Header(); ?>

<div id="videos">
    
    <h1><?=Language::GetText('videos_header')?></h1>
    <?php if (!empty ($count)): ?>

        <div class="list">
            <?php View::RepeatingBlock('video.tpl', $videos); ?>

            <?php if ($count > 20): ?>
                <div id="load-more">
                    <form>
                    <input type="hidden" id="loadLocation" name="loadLocation" value="videos" />
                    <input type="hidden" id="format" name="format" value="html" />
                    <input type="hidden" id="block" name="block" value="video" />
                    <input type="hidden" id="submitted" name="submitted" value="true" />
                    <input type="hidden" id="max" name="max" value="<?=$count?>" />
                    <input type="hidden" id="start" name="start" value="20" />
                    </form>
                    <div>
                        <span style="display:none;" id="loading-text">Loading...</span>
                        <span id="load-more-text">Load 20 More</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <div class="block"><strong><?=Language::GetText('no_videos')?></strong></div>
    <?php endif; ?>
    
</div>

<?php View::Footer(); ?>