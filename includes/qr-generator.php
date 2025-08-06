<?php
function grc_generate_qr_code($data) {
    // Verify the QR code library exists
    $qrlib_path = plugin_dir_path(__FILE__) . '../vendor/phpqrcode.php';

    if (!file_exists($qrlib_path)) {
        error_log('QR Code library not found at: ' . $qrlib_path);
        return false;
    }

    require_once $qrlib_path;

    $upload_dir = wp_upload_dir();
    $qr_dir = $upload_dir['basedir'] . '/guest-qr-codes/';

    // Create directory if it doesn't exist
    if (!file_exists($qr_dir)) {
        if (!wp_mkdir_p($qr_dir)) {
            error_log('Failed to create QR code directory: ' . $qr_dir);
            return false;
        }
    }

    $filename = 'qr-' . md5($data . time()) . '.png';
    $filepath = $qr_dir . $filename;

    try {
        // Generate QR code
        QRcode::png($data, $filepath, QR_ECLEVEL_L, 10);

        // Verify the file was created
        if (!file_exists($filepath)) {
            error_log('QR code generation failed - file not created');
            return false;
        }

        return $upload_dir['baseurl'] . '/guest-qr-codes/' . $filename;
    } catch (Exception $e) {
        error_log('QR code generation error: ' . $e->getMessage());
        return false;
    }
}