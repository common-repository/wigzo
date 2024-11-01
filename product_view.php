<?php
if (!defined('ABSPATH')) exit;
global $wp_current_filter;
global $woocommerce;

$page = $wp_current_filter[0];
include_once(ABSPATH . 'wp-admin/includes/plugin.php');
?>
<script>
  jQuery(function ($) { // DOM is now ready and jQuery's $ alias sandboxed
    wigzo("track", "productview");
  });
</script>
