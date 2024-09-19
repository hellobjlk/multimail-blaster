<?php
// SMTP Manager page with Tabs

// Enqueue CSS and JS for the SMTP Manager
function mmb_enqueue_smtp_manager_assets($hook_suffix) {
    // Only load assets on the SMTP Manager page
    if ($hook_suffix === 'multimail-blaster_page_mmb-smtp-manager') {
        // Correct path to CSS
        wp_enqueue_style('mmb-smtp-css', plugin_dir_url(__FILE__) . '../assets/css/smtp-manager.css');
        // Correct path to JS
        wp_enqueue_script('mmb-smtp-js', plugin_dir_url(__FILE__) . '../assets/js/smtp-manager.js', array('jquery'), null, true);
    }
}
add_action('admin_enqueue_scripts', 'mmb_enqueue_smtp_manager_assets');



// Function to display the SMTP Manager page with tabs
function mmb_smtp_manager_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('SMTP Manager', 'multimail-blaster'); ?></h1>
        
        <!-- Tabs Navigation -->
        <h2 class="nav-tab-wrapper">
            <a href="#tab-1" class="nav-tab nav-tab-active"><?php esc_html_e('Add SMTP Accounts', 'multimail-blaster'); ?></a>
            <a href="#tab-2" class="nav-tab"><?php esc_html_e('Manage SMTP Accounts', 'multimail-blaster'); ?></a>
            <a href="#tab-3" class="nav-tab"><?php esc_html_e('Manage SMTP Groups', 'multimail-blaster'); ?></a>
            <a href="#tab-4" class="nav-tab"><?php esc_html_e('SMTP Settings', 'multimail-blaster'); ?></a>
        </h2>

        <!-- Tab Content -->
        <div id="tab-1" class="tab-content" style="display: block;">
            <?php require_once(plugin_dir_path(__FILE__) . 'smtp-add.php'); ?>
        </div>

        <div id="tab-2" class="tab-content" style="display:none;">
            <?php require_once(plugin_dir_path(__FILE__) . 'smtp-list.php'); ?>
        </div>

        <div id="tab-3" class="tab-content" style="display:none;">
            <?php require_once(plugin_dir_path(__FILE__) . 'smtp-groups.php'); ?>
        </div>

        <div id="tab-4" class="tab-content" style="display:none;">
            <?php require_once(plugin_dir_path(__FILE__) . 'smtp-settings.php'); ?>
        </div>
    </div>

    <!-- JavaScript for Tab Switching -->
    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        const tabs = document.querySelectorAll('.nav-tab');
        const contents = document.querySelectorAll('.tab-content');

        tabs.forEach(function (tab, index) {
            tab.addEventListener('click', function (e) {
                e.preventDefault();

                tabs.forEach(function (item) {
                    item.classList.remove('nav-tab-active');
                });
                tab.classList.add('nav-tab-active');

                contents.forEach(function (content) {
                    content.style.display = 'none';
                });
                contents[index].style.display = 'block';
            });
        });
    });
    </script>
    <script>
    document.getElementById('select-all').addEventListener('click', function(event) {
    const checkboxes = document.querySelectorAll('input[name="smtp_ids[]"]');
    checkboxes.forEach(checkbox => checkbox.checked = event.target.checked);
    });
    </script>

    <?php
}
?>
