<?php

// Include required files
include_once (dirname (dirname (__FILE__)) . '/cc-core/config/admin.bootstrap.php');
App::LoadClass ('User');


// Establish page variables, objects, arrays, etc
Functions::RedirectIf ($logged_in = User::LoginCheck(), HOST . '/login/');
$admin = new User ($logged_in);
Functions::RedirectIf (User::CheckPermissions ('admin_panel', $admin), HOST . '/myaccount/');
$page_title = 'CumulusClips Admin Panel - Dashboard';
$first_run = null;


// Execute post install / first run operations
if (isset ($_GET['first_run']) && file_exists (DOC_ROOT . '/cc-install')) {
    Settings::Set ('version', CURRENT_VERSION);
    Filesystem::Open();
    Filesystem::Delete (DOC_ROOT . '/cc-install');
    Filesystem::Close();
    $first_run = true;
}


// Retrieve news from mothership
if (isset ($_POST['news'])) {
    
    $curl_handle = curl_init();
    curl_setopt ($curl_handle, CURLOPT_URL, MOTHERSHIP_URL . '/news/');
    curl_setopt ($curl_handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt ($curl_handle, CURLOPT_FOLLOWLOCATION, true);
    $news = curl_exec ($curl_handle);
    curl_close ($curl_handle);
    $news = (!empty ($news)) ? $news : '<strong>Nothing to report.</strong>';
    exit ($news);
    
}

// Retrieve Video totals
$result_videos = $db->Query ("SELECT COUNT(video_id) as count FROM " . DB_PREFIX . "videos WHERE status = 'approved'");
$videos = $db->FetchObj ($result_videos);

// Retrieve User totals
$result_users = $db->Query ("SELECT COUNT(user_id) as count FROM " . DB_PREFIX . "users WHERE status = 'active'");
$members = $db->FetchObj ($result_users);

// Retrieve Comment totals
$result_comments = $db->Query ("SELECT COUNT(comment_id) as count FROM " . DB_PREFIX . "comments WHERE status = 'approved'");
$comments = $db->FetchObj ($result_comments);

// Retrieve Rating totals
$result_ratings = $db->Query ("SELECT COUNT(rating_id) as count FROM " . DB_PREFIX . "ratings");
$ratings = $db->FetchObj ($result_ratings);


// Output Header
include ('header.php');

?>

<div id="dashboard">

    <h1>Dashboard</h1>

    <?php if ($first_run): ?>
    <div class="success">
        <p>All done! Your video site is now ready for use. This is your admin panel,
        we went ahead and logged you in so that you can start exploring.</p>

        <p>Your login for the main site and the admin panel are one in the same. To
        enter the admin panel simply login and click on 'Admin'.</p>

        <p>Thank you for choosing CumulusClips as your video sharing platform.</p>
        <p><a href="<?=HOST?>/" class="button">View My Site</a></p>
    </div>
    <?php endif; ?>


    <div id="news" class="block">
        <h2>News from the mothership</h2>
        <div><p class="loading">Loading...</p></div>
    </div>

    <div class="block">
        <h2>Totals Report</h2>
        <p><strong>Videos: </strong><?=$videos->count?></p>
        <p><strong>Members: </strong><?=$members->count?></p>
        <p><strong>Comments: </strong><?=$comments->count?></p>
        <p><strong>Ratings: </strong><?=$ratings->count?></p>
    </div>

</div>

<?php include ('footer.php'); ?>