<?php
// campaign-manager.php

function mmb_campaign_manager_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Campaign Manager', 'multimail-blaster'); ?></h1>
        
        <!-- Tabs Navigation -->
        <h2 class="nav-tab-wrapper">
            <a href="#tab-1" class="nav-tab nav-tab-active"><?php esc_html_e('Add Campaign', 'multimail-blaster'); ?></a>
            <a href="#tab-2" class="nav-tab"><?php esc_html_e('Manage Campaigns', 'multimail-blaster'); ?></a>
        </h2>

        <!-- Tab Content -->
        <div id="tab-1" class="tab-content">
            <?php require_once(plugin_dir_path(__FILE__) . 'campaign-add.php'); ?>
        </div>

        <div id="tab-2" class="tab-content" style="display:none;">
            <?php require_once(plugin_dir_path(__FILE__) . 'campaign-list.php'); ?>
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
?>
