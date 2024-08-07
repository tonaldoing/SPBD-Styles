<?php
// Data Custom FIelds
function create_template_and_css_file($post) {
    $current_screen = get_current_screen();

    if ($current_screen && $current_screen->post_type === 'spbd_style') {

        $page_template_url = get_post_meta($post->ID, 'page_template_url', true);
        $css_template_url = get_post_meta($post->ID, 'css_template_url', true);
        $js_template_url = get_post_meta($post->ID, 'js_template_url', true); 
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row">Page Template URL:</th>
                <td><?php echo esc_html($page_template_url); ?></td>
            </tr>
            <tr>
                <th scope="row">CSS Template URL:</th>
                <td><?php echo esc_html($css_template_url); ?></td>
            </tr>
            <tr>
                <th scope="row">JS Template URL:</th>
                <td><?php echo esc_html($js_template_url); ?></td>
            </tr>
        </table>
        <?php
    }
}
add_action('edit_form_after_title', 'create_template_and_css_file');