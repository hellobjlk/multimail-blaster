<?php
// Include the SMTP rotation and email sending logic
require_once plugin_dir_path(__FILE__) . '../includes/smtp-rotation.php'; 

global $wpdb;

// Fetch campaigns
$campaigns = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mmb_campaigns");

// Fetch recipient groups
$recipient_groups = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mmb_recipient_groups");

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
    </table>
    <?php submit_button(__('Run Campaign', 'multimail-blaster')); ?>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $campaign_id = intval($_POST['campaign_id']);
    $recipient_group_id = intval($_POST['recipient_group_id']);

    // Run the campaign
    mmb_run_campaign($campaign_id, $recipient_group_id);
}

// Function to run the campaign
function mmb_run_campaign($campaign_id, $recipient_group_id) {
    global $wpdb;

    // Fetch campaign details
    $campaign = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}mmb_campaigns WHERE id = %d", $campaign_id));

    // Fetch recipients in the group
    $recipients = $wpdb->get_results($wpdb->prepare(
        "SELECT r.* FROM {$wpdb->prefix}mmb_recipients r
         JOIN {$wpdb->prefix}mmb_recipient_group_relationship gr ON r.id = gr.recipient_id
         WHERE gr.group_id = %d", $recipient_group_id
    ));

    if ($campaign && $recipients) {
        $batch_count = 0;

        foreach ($recipients as $recipient) {
            $smtp_account = get_next_smtp_account(); // Rotate through SMTP accounts

            if ($smtp_account) {
                // Personalize email content
                $personalized_message = str_replace(
                    ['{greeting}', '{message}'],
                    [$recipient->personalized_greeting, $recipient->personalized_message],
                    $campaign->message
                );

                // Send email using SMTP account
                $email_sent = send_email_with_smtp_rotation($smtp_account, $recipient->recipient_email, $campaign->subject, $personalized_message);

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

                // Batch processing logic
                $batch_count++;
                if ($batch_count >= $smtp_account->batch_size) {
                    sleep($smtp_account->rotation_delay); // Delay between batches
                    $batch_count = 0;
                }
            }
        }

        echo '<div class="notice notice-success is-dismissible"><p>' . __('Campaign run successfully!', 'multimail-blaster') . '</p></div>';
    } else {
        echo '<div class="notice notice-error is-dismissible"><p>' . __('Error: Campaign could not be run.', 'multimail-blaster') . '</p></div>';
    }
}
?>
