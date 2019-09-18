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

add_action('admin_post_trel_send', 'trel_check_admin_post');
function trel_check_admin_post()
{
    $retrieved_nonce = $_REQUEST['_wpnonce'];
    if (!wp_verify_nonce($retrieved_nonce, 'trel_send_mail')) die('Failed security check');
    if (isset($_POST['to']) && isset($_POST['mail_content'])) {
        $to = explode(',', $_POST['to']);
        $to = trel_email_array_validator($to);
        if (preg_match('/^(text\/plain|text\/html)$/', $_POST['content-type']))
            $content_type = $_POST['content-type'];
        else
            $content_type = 'text/plain';
        $from = sanitize_email($_POST['from']);
        $reply_to=sanitize_email($_POST['reply-to']);
        $cc = explode(',', $_POST['cc']);
        $cc = trel_email_array_validator($cc);
        $bcc = explode(',', $_POST['bcc']);
        $bcc = trel_email_array_validator($bcc);
        $subject = sanitize_text_field($_POST['subject']);
        $message = wp_kses_post($_POST['mail_content']);
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
        
        $success = wp_mail($to, $subject, $message, $headers, $attachments);
        wp_redirect($_SERVER['HTTP_REFERER']."&success=".$success);
    } else
        wp_redirect($_SERVER['HTTP_REFERER']."&success=".false);
}

function trel_render_emailer_page()
{
    function trel_enqueue_media_lib_uploader() 
    {
        //Core media script
        wp_enqueue_media();
        // Your custom js file
        wp_register_script('media-lib-uploader-js', plugins_url('media-lib-uploader.js' , __FILE__ ), array('jquery'));
        wp_enqueue_script('media-lib-uploader-js');
    }
    add_action('admin_enqueue_scripts', 'trel_enqueue_media_lib_uploader');
    wp_enqueue_style('style-trelire', plugin_dir_url(__FILE__).'Templates/assets/style.css');
    include __DIR__.'/Templates/sending_page.php';
}

add_action('admin_menu', function () {
    add_management_page( 'TreLire', 'TreLire', 'publish_posts', 'trelire', 'trel_render_emailer_page');
});

function trel_email_array_validator(array $emails)
{
    foreach ($emails as $index => $email) {
        $emails[$index] = sanitize_email($email); 
    }
    return $emails;
}