<?php
// run-campaign.php

global $wpdb;

// Fetch campaigns
$campaigns = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mmb_campaigns");

// Fetch recipient groups
$recipient_groups = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mmb_recipient_groups");

// Fetch SMTP accounts
$smtp_accounts = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mmb_smtp_accounts");

// Fetch SMTP groups
$smtp_groups = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mmb_smtp_groups");

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

        <!-- Select SMTP Group or Individual SMTP Account -->
        <tr>
            <th><label for="smtp_type"><?php esc_html_e('Send Via', 'multimail-blaster'); ?></label></th>
            <td>
                <select id="smtp_type" name="smtp_type" required onchange="toggleSmtpSelection(this.value);">
                    <option value=""><?php esc_html_e('Select SMTP Type', 'multimail-blaster'); ?></option>
                    <option value="smtp_account"><?php esc_html_e('Individual SMTP Account', 'multimail-blaster'); ?></option>
                    <option value="smtp_group"><?php esc_html_e('SMTP Group', 'multimail-blaster'); ?></option>
                </select>
            </td>
        </tr>

        <!-- Select Individual SMTP Account -->
        <tr id="smtp_account_row" style="display: none;">
            <th><label for="smtp_id"><?php esc_html_e('Select SMTP Account', 'multimail-blaster'); ?></label></th>
            <td>
                <select id="smtp_id" name="smtp_id">
                    <option value=""><?php esc_html_e('Select an SMTP Account', 'multimail-blaster'); ?></option>
                    <?php foreach ($smtp_accounts as $smtp) : ?>
                        <option value="<?php echo esc_attr($smtp->id); ?>"><?php echo esc_html($smtp->smtp_host); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>

        <!-- Select SMTP Group -->
        <tr id="smtp_group_row" style="display: none;">
            <th><label for="smtp_group_id"><?php esc_html_e('Select SMTP Group', 'multimail-blaster'); ?></label></th>
            <td>
                <select id="smtp_group_id" name="smtp_group_id">
                    <option value=""><?php esc_html_e('Select an SMTP Group', 'multimail-blaster'); ?></option>
                    <?php foreach ($smtp_groups as $group) : ?>
                        <option value="<?php echo esc_attr($group->id); ?>"><?php echo esc_html($group->group_name); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>

    </table>

    <!-- Submit Button -->
    <?php submit_button(__('Run Campaign', 'multimail-blaster')); ?>
</form>

<script>
    // Show/Hide SMTP Account or SMTP Group based on selection
    function toggleSmtpSelection(value) {
        const smtpAccountRow = document.getElementById('smtp_account_row');
        const smtpGroupRow = document.getElementById('smtp_group_row');

        if (value === 'smtp_account') {
            smtpAccountRow.style.display = '';
            smtpGroupRow.style.display = 'none';
        } else if (value === 'smtp_group') {
            smtpAccountRow.style.display = 'none';
            smtpGroupRow.style.display = '';
        } else {
            smtpAccountRow.style.display = 'none';
            smtpGroupRow.style.display = 'none';
        }
    }
</script>

<?php
// Handle form submission and run the campaign
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $campaign_id = intval($_POST['campaign_id']);
    $recipient_group_id = intval($_POST['recipient_group_id']);
    $smtp_type = sanitize_text_field($_POST['smtp_type']);
    $smtp_id = null;
    $smtp_group_id = null;

    if ($smtp_type === 'smtp_account') {
        $smtp_id = intval($_POST['smtp_id']);
    } elseif ($smtp_type === 'smtp_group') {
        $smtp_group_id = intval($_POST['smtp_group_id']);
    }

    // Run the campaign
    mmb_run_campaign($campaign_id, $recipient_group_id, $smtp_id, $smtp_group_id);
}

// Function to handle running the campaign
function mmb_run_campaign($campaign_id, $recipient_group_id, $smtp_id = null, $smtp_group_id = null) {
    global $wpdb;

    // Fetch campaign details
    $campaign = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}mmb_campaigns WHERE id = %d", $campaign_id));

    // Fetch recipients in the group
    $recipients = $wpdb->get_results($wpdb->prepare(
        "SELECT r.* FROM {$wpdb->prefix}mmb_recipients r
         JOIN {$wpdb->prefix}mmb_recipient_group_relationship gr ON r.id = gr.recipient_id
         WHERE gr.group_id = %d", $recipient_group_id
    ));

    // Get SMTP details
    if ($smtp_id) {
        // Single SMTP account selected
        $smtp_account = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}mmb_smtp_accounts WHERE id = %d", $smtp_id));
    } elseif ($smtp_group_id) {
        // SMTP group selected, fetch all accounts in the group
        $smtp_accounts = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}mmb_smtp_accounts WHERE smtp_group_id = %d", $smtp_group_id));
    }

    // Use the smtp_rotation function to fetch the next available SMTP account
    $smtp_account = smtp_rotation();

    if (!$smtp_account) {
    echo '<div class="notice notice-error is-dismissible"><p>' . __('No available SMTP accounts for sending.', 'multimail-blaster') . '</p></div>';
    return;
    }


    // Process and send emails to recipients using the selected SMTP account or group
    if ($campaign && $recipients) {
        foreach ($recipients as $recipient) {
            // Personalize email content
            $personalized_message = str_replace(
                ['{greeting}', '{message}'],
                [$recipient->personalized_greeting, $recipient->personalized_message],
                $campaign->message
            );

            // If sending via individual SMTP account
            if ($smtp_account) {
                $email_sent = send_campaign_email($smtp_account, $recipient->recipient_email, $campaign->subject, $personalized_message);
            }

            // If sending via SMTP group (loop through accounts)
            if ($smtp_accounts) {
                foreach ($smtp_accounts as $smtp_account) {
                    $email_sent = send_campaign_email($smtp_account, $recipient->recipient_email, $campaign->subject, $personalized_message);
                }
            }

            // Log the result
            $wpdb->insert(
                "{$wpdb->prefix}mmb_campaign_logs",
                [
                    'campaign_id' => $campaign_id,
                    'recipient_id' => $recipient->id,
                    'smtp_id' => $smtp_account->id ?? null,
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
function send_campaign_email($smtp_account, $recipient_email, $subject, $message) {
    // Set up email headers
    $headers = array('Content-Type: text/html; charset=UTF-8');

    // Simulate email sending (you can integrate SMTP sending logic here)
    $success = wp_mail($recipient_email, $subject, $message, $headers);

    return $success;
}
?>
