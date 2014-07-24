<?php

// Include required files
include_once (dirname (dirname (__FILE__)) . '/config/bootstrap.php');
App::LoadClass ('User');
App::LoadClass ('Page');


// Establish page variables, objects, arrays, etc
View::InitView();
Plugin::Trigger ('page.start');
View::$vars->logged_in = User::LoginCheck();
if (View::$vars->logged_in) View::$vars->user = new User (View::$vars->logged_in);
$page_id = null;


// Parse preview request
if (!empty ($_GET['preview']) && is_numeric ($_GET['preview'])) {
    $page_id = Page::Exist (array ('page_id' => $_GET['preview']));


// Parse the URI request
} else {
    $request = preg_replace ('/^\/?(.*?)\/?$/', '$1', basename ($_SERVER['REQUEST_URI']));
    $page_id = Page::Exist (array ('slug' => $request, 'status' => 'published'));
}



### Validate requested page
if ($page_id) {

    // Retrieve custom page
    $page = new Page ($page_id);
    $page_name = 'page_' . $page->slug;

    // Set view settings for custom page
    View::$vars->page = $page;
    View::$options->page = $page_name;
    View::$vars->meta = Language::GetMeta ($page_name);
    if (empty (View::$vars->meta->title)) View::$vars->meta->title = $page->title;

} else {
    App::Throw404();
}


// Output Page
Plugin::Trigger ('page.before_render');
View::Render ('page.tpl');

?>