<?php

// Include required files
include_once (dirname (dirname (__FILE__)) . '/cc-core/config/admin.bootstrap.php');
App::LoadClass ('User');


// Establish page variables, objects, arrays, etc
Functions::RedirectIf ($logged_in = User::LoginCheck(), HOST . '/login/');
$admin = new User ($logged_in);
Functions::RedirectIf (User::CheckPermissions ('admin_panel', $admin), HOST . '/myaccount/');
$page_title = 'Video Settings';
$data = array();
$errors = array();
$warnings = array();
$message = null;

$data['php'] = Settings::Get('php');
$data['ffmpeg'] = Settings::Get('ffmpeg');
$data['flv_options'] = Settings::Get('flv_options');
$data['mobile_options'] = Settings::Get('mobile_options');
$data['thumb_options'] = Settings::Get('thumb_options');
$data['debug_conversion'] = Settings::Get('debug_conversion');
$data['video_size_limit'] = Settings::Get('video_size_limit');
$data['flv_url'] = Settings::Get('flv_url');
$data['mobile_url'] = Settings::Get('mobile_url');
$data['thumb_url'] = Settings::Get('thumb_url');





/***********************
Handle form if submitted
***********************/

if (isset ($_POST['submitted'])) {

    // Validate log encoding setting
    if (isset ($_POST['debug_conversion']) && in_array ($_POST['debug_conversion'], array ('1', '0'))) {
        $data['debug_conversion'] = $_POST['debug_conversion'];
    } else {
        $errors['debug_conversion'] = 'Invalid encoding log option';
    }


    // Validate video size limit
    if (!empty ($_POST['video_size_limit']) && is_numeric ($_POST['video_size_limit'])) {
        $data['video_size_limit'] = trim ($_POST['video_size_limit']);
    } else {
        $errors['video_size_limit'] = 'Invalid video size limit';
    }


    // Validate flv video url
    if (!empty ($_POST['flv_url']) && !ctype_space ($_POST['flv_url'])) {
        $data['flv_url'] = htmlspecialchars (trim ($_POST['flv_url']));
    } else {
        $data['flv_url'] = '';
    }


    // Validate mobile video url
    if (!empty ($_POST['mobile_url']) && !ctype_space ($_POST['mobile_url'])) {
        $data['mobile_url'] = htmlspecialchars (trim ($_POST['mobile_url']));
    } else {
        $data['mobile_url'] = '';
    }


    // Validate video thumbnail url
    if (!empty ($_POST['thumb_url']) && !ctype_space ($_POST['thumb_url'])) {
        $data['thumb_url'] = htmlspecialchars (trim ($_POST['thumb_url']));
    } else {
        $data['thumb_url'] = '';
    }


    // Validate flv encoding options
    if (!empty ($_POST['flv_options']) && !ctype_space ($_POST['flv_options'])) {
        $data['flv_options'] = htmlspecialchars (trim ($_POST['flv_options']));
    } else {
        $errors['flv_options'] = 'Invalid flv encoding options';
    }


    // Validate mobile encoding options
    if (!empty ($_POST['mobile_options']) && !ctype_space ($_POST['mobile_options'])) {
        $data['mobile_options'] = htmlspecialchars (trim ($_POST['mobile_options']));
    } else {
        $errors['mobile_options'] = 'Invalid mobile encoding options';
    }


    // Validate thumbnail encoding options
    if (!empty ($_POST['thumb_options']) && !ctype_space ($_POST['thumb_options'])) {
        $data['thumb_options'] = htmlspecialchars (trim ($_POST['thumb_options']));
    } else {
        $errors['thumb_options'] = 'Invalid thumbnail encoding options';
    }


    // Validate php-cli path
    if (empty ($_POST['php'])) {
        @exec('whereis php', $whereis_results);
        $phpPaths = explode (' ', preg_replace ('/^php:\s?/','', $whereis_results[0]));
    } else if (!empty ($_POST['php']) && file_exists ($_POST['php'])) {
        $phpPaths = array(rtrim ($_POST['php'], '/'));
    } else {
        $phpPaths = array();
    }
    
    $phpBinary = false;
    foreach ($phpPaths as $phpExe) {
        if (!is_executable($phpExe)) continue;
        @exec($phpExe . ' -r "' . "echo 'cliBinary';" . '" 2>&1 | grep cliBinary', $phpCliResults);
        $phpCliResults = implode(' ', $phpCliResults);
        if (!empty($phpCliResults)) {
            $phpCliBinary = $phpExe;
            break;
        }
    }

    if ($phpCliBinary) {
        $data['php'] = $phpCliBinary;
    } else {
        $warnings['php'] = 'Unable to locate path to PHP-CLI';
        $data['php'] = '';
    }
        


    // Validate ffmpeg path
    if (empty ($_POST['ffmpeg'])) {

        // Check if FFMPEG is installed (using which)
        @exec ('which ffmpeg', $which_results_ffmpeg);
        if (empty ($which_results_ffmpeg)) {

            // Check if FFMPEG is installed (using whereis)
            @exec ('whereis ffmpeg', $whereis_results_ffmpeg);
            $whereis_results_ffmpeg = preg_replace ('/^ffmpeg:\s?/','', $whereis_results_ffmpeg[0]);
            if (empty ($whereis_results_ffmpeg)) {
                $warnings['ffmpeg'] = 'Unable to locate FFMPEG';
                $data['ffmpeg'] = '';
            } else {
                $data['ffmpeg'] = $whereis_results_ffmpeg;
            }

        } else {
            $data['ffmpeg'] = $which_results_ffmpeg[0];
        }

    } else if (file_exists ($_POST['ffmpeg'])) {
        $data['ffmpeg'] = rtrim ($_POST['ffmpeg'], '/');
    } else {
        $errors['ffmpeg'] = 'Invalid path to FFMPEG';
    }


    
    // Update video if no errors were made
    if (empty ($errors)) {

        // Check if there were warnings
        if (!empty ($warnings)) {

            $data['enable_uploads'] = 0;
            $message = 'Settings have been updated, but there are notices.';
            $message .= '<h3>Notice:</h3>';
            $message .= '<p>The following requirements were not met. As a result video uploads have been disabled.';
            $message .= '<br /><br /> - ' . implode ('<br /> - ', $warnings);
            $message .= '</p><p class="small">If you\'re using a plugin or service for encoding videos you can ignore this message.</p>';
            $message_type = 'notice';

        } else {
            $data['enable_uploads'] = 1;
            $message = 'Settings have been updated';
            $message .= (Settings::Get ('enable_uploads') == 0) ? ', and video uploads have been enabled.' : '.';
            $message_type = 'success';
        }

        foreach ($data as $key => $value) {
            Settings::Set ($key, $value);
        }

    } else {
        $message = 'The following errors were found. Please correct them and try again.';
        $message .= '<br /><br /> - ' . implode ('<br /> - ', $errors);
        $message_type = 'error';
    }

}


