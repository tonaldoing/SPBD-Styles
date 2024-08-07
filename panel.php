<?php

function add_spbd_style_admin_page() {
    add_menu_page(
        'SPBD Style',             // Page title
        'SPBD Style',             // Menu text
        'manage_options',         // Required capability to access the page
        'spbd_style',             // Page slug
        'show_spbd_page_content', // Function to display page content
        'dashicons-admin-generic' // Icon 
    );
};

// Function to display content on the custom page
function show_spbd_page_content() {
    echo '<div class="wrap">';
    echo '<h2>SPBD Style</h2>';
    
    $child_theme_directory = trailingslashit(get_stylesheet_directory());
    $child_templates = glob($child_theme_directory . 'page-*.php');

    echo '<a href="' . admin_url('post-new.php?post_type=spbd_style') . '" class="page-title-action">Add New</a>';

    // Page Templates
    echo '<h3>Page Templates</h3>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Template Name</th>';
    echo '<th>CSS</th>';
    echo '<th>JS</th>';
    echo '<th>Edit Template</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    $template_args = array(
        'post_type' => 'spbd_style',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'global_style', 
                'field'    => 'slug', 
                'terms'    => 'is_global', 
                'operator' => 'NOT IN',
            ),
        ),
    );    
    $spbd_styles_template = get_posts($template_args);

    foreach ($spbd_styles_template as $style) {
        echo '<tr>';
        echo '<td>' . esc_html(get_post_meta($style->ID, 'page_template_url', true)) . '</td>';
        echo '<td>' . esc_html(get_post_meta($style->ID, 'css_template_url', true)) . '</td>';
        echo '<td>' . esc_html(get_post_meta($style->ID, 'js_template_url', true)) . '</td>';
        echo '<td><a href="' . admin_url('post.php?post=' . $style->ID . '&action=edit') . '">Edit</a> | <a href="' . get_delete_post_link($style->ID) . '">Trash</a></td>';

        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';

    // Global Templates
    echo '<br><br><h3>Global Templates</h3>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Template Name</th>';
    echo '<th>CSS</th>';
    echo '<th>JS</th>';
    echo '<th>Edit Template</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    $global_args = array(
        'post_type' => 'spbd_style',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'global_style', 
                'field'    => 'slug', 
                'terms'    => 'is_global', 
            ),
        ),
    );    
    $spbd_styles_global = get_posts($global_args);

    foreach ($spbd_styles_global as $style) {
        echo '<tr>';
        echo '<td>' . esc_html(get_post_meta($style->ID, 'page_template_url', true)) . '</td>';
        echo '<td>' . esc_html(get_post_meta($style->ID, 'css_template_url', true)) . '</td>';
        echo '<td>' . esc_html(get_post_meta($style->ID, 'js_template_url', true)) . '</td>';
        echo '<td><a href="' . admin_url('post.php?post=' . $style->ID . '&action=edit') . '">Edit</a> | <a href="' . get_delete_post_link($style->ID) . '">Trash</a></td>';

        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
};

add_action('admin_menu', 'add_spbd_style_admin_page');
