<?php
// smtp-manager.php

// Function to render the SMTP Manager page with tabs
function mmb_smtp_manager_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('SMTP Manager', 'multimail-blaster'); ?></h1>

        <!-- Tabs Navigation -->
        <h2 class="nav-tab-wrapper">
            <a href="#tab-1" class="nav-tab nav-tab-active"><?php esc_html_e('Add SMTP Account', 'multimail-blaster'); ?></a>
            <a href="#tab-2" class="nav-tab"><?php esc_html_e('Manage SMTP Accounts', 'multimail-blaster'); ?></a>
            <a href="#tab-3" class="nav-tab"><?php esc_html_e('SMTP Groups', 'multimail-blaster'); ?></a>
            <a href="#tab-4" class="nav-tab"><?php esc_html_e('SMTP Settings', 'multimail-blaster'); ?></a>
        </h2>

        <!-- Tab Content -->
        <div id="tab-1" class="tab-content">
            <?php include_once plugin_dir_path(__FILE__) . 'smtp-add.php'; ?>
        </div>

        <div id="tab-2" class="tab-content" style="display:none;">
            <?php include_once plugin_dir_path(__FILE__) . 'smtp-list.php'; ?>
        </div>

        <div id="tab-3" class="tab-content" style="display:none;">
            <?php include_once plugin_dir_path(__FILE__) . 'smtp-groups.php'; ?>
        </div>

        <div id="tab-4" class="tab-content" style="display:none;">
            <?php include_once plugin_dir_path(__FILE__) . 'smtp-settings.php'; ?>
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
    <?php
}
