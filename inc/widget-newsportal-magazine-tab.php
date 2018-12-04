<?php
/**
 * Newsportal Magazine : Tab widget
 *
 * Widget show the tab widget
 *
 * @package Theme Egg
 * @subpackage Newsportal Magazine
 * @since 1.0.0
 */
add_action('widgets_init', 'newsportal_magazine_register_tab_widget');

function newsportal_magazine_register_tab_widget()
{
    register_widget('Newsportal_Magazine_Tab');
}

if (!class_exists('Newsportal_Magazine_Tab')):

    class Newsportal_Magazine_Tab extends WP_widget
    {

        /**
         * Register widget with WordPress.
         */
        public function __construct()
        {
            $widget_ops = array(
                'classname' => 'eggnews_tab',
                'description' => __('Displays the latest posts, popular posts and the recent comments in tab.', 'newsportal-magazine')
            );
            parent::__construct('eggnews_tab', __('Tab Widget', 'newsportal-magazine'), $widget_ops);
        }

        /**
         * Helper function that holds widget fields
         * Array is used in update and form functions
         */
        private function widget_fields()
        {

            $fields = array(
                'banner_title' => array(
                    'eggnews_widgets_name' => 'number_of_posts_to_display',
                    'eggnews_widgets_title' => __('Number of posts to display.', 'newsportal-magazine'),
                    'eggnews_widgets_field_type' => 'number',
                    'eggnews_widgets_default' => 5,
                ),
                'banner_rel' => array(
                    'eggnews_widgets_name' => 'show_post_by_view_count',
                    'eggnews_widgets_title' => __('Show popular post by view count.', 'newsportal-magazine'),
                    'eggnews_widgets_field_type' => 'checkbox'
                )
            );

            return $fields;
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget($args, $instance)
        {
            extract($args);
            if (empty($instance)) {
                return;
            }
            if (is_active_widget(false, false, $this->id_base) || is_customize_preview()) {
                wp_enqueue_script('eggnews-easy-tabs');
            }
            $number_of_posts_to_display = empty($instance['number_of_posts_to_display']) ? 5 : $instance['number_of_posts_to_display'];
            $show_post_by_view_count = empty($instance['show_post_by_view_count']) ? 'false' : 'true';

            echo $before_widget;
            ?>
            <div class="tab-widget">
                <ul class="widget-tabs">
                    <li class="tabs popular-tabs"><a
                                href="#popular"><?php _e('<i class="fa fa-star"></i>Popular', 'newsportal-magazine'); ?></a>
                    </li>
                    <li class="tabs recent-tabs"><a
                                href="#recent"><?php _e('<i class="fa fa-history"></i>Recent', 'newsportal-magazine'); ?></a>
                    </li>
                    <li class="tabs comment-tabs"><a
                                href="#comment"><?php _e('<i class="fa fa-comment"></i>Comment', 'newsportal-magazine'); ?></a>
                    </li>
                </ul>

                <div class="tabbed-widget-popular" id="popular">
                    <?php
                    global $post;

                    $args = array();
                    if ($show_post_by_view_count == 'false') {
                        $args = array(
                            'posts_per_page' => $number_of_posts_to_display,
                            'post_type' => 'post',
                            'ignore_sticky_posts' => true,
                            'orderby' => 'comment_count',
                            'no_found_rows' => true
                        );
                    } else {
                        $args = array(
                            'posts_per_page' => $number_of_posts_to_display,
                            'post_type' => 'post',
                            'ignore_sticky_posts' => true,
                            'meta_key' => 'total_number_of_views',
                            'orderby' => 'meta_value_num',
                            'order' => 'DESC',
                            'no_found_rows' => true
                        );
                    }

                    $get_featured_posts = new WP_Query($args);
                    ?>
                    <?php $featured = 'eggnews-pro-tab-thumbnail'; ?>
                    <?php
                    $i = 1;
                    while ($get_featured_posts->have_posts()):$get_featured_posts->the_post();
                        ?>
                        <div class="single-article clearfix">
                            <?php
                            if (has_post_thumbnail()) {
                                $image = '';
                                $title_attribute = get_the_title($post->ID);
                                $image_attr = array(
                                    'title' => esc_attr($title_attribute),
                                    'alt' => esc_attr($title_attribute)
                                );
                                $image .= '<figure class="tabbed-images">';
                                $image .= '<a href="' . get_permalink() . '" title="' . the_title('', '', false) . '">';
                                $image .= get_the_post_thumbnail(null, $featured) . '</a>';
                                $image .= '</figure>';
                                echo $image;
                            }
                            ?>
                            <div class="article-content">
                                <h3 class="entry-title">
                                    <a href="<?php the_permalink(); ?>"
                                       title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <div class="below-entry-meta">
                                    <?php
                                    $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
                                    $time_string = sprintf($time_string, esc_attr(get_the_date('c')), esc_html(get_the_date())
                                    );
                                    printf(__('<span class="posted-on"><a href="%1$s" title="%2$s" rel="bookmark">%3$s</a></span>', 'newsportal-magazine'), esc_url(get_permalink()), esc_attr(get_the_time()), $time_string
                                    );
                                    ?>
                                    &nbsp;<span class="byline">
                                        <span class="author vcard">
                                            <a
                                                    class="url fn n"
                                                    href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>"
                                                    title="<?php echo get_the_author(); ?>"><?php echo esc_html(get_the_author()); ?></a>
                                        </span>
                                    </span>&nbsp;
                                    <span class="comments"><i
                                                class="fa fa-comment"></i> <?php comments_popup_link(__('No Comments', 'newsportal-magazine'), __('1 Comment', 'newsportal-magazine'), __('% Comments', 'newsportal-magazine')); ?></span>
                                </div>
                            </div>

                        </div>
                        <?php
                        $i++;
                    endwhile;
                    // Reset Post Data
                    wp_reset_query();
                    ?>
                </div>

                <div class="tabbed-widget-recent" id="recent">
                    <?php
                    global $post;

                    $get_featured_posts = new WP_Query(array(
                        'posts_per_page' => $number_of_posts_to_display,
                        'post_type' => 'post',
                        'ignore_sticky_posts' => true,
                        'no_found_rows' => true
                    ));
                    ?>
                    <?php $featured = 'newsportal-magazine-tab-thumbnail'; ?>
                    <?php
                    $i = 1;
                    while ($get_featured_posts->have_posts()):$get_featured_posts->the_post();
                        ?>
                        <div class="single-article clearfix">
                            <?php
                            if (has_post_thumbnail()) {
                                $image = '';
                                $title_attribute = get_the_title($post->ID);
                                $image .= '<figure class="tabbed-images">';
                                $image .= '<a href="' . get_permalink() . '" title="' . the_title('', '', false) . '">';
                                $image .= get_the_post_thumbnail($post->ID, $featured, array(
                                        'title' => esc_attr($title_attribute),
                                        'alt' => esc_attr($title_attribute)
                                    )) . '</a>';
                                $image .= '</figure>';
                                echo $image;
                            }
                            ?>
                            <div class="article-content">
                                <h3 class="entry-title">
                                    <a href="<?php the_permalink(); ?>"
                                       title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <div class="below-entry-meta">
                                    <?php
                                    $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
                                    $time_string = sprintf($time_string, esc_attr(get_the_date('c')), esc_html(get_the_date())
                                    );
                                    printf(__('<span class="posted-on"><a href="%1$s" title="%2$s" rel="bookmark">%3$s</a></span>', 'newsportal-magazine'), esc_url(get_permalink()), esc_attr(get_the_time()), $time_string
                                    );
                                    ?>
                                    <span class="byline"><span class="author vcard"></i><a
                                                    class="url fn n"
                                                    href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>"
                                                    title="<?php echo get_the_author(); ?>"><?php echo esc_html(get_the_author()); ?></a></span></span>
                                </div>
                            </div>

                        </div>
                        <?php
                        $i++;
                    endwhile;
                    // Reset Post Data
                    wp_reset_query();
                    ?>
                </div>

                <div class="tabbed-widget-comment" id="comment">
                    <?php
                    $comments_query = new WP_Comment_Query();
                    $comments = $comments_query->query(array(
                        'number' => $number_of_posts_to_display,
                        'status' => 'approve'
                    ));
                    $commented = '';
                    if ($comments) : foreach ($comments as $comment) :
                        $commented .= '<li class="tabbed-comment-widget">';
                        $commented .= '<a class="author-thumbnail" href="' . get_permalink($comment->comment_post_ID) . '#comment-' . $comment->comment_ID . '">';
                        $commented .= get_avatar($comment->comment_author_email, '50');
                        $commented .= '</a>';
                        $commented .= '<div class="comment-wraper">';
                        $commented .= '<a class="author-nicename" href="' . get_permalink($comment->comment_post_ID) . '#comment-' . $comment->comment_ID . '">';
                        $commented .= get_comment_author($comment->comment_ID);
                        $commented .= '</a>' . ' ' . __('says:', 'newsportal-magazine');
                        $commented .= '<p class="commented">' . strip_tags(substr(apply_filters('get_comment_text', $comment->comment_content), 0, '50')) . '...</p>';
                        $commented .= '</div>';
                        $commented .= '</li>';
                    endforeach;
                    else :
                        $commented .= __('No comments', 'newsportal-magazine');
                    endif;
                    echo $commented;
                    ?>
                </div>

            </div>
            <?php
            echo $after_widget;
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @see     WP_Widget::update()
         *
         * @param   array $new_instance Values just sent to be saved.
         * @param   array $old_instance Previously saved values from database.
         *
         * @uses    eggnews_widgets_updated_field_value()     defined in eggnews-widget-fields.php
         *
         * @return  array Updated safe values to be saved.
         */
        public function update($new_instance, $old_instance)
        {
            $instance = $old_instance;

            $widget_fields = $this->widget_fields();

            // Loop through fields
            foreach ($widget_fields as $widget_field) {

                extract($widget_field);
                // Use helper function to get updated field values
                if (isset($new_instance[$eggnews_widgets_name])) {
                    $instance[$eggnews_widgets_name] = eggnews_widgets_updated_field_value($widget_field, $new_instance[$eggnews_widgets_name]);
                } else {
                    $instance[$eggnews_widgets_name] = eggnews_widgets_updated_field_value($widget_field, null);
                }
            }

            return $instance;
        }

        /**
         * Back-end widget form.
         *
         * @see     WP_Widget::form()
         *
         * @param   array $instance Previously saved values from database.
         *
         * @uses    eggnews_widgets_show_widget_field()       defined in eggnews-widget-fields.php
         */
        public function form($instance)
        {
            $widget_fields = $this->widget_fields();

            // Loop through fields
            foreach ($widget_fields as $widget_field) {

                // Make array elements available as variables
                extract($widget_field);
                $eggnews_widgets_field_value = !empty($instance[$eggnews_widgets_name]) ? wp_kses_post($instance[$eggnews_widgets_name]) : '';
                eggnews_widgets_show_widget_field($this, $widget_field, $eggnews_widgets_field_value);
            }
        }

    }
endif;