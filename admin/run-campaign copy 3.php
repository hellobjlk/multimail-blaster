<?php
// run-campaign.php

global $wpdb;

// Fetch campaigns
$campaigns = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mmb_campaigns");

// Fetch recipient groups
$recipient_groups = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mmb_recipient_groups");

// Fetch SMTP accounts
$smtp_table = $wpdb->prefix . 'mmb_smtp_accounts';
$smtp_accounts = $wpdb->get_results("SELECT * FROM $smtp_table");

// Error handling if no SMTP accounts are found
if (empty($smtp_accounts)) {
    echo '<div class="notice notice-error is-dismissible"><p>' . __('No SMTP accounts found. Please add an SMTP account.', 'multimail-blaster') . '</p></div>';
    return;
}

?>
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

<?php
// Handle form submission and run the campaign
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $campaign_id = intval($_POST['campaign_id']);
    $recipient_group_id = intval($_POST['recipient_group_id']);
    $smtp_id = intval($_POST['smtp_id']);

    // Run the campaign
    mmb_run_campaign($campaign_id, $recipient_group_id, $smtp_id);
}

// Function to handle running the campaign
function mmb_run_campaign($campaign_id, $recipient_group_id, $smtp_id) {
    global $wpdb;

    // Fetch campaign details
    $campaign = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}mmb_campaigns WHERE id = %d", $campaign_id));

    // Fetch recipients in the group
    $recipients = $wpdb->get_results($wpdb->prepare(
        "SELECT r.* FROM {$wpdb->prefix}mmb_recipients r
         JOIN {$wpdb->prefix}mmb_recipient_group_relationship gr ON r.id = gr.recipient_id
         WHERE gr.group_id = %d", $recipient_group_id
    ));

    // Fetch SMTP account details
    $smtp_account = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}mmb_smtp_accounts WHERE id = %d", $smtp_id));

    if ($campaign && $recipients && $smtp_account) {
        foreach ($recipients as $recipient) {
            // Personalize email content
            $personalized_message = str_replace(
                ['{greeting}', '{message}'],
                [$recipient->personalized_greeting, $recipient->personalized_message],
                $campaign->message
            );

            // Send email using wp_mail() (actual email sending function)
            $email_sent = send_campaign_email($recipient, $campaign->subject, $personalized_message, $smtp_account);

            // Log the result
            $wpdb->insert(
                "{$wpdb->prefix}mmb_campaign_logs",
                [
                    'campaign_id' => $campaign_id,
                    'recipient_id' => $recipient->id,
                    'smtp_id' => $smtp_account->id,
                    'status' => $email_sent ? 'sent' : 'failed',
                    'sent_at' => current_time('mysql'),
                ]
            );
        }

        echo '<div class="notice notice-success is-dismissible"><p>' . __('Campaign run successfully!', 'multimail-blaster') . '</p></div>';
    } else {
        echo '<div class="notice notice-error is-dismissible"><p>' . __('Error: Campaign could not be run.', 'multimail-blaster') . '</p></div>';
    }
}

// Function to send emails using wp_mail()
function send_campaign_email($recipient, $subject, $message, $smtp_account) {
    // Set up email headers
    $headers = array('Content-Type: text/html; charset=UTF-8');

    // Use wp_mail() to send emails
    $success = wp_mail($recipient->recipient_email, $subject, $message, $headers);

    return $success;
}
