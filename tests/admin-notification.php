<?php
/** Isolated notification tests: mail is captured, never delivered. */
define('ABSPATH', __DIR__);
define('GPR_RESULTS_TRANSIENT_TTL', 900);
define('MINUTE_IN_SECONDS', 60);
$state=[]; $mail=[]; $allowed=true; $uid=7;
function __($s,$d=null){return $s;}
function add_action($name,$callback,...$a){if($name==='phpmailer_init')$GLOBALS['mail_hook']=$callback;}
function remove_action(...$a){unset($GLOBALS['mail_hook']);}
function esc_html($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function esc_attr($s){return esc_html($s);}
function esc_url($s){return esc_html($s);}
function esc_html_e($s,$d){echo esc_html($s);}
function get_bloginfo($k){return 'en-US';}
function get_current_user_id(){return $GLOBALS['uid'];}
function current_user_can($c){return $GLOBALS['allowed'];}
function wp_generate_uuid4(){return 'summary-id';}
function set_transient($k,$v,$ttl){$GLOBALS['state'][$k]=$v;}
function get_transient($k){return $GLOBALS['state'][$k]??false;}
function is_email($s){return filter_var($s,FILTER_VALIDATE_EMAIL);}
function admin_url($s){return 'https://example.test/wp-admin/'.$s;}
function home_url($s){return $GLOBALS['test_site_url'] ?? 'https://example.test/';}
function wp_parse_url($url,$part){return parse_url($url,$part);}
function wp_date($f,$t){return '2026-10-18 12:00 PDT';}
function get_users($args){return [(object)['ID'=>1,'display_name'=>'First Admin','user_email'=>'one@example.test'],(object)['ID'=>2,'display_name'=>'Second Admin','user_email'=>'two@example.test']];}
function wp_mail($to,$subject,$body,$headers){$mailer=new stdClass();if(isset($GLOBALS['mail_hook']))($GLOBALS['mail_hook'])($mailer);$alt=$mailer->AltBody??'';$GLOBALS['mail'][]=compact('to','subject','body','headers','alt');return $to==='one@example.test';}
class WP_Error{public $code;function __construct($c,$m){$this->code=$c;}}
require dirname(__DIR__).'/includes/email.php';
require dirname(__DIR__).'/includes/email-template.php';
require dirname(__DIR__).'/includes/admin-notification.php';
function check($name,$ok){if(!$ok){throw new Exception($name);}echo "PASS $name\n";}
$payload=['summary'=>['total'=>3,'processed'=>3,'success'=>1,'failed'=>1,'skipped'=>1],'scope_label'=>'All users','notification_label'=>'Email notifications skipped.','excluded_count'=>1,'results'=>[['password'=>'secret','email'=>'private@example.test']]];
gpr_save_notification_summary($payload);
check('no individual results persisted',!isset($state['gpr_results_email_7']['results']));
$allowed=false;check('unauthorized send rejected',gpr_send_notification('summary-id',[1],'')->code==='forbidden');$allowed=true;
$uid=8;check('other administrator cannot send owner summary',gpr_send_notification('summary-id',[1],'')->code==='expired');$uid=7;
check('stale summary rejected',gpr_send_notification('older-id',[1],'')->code==='expired');
check('non-admin recipient rejected',gpr_send_notification('summary-id',[999],'')->code==='recipients');
check('no mail on rejected requests',count($mail)===0);
$result=gpr_send_notification('summary-id',[1,1,2,999],'Please review.');
check('partial failure and acceptance clearly reported',strpos($result,'sending: 1. Failed: 1.')!==false);
check('recipients validated and deduplicated',count($mail)===2);
check('personal note and summary included',strpos($mail[0]['body'],'Please review.')!==false && strpos($mail[0]['alt'],'Failed: 1')!==false);
check('no password or individual email in summary',strpos($mail[0]['body'],'secret')===false && strpos($mail[0]['body'],'private@example.test')===false);
check('repeat send throttled',gpr_send_notification('summary-id',[1],'')->code==='cooldown');
unset($state['gpr_results_email_7']);check('expired summary rejected',gpr_send_notification('summary-id',[1],'')->code==='expired');

check('administrator subject includes domain and plugin', $mail[0]['subject']==='[example.test] [Group Password Reset] Password Reset Summary');
check('user subject includes domain and plugin',gpr_email_subject('Password Reset')==='[example.test] [Group Password Reset] Password Reset');
$GLOBALS['test_site_url']='http://localhost:8081/wordpress/';
check('local port included and path omitted',gpr_email_subject('Password Reset')==='[localhost:8081] [Group Password Reset] Password Reset');
$GLOBALS['test_site_url']='https://shop.example.test/subdirectory/';
check('subdomain preserved and path omitted',gpr_email_subject('Password Reset')==='[shop.example.test] [Group Password Reset] Password Reset');

check('WordPress-style greeting and paragraph spacing',strpos($mail[0]['alt'],"Hello,\r\n\r\nA site administrator")===0);
check('site, dashboard link and closing present',strpos($mail[0]['alt'],'Site: https://example.test/')!==false && strpos($mail[0]['body'],'wp-admin/tools.php?page=group-password-reset')!==false && strpos($mail[0]['alt'],'Thank you,')!==false);
check('failure follow-up guidance included',strpos($mail[0]['alt'],'finished with failures')!==false);

check('HTML and plain text alternatives supplied',strpos($mail[0]['body'],'<!doctype html>')!==false && in_array('Content-Type: text/html; charset=UTF-8',$mail[0]['headers']) && strlen($mail[0]['alt'])>100);
check('recipient personalized',strpos($mail[0]['body'],'Hello First Admin,')!==false);
check('mail hook removed after sending',!isset($GLOBALS['mail_hook']));
$entry=['time'=>1,'summary'=>$payload['summary'],'scope_label'=>'<img src=x onerror=alert(1)>','notification_label'=>'<script>bad</script>','excluded_count'=>1];
$html=gpr_notification_html($entry,'<script>note</script>','<b>Name</b>');
check('HTML dynamic values escaped',strpos($html,'<script>')===false && strpos($html,'<img src=x')===false && strpos($html,'&lt;b&gt;Name')!==false);
check('full width layout',strpos($html,'max-width:600px')===false && strpos($html,'width="600"')===false);
check('HTML failure guidance present',strpos($html,'Follow-up needed')!==false);
$entry['summary']['failed']=0;
check('failure warning omitted on clean run',strpos(gpr_notification_html($entry),'Follow-up needed')===false);
