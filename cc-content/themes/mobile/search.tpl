<?php View::Header(); ?>

<div id="search">

    <!-- BEGIN SEARCH FORM -->
    <div id="search-form">
        <form action="<?=MOBILE_HOST?>/s/" method="post">
        <input type="text" name="keyword" id="search-field" title="<?=Language::GetText('search_text')?>" value="" />
        <input type="hidden" name="submitted" value="TRUE" />
        <input id="search-button" type="image" value="<?=Language::GetText('search_text')?>" src="<?=$config->theme_url?>/images/search-button.png">
        </form>
    </div>
    <!-- END SEARCH FORM -->


    <h1><?=Language::GetText('search_header')?></h1>
    <?php if (isset ($count)): ?>

        <p><strong><?=Language::GetText('results_for')?>: </strong><?=$keyword?></p>

        <?php if (empty ($count)): ?>

            <!-- BEGIN NO RESULTS FOUND -->
            <div class="block"><strong><?=Language::GetText('no_results')?></strong></div>
            <!-- END NO RESULTS FOUND -->

        <?php else: ?>

            <!-- BEGIN SEARCH RESULTS -->
            <div class="list">
                <?php View::RepeatingBlock ('video.tpl', $search_videos); ?>

                <?php if ($count > 20): ?>
                    <div id="load-more">
                        <form>
                        <input type="hidden" id="loadLocation" name="loadLocation" value="search" />
                        <input type="hidden" id="format" name="format" value="html" />
                        <input type="hidden" id="block" name="block" value="video" />
                        <input type="hidden" id="submitted" name="submitted" value="true" />
                        <input type="hidden" id="keyword" name="keyword" value="<?=$keyword?>" />
                        <input type="hidden" id="max" name="max" value="<?=$count?>" />
                        <input type="hidden" id="start" name="start" value="20" />
                        </form>
                        <div>
                            <span id="loading-text" style="display:none;"><?=Language::GetText('loading')?>...</span>
                            <span id="load-more-text"><?=Language::GetText('load_more')?></span>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
            <!-- END SEARCH RESULTS -->

        <?php endif; ?>
						
    <?php else: ?>

        <!-- BEGIN SEARCH PAGE -->
        <div class="block">
            <p><?=Language::GetText('search_body')?></p>
        </div>
        <!-- END SEARCH PAGE -->

    <?php endif; ?>

</div>

<?php View::Footer(); ?>