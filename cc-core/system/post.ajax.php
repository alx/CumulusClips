<?php

// Include required files
include_once (dirname (dirname (__FILE__)) . '/config/bootstrap.php');
App::LoadClass ('User');
App::LoadClass ('Post');
Plugin::Trigger ('post.ajax.start');


// Establish page variables, objects, arrays, etc
$logged_in = User::LoginCheck();
if (!$logged_in) App::Throw404();
$user = new User ($logged_in);
$data = array();





/***********************
Handle page if submitted
***********************/

if (isset ($_POST['submitted'])) {

    // Save update if no errors were found
    if (!empty ($_POST['post']) && !ctype_space ($_POST['post'])) {

        $data['post'] = htmlspecialchars (trim ($_POST['post']));
        $data['user_id'] = $user->user_id;
        Plugin::Trigger ('post.ajax.before_post_update');
        $post_id = Post::Create ($data);
        $post = new Post ($post_id);

        // Retrieve new formatted status updated
        View::InitView();
        ob_start();
        View::RepeatingBlock('post.tpl', array ($post->post_id));
        $status_update = ob_get_contents();
        ob_end_clean();

        Plugin::Trigger ('post.ajax.post_update');
        echo json_encode (array ('result' => 1, 'msg' => (string) Language::GetText('success_status_updated'), 'other' => $status_update));
        exit();

    } else {
        echo json_encode (array ('result' => 0, 'msg' => (string) Language::GetText ('error_status_update')));
        exit();
    }

}   // END verify if page was submitted

?>