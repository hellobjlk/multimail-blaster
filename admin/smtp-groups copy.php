<?php
// smtp-groups.php

function mmb_list_smtp_groups() {
    ?>
    <h3><?php esc_html_e('Add New SMTP Group', 'multimail-blaster'); ?></h3>
    <form method="POST" action="">
        <input type="text" name="smtp_group_name" placeholder="<?php esc_attr_e('Group Name', 'multimail-blaster'); ?>" required />
        <?php submit_button(__('Add Group', 'multimail-blaster')); ?>
    </form>

    <h3><?php esc_html_e('Existing SMTP Groups', 'multimail-blaster'); ?></h3>
    <!-- List of SMTP groups would go here -->
    <p><?php esc_html_e('No SMTP groups found.', 'multimail-blaster'); ?></p>
    <?php
}
mmb_list_smtp_groups();
