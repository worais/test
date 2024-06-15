<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<h3>Configs:</h3>
<form id="form">
    <?php
    $configs = apply_filters('get_user_config', USER_CONFIGS);
    foreach(USER_CONFIGS as $key => $default_value){
    ?>
        <p class="form-row form-row-wide">
            <label><?php echo esc_html(sanitize_text_field($key)); ?>
            <span class="woocommerce-input-wrapper">
                <input type="text" class="input-text" 
                name="<?php echo esc_html(sanitize_text_field($key)); ?>" 
                value="<?php echo esc_html(sanitize_text_field($configs[$key])); ?>">
            </span>
            </label>
        </p>
    <?php } ?>
    <div id="result"></div>
    <?php wp_nonce_field('set_user_config'); ?>
    <button type="submit" class="button" id="btn-save">Save</button>
</form>
<script>
var ajaxurl = "<?php echo esc_html( sanitize_text_field( admin_url( 'admin-ajax.php' ) ) ); ?>";

jQuery('#form').submit(function(e){
    const $btn = jQuery('#btn-save');
    const data = { action: 'set_user_config' };
    jQuery('#form').find('input, textarea, select').each(function(x, field) {
        if(field.type == 'checkbox'){
            data[field.name] = (field.checked)? 1 : 0;
        } else {
            data[field.name] = field.value;
        }
    });

    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data,                                         
        beforeSend:function() {
            $btn.addClass('loading');
            $btn.removeClass('error');
        },                
        success:function(data) {
            jQuery('#result').html(data);
        },   
        error:function(xhr, ajaxOptions, thrownError) {
            jQuery('#result').html(`<div class='woocommerce-error'>${thrownError}</div>`);
        },
        complete:function() {
            $btn.removeClass('loading');
        },                                                             
    })   
    
    e.preventDefault();
})
</script>