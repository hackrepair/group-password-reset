<?php
/**
 * Branded administrator summary email, matching Site Inspector.
 *
 * @package GroupPasswordReset
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gpr_notification_html( $entry, $note = '', $recipient_name = '' ) {
	$failed  = ! empty( $entry['summary']['failed'] );
	$heading = $failed ? __( 'Password reset finished with failures', 'group-password-reset' ) : __( 'Password reset run complete', 'group-password-reset' );
	$intro   = __( 'A site administrator has shared this password reset summary with you. The counts below cover the entire run.', 'group-password-reset' );
	$greet   = __( 'Hello,', 'group-password-reset' );
	if ( '' !== $recipient_name ) {
		/* translators: %s: recipient display name. */
		$greet = sprintf( __( 'Hello %s,', 'group-password-reset' ), $recipient_name );
	}
	ob_start();
	?>
<!doctype html>
<html lang="<?php echo esc_attr( get_bloginfo( 'language' ) ); ?>">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?php echo esc_html( gpr_email_subject( __( 'Password Reset Summary', 'group-password-reset' ) ) ); ?></title></head>
<body style="margin:0;padding:0;background:#f3eee9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#1d2327;">
<div style="display:none;max-height:0;overflow:hidden;"><?php echo esc_html( $heading . '. ' . gpr_email_domain() ); ?></div>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3eee9;border-collapse:collapse;"><tr><td align="center" style="padding:24px 12px;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;">
<tr><td style="background:#2b170d;border-radius:8px 8px 0 0;padding:22px 24px;">
<div style="font-size:12px;font-weight:700;letter-spacing:1.5px;color:#ff9933;text-transform:uppercase;"><?php esc_html_e( "The Hack Repair Guy's", 'group-password-reset' ); ?></div>
<h1 style="font-size:26px;line-height:1.25;font-weight:700;color:#ffffff;margin:6px 0 10px;"><?php esc_html_e( 'Group Password Reset', 'group-password-reset' ); ?></h1>
<div style="font-size:14px;line-height:1.6;color:#e8d8cc;overflow-wrap:anywhere;"><?php echo esc_html( gpr_email_domain() ); ?><br><?php echo esc_html( wp_date( 'Y-m-d H:i T', $entry['time'] ) ); ?></div>
</td></tr>
<tr><td style="background:#ffffff;padding:24px;border:1px solid #ead9cc;border-top:0;">
<p style="font-size:15px;line-height:1.6;margin:0 0 14px;"><?php echo esc_html( $greet ); ?></p>
	<?php if ( '' !== trim( $note ) ) : ?>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px;"><tr><td style="background:#fff8f2;border-left:4px solid #b34700;padding:12px 14px;font-size:14px;line-height:1.6;overflow-wrap:anywhere;">
<strong style="display:block;color:#8f3a00;margin-bottom:6px;"><?php esc_html_e( 'A note from the sender', 'group-password-reset' ); ?></strong><?php echo nl2br( esc_html( $note ) ); ?>
</td></tr></table>
	<?php endif; ?>
<h2 style="font-size:21px;line-height:1.35;margin:0 0 10px;color:#2b170d;"><?php echo esc_html( $heading ); ?></h2>
<p style="font-size:15px;line-height:1.6;margin:0 0 20px;"><?php echo esc_html( $intro ); ?></p>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #ead9cc;border-left:4px solid #b34700;border-collapse:collapse;margin-bottom:20px;"><tr><td style="padding:14px 16px;">
<h3 style="font-size:16px;color:#8f3a00;margin:0 0 12px;"><?php esc_html_e( 'Results at a glance', 'group-password-reset' ); ?></h3>
<p style="font-size:14px;line-height:1.6;margin:0 0 12px;"><strong><?php esc_html_e( 'Scope:', 'group-password-reset' ); ?></strong> <?php echo esc_html( $entry['scope_label'] ); ?></p>
<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
<caption style="text-align:left;font-weight:700;padding:0 0 8px;"><?php esc_html_e( 'Password reset results', 'group-password-reset' ); ?></caption>
<tbody>
	<?php
	$labels = array(
		'total'     => __( 'Matched users', 'group-password-reset' ),
		'processed' => __( 'Processed', 'group-password-reset' ),
		'success'   => __( 'Success', 'group-password-reset' ),
		'failed'    => __( 'Failed', 'group-password-reset' ),
		'skipped'   => __( 'Skipped', 'group-password-reset' ),
	);
	foreach ( $labels as $key => $label ) :
		?>
<tr><th scope="row" align="left" style="padding:9px 10px;border-bottom:1px solid #f0e6de;background:#faf6f2;font-weight:400;"><?php echo esc_html( $label ); ?></th><td align="right" style="padding:9px 10px;border-bottom:1px solid #f0e6de;font-weight:700;"><?php echo esc_html( (string) (int) $entry['summary'][ $key ] ); ?></td></tr>
	<?php endforeach; ?>
<tr><th scope="row" align="left" style="padding:9px 10px;background:#faf6f2;font-weight:400;"><?php esc_html_e( 'Excluded accounts', 'group-password-reset' ); ?></th><td align="right" style="padding:9px 10px;font-weight:700;"><?php echo esc_html( (string) (int) $entry['excluded_count'] ); ?></td></tr>
</tbody></table>
</td></tr></table>
<h3 style="font-size:16px;margin:0 0 8px;color:#8f3a00;"><?php esc_html_e( 'User notifications', 'group-password-reset' ); ?></h3>
<p style="font-size:14px;line-height:1.6;margin:0 0 18px;"><?php echo esc_html( $entry['notification_label'] ); ?></p>
	<?php if ( $failed ) : ?>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px;"><tr><td style="background:#fff8e5;border-left:4px solid #996800;padding:12px 14px;font-size:14px;line-height:1.6;">
<strong><?php esc_html_e( 'Follow-up needed', 'group-password-reset' ); ?></strong><br><?php esc_html_e( 'Contact the administrator who ran the reset about the failed results. A password may have changed even when its notification email could not be sent.', 'group-password-reset' ); ?>
</td></tr></table>
	<?php endif; ?>
<table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 16px;"><tr><td bgcolor="#b34700" style="border-radius:4px;"><a href="<?php echo esc_url( admin_url( 'tools.php?page=group-password-reset' ) ); ?>" style="display:inline-block;padding:13px 18px;color:#ffffff;font-size:14px;font-weight:700;text-decoration:none;"><?php esc_html_e( 'Open Group Password Reset', 'group-password-reset' ); ?></a></td></tr></table>
<p style="font-size:12px;line-height:1.6;margin:0 0 18px;color:#646970;"><?php esc_html_e( 'Administrator sign-in required. This opens the plugin dashboard; detailed results are not stored as a report history.', 'group-password-reset' ); ?></p>
<p style="font-size:14px;line-height:1.6;margin:0;"><?php esc_html_e( 'If you did not expect this activity, contact your site administrator promptly.', 'group-password-reset' ); ?></p>
</td></tr>
<tr><td style="padding:18px 24px;font-size:12px;line-height:1.6;color:#646970;">
<strong><?php esc_html_e( "The Hack Repair Guy's Group Password Reset", 'group-password-reset' ); ?></strong><br>
<a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:#8f3a00;overflow-wrap:anywhere;"><?php echo esc_html( home_url( '/' ) ); ?></a><br>
	<?php esc_html_e( 'Email acceptance does not confirm delivery. This summary excludes passwords, reset links, and individual account details.', 'group-password-reset' ); ?>
</td></tr>
</table></td></tr></table>
</body></html>
	<?php
	return ob_get_clean();
}
