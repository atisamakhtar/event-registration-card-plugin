<?php

function grc_registration_form() {
    ob_start(); ?>
    <form id="grc-registration-form" method="post">
        <div class="grc-form-group">
            <label for="grc-title">Title*</label>
            <select id="grc-title" name="grc_title" required>
                <option value="">Select</option>
                <option value="Mr.">Mr.</option>
                <option value="Mrs.">Mrs.</option>
                <option value="Ms.">Ms.</option>
                <option value="Dr.">Dr.</option>
            </select>
        </div>

        <div class="grc-form-group">
            <label for="grc-first-name">First Name*</label>
            <input type="text" id="grc-first-name" name="grc_first_name" required>
        </div>

        <div class="grc-form-group">
            <label for="grc-last-name">Last Name*</label>
            <input type="text" id="grc-last-name" name="grc_last_name" required>
        </div>

        <div class="grc-form-group">
            <label for="grc-company">Company / Organization Name*</label>
            <input type="text" id="grc-company" name="grc_company" required>
        </div>

        <div class="grc-form-group">
            <label for="grc-job-title">Job title*</label>
            <input type="text" id="grc-job-title" name="grc_job_title" required>
        </div>

        <div class="grc-form-group">
            <label for="grc-email">Email Address*</label>
            <input type="email" id="grc-email" name="grc_email" required>
        </div>

        <div class="grc-form-group">
            <label for="grc-mobile">Mobile*</label>
            <input type="tel" id="grc-mobile" name="grc_mobile" required>
        </div>

        <input type="hidden" name="grc_nonce" value="<?php echo wp_create_nonce('grc-register-nonce'); ?>">
        <button type="submit" name="grc_submit">Register</button>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('guest_registration_form', 'grc_registration_form');