<?php View::Header(); ?>

<div id="home">

    <!-- BEGIN FEATURED VIDEO -->
    <h1><?=Language::GetText('featured')?></h1>
    <div id="featured-video">
        <?php if (!empty ($featured_video)): ?>

            <div class="list">
                <?php View::RepeatingBlock ('video.tpl', $featured_video); ?>
            </div>

        <?php else: ?>
            <div class="block"><strong><?=Language::GetText('no_videos')?></strong></div>
        <?php endif; ?>
    </div>
    <!-- END FEATURED VIDEO -->
    
    
    
    <!-- BEGIN RECENT VIDEOS -->
    <h1><?=Language::GetText('recent_videos')?></h1>
    <?php if (!empty ($recent_videos)): ?>

        <div class="list">
            <?php View::RepeatingBlock ('video.tpl', $recent_videos); ?>
        </div>

    <?php else: ?>
        <div class="block"><strong><?=Language::GetText('no_videos')?></strong></div>
    <?php endif; ?>
    <!-- END RECENT VIDEOS -->

</div>

<?php View::Footer(); ?>