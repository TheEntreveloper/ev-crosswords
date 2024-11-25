<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open();
    ?>
    <div class="wp-site-blocks">

        <?php echo do_blocks('<!-- wp:template-part {"slug":"header","area":"header","tagName":"header"} /-->'); ?>

        <?php echo do_blocks('<!-- wp:group {"tagName":"main","layout":{"inherit":true}} -->'); ?>

        <main class="wp-block-group">