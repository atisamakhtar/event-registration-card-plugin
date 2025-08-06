<?php

add_action('init', 'grc_process_registration');
function grc_process_registration() {
    if (isset($_POST['grc_submit']) && isset($_POST['grc_nonce']) && wp_verify_nonce($_POST['grc_nonce'], 'grc-register-nonce')) {

        try {
            // Sanitize inputs
            $title = sanitize_text_field($_POST['grc_title']);
            $first_name = sanitize_text_field($_POST['grc_first_name']);
            $last_name = sanitize_text_field($_POST['grc_last_name']);
            $company = sanitize_text_field($_POST['grc_company']);
            $job_title = sanitize_text_field($_POST['grc_job_title']);
            $email = sanitize_email($_POST['grc_email']);
            $mobile = sanitize_text_field($_POST['grc_mobile']);

            // Generate username
            $username = strtolower($first_name . $last_name . rand(100, 999));

            // Create user
            $user_id = wp_create_user($username, wp_generate_password(), $email);

            if (is_wp_error($user_id)) {
                throw new Exception('User creation failed: ' . $user_id->get_error_message());
            }

            // Set user role
            $user = new WP_User($user_id);
            $user->set_role('guest');

            // Add user meta
            update_user_meta($user_id, 'title', $title);
            update_user_meta($user_id, 'first_name', $first_name);
            update_user_meta($user_id, 'last_name', $last_name);
            update_user_meta($user_id, 'company', $company);
            update_user_meta($user_id, 'job_title', $job_title);
            update_user_meta($user_id, 'mobile', $mobile);

            // Generate QR code
            $qr_data = [
                'name' => "$first_name $last_name",
                'company' => $company,
                'email' => $email,
                'mobile' => $mobile,
                'timestamp' => time()
            ];

            $qr_code = grc_generate_qr_code(json_encode($qr_data));

            if (!$qr_code) {
                error_log('QR code generation failed, continuing without it');
            }

            // Send email
            if (!grc_send_registration_email($user_id, $qr_code)) {
                error_log('Email sending failed, but registration completed');
            }

            wp_redirect(home_url('/registration-success'));
            exit;

        } catch (Exception $e) {
            error_log('Registration error: ' . $e->getMessage());
            wp_redirect(home_url('/registration-error'));
            exit;
        }
    }
}