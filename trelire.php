<?php
/**
 * Plugin Name: TreLire
 * Description: Nobody needs an email client when you have a website!
 * Version:     1.0.0
 * Author:      Simone Montali @ Caffeina
 * Author URI:  https://caffeina.com/
 * Plugin URI:  https://github.com/simmontali/trelire
 */



defined('ABSPATH') or die('No script kiddies please!');

if (isset($_POST['to'])) {
    $to = explode(',', $_POST['to']);
    $content_type = $_POST['content-type'];
    $from = $_POST['from'];
    $reply_to=$_POST['reply-to'];
    $cc = explode(',', $_POST['cc']);
    $bcc = explode(',', $_POST['bcc']);
    $subject = $_POST['subject'];
    $message = $_POST['mail_content'];
    $attachments = [];
    $inserted_attachments = (!empty($_POST['attachment'])) ? explode(',', $_POST['attachment']) : '';
    if (is_array($inserted_attachments)) {
        foreach ($inserted_attachments as $inserted_attachment) {
            $attachments[] = get_attached_file(intval($inserted_attachment));
        }
    }
    $headers = [];
    $headers[] = 'Content-type: '.$content_type;
    $headers[] = 'From: '.$from;
    if ($message == '') {
        add_action(
            'admin_notices',
            function () {
                ?>
                <div class="notice notice-error is-dismissible">
                    <p>Please write a message.</p>
                </div>
        
                <?php
            }
        );
    }
    if ($reply_to!= '')
        $headers[] = 'Reply-to: '.$reply_to;
    foreach ($cc as $cc_addr)
        $headers[] = 'cc: '.$cc_addr;
    foreach ($bcc as $bcc_addr)
        $headers[] = 'bcc: '.$bcc_addr;
    add_action(
        'wp_loaded',
        function () use ($to, $subject, $message, $headers, $attachments) {
            if (wp_mail($to, $subject, $message, $headers, $attachments))
                add_action(
                    'admin_notices',
                    function () {
                        ?>
                        <div class="notice notice-success is-dismissible">
                            <p>Email sent correctly.</p>
                        </div>
                
                        <?php
                    }
                );
            else
                add_action(
                    'admin_notices',
                    function () {
                        ?>
                        <div class="notice notice-error is-dismissible">
                            <p>Email not sent!</p>
                        </div>
                
                        <?php
                    }
                );
        }
    );
}

function render_emailer_page()
{
    function my_enqueue_media_lib_uploader() {

        //Core media script
        wp_enqueue_media();
    
        // Your custom js file
        wp_register_script( 'media-lib-uploader-js', plugins_url( 'media-lib-uploader.js' , __FILE__ ), array('jquery') );
        wp_enqueue_script( 'media-lib-uploader-js' );
    }
    add_action('admin_enqueue_scripts', 'my_enqueue_media_lib_uploader');
    wp_enqueue_style('style-trelire', plugin_dir_url(__FILE__).'Templates/assets/style.css');
    include __DIR__.'/Templates/sending_page.php';
}

add_action('admin_menu', function () {
    add_management_page( 'TreLire', 'TreLire', 'publish_posts', 'trelire', 'render_emailer_page');
});
