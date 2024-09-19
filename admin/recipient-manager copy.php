<?php
// Enqueue scripts and styles for Recipients Manager
function mmb_enqueue_recipient_manager_assets() {
    wp_enqueue_style('mmb-recipient-manager-css', plugin_dir_url(__FILE__) . '../assets/css/recipient-manager.css');
    wp_enqueue_script('mmb-recipient-manager-js', plugin_dir_url(__FILE__) . '../assets/js/recipient-manager.js', array('jquery'), false, true);
}
add_action('admin_enqueue_scripts', 'mmb_enqueue_recipient_manager_assets');


// Function to render the Recipients Manager page with tabs.
function mmb_recipient_manager_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Recipients Manager', 'multimail-blaster'); ?></h1>

        <!-- Tabs Navigation -->
        <h2 class="nav-tab-wrapper">
            <a href="#tab-1" class="nav-tab nav-tab-active"><?php esc_html_e('Add Recipients', 'multimail-blaster'); ?></a>
            <a href="#tab-2" class="nav-tab"><?php esc_html_e('List Recipients', 'multimail-blaster'); ?></a>
            <a href="#tab-3" class="nav-tab"><?php esc_html_e('Recipient Groups', 'multimail-blaster'); ?></a>
        </h2>

        <!-- Tab Content -->
        <div id="tab-1" class="tab-content">
            <h2><?php esc_html_e('Add Recipients', 'multimail-blaster'); ?></h2>
            <?php include_once(plugin_dir_path(__FILE__) . 'recipient-add.php'); ?>
        </div>

        <div id="tab-2" class="tab-content" style="display:none;">
            <h2><?php esc_html_e('List Recipients', 'multimail-blaster'); ?></h2>
            <?php include_once(plugin_dir_path(__FILE__) . 'recipient-list.php'); ?>
        </div>

        <div id="tab-3" class="tab-content" style="display:none;">
            <h2><?php esc_html_e('Recipient Groups', 'multimail-blaster'); ?></h2>
            <?php include_once(plugin_dir_path(__FILE__) . 'recipient-groups.php'); ?>
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

                // Remove active class from all tabs and hide all content
                tabs.forEach(function (item) {
                    item.classList.remove('nav-tab-active');
                });
                contents.forEach(function (content) {
                    content.style.display = 'none';
                });

                // Add active class to clicked tab and show corresponding content
                tab.classList.add('nav-tab-active');
                contents[index].style.display = 'block';
            });
        });
    });
    </script>
    <?php
}
?>
