<?php
$is_user_logged_in = is_user_logged_in();
$booked_current_user = $is_user_logged_in ? wp_get_current_user() : false;
$user_id = $booked_current_user->ID;

$guest_booking = get_option( 'booked_booking_type', 'registered' ) === 'guest';
$new_appointment_default = get_option('booked_new_appointment_default','draft');

$customer_type = 'current';
if ( ! $is_user_logged_in ) {
	$customer_type = 'new';

	if ( $guest_booking ) {
		$customer_type = 'guest';
	}
}

// check the limit
$reached_limit = false;
$will_reached_limit = false;
$appointment_limit = get_option( 'booked_appointment_limit' );
if ( $is_user_logged_in && $appointment_limit ) {
	$upcoming_user_appointments = booked_user_appointments( $booked_current_user->ID, true );
	$reached_limit = $upcoming_user_appointments >= $appointment_limit;

	// check the reached limit when there are more than one appointment to book
	// in some cases the limit might be reached after booking too many appointments at a time
	if ( $total_appts > 1 ) {
		$will_reached_limit = ( $upcoming_user_appointments + $total_appts ) >= $appointment_limit;
	}
}
?>

<?php // Not logged in and guest booking is disabled ?>
<?php if ( ! $is_user_logged_in && ! $guest_booking ): ?>

	<form name="customerChoices" action="" id="customerChoices" class="bookedClearFix"<?php echo ( !get_option('users_can_register') ? ' style="display:none;"' : '' ); ?>>

		<?php if ( get_option('users_can_register') ): ?>
			<div class="field">
				<span class="checkbox-radio-block">
					<input data-condition="customer_choice" type="radio" name="customer_choice[]" id="customer_new" value="new" checked="checked">
					<label for="customer_new"><?php esc_html_e('New customer','booked'); ?></label>
				</span>
			</div>
		<?php endif; ?>

		<div class="field">
			<span class="checkbox-radio-block">
				<input data-condition="customer_choice" type="radio" name="customer_choice[]" id="customer_current" value="current"<?php echo ( !get_option('users_can_register') ? ' checked="checked"' : '' ); ?>>
				<label for="customer_current"><?php esc_html_e('Current customer','booked'); ?></label>
			</span>
		</div>
	</form>

	<div class="condition-block customer_choice<?php echo ( !get_option('users_can_register') && !is_user_logged_in() ? ' default' : '' ); ?>" id="condition-current">

		<?php
		$tmp_bookings = $bookings;
		$first_booking = array_shift( $tmp_bookings );
		$first_booking = ! empty($first_booking) ? $first_booking[0] : array( 'date' => '', 'title' => '', 'timeslot' => '', 'calendar_id' => '' );
		?>
		<form id="ajaxlogin" action="" method="post" data-date="<?php echo $first_booking['date']; ?>" data-title="<?php echo $first_booking['title']; ?>" data-timeslot="<?php echo $first_booking['timeslot']; ?>" data-calendar-id="<?php echo $first_booking['calendar_id']; ?>">
			<div class="cf-block">

				<?php include(QUICKCAL_AJAX_INCLUDES_DIR . 'front/appointment-form/form-fields-login.php'); ?>

				<input type="hidden" name="action" value="booked_ajax_login">
				<?php wp_nonce_field( 'ajax_login_nonce', 'security' ); ?>

				<div class="field">
					<p class="status"></p>
				</div>

				<?php if ( !is_multisite() ): ?>
				<a href="#" class="booked-forgot-password"><?php esc_html_e( 'I forgot my password.', 'booked' ); ?></a>
				<?php endif; ?>

			</div>

			<div class="field">
				<input name="submit" type="submit" class="button button-primary" value="<?php esc_html_e('Sign in', 'booked') ?>">
				<button class="cancel button"><?php esc_html_e('Cancel','booked'); ?></button>
			</div>
		</form>

		<?php if ( !is_multisite() ): ?>
			<form id="ajaxforgot" action="" method="post">
				<div class="cf-block" style="margin:0 0 5px;">

					<?php include(QUICKCAL_AJAX_INCLUDES_DIR . 'front/appointment-form/form-fields-forgot.php'); ?>

					<input type="hidden" name="action" value="booked_ajax_forgot">
					<?php wp_nonce_field( 'ajax_forgot_nonce', 'security' ); ?>

					<div class="field">
						<p class="status"></p>
					</div>

				</div>

				<div class="field">
					<input name="submit_forgot" type="submit" class="button button-primary" value="<?php esc_html_e('Reset Password', 'booked') ?>">
					<button class="booked-forgot-goback button"><?php esc_html_e('Go Back','booked'); ?></button>
				</div>
			</form>
		<?php endif; ?>

	</div>

<?php endif ?>

<?php // The booking form ?>

<?php
	// WordPressの環境で実行されていることを確認
	if (!defined('ABSPATH')) exit;

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if (isset($_POST['action']) && $_POST['action'] === 'booked_add_appt') {
			if (!isset($_POST['appointment_form_nonce']) || !wp_verify_nonce($_POST['appointment_form_nonce'], 'booked_appointment_form')) {
				die('セキュリティチェックに失敗しました');
			}

			// Proceed with form processing (e.g., update_user_meta)

			if ($user_id) {
				$fields_to_update = ['first_name', 'last_name', 'billing_first_name_kana', 'billing_last_name_kana', 'billing_phone'];

				foreach ($fields_to_update as $field) {
					if (isset($_POST[$field])) {
						update_user_meta($user_id, $field, sanitize_text_field($_POST[$field]));
					}
				}
			}
		}
	}
    // カスタムフィールドの定義
    $custom_fields = [
        ['name' => 'last_name', 'value' => '姓', 'required' => true],
        ['name' => 'first_name', 'value' => '名', 'required' => true],
        ['name' => 'billing_last_name_kana', 'value' => '姓（フリガナ）', 'required' => true],
        ['name' => 'billing_first_name_kana', 'value' => '名（フリガナ）', 'required' => true],
        ['name' => 'billing_phone', 'value' => '電話番号', 'required' => true],
    ];
?>

<div class="condition-block customer_choice<?php echo ( $guest_booking || get_option('users_can_register') && !$is_user_logged_in || $is_user_logged_in ? ' default' : '' ); ?>" id="condition-new">
	
	<?php
	// Get the current user's ID
	$user_id = get_current_user_id();

	// Check if the required user meta fields are set
	$last_name = get_user_meta($user_id, 'last_name', true);
	$first_name = get_user_meta($user_id, 'first_name', true);
	$billing_last_name_kana = get_user_meta($user_id, 'billing_last_name_kana', true);
	$billing_first_name_kana = get_user_meta($user_id, 'billing_first_name_kana', true);
	$billing_phone = get_user_meta($user_id, 'billing_phone', true);

	// Check if all required fields are present and not empty
	if (empty($last_name) || empty($first_name) || empty($billing_last_name_kana) || empty($billing_first_name_kana) || empty($billing_phone)) { ?>
		<div class="field">
		<label class="field-label">個人情報を入力してください。<i class="required-asterisk fa-solid fa-asterisk"></i></label>
		</div>
		<?php
		echo do_shortcode('[personal_information_form]');
		?>
	<?php
	} else {?>
		<form action="" method="post" id="newAppointmentForm">
        <?php wp_nonce_field('booked_appointment_form', 'appointment_form_nonce'); ?>
        <input type="hidden" name="customer_type" value="<?php echo esc_attr($customer_type); ?>" />
        <input type="hidden" name="action" value="booked_add_appt" />

        <?php if ( $is_user_logged_in ): ?>
            <input type="hidden" name="user_id" value="<?php echo esc_attr($booked_current_user->ID); ?>" />
        <?php endif; ?>

        <?php
        $error_message = '';

        // ユーザーが制限に達した場合
        if ( $reached_limit ) {
            $error_message = sprintf(_n("申し訳ありませんが、予約制限に達しました。各ユーザーは一度に%d件の予約しかできません。","申し訳ありませんが、予約制限に達しました。各ユーザーは一度に%d件の予約しかできません。", $appointment_limit, "booked" ), $appointment_limit);
        }

        // ユーザーがまだ制限に達していないが、次の予約で制限を超える場合
        if ( $will_reached_limit && ! $reached_limit ) {
            $error_message = sprintf(esc_html__("申し訳ありませんが、一度に予約できる数を超えようとしています。各ユーザーは一度に%d件までしか予約できません。", "booked" ), $appointment_limit);
        }

        // エラーメッセージがある場合、表示
        if ( $error_message ) {
            echo wpautop( $error_message );
        }

        // エラーがなく、ユーザーがログインしている場合
        if ( $is_user_logged_in && ! $error_message ) {
			$is_reversed = ( 'ja' ===  get_locale() ) ? true : true;
            $msg = sprintf( _n( '%sさんの予約をリクエストしようとしています。', '%sさんの予約をリクエストしようとしています。', $total_appts, 'booked' ), '<em>' . quickcal_get_name( $booked_current_user->ID, 'full', $is_reversed ) . '</em>' ) . ' ' . _n( ':', '', $total_appts, 'booked' );
            echo wpautop( $msg );
        }

        // エラーがなく、ユーザーがログインしていない場合
        if ( ! $is_user_logged_in && ! $error_message ) {
            $msg = _n( '以下のご予約でお間違いがないかご確認ください:', '以下のご予約でお間違いがないかご確認ください:', $total_appts, 'booked' );
            echo wpautop( $msg );
        }

        // エラーがない場合、予約をリスト表示
        if ( ! $error_message ) {
            // カレンダーと予約のリストを含める
            include( QUICKCAL_AJAX_INCLUDES_DIR . 'front/appointment-form/bookings.php' );
        }?>

        <?php if ( ! $is_user_logged_in && ! $error_message && class_exists('ReallySimpleCaptcha') ) : ?>
            <?php
            $rsc_url = WP_PLUGIN_URL . '/really-simple-captcha/';
            $captcha = new ReallySimpleCaptcha();
            $captcha->bg = array(245,245,245);
            $captcha->fg = array(150,150,150);
            $captcha_word = $captcha->generate_random_word(); //ランダムな文字列を生成
            $captcha_prefix = mt_rand(); //ランダムな数字
            $captcha_image = $captcha->generate_image($captcha_prefix, $captcha_word); //画像ファイルを生成。ファイル名を返す
            $captcha_file = rtrim(get_bloginfo('wpurl'), '/') . '/wp-content/plugins/really-simple-captcha/tmp/' . $captcha_image; //キャプチャ画像の絶対URLを構築
            ?>
            <p class="captcha">
                <label for="captcha_code"><?php esc_html_e('以下のテキストを入力してください:','booked'); ?></label>
                <img class="captcha-image" src="<?php echo esc_url($rsc_url . 'tmp/' . $captcha_image); ?>">
            </p>

            <div class="field">
                <input type="text" name="captcha_code" class="textfield large" value="" tabindex="104" />
                <input type="hidden" name="captcha_word" value="<?php echo esc_attr($captcha_word); ?>" />
            </div>

            <br>
        <?php endif; ?>

        <div class="field">
            <p class="status"></p>
        </div>

        <div class="field">
            <?php if ( $error_message ): ?>
                <button class="cancel button"><?php esc_html_e('了解','booked'); ?></button>
            <?php else: ?>
                <input type="submit" id="submit-request-appointment" class="button button-primary" value="<?php echo ( $new_appointment_default == 'draft' ? esc_html( _n( '予約をリクエスト', '予約をリクエスト', $total_appts, 'booked' ) ) : esc_html( _n( '予約する', '予約する', $total_appts, 'booked' ) ) ); ?>">
                <button class="cancel button"><?php esc_html_e('キャンセル','booked'); ?></button>
            <?php endif; ?>
        </div>
    </form>
	<?php
	}
	?>
    
</div>