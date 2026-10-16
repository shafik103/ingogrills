<?php
if (!defined('ABSPATH')) exit;

/*
 * SMTP for outgoing mail (enquiry notifications, password resets).
 *
 * Credentials are NOT stored in the theme or git. Add these lines to wp-config.php
 * on the server, above the "That's all, stop editing!" line.
 *
 * Google Workspace (Gmail) - use an App Password, not the normal account password:
 *
 *   define('IG_SMTP_HOST', 'smtp.gmail.com');
 *   define('IG_SMTP_PORT', 465);
 *   define('IG_SMTP_SECURE', 'ssl');                  // or 587 with 'tls'
 *   define('IG_SMTP_USER', 'info@indogrills.com');
 *   define('IG_SMTP_PASS', 'xxxx xxxx xxxx xxxx');    // 16-character App Password
 *
 * (cPanel mailbox instead: host mail.indogrills.com, port 465, 'ssl', the mailbox password.)
 *
 * Without IG_SMTP_PASS the site falls back to PHP mail().
 */

add_filter('wp_mail_from', fn($f) => indogrills_contact_email());
add_filter('wp_mail_from_name', fn($n) => 'IndoGrills Enquiries');

add_action('phpmailer_init', function ($m) {
    if (!defined('IG_SMTP_HOST') || !defined('IG_SMTP_PASS')) return;
    $m->isSMTP();
    $m->Host       = IG_SMTP_HOST;
    $m->Port       = defined('IG_SMTP_PORT') ? (int) IG_SMTP_PORT : 465;
    $m->SMTPSecure = defined('IG_SMTP_SECURE') ? IG_SMTP_SECURE : 'ssl';
    $m->SMTPAuth   = true;
    $m->Username   = defined('IG_SMTP_USER') ? IG_SMTP_USER : indogrills_contact_email();
    $m->Password   = IG_SMTP_PASS;
    $m->From       = indogrills_contact_email();
    $m->FromName   = 'IndoGrills Enquiries';
    $m->CharSet    = 'UTF-8';
});

$GLOBALS['ig_mail_error'] = '';
add_action('wp_mail_failed', function ($err) {
    $GLOBALS['ig_mail_error'] = $err->get_error_message();
    error_log('IndoGrills mail failed: ' . $err->get_error_message());
});

// Admin tool: Tools > Test Email (sends a test message to the contact address)
add_action('admin_menu', function () {
    add_management_page('Test Email', 'Test Email', 'manage_options', 'ig-test-email', function () {
        echo '<div class="wrap"><h1>Test Email</h1>';
        if (isset($_POST['ig_test']) && check_admin_referer('ig_test_email')) {
            $to = sanitize_email($_POST['to'] ?? '') ?: indogrills_contact_email();
            $ok = wp_mail($to, 'IndoGrills test email', "This is a test email from " . home_url() . "\nSent: " . current_time('mysql'));
            echo $ok ? '<div class="notice notice-success"><p>Sent to ' . esc_html($to) . '. Check the inbox (and spam).</p></div>'
                     : '<div class="notice notice-error"><p>Sending failed.</p><p><strong>Reason:</strong> <code>' . esc_html($GLOBALS['ig_mail_error'] ?: 'unknown (see the server error log)') . '</code></p></div>';
        }
        $on = defined('IG_SMTP_HOST') && defined('IG_SMTP_PASS');
        echo '<p>SMTP: <strong>' . ($on ? 'configured (' . esc_html(IG_SMTP_HOST) . ')' : 'not configured - using PHP mail()') . '</strong></p>';
        echo '<form method="post">'; wp_nonce_field('ig_test_email');
        echo '<p><input type="email" name="to" value="' . esc_attr(indogrills_contact_email()) . '" class="regular-text"></p>';
        echo '<p><button class="button button-primary" name="ig_test" value="1">Send test email</button></p></form></div>';
    });
});
