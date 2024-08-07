<?php

/*
Index:

/Configuration
1- Custom Post Type "SPBD Style".
2- "SPBD Styles" Form.

/Functions
3- JS.
4- CSS.
5- Template.
6- Pages.
7- Merge.
8- Remove.

*/

/**
 * 
 * Custom Post Type "SPBD Style"
 * 
*/

function create_cpt_spbd_style() {
    $args = array(
        'label' => 'SPBD Styles',
        'public' => true,
        'show_in_menu' => false, 
        'supports' => array('title', 'custom-fields'),
    );
    register_post_type('spbd_style', $args);

    add_action('add_meta_boxes_spbd_style', 'add_spbd_files_form');
}
add_action('init', 'create_cpt_spbd_style');


//Post taxonomy - global stylesheet
function register_global_style_taxonomy() {
    $labels = array(
        'name'                       => _x( 'Global Styles', 'Taxonomy general name', 'text_domain' ),
        'singular_name'              => _x( 'Global Style', 'Taxonomy singular name', 'text_domain' ),
        'menu_name'                  => __( 'Global Styles', 'text_domain' ),
        'all_items'                  => __( 'All Global Styles', 'text_domain' ),
        'parent_item'                => __( 'Parent Global Style', 'text_domain' ),
        'parent_item_colon'          => __( 'Parent Global Style:', 'text_domain' ),
        'new_item_name'              => __( 'New Global Style Name', 'text_domain' ),
        'add_new_item'               => __( 'Add New Global Style', 'text_domain' ),
        'edit_item'                  => __( 'Edit Global Style', 'text_domain' ),
        'update_item'                => __( 'Update Global Style', 'text_domain' ),
        'view_item'                  => __( 'View Global Style', 'text_domain' ),
        'separate_items_with_commas' => __( 'Separate Global Styles with commas', 'text_domain' ),
        'add_or_remove_items'        => __( 'Add or remove Global Styles', 'text_domain' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
        'popular_items'              => __( 'Popular Global Styles', 'text_domain' ),
        'search_items'               => __( 'Search Global Styles', 'text_domain' ),
        'not_found'                  => __( 'No Global Styles found', 'text_domain' ),
        'no_terms'                   => __( 'No Global Styles', 'text_domain' ),
        'items_list'                 => __( 'Global Styles list', 'text_domain' ),
        'items_list_navigation'      => __( 'Global Styles list navigation', 'text_domain' ),
    );

    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true, 
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
    );

    // Register the custom taxonomy
    register_taxonomy( 'global_style', array( 'spbd_style' ), $args );

    if (taxonomy_exists('global_style')) {
        $term = term_exists('is global', 'global_style');
    
        if ($term === 0 || $term === null) {
            $term_data = wp_insert_term(
                'is global',    
                'global_style', 
                array(
                    'slug' => 'is_global', 
                )
            );
        } 
    } 
}
add_action( 'init', 'register_global_style_taxonomy', 0 );


/**
 * 
 * SPBD Styles Form.
 * 
 */


// Form

function add_spbd_files_form() {
    add_meta_box(
        'add_spbd_files_form',
        'Files form',
        'display_spbd_files_form',
        'spbd_style',
        'normal',
        'high'
    );
}

function display_spbd_files_form($post) {
    $css_checkbox = get_post_meta($post->ID, 'css_file_checkbox', true);
    $js_checkbox = get_post_meta($post->ID, 'js_file_checkbox', true);
    $template_checkbox = get_post_meta($post->ID, 'template_checkbox', true);
    $selected_pages = get_post_meta($post->ID, 'selected_pages', true);

    // Obtener todas las páginas
    $pages = get_pages();

    ?>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="css_file_checkbox">CSS:</label></th>
            <td>
                <input type="checkbox" id="css_file_checkbox" name="css_file_checkbox" value="1" <?php checked(1, $css_checkbox, true); ?> />
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="js_file_checkbox">JS:</label></th>
            <td>
                <input type="checkbox" id="js_file_checkbox" name="js_file_checkbox" value="1" <?php checked(1, $js_checkbox, true); ?> />
            </td>
        </tr>
        <!--
        TO BE REMOVED
        <tr>
            <th scope="row"><label for="template_checkbox">Template:</label></th>
            <td>
                <input type="checkbox" id="template_checkbox" name="template_checkbox" value="1" <?php checked(1, $template_checkbox, true); ?> />
            </td>
        </tr>
        <tr>
        -->
            <th scope="row"><label for="selected_pages">Select Pages:</label></th>
            <td>
                <?php foreach ($pages as $page) : ?>
                    <?php
                    $page_checkbox = in_array($page->ID, (array) $selected_pages);
                    ?>
                    <label><input type="checkbox" class="page-checkbox" name="selected_pages[]" value="<?php echo esc_attr($page->ID); ?>" <?php checked(true, $page_checkbox); ?> /> <?php echo esc_html($page->post_title); ?></label><br>
                <?php endforeach; ?>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * 
 * JS.
 * 
*/

// Create JS File
function create_js_file($post_id) {
    // Check if saving is an autosave or revision
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check if the user has permission to edit the post
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Check if it's the correct post type
    if ('spbd_style' !== get_post_type($post_id)) {
        return;
    }

    // Get the checkbox value
    $js_file_checkbox_value = isset($_POST['js_file_checkbox']) ? 1 : 0;

    // Get current meta value
    $current_value = get_post_meta($post_id, 'js_file_checkbox', true);

    // Update post meta with checkbox value
    update_post_meta($post_id, 'js_file_checkbox', $js_file_checkbox_value);

    // Path to JS file and trash directory
    $child_theme_path = get_stylesheet_directory();
    $post_title = get_the_title($post_id);
    $post_slug = sanitize_title($post_title);
    $js_file_path = $child_theme_path . '/js/' . $post_slug . '.js';
    $js_trash_path = $child_theme_path . '/js/trash/';

    if ($js_file_checkbox_value && $js_file_checkbox_value != $current_value) {
        // Checkbox was checked and is different from previous state
        // Ensure file exists or create it
        if (!file_exists($js_file_path)) {
            // Check if file exists in trash and move it back
            $js_trash_file_path = $js_trash_path . $post_slug . '.js';
            if (file_exists($js_trash_file_path)) {
                rename($js_trash_file_path, $js_file_path);
            } else {
                // Create directory if it doesn't exist
                if (!file_exists($child_theme_path . '/js/')) {
                    mkdir($child_theme_path . '/js/');
                }

                // Default content
                $js_content = "jQuery(document).ready(function($){\n});";

                // Write content to the file
                file_put_contents($js_file_path, $js_content);

                // Update meta with template URL
                update_post_meta($post_id, 'js_template_url', sanitize_text_field($post_slug).'.js');
            }
        }
    } elseif (!$js_file_checkbox_value && $js_file_checkbox_value != $current_value) {
        // Checkbox was unchecked and is different from previous state
        // Move file to trash if it exists
        if (file_exists($js_file_path)) {
            // Check if trash directory exists
            if (!file_exists($js_trash_path)) {
                mkdir($js_trash_path);
            }

            // Determine new file path in trash
            $js_trash_file_path = $js_trash_path . $post_slug . '.js';

            // Move file to trash
            if (rename($js_file_path, $js_trash_file_path)) {
                // Update meta to indicate no template URL
                update_post_meta($post_id, 'js_template_url', sanitize_text_field($post_slug).'.js');
            }
        }
    }
}
add_action('save_post_spbd_style', 'create_js_file');

/**
 * 
 * CSS.
 * 
*/

// Create CSS File
function create_css_file($post_id) {
    // Check if saving is an autosave or revision
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check if the user has permission to edit the post
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Check if it's the correct post type
    if ('spbd_style' !== get_post_type($post_id)) {
        return;
    }

    // Get the checkbox value
    $css_file_checkbox_value = isset($_POST['css_file_checkbox']) ? 1 : 0;

    // Update post meta with checkbox value
    update_post_meta($post_id, 'css_file_checkbox', $css_file_checkbox_value);

    // Path to CSS file
    $child_theme_path = get_stylesheet_directory();
    $post_title = get_the_title($post_id);
    $post_slug = sanitize_title($post_title);
    $css_file_path = $child_theme_path . '/css/' . $post_slug . '.css';
    $css_trash_path = $child_theme_path . '/css/trash/';

    if ($css_file_checkbox_value) {
        // Checkbox is checked, create or update the CSS file

        // Create directory if it doesn't exist
        if (!file_exists($child_theme_path . '/css/')) {
            mkdir($child_theme_path . '/css/');
        }

        // Template content based on global or single-page template
        $template_path = '';
        $terms = wp_get_object_terms($post_id, 'global_style', array('fields' => 'slugs'));
        if (in_array('is_global', $terms)) {
            $template_path = get_template_directory() . '/css-templates/css-global-template.css';
        } else {
            $template_path = get_template_directory() . '/css-templates/css-single-page-template.css';
        }

        // Default content
        $css_content = file_get_contents($template_path);

        // Write content to the file
        file_put_contents($css_file_path, $css_content);

        // Update meta with template URL
        update_post_meta($post_id, 'css_template_url', $post_slug . '.css');

        // Check if there's a file in the trash to delete
        $css_trash_file_path = $css_trash_path . $post_slug . '.css';
        if (file_exists($css_trash_file_path)) {
            unlink($css_trash_file_path); // Delete file from trash if it exists
        }
    } else {
        // Checkbox is unchecked, move file to trash if it exists
        if (file_exists($css_file_path)) {
            // Check if trash directory exists
            if (!file_exists($css_trash_path)) {
                mkdir($css_trash_path);
            }

            // Determine new file path in trash
            $css_trash_file_path = $css_trash_path . $post_slug . '.css';

            // Move file to trash
            if (rename($css_file_path, $css_trash_file_path)) {
                // Update meta to indicate no template URL
                update_post_meta($post_id, 'css_template_url', '');
            }
        }
    }
}
add_action('save_post_spbd_style', 'create_css_file');

/**
 * 
 * Template.
 * 
*/

// Create Template Page File
function create_template_file($post_id) {
    // Check if saving is an autosave or revision
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check if the user has permission to edit the post
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Check if it's the correct post type
    if ('spbd_style' !== get_post_type($post_id)) {
        return;
    }

    // Get the checkbox value
    $template_file_checkbox = isset($_POST['template_checkbox']) ? 1 : 0;

    // Update post meta with checkbox value
    update_post_meta($post_id, 'template_checkbox', $template_file_checkbox);

    // Handle template file creation, deletion, and restoration based on checkbox state
    $post_title = get_the_title($post_id);
    $post_slug = sanitize_title($post_title);
    $child_theme_path = get_stylesheet_directory();
    $new_page_file_path = $child_theme_path . '/page-' . $post_slug . '.php';
    $page_trash_path = $child_theme_path . '/trash/';

    if ($template_file_checkbox) {
        // Checkbox is checked
        if (file_exists($page_trash_path . 'page-' . $post_slug . '.php')) {
            // Restore file from trash if it exists
            if (rename($page_trash_path . 'page-' . $post_slug . '.php', $new_page_file_path)) {
                // Update meta with template URL
                update_post_meta($post_id, 'page_template_url', 'page-' . $post_slug . '.php');
            }
        } else {
            // File doesn't exist in trash, create new template
            if (!file_exists($new_page_file_path)) {
                $template_name_line = "<?php /* Template Name: $post_title */ ?>\n";
                file_put_contents($new_page_file_path, $template_name_line);
                // Optionally append content from a parent template
                $parent_page_template = get_template_directory() . '/page.php';
                if (file_exists($parent_page_template)) {
                    $parent_page_content = file_get_contents($parent_page_template);
                    file_put_contents($new_page_file_path, $parent_page_content, FILE_APPEND);
                }
                // Update meta with template URL
                update_post_meta($post_id, 'page_template_url', 'page-' . $post_slug . '.php');
            }
        }
    } else {
        // Checkbox is unchecked, move file to trash if it exists
        if (file_exists($new_page_file_path)) {
            // Check if trash directory exists
            if (!file_exists($page_trash_path)) {
                mkdir($page_trash_path);
            }

            // Determine new file path in trash
            $page_trash_file_path = $page_trash_path . 'page-' . $post_slug . '.php';

            // Move file to trash
            if (rename($new_page_file_path, $page_trash_file_path)) {
                // Update meta to indicate no template URL
                update_post_meta($post_id, 'page_template_url', '');
            }
        }
    }
}
add_action('save_post_spbd_style', 'create_template_file');

/**
 * 
 * Pages.
 * 
*/

function set_page_files($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if ('spbd_style' !== get_post_type($post_id)) {
        return;
    }

    $selected_pages = isset($_POST['selected_pages']) ? array_map('intval', $_POST['selected_pages']) : array();
    update_post_meta($post_id, 'selected_pages', $selected_pages);

    $css_checkbox = isset($_POST['css_file_checkbox']) ? 1 : 0;
    $js_checkbox = isset($_POST['js_file_checkbox']) ? 1 : 0;
    $template_checkbox = isset($_POST['template_checkbox']) ? 1 : 0;

    update_post_meta($post_id, 'css_file_checkbox', $css_checkbox);
    update_post_meta($post_id, 'js_file_checkbox', $js_checkbox);
    update_post_meta($post_id, 'template_checkbox', $template_checkbox);

}

add_action('save_post_spbd_style', 'set_page_files');

function enqueue_custom_css_js_for_selected_pages() {
    if (is_page()) {
        global $post;
        $page_id = $post->ID;

        $args = array(
            'post_type' => 'spbd_style',
            'posts_per_page' => -1
        );

        $posts = get_posts($args);

        foreach ($posts as $spbd_post) {
            $selected_pages = get_post_meta($spbd_post->ID, 'selected_pages', true);

            if (!empty($selected_pages) && in_array($page_id, $selected_pages)) {
                $post_slug = $spbd_post->post_name;
                $css_checkbox = get_post_meta($spbd_post->ID, 'css_file_checkbox', true);
                $js_checkbox = get_post_meta($spbd_post->ID, 'js_file_checkbox', true);

                if ($css_checkbox) {
                    $css_file = get_stylesheet_directory_uri() . '/css/' . $post_slug . '.css';
                    wp_enqueue_style('custom-css-' . $spbd_post->ID, $css_file, array(), null);
                }

                if ($js_checkbox) {
                    $js_file = get_stylesheet_directory_uri() . '/js/' . $post_slug . '.js';
                    wp_enqueue_script('custom-js-' . $spbd_post->ID, $js_file, array(), null, true);
                }
            }
        }
    }
}
add_action('wp_enqueue_scripts', 'enqueue_custom_css_js_for_selected_pages');


/**
 * 
 * Merge.
 * 
*/
/*

TO BE REMOVED
//Merge Template & CSS
function merge_css_file() {
    $template_slug = get_page_template_slug(get_the_ID());
    if ( is_page_template( $template_slug ) ) {
        $find = array("page-",".php");
        $replace = array("","");
        $file_name = str_replace($find,$replace,$template_slug);
        wp_enqueue_style('spbd-'.$file_name, get_stylesheet_directory_uri() . "/css/" .$file_name. '.css', array(), '1.0', 'all');
    }
}
add_action('wp_head', 'merge_css_file', 50, 3);

// Merge Template & JS
function merge_js_file() {
    if (is_page()) {
        $template_slug = get_page_template_slug(get_the_ID());
        if ($template_slug) {
            $find = array("page-", ".php");
            $replace = array("", "");
            $file_name = str_replace($find, $replace, $template_slug);

            $post = get_page_by_path($file_name, OBJECT, 'spbd_style');
            if ($post) {
                $js_file_checkbox = get_post_meta($post->ID, 'js_file_checkbox', true);

                if ($js_file_checkbox) {
                    wp_enqueue_script('spbd-' . $file_name, get_stylesheet_directory_uri() . "/js/" . $file_name . '.js', array(), '1.0', true);
                }
            }
        }
    }
}
add_action('wp_enqueue_scripts', 'merge_js_file');
*/
// Add Global CSS
function add_global_css_file() {
    $args = array(
        'post_type' => 'spbd_style',
        'tax_query' => array(
            array(
                'taxonomy' => 'global_style',
                'field'    => 'slug',
                'terms'    => 'is_global',
            ),
        ),
    );

    $query = new WP_Query($args);
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $file_name = get_post_field('post_name', get_the_ID());

            // Check if the stylesheet is already enqueued
            if (!wp_style_is($file_name, 'enqueued')) {
                wp_enqueue_style($file_name, get_stylesheet_directory_uri() . "/css/" . $file_name . '.css', array(), '1.0', 'all');
            }
        }
        wp_reset_postdata();
    }
}
add_action('wp_head', 'add_global_css_file', 20);

// Add Global JS
function add_global_js_file() {
    $args = array(
        'post_type' => 'spbd_style',
        'tax_query' => array(
            array(
                'taxonomy' => 'global_style',
                'field'    => 'slug',
                'terms'    => 'is_global',
            ),
        ),
    );

    $query = new WP_Query($args);
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $file_name = get_post_field('post_name', get_the_ID());

            // Check if the script is already enqueued
            if (!wp_script_is($file_name, 'enqueued')) {
                wp_enqueue_script($file_name, get_stylesheet_directory_uri() . "/js/" . $file_name . '.js', array(), '1.0', true);
            }
        }
        wp_reset_postdata();
    }
}
add_action('wp_footer', 'add_global_js_file', 10);

/**
 * 
 * Remove.
 * 
*/

// Remove Template files
function remove_deleted_files() {
    $args = array(
        'post_type' => 'spbd_style',
        'post_status'    => 'trash',
        'posts_per_page' => -1, 
    );    
    $query = new WP_Query($args);    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $trashed_file_name = get_post_field('post_name', get_the_ID()); 

            if($trashed_file_name){    
                $file_name =  str_replace('__trashed','',$trashed_file_name);                
                $trash_folder = get_stylesheet_directory() . '/trash/';
                $trash_css_folder = get_stylesheet_directory() . '/css/trash/';
                $trash_js_folder = get_stylesheet_directory() . '/js/trash/';

                // create trash folders
                if (!file_exists($trash_folder)) {
                    mkdir($trash_folder);
                }
                if (!file_exists($trash_css_folder)) {
                    mkdir($trash_css_folder);
                }
                if (!file_exists($trash_js_folder)) {
                    mkdir($trash_js_folder);
                }

                // move template files
                $template_page = get_stylesheet_directory(). '/page-' . $file_name . '.php';
                if(file_exists($template_page)){
                    rename($template_page, $trash_folder.$file_name.'.php');
                }
                $template_css = get_stylesheet_directory() . "/css/" .$file_name. '.css';
                if(file_exists($template_css)){
                    rename($template_css, $trash_css_folder.$file_name.'.css');
                }
                $template_js = get_stylesheet_directory() . "/js/" .$file_name. '.js';
                if(file_exists($template_js)){
                    rename($template_js, $trash_js_folder.$file_name.'.js');
                }
            }             
        }
        wp_reset_postdata();
    } 
}
add_action('admin_menu', 'remove_deleted_files', 10, 3);
