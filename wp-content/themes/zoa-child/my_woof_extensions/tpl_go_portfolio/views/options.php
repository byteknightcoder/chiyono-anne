<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>

<section id="tabs-<?php echo sanitize_title($folder_name) ?>">

    <?php WOOF_EXT::draw_options($options, $folder_name) ?>

</section>



