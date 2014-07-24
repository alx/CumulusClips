<?php

// Include required files
include_once (dirname (dirname (__FILE__)) . '/config/bootstrap.php');
App::LoadClass ('User');
App::LoadClass ('Video');
App::LoadClass ('Comment');
Plugin::Trigger ('comment.ajax.start');


// Establish page variables, objects, arrays, etc
$logged_in = User::LoginCheck();
if ($logged_in) $user = new User ($logged_in);
Plugin::Trigger ('comment.ajax.login_check');
$Errors = array();
$data = array();



// Verify a video was selected
if (isset ($_POST['video_id']) && is_numeric ($_POST['video_id'])) {
    $video = new Video ($_POST['video_id']);
} else {
    App::Throw404();
}



// Check if video is valid
if (!$video->found || $video->status != 'approved') {
    App::Throw404();
}





/***********************
Handle page if submitted
***********************/

if (isset ($_POST['submitted'])) {

    // Verify user is logged in
    if ($logged_in) {
        $data['user_id'] = $user->user_id;
    } else {

        $data['user_id'] = 0;
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

        // Validate name
        if (!empty ($_POST['name']) && !ctype_space ($_POST['name'])) {
            $data['name'] = htmlspecialchars ( trim ($_POST['name']));
        } else {
            $Errors['name'] = Language::GetText('error_name');
        }


        // Validate email address
        $email_pattern = '/^[a-z0-9][a-z0-9_\.\-]+@[a-z0-9][a-z0-9\.\-]+\.[a-z0-9]{2,4}$/i';
        if (!empty ($_POST['email']) && !ctype_space ($_POST['email']) && preg_match ($email_pattern, $_POST['email'])) {
            $data['email'] = htmlspecialchars ( trim ($_POST['email']));
        } else {
            $Errors['email'] = Language::GetText('error_email');
        }


        // Validate website
        $website_pattern = '/^(https?:\/\/)?[a-z0-9][a-z0-9\.\-]+\.[a-z0-9]{2,4}$/i';
        if (!empty ($_POST['website']) && !ctype_space ($_POST['website']) && preg_match ($website_pattern, $_POST['website'], $matches)) {
            $data['website'] = (empty ($matches[1]) ? 'http://' : '') . htmlspecialchars (trim ($_POST['website']));
        }

    }

    // Validate comments
    if (!empty ($_POST['comments']) && !ctype_space ($_POST['comments'])) {
        $data['comments'] = htmlspecialchars ( trim ($_POST['comments']));
    } else {
        $Errors['comments'] = Language::GetText('error_comment');
    }

    // Validate output format block
    if (!empty ($_POST['block'])) {
        $block = $_POST['block'] . '.tpl';
    } else {
        $block = null;
    }


    // Save comment if no errors were found
    if (empty ($Errors)) {

        $data['video_id'] = $video->video_id;
        $data['status'] = 'approved';
        Plugin::Trigger ('comment.ajax.before_post_comment');
        $comment_id = Comment::Create ($data);
        $comment = new Comment ($comment_id);
        $comment->Approve ('activate');

        // Retrieve formatted new comment
        if (Settings::Get('auto_approve_comments') == 1) {
            
            if ($block) {
                View::InitView();
                ob_start();
                View::RepeatingBlock ($block, array ($comment->comment_id));
                $output = ob_get_contents();
                ob_end_clean();
            } else {
                $output = $comment;
            }
            
            $message = (string)Language::GetText ('success_comment_posted');
            $other = array ('auto_approve' => 1, 'output' => $output);
            
        } else {
            $message = (string)Language::GetText ('success_comment_approve');
            $other = array ('auto_approve' => 0, 'output' => '');
        }

        echo json_encode (array ('result' => 1, 'msg' => $message, 'other' => $other));
        Plugin::Trigger ('comment.ajax.post_comment');
        exit();

    } else {
        $error_msg = Language::GetText('errors_below');
        $error_msg .= '<br /><br /> - ' . implode ('<br /> - ', $Errors);
        echo json_encode (array ('result' => 0, 'msg' => $error_msg));
        exit();
    }

}   // END verify if page was submitted	

?>