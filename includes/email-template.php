<?php
function grc_send_registration_email($user_id, $qr_code) {
    // Verify user exists
    $user = get_userdata($user_id);
    if (!$user) {
        error_log('User not found with ID: ' . $user_id);
        return false;
    }

    // Get all user meta
    $title = get_user_meta($user_id, 'title', true);
    $first_name = get_user_meta($user_id, 'first_name', true);
    $last_name = get_user_meta($user_id, 'last_name', true);
    $company = get_user_meta($user_id, 'company', true);
    $job_title = get_user_meta($user_id, 'job_title', true);
    $mobile = get_user_meta($user_id, 'mobile', true);

    // Verify required fields
    if (empty($user->user_email) {
        error_log('No email address for user ID: ' . $user_id);
        return false;
    }

    $to = $user->user_email;
    $subject = 'Your Guest Registration Card';
    $message = grc_get_email_template($user, $title, $first_name, $last_name, $company, $job_title, $mobile, $qr_code);

    // Set proper headers
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . get_bloginfo('name') . ' <noreply@' . parse_url(home_url(), PHP_URL_HOST) . '>',
        'Reply-To: ' . get_bloginfo('name') . ' <noreply@' . parse_url(home_url(), PHP_URL_HOST) . '>'
    );

    // Add error logging
    add_action('wp_mail_failed', function ($error) {
        error_log('Mail failed: ' . $error->get_error_message());
    });

    // Send email
    $sent = wp_mail($to, $subject, $message, $headers);

    if (!$sent) {
        error_log('Failed to send registration email to: ' . $to);
    }

    return $sent;
}

function grc_get_email_template($user, $title, $first_name, $last_name, $company, $job_title, $mobile, $qr_code) {
    ob_start(); ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <style type="text/css">
            .grc-card {
                width: 100%;
                max-width: 600px;
                margin: 0 auto;
                border: 1px solid #ddd;
                padding: 20px;
                font-family: Arial, sans-serif;
                background-color: #ffffff;
            }
            .grc-header {
                background-color: #f5f5f5;
                padding: 15px;
                text-align: center;
                margin-bottom: 20px;
            }
            .grc-details {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
            }
            .grc-user-info {
                width: 60%;
                padding-right: 20px;
                box-sizing: border-box;
            }
            .grc-qr-code {
                width: 35%;
                text-align: center;
            }
            .grc-footer {
                margin-top: 20px;
                padding-top: 15px;
                text-align: center;
                font-size: 12px;
                color: #777;
                border-top: 1px solid #eee;
            }
            @media only screen and (max-width: 600px) {
                .grc-user-info, .grc-qr-code {
                    width: 100%;
                    padding-right: 0;
                }
                .grc-qr-code {
                    margin-top: 20px;
                }
            }
        </style>
    </head>
    <body>
        <div class="grc-card">
            <div class="grc-header">
                <h2>Guest Registration Card</h2>
            </div>
            <div class="grc-details">
                <div class="grc-user-info">
                    <p><strong>Name:</strong> <?php echo esc_html($title . ' ' . $first_name . ' ' . $last_name); ?></p>
                    <p><strong>Company:</strong> <?php echo esc_html($company); ?></p>
                    <p><strong>Job Title:</strong> <?php echo esc_html($job_title); ?></p>
                    <p><strong>Email:</strong> <?php echo esc_html($user->user_email); ?></p>
                    <p><strong>Mobile:</strong> <?php echo esc_html($mobile); ?></p>
                </div>
                <div class="grc-qr-code">
                    <?php if ($qr_code) : ?>
                        <img src="<?php echo esc_url($qr_code); ?>" alt="QR Code" width="150" style="max-width:100%; height:auto;">
                        <p>Scan this QR code for digital access</p>
                    <?php else : ?>
                        <p>QR Code not available</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="grc-footer">
                <p>This is an automatically generated guest registration card.</p>
            </div>
        </div>
    </body>
    </html>
    <?php
    return ob_get_clean();
}