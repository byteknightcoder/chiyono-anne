<?php

add_action('template_redirect', 'fsr_save_session');
function fsr_save_session() {
    if (is_page('family-sale-request')) {
        // Store data in transient for 24 hours
        set_transient('is_family_sale_request', 'yes', 24 * HOUR_IN_SECONDS);
    } else {
        // Delete the transient when leaving the family sale request page
        delete_transient('is_family_sale_request');
    }
}

add_shortcode('fsr_login_form', 'fsr_login_form');
function fsr_login_form() {
    ob_start();
    ?>
    <div class="auth__container set-division max-width--med-tab fsr_login_form">
        <?php do_action('woocommerce_before_customer_login_form'); ?>

        <?php if (get_option('woocommerce_enable_myaccount_registration') === 'yes') : ?>

            <div class="row row-sm--fluid flex-justify-between" id="customer_login">

                <div class="col-md-5 col-xs-12 col-sm--fluid auth__section">

                <?php endif; ?>

                <h2 class="auth__title heading heading--small"><?php esc_html_e('Sign in', 'zoa'); ?></h2>
                <p class="form__description p4"><?php esc_html_e('Welcome back! If you already have an account with us, please sign in.', 'zoa'); ?></p>

                <form class="woocommerce-form woocommerce-form-login login auth__form" method="post">

                    <?php do_action('woocommerce_login_form_start'); ?>
                    <?php
                        $default_username = '';
                        if (!empty($_POST['mobile/email'])) {
                            $default_username = esc_attr(wp_unslash($_POST['mobile/email']));
                        } elseif (!empty($_POST['username'])) {
                            $default_username = esc_attr(wp_unslash($_POST['username']));
                        }
                    ?>
                    <div class="form-row required">
                        <div class="field-wrapper">
                            <label class="form-row__label light-copy" for="username"><?php esc_html_e('Username or email address', 'woocommerce'); ?>&nbsp;<span class="required">*</span></label>
                            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo (!empty($default_username) ) ? $default_username : ''; ?>" /><?php // @codingStandardsIgnoreLine  ?>
                        </div></div>
                    <div class="form-row required">
                        <div class="field-wrapper">
                            <label class="form-row__label light-copy" for="password"><?php esc_html_e('Password', 'woocommerce'); ?>&nbsp;<span class="required">*</span></label>
                            <input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" />
                        </div></div>

                    <?php do_action('woocommerce_login_form'); ?>

                    <div class="form-row label-inline login-rememberme label-inline form-indent">
                        <div class="field-wrapper">
                            <?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>
                            <input class="input-checkbox input-control" name="rememberme" type="checkbox" id="rememberme" value="forever" /> 
                            <label class="form-row__inline-label control-label checkbox icon--tick">
                                <?php esc_html_e('Remember me', 'woocommerce'); ?>
                            </label>
                        </div>
                    </div>

                    <div class="form-row form-row-button">
                        <button type="submit" class="button button--primary button--full" name="login" value="<?php esc_attr_e('Log in', 'woocommerce'); ?>"><?php esc_html_e('Log in', 'woocommerce'); ?></button>
                    </div>
                    <div class="align--center"><a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="cta p6"><?php esc_html_e('Lost your password?', 'woocommerce'); ?></a></div>

                    <?php do_action('woocommerce_login_form_end'); ?>

                </form>

                <?php if (get_option('woocommerce_enable_myaccount_registration') === 'yes') : ?>

                </div>

                <div class="col-md-5 col-xs-12 col-sm--fluid auth__section">

                    <h2 class="auth__title heading heading--small"><?php esc_html_e('New Register', 'zoa'); ?></h2>
                    <p class="form__description p4"><?php esc_html_e('Please create your account here.', 'zoa'); ?></p>

                    <form method="post" class="woocommerce-form woocommerce-form-register register auth__form">

                        <?php do_action('woocommerce_register_form_start'); ?>

                        <?php if ('no' === get_option('woocommerce_registration_generate_username')) : ?>

                            <div class="form-row required">
                                <div class="field-wrapper">
                                    <label class="form-row__label light-copy" for="reg_username"><?php esc_html_e('Username', 'woocommerce'); ?>&nbsp;<span class="required">*</span></label>
                                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo (!empty($_POST['username']) ) ? esc_attr(wp_unslash($_POST['username'])) : ''; ?>" /><?php // @codingStandardsIgnoreLine  ?>
                                </div>
                            </div>

                        <?php endif; ?>

                        <div class="form-row required">
                            <div class="field-wrapper">
                                <label class="form-row__label light-copy" for="reg_email"><?php esc_html_e('Email address', 'woocommerce'); ?>&nbsp;<span class="required">*</span></label>
                                <input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo (!empty($_POST['email']) ) ? esc_attr(wp_unslash($_POST['email'])) : ''; ?>" /><?php // @codingStandardsIgnoreLine  ?>
                            </div>
                        </div>

                        <?php if ('no' === get_option('woocommerce_registration_generate_password')) : ?>

                            <div class="form-row required">
                                <div class="field-wrapper">
                                    <label class="form-row__label light-copy" for="reg_password"><?php esc_html_e('Password', 'woocommerce'); ?>&nbsp;<span class="required">*</span></label>
                                    <input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" />
                                </div>
                            </div>

                        <?php endif; ?>

                        <?php do_action('woocommerce_register_form'); ?>

                        <div class="form-row form-row-button">
                            <?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>
                            <button type="submit" class="button button--primary button--full" name="register" value="<?php esc_attr_e('Register', 'woocommerce'); ?>"><?php esc_html_e('Create Account', 'zoa'); ?></button>
                        </div>

                        <?php do_action('woocommerce_register_form_end'); ?>

                    </form>

                </div>

            </div>
        <?php endif; ?>

        <?php do_action('woocommerce_after_customer_login_form'); ?>
    </div><!--/auth__container-->
    <?php
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
}

add_filter('the_content', 'fsr_filter_the_content_in_the_main_loop', 1);
function fsr_filter_the_content_in_the_main_loop($content) {
    if (is_page('family-sale-request')) {
        $current_user_id = get_current_user_id();
        if (isset($current_user_id) && $current_user_id > 0 && is_user_logged_in()) {
            return $content;
        } else {
            return do_shortcode($content);
        }
    }

    return $content;
}
