<?php
if (! defined('ABSPATH') ) { exit;
}

class User_Config extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'my-text',
            'User configs'
        );
        add_action(
            'widgets_init', function () {
                register_widget('User_Config');
            }
        );
    }

    public $args = array(
    'before_title'  => '<h4 class="widgettitle">',
    'after_title'   => '</h4>',
    'before_widget' => '<div class="widget-wrap">',
    'after_widget'  => '</div></div>',
    );

    public function widget( $args, $instance )
    {
        echo wp_kses($args['before_widget'], ['div' => []]);
        if (! empty($instance['title']) ) {
            echo wp_kses($args['before_title'], ['h4' => []]);
            echo esc_html(sanitize_text_field(apply_filters('widget_title', $instance['title'])));
            echo wp_kses($args['after_title'], ['h4' => []]);
        }
        echo '<div class="textwidget">';
        echo esc_html(sanitize_text_field($instance['text']));
        echo '</div>';
        echo wp_kses($args['after_widget'], ['div' => []]);

        $configs = apply_filters('get_user_config', USER_CONFIGS);
        foreach(USER_CONFIGS as $key => $default_value){
            echo "<strong>".esc_html(sanitize_text_field($key))."</strong>: ".esc_html(sanitize_text_field($configs[$key]))."<br />";
        }
    }

    public function form( $instance )
    {
        $title = ! empty($instance['title']) ? $instance['title'] : '';
        $text  = ! empty($instance['text']) ? $instance['text'] : '';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php echo esc_html__('Title:', 'text_domain'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('Text')); ?>"><?php echo esc_html__('Text:', 'text_domain'); ?></label>
            <textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('text')); ?>" name="<?php echo esc_attr($this->get_field_name('text')); ?>" type="text" cols="30" rows="10"><?php echo esc_attr($text); ?></textarea>
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance )
    {
        $instance          = array();
        $instance['title'] = ( ! empty($new_instance['title']) ) ? wp_strip_all_tags($new_instance['title']) : '';
        $instance['text']  = ( ! empty($new_instance['text']) ) ? $new_instance['text'] : '';
        return $instance;
    }
}
$user_Config = new User_Config();