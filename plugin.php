<?php
/*
Plugin Name: Create-O-Form
Plugin URI: https://github.com/Maxeption/create-o-form
Description: A plugin that creates forms to the users liking.
Author: ELBoustani ELMahdi
Author URI: https://github.com/Maxeption
Version:1.0.1
*/


//hooking wordpress predefined functions
add_action("admin_menu", "addMenu");


//adding the plugin to the admin dashboard
function addMenu(){
    add_menu_page("Create-O-Form", "Create-O-Form", 1, "Create_O_Form", "formMenu");
}

//creating the tables to toggle on/off the form
function inputsTable()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $tablename = 'inputs';
    $sql = "CREATE TABLE $wpdb->base_prefix$tablename (
        id INT,
        firstname BOOLEAN,
        lastname BOOLEAN,
        email BOOLEAN,
        sbjct BOOLEAN,
        msg BOOLEAN
        ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    //Creates a table in the database, if it doesn’t already exist.
    maybe_create_table($wpdb->base_prefix . $tablename, $sql);

}

//creating the tables where the form inputs are stored
function inputTable()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $tablename = 'input';
    $sql = "CREATE TABLE $wpdb->base_prefix$tablename (
        id INT PRIMARY KEY AUTO_INCREMENT, 
        firstname VARCHAR(255),
        lastname VARCHAR(255),
        email VARCHAR(255),
        sbjct VARCHAR(255),
        msg VARCHAR(255)
        ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    maybe_create_table($wpdb->base_prefix . $tablename, $sql);

}
//inserting the toggling of the form
function insertInputs(){
    global $wpdb;
    $wpdb->insert($wpdb->base_prefix.'inputs',['id' => 1, 'firstname' => true, 'lastname' => true, 'email' => true, 'sbjct' => true, 'msg' => true]);
}

//selecting all from table to check which input has been toggeled
function getInputs(){
    global $wpdb;
    $tableName = 'inputs';
    $inputs = $wpdb->get_row("SELECT * FROM $wpdb->base_prefix$tableName WHERE id = 1");
    return $inputs;
}

//toggling the form on the menu page and generating a short code
function formMenu(){
    $inputs = getInputs();
    ?>
    <div class="content">    
        <form method="post" action="">
            <div class="input-content">
                <input type="checkbox" id="firstname" name="firstname" value="true" <?php echo $inputs->firstname == 1 ? 'checked' : '' ?>>
                <label for="">First Name</label>
            </div>
            <div class="input-content">
                <input type="checkbox" id="lastname" name="lastname" value="true" <?php echo $inputs->lastname == 1 ? 'checked' : '' ?>>
                <label for="">Last Name</label>
            </div>
            <div class="input-content">
                <input type="checkbox" id="email" name="email" value="true" <?php echo $inputs->email == 1 ? 'checked' : '' ?>>
                <label for="">Email</label>
            </div>
            <div class="input-content">
                <input type="checkbox" id="sbjct" name="sbjct" value="true" <?php echo $inputs->sbjct == 1 ? 'checked' : '' ?>>
                <label for="">Subject</label>
            </div>
            <div class="input-content">
                <input type="checkbox" id="msg" name="msg" value="true" <?php echo $inputs->msg == 1 ? 'checked' : '' ?>>
                <label for="">Message</label>
            </div>
            <div class="input-content">
                <button type="submit" name="formInputs">Create</button>
            </div>
        </form>
    </div>
    <?php
    echo 'shortcode : ' . '[CreateoForm]';
};

//filtering the form toggles 
if (isset($_POST['formInputs'])){
    $firstname = filter_var($_POST['firstname'] ?? false, FILTER_VALIDATE_BOOLEAN);
    $lastname = filter_var($_POST['lastname'] ?? false, FILTER_VALIDATE_BOOLEAN);
    $email = filter_var($_POST['email'] ?? false, FILTER_VALIDATE_BOOLEAN);
    $sbjct = filter_var($_POST['sbjct'] ?? false, FILTER_VALIDATE_BOOLEAN);
    $msg = filter_var($_POST['msg'] ?? false, FILTER_VALIDATE_BOOLEAN);

    global $wpdb;
    $wpdb->update($wpdb->base_prefix.'inputs', ['firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'sbjct' => $sbjct, 'msg' => $msg], ['id' => 1]);
}

//the form generated using the short code (only the inputs toggeled in the menu are visible)
function formCode(){
    getInputs();
    echo '<form method="post" action="">';

    if (getInputs()->firstname){
        echo 'Your First Name (required) <br> ';
        echo '<input type="text" name="firstname" size="50"><br>';
    }
    if (getInputs()->lastname){
        echo 'Your Last Name (required) <br> ';
        echo '<input type="text" name="lastname" size="50"><br>';
    }
    if (getInputs()->email){
        echo 'Your Email (required) <br> ';
        echo '<input type="text" name="email" size="50"><br>';
    }
    if (getInputs()->sbjct){
        echo 'Your Subject (required) <br> ';
        echo '<input type="text" name="sbjct" size="50"><br>';
    }
    if (getInputs()->msg){
        echo 'Your Message (required) <br> ';
        echo '<textarea type="text" rows="10" cols="35" name="msg" size="50"></textarea><br>';
    }
    echo '<p><input type="submit" name="userInputs" value="Send"></p>';
    echo '</form>';
}

//posting the form data
if (isset($_POST['userInputs'])){
    $firstname = filter_var($_POST['firstname']);
    $lastname = filter_var($_POST['lastname']);
    $email = filter_var($_POST['email']);
    $sbjct = filter_var($_POST['sbjct']);
    $msg = filter_var($_POST['msg']);

    global $wpdb;
    $wpdb->insert($wpdb->base_prefix.'input', ['firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'sbjct' => $sbjct, 'msg' => $msg]);
}

function shortCode(){
    formCode();
    return ob_get_clean();
}

add_shortcode('CreateoForm', 'shortCode');

register_activation_hook(__FILE__, 'inputsTable');

register_activation_hook(__FILE__, 'inputTable');

register_activation_hook(__FILE__, 'insertInputs');