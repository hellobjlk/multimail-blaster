<?php
// Run Campaign Page

function mmb_run_campaign_page() {
    global $wpdb;
    
    // Fetch campaigns
    $campaigns = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mmb_campaigns");
    
    // Fetch recipient groups
    $recipient_groups = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mmb_recipient_groups");
    
    // Fetch SMTP accounts
    $smtp_accounts = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mmb_smtp_accounts");

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

        <!-- Submit to Start Campaign -->
        <?php submit_button(__('Run Campaign', 'multimail-blaster')); ?>
    </form>

    <?php
    // Handle campaign run process after form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $campaign_id = intval($_POST['campaign_id']);
        $recipient_group_id = intval($_POST['recipient_group_id']);
        $smtp_id = intval($_POST['smtp_id']);

        // Run the campaign (this would include sending emails and logging progress)
        mmb_run_campaign($campaign_id, $recipient_group_id, $smtp_id);
    }
}

// Function to handle sending the campaign
function mmb_run_campaign($campaign_id, $recipient_group_id, $smtp_id) {
    global $wpdb;
    
    // Fetch campaign details
    $campaign = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}mmb_campaigns WHERE id = %d", $campaign_id));
    
    // Fetch recipients from the selected group
    $recipients = $wpdb->get_results($wpdb->prepare(
        "SELECT r.*, rg.group_name FROM {$wpdb->prefix}mmb_recipients r
         JOIN {$wpdb->prefix}mmb_recipient_group_relationship gr ON r.id = gr.recipient_id
         JOIN {$wpdb->prefix}mmb_recipient_groups rg ON rg.id = gr.group_id
         WHERE gr.group_id = %d", $recipient_group_id
    ));

    // Fetch SMTP account details
    $smtp_account = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}mmb_smtp_accounts WHERE id = %d", $smtp_id));

    if ($campaign && $recipients && $smtp_account) {
        // Here, you would loop through the recipients and send emails using the selected SMTP account
        foreach ($recipients as $recipient) {
            // Personalize email body
            $personalized_message = str_replace(
                array('{greeting}', '{message}'), 
                array($recipient->personal_greeting, $recipient->personal_message), 
                $campaign->message
            );

            // Send email function
            $email_sent = mmb_send_email($smtp_account, $recipient->recipient_email, $campaign->subject, $personalized_message);
            
            // Log result in campaign_logs table
            $wpdb->insert(
                "{$wpdb->prefix}mmb_campaign_logs",
                array(
                    'campaign_id' => $campaign_id,
                    'recipient_id' => $recipient->id,
                    'smtp_id' => $smtp_id,
                    'status' => $email_sent ? 'sent' : 'failed',
                    'sent_at' => current_time('mysql'),
                )
            );
        }

        echo '<div class="notice notice-success is-dismissible"><p>' . __('Campaign run completed successfully!', 'multimail-blaster') . '</p></div>';
    } else {
        echo '<div class="notice notice-error is-dismissible"><p>' . __('Error: Unable to run campaign.', 'multimail-blaster') . '</p></div>';
    }
}

// Function to handle actual email sending via SMTP
function mmb_send_email($smtp_account, $to_email, $subject, $message) {
    // Here you'd configure the SMTP settings and send the email using PHPMailer or WordPress' wp_mail()
    // For simplicity, returning true to simulate successful email sending.
    return true;
}
?>