// Output Header
include ('header.php');

?>

<div id="settings-video">

    <h1>Video Settings</h1>

    <?php if ($message): ?>
    <div class="<?=$message_type?>"><?=$message?></div>
    <?php endif; ?>


    <div class="block">

        <form action="<?=ADMIN?>/settings_video.php" method="post">

            <div class="row <?=(isset ($errors['enable_uploads'])) ? ' errors' : ''?>">
                <label>Video Uploads:</label>
                <span id="enable_uploads"><?=(Settings::Get('enable_uploads')=='1')?'Enabled':'Disabled'?></span>
            </div>

            <div class="row <?=(isset ($errors['debug_conversion'])) ? ' errors' : ''?>">
                <label>Log Encoding:</label>
                <select name="debug_conversion" class="dropdown">
                    <option value="1" <?=($data['debug_conversion']=='1')?'selected="selected"':''?>>On</option>
                    <option value="0" <?=($data['debug_conversion']=='0')?'selected="selected"':''?>>Off</option>
                </select>
            </div>

            <div class="row <?=(isset ($errors['php'])) ? ' errors' : ''?>">
                <label>PHP Path:</label>
                <input class="text" type="text" name="php" value="<?=$data['php']?>" />
                <a class="more-info" title="If left blank, CumulusClips will attempt to detect its location">More Info</a>
            </div>

            <div class="row <?=(isset ($errors['ffmpeg'])) ? ' errors' : ''?>">
                <label>FFMPEG Path:</label>
                <input class="text" type="text" name="ffmpeg" value="<?=$data['ffmpeg']?>" />
                <a class="more-info" title="If left blank, CumulusClips will attempt to detect its location">More Info</a>
            </div>

            <div class="row <?=(isset ($errors['flv_options'])) ? ' errors' : ''?>">
                <label>FLV Options:</label>
                <input class="text" type="text" name="flv_options" value="<?=$data['flv_options']?>" />
            </div>

            <div class="row <?=(isset ($errors['mobile_options'])) ? ' errors' : ''?>">
                <label>Mobile Options:</label>
                <input class="text" type="text" name="mobile_options" value="<?=$data['mobile_options']?>" />
            </div>

            <div class="row <?=(isset ($errors['thumb_options'])) ? ' errors' : ''?>">
                <label>Thumbnail Options:</label>
                <input class="text" type="text" name="thumb_options" value="<?=$data['thumb_options']?>" />
            </div>

            <div class="row <?=(isset ($errors['video_size_limit'])) ? ' errors' : ''?>">
                <label>Video Site Limit:</label>
                <input class="text" type="text" name="video_size_limit" value="<?=$data['video_size_limit']?>" />
                (Bytes)
            </div>

            <div class="row <?=(isset ($errors['flv_url'])) ? ' errors' : ''?>">
                <label>FLV Video URL:</label>
                <input class="text" type="text" name="flv_url" value="<?=$data['flv_url']?>" />
                <a class="more-info" title="If left blank, defaults to '<?=HOST?>/cc-content/uploads/flv/'">More Info</a>
            </div>

            <div class="row <?=(isset ($errors['mobile_url'])) ? ' errors' : ''?>">
                <label>Mobile Video URL:</label>
                <input class="text" type="text" name="mobile_url" value="<?=$data['mobile_url']?>" />
                <a class="more-info" title="If left blank, defaults to '<?=HOST?>/cc-content/uploads/mobile/'">More Info</a>
            </div>

            <div class="row <?=(isset ($errors['thumb_url'])) ? ' errors' : ''?>">
                <label>Thumbnail URL:</label>
                <input class="text" type="text" name="thumb_url" value="<?=$data['thumb_url']?>" />
                <a class="more-info" title="If left blank, defaults to '<?=HOST?>/cc-content/uploads/thumbs/'">More Info</a>
            </div>

            <div class="row-shift">
                <input type="hidden" name="submitted" value="TRUE" />
                <input type="submit" class="button" value="Update Settings" />
            </div>
        </form>

    </div>


</div>

<?php include ('footer.php'); ?>