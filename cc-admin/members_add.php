<?php

// Include required files
include_once (dirname (dirname (__FILE__)) . '/cc-core/config/admin.bootstrap.php');
App::LoadClass ('User');


// Establish page variables, objects, arrays, etc
Functions::RedirectIf ($logged_in = User::LoginCheck(), HOST . '/login/');
$admin = new User ($logged_in);
Functions::RedirectIf (User::CheckPermissions ('admin_panel', $admin), HOST . '/myaccount/');
$page_title = 'Add New Member';
$data = array();
$errors = array();
$message = null;





/***********************
Handle form if submitted
***********************/

if (isset ($_POST['submitted'])) {

    // Validate role
    if (!empty ($_POST['role']) && !ctype_space ($_POST['role'])) {
        $data['role'] = htmlspecialchars (trim ($_POST['role']));
    } else {
        $errors['role'] = 'Invalid role';
    }


    // Validate email
    if (!empty ($_POST['email']) && preg_match ('/^[a-z0-9][a-z0-9\._-]+@[a-z0-9][a-z0-9\.-]+\.[a-z0-9]{2,4}$/i', $_POST['email'])) {
        if (!User::Exist (array ('email' => $_POST['email']))) {
            $data['email'] = htmlspecialchars (trim ($_POST['email']));
        } else {
            $errors['email'] = 'Email is unavailable';
        }
    } else {
        $errors['email'] = 'Invalid email address';
    }


    // Validate Username
    if (!empty ($_POST['username']) && !ctype_space ($_POST['username'])) {
        if (!User::Exist (array ('username' => $_POST['username']))) {
            $data['username'] = htmlspecialchars (trim ($_POST['username']));
        } else {
            $errors['username'] = 'Username is unavailable';
        }
    } else {
        $errors['username'] = 'Invalid username';
    }


    // Validate password
    if (!empty ($_POST['password']) && !ctype_space ($_POST['password'])) {
        $data['password'] = trim ($_POST['password']);
    } else {
        $errors['password'] = 'Invalid password';
    }


    // Validate first name
    if (!empty ($_POST['first_name']) && !ctype_space ($_POST['first_name'])) {
        $data['first_name'] = htmlspecialchars (trim ($_POST['first_name']));
    }


    // Validate last name
    if (!empty ($_POST['last_name']) && !ctype_space ($_POST['last_name'])) {
        $data['last_name'] = htmlspecialchars (trim ($_POST['last_name']));
    }


    // Validate website
    if (!empty ($_POST['website']) && !ctype_space ($_POST['website'])) {
        $website = $_POST['website'];
        if (preg_match ('/^(https?:\/\/)?[a-z0-9][a-z0-9\.-]+\.[a-z0-9]{2,4}.*$/i', $website, $matches)) {
            $website = (empty ($matches[1]) ? 'http://' : '') . $website;
            $data['website'] = htmlspecialchars (trim ($website));
        } else {
            $errors['website'] = 'Invalid website';
        }
    }


    // Validate about me
    if (!empty ($_POST['about_me']) && !ctype_space ($_POST['about_me'])) {
        $data['about_me'] = htmlspecialchars (trim ($_POST['about_me']));
    }



    ### Create user if no errors were found
    if (empty ($errors)) {

        // Create user
        $data['password'] = md5 ($data['password']);
        $data['status'] = 'new';
        $id = User::Create ($data);
        $user = new User ($id);
        $user->Approve ('create');
        unset ($data);

        // Output message
        $message = 'Member has been added.';
        $message_type = 'success';

    } else {
        $message = 'The following errors were found. Please correct them and try again.';
        $message .= '<br /><br /> - ' . implode ('<br /> - ', $errors);
        $message_type = 'error';
    }

}


// Output Header
include ('header.php');

?>

<div id="members-add">

    <h1>Add New Member</h1>

    <?php if ($message): ?>
    <div class="<?=$message_type?>"><?=$message?></div>
    <?php endif; ?>


    <div class="block">

        <form action="<?=ADMIN?>/members_add.php" method="post">

            <div class="row-shift">An asterisk (*) denotes required field.</div>

            <div class="row<?=(isset ($errors['status'])) ? ' errors' : ''?>">
                <label>*Role:</label>
                <select name="role" class="dropdown">
                <?php foreach ($config->roles as $key => $value): ?>
                    <option value="<?=$key?>" <?=(isset ($data['role']) && $data['role'] == $key)?'selected="selected"':''?>><?=$value['name']?></option>
                <?php endforeach; ?>
                </select>
            </div>

            <div class="row">
                <label class="<?=(isset ($errors['email'])) ? 'errors' : ''?>">*E-mail:</label>
                <input name="email" type="text" class="text" value="<?=(isset ($errors, $data['email'])) ? $data['email'] : ''?>" />
            </div>

            <div class="row">
                <label class="<?=(isset ($errors['username'])) ? 'errors' : ''?>">*Username:</label>
                <input name="username" type="text" class="text" value="<?=(isset ($errors, $data['username'])) ? $data['username']:''?>" maxlength="30" />
                <br /><span id="status"></span>
            </div>

            <div class="row-shift">Username can only contain alphanumeric (a-z, 0-9) characters, no spaces or special characters.</div>

            <div class="row">
                <label class="<?=(isset ($errors['password'])) ? 'errors' : ''?>">*Password:</label>
                <input name="password" type="password" class="text mask" value="<?=(isset ($errors, $data['password'])) ? htmlspecialchars ($data['password']):''?>" />
            </div>

            <div class="row">
                <label>First Name:</label>
                <input name="first_name" type="text" class="text" value="<?=(isset ($errors, $data['first_name'])) ? $data['first_name'] : ''?>" />
            </div>

            <div class="row">
                <label>Last Name:</label>
                <input name="last_name" type="text" class="text" value="<?=(isset ($errors, $data['last_name'])) ? $data['last_name'] : ''?>" />
            </div>

            <div class="row">
                <label class="<?=(isset ($errors['website'])) ? 'errors' : ''?>">Website:</label>
                <input name="website" type="text" class="text" value="<?=(isset ($errors, $data['website'])) ? $data['website'] : ''?>" />
            </div>

            <div class="row">
                <label>About Me:</label>
                <textarea name="about_me" rows="5" cols="50" class="text"><?=(isset ($errors, $data['about_me'])) ? $data['about_me']:''?></textarea>
            </div>

            <div class="row-shift">
                <input type="hidden" name="submitted" value="TRUE" />
                <input type="submit" class="button" value="Create Member" />
            </div>

        </form>

    </div>

</div>

<?php include ('footer.php'); ?>