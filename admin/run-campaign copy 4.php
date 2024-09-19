<?php
// run-campaign.php

global $wpdb;

// Fetch campaigns
$campaigns = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mmb_campaigns");

// Fetch recipient groups
$recipient_groups = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mmb_recipient_groups");

// Fetch SMTP accounts
$smtp_accounts = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mmb_smtp_accounts");

// Logging function for campaign actions
function log_campaign_event($message) {
    $log_dir = plugin_dir_path(__FILE__) . '../logs/';
    $log_file = $log_dir . 'campaign-log.txt';

    // Ensure logs directory exists, create it if necessary
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0755, true);
    }

    // Log the message with a timestamp
    $timestamp = date('[Y-m-d H:i:s]');
    file_put_contents($log_file, $timestamp . ' ' . $message . PHP_EOL, FILE_APPEND);
}

// Function to send emails using wp_mail() and log results
function send_campaign_email($smtp_account, $recipient, $subject, $message) {
    global $phpmailer;

    // Set up email headers
    $headers = array('Content-Type: text/html; charset=UTF-8');

    // Attempt to send the email using wp_mail()
    $success = wp_mail($recipient->recipient_email, $subject, $message, $headers);

    // Check for errors and log them
    if (!$success) {
        $error_message = isset($phpmailer->ErrorInfo) ? $phpmailer->ErrorInfo : 'Unknown error during email sending';
        log_campaign_event("Email failed to: {$recipient->recipient_email} with error: {$error_message}");
    } else {
        log_campaign_event("Email sent successfully to: {$recipient->recipient_email} via SMTP: {$smtp_account->smtp_host}");
    }

    return $success;
}

// Function to handle running the campaign and processing recipients
function mmb_run_campaign($campaign_id, $recipient_group_id, $smtp_id) {
    global $wpdb;

    // Fetch campaign details
    $campaign = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}mmb_campaigns WHERE id = %d", $campaign_id));

    // Fetch recipients from the selected group
    $recipients = $wpdb->get_results($wpdb->prepare(
        "SELECT r.* FROM {$wpdb->prefix}mmb_recipients r
         JOIN {$wpdb->prefix}mmb_recipient_group_relationship gr ON r.id = gr.recipient_id
         WHERE gr.group_id = %d", $recipient_group_id
    ));

    // Fetch SMTP account details
    $smtp_account = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}mmb_smtp_accounts WHERE id = %d", $smtp_id));

    if ($campaign && $recipients && $smtp_account) {
        log_campaign_event("Starting campaign ID: $campaign_id for recipient group ID: $recipient_group_id using SMTP ID: $smtp_id");

        foreach ($recipients as $recipient) {
            // Personalize email content
            $personalized_message = str_replace(
                ['{greeting}', '{message}'],
                [$recipient->personalized_greeting, $recipient->personalized_message],
                $campaign->message
            );

            // Attempt to send the email
            $email_sent = send_campaign_email($smtp_account, $recipient, $campaign->subject, $personalized_message);

            // Log the result of email sending
            $wpdb->insert(
                "{$wpdb->prefix}mmb_campaign_logs",
                [
                    'campaign_id' => $campaign_id,
                    'recipient_id' => $recipient->id,
                    'smtp_id' => $smtp_id,
                    'status' => $email_sent ? 'sent' : 'failed',
                    'sent_at' => current_time('mysql'),
                ]
            );
        }

        echo '<div class="notice notice-success is-dismissible"><p>' . __('Campaign run successfully!', 'multimail-blaster') . '</p></div>';
    } else {
        log_campaign_event("Error: Missing campaign, recipients, or SMTP account data.");
        echo '<div class="notice notice-error is-dismissible"><p>' . __('Error: Campaign could not be run.', 'multimail-blaster') . '</p></div>';
    }
}

// Handle form submission and run the campaign
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campaign_id = intval($_POST['campaign_id']);
    $recipient_group_id = intval($_POST['recipient_group_id']);
    $smtp_id = intval($_POST['smtp_id']);

    // Run the campaign
    mmb_run_campaign($campaign_id, $recipient_group_id, $smtp_id);
}
?>

<!-- HTML Form to select campaign, recipient group, and SMTP account -->
<h2><?php esc_html_e('Run Campaign', 'multimail-blaster'); ?></h2>

<form method="POST" action="">
    <table class="form-table">
        <!-- Select Campaign -->
        <tr>
            <th><label for="campaign_id"><?php esc_html_e('Select Campaign', 'multimail-blaster'); ?></label></th>
            <td>
                <select id="campaign_id" name="campaign_id" required>
                    <option value=""><?php esc_html_e('Select a Campaign', 'multimail-blaster'); ?></option>
                    <?php foreach ($campaigns as $campaign) : ?>
                        <option value="<?php echo esc_attr($campaign->id); ?>"><?php echo esc_html($campaign->campaign_name); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>

        <!-- Select Recipient Group -->
        <tr>
            <th><label for="recipient_group_id"><?php esc_html_e('Select Recipient Group', 'multimail-blaster'); ?></label></th>
            <td>
                <select id="recipient_group_id" name="recipient_group_id" required>
                    <option value=""><?php esc_html_e('Select a Recipient Group', 'multimail-blaster'); ?></option>
                    <?php foreach ($recipient_groups as $group) : ?>
                        <option value="<?php echo esc_attr($group->id); ?>"><?php echo esc_html($group->group_name); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>

        <!-- Select SMTP Account -->
        <tr>
            <th><label for="smtp_id"><?php esc_html_e('Select SMTP Account', 'multimail-blaster'); ?></label></th>
            <td>
                <select id="smtp_id" name="smtp_id" required>
                    <option value=""><?php esc_html_e('Select an SMTP Account', 'multimail-blaster'); ?></option>
                    <?php foreach ($smtp_accounts as $smtp) : ?>
                        <option value="<?php echo esc_attr($smtp->id); ?>"><?php echo esc_html($smtp->smtp_host); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>

    <!-- Submit Button -->
    <?php submit_button(__('Run Campaign', 'multimail-blaster')); ?>
</form>

