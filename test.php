<?php

require(__DIR__.'/../../../config.php');
require_once($CFG->dirroot . '/lib/filelib.php');
$botaccount = '+491794411360';
$captcha = 'signal-recaptcha-v2.6LfBXs0bAAAAAAjkDyyI1Lk5gBAUWfhI_bIyox5W.registration.03AGdBq277VIPPm4QLXePbZxU0FAZ2RheW6N8o0u9PZeSSQRoJF-V3i8eGK9rLMr4HRB3vf2ENoaPqi_v_MqZPtZ11jsHFvEyoRKOCbHsx1wCwlATelT8X6ZGftNiTgO8yNYjbeUyw57oDyLBrki952WZbnKkKG8Fzqis9V2Oqzq6RZFWGR0PpYhmTe4L3VpqSEGXoiS--Zfw6-9lb72atZBlXAswHX-V1Iz5G8TOaGEvND0vN5CJlwFF-h_kxxAqNXy4sykDDsK6Q3GK1OoTOih46Kk4zrCg2ms_qkOi6U4mNUKewhtZt0gpUivdByrhje-oXpDSkLciP4ZcjxvV6VWJhCX62zB_2t_mIVcq0D1j00RwEKWU8GEybNLWZJN2twrDTERloqjzeZ70kNgjrKJJZUH_r6uuXR7Nr2ATb6Q758nGftY-kkK6HX3Yj-TkWbTrbnQqwmoA_G54Ra6Xw9JOLLCVvTKY4r4scPxGskGoLV15JbWRgs8X32SHIDYM8GsKSvRDPvoYq5KkdXz3e9uQoRTjkCpWaTM8ZLkYYi4PzuwyTFeWXZ-8RdvNoKatGnnv9TQH7ZO3_x38AxukR3gv8KNl9kS8ZZuVscHJ0C4cIw2n2iUFVYrQJ6TeiCdWl_ymcjIj73s66XbrIOwW7u0EyHqFdjx40I1L473mzeuEwBr-JFxnBTgYiLek2_oBazLeLNQdi8e1JA9vubKPGmhttcmgNDNuLstY3kjDUjHksDtA_RJCog2B2fOLxL_kMEfLpVvxTDxbmuRiw0R0_cVuZBd0c77Cjhc81SccSVIbMYrPZEUWyk4s0ShmxjDipiCBDqUmjTJIkkYpHsb0Up094rslk2BgrrTWVs0git-t7nMKOQRRq8Dvbmdf1SHW6x_rKmotbd5HF-b7LVHbKwlAFEJflukxl7UAyuRkfHDZAVIxVr5odWiVGPCW-MdMcBkHV1uMNbFqpoSzfWCdarXHPHkn1LLTIlMtBTE9Putxm_xv0pLjopAU5jeKPdbLTVORhFWux1ndQzX6xOWaHosQxn_B9D0y0THKB8Dm6fDEkFFnO7e3wl0fGQVFFPQ2f7kmh14JOyQo1O7SsJRajKVqlXi7pQheYYsqj3dBq6TPOhOsuj57IDOiZvDEw1pBrKpT8KHn1p16PV_ig6CLq9myb5f6DSC0t-nEJn46OTRzwGEqFrlaUYDneGxv0EkYA8ebM5AEsRoLypLffk2RYGKYFuEoXagp13NzXVGe3zPYEP_T79WDeMvOLHJTeaEwgCms7J7VrpXueU4TbdCxeeK2npvACDsSJLf8s9nt8AAYWn14cxVUoFjI';
$signal_api_host = 'localhost:4040';
$curl = new \curl();

// $response = json_decode($curl->delete($signal_api_host  . '/accounts/' . $botaccount));
// print_r($response);echo('</br>');
// print_r($curl->get_info());
// echo('</br>');echo('</br>');echo('</br>');

// $json = json_encode(['captcha' => $captcha, 'use_voice' => false]);
// $response = json_decode($curl->post($signal_api_host  . '/accounts/' . $botaccount, $json));
// print_r($response);echo('</br>');
// print_r($curl->get_info());
// echo('</br>');echo('</br>');

// $http_code = $curl->get_info()['http_code'];
// if ($http_code >= 300) {
//     \core\notification::error(get_string('errorcreatingaccount', 'message_signal'));
// } else {
//     // $this->set_config('verified', false);
//     \core\notification::success(get_string('accountcreated', 'message_signal'));
// }


$response = json_decode($curl->get($signal_api_host  . '/accounts/' . $botaccount));
print_r($response);echo('</br>');
print_r($curl->get_info());
echo('</br>');echo('</br>');echo('</br>');

$http_code = $curl->get_info()['http_code'];
//     return \core\notification::error(get_string('errorverifingaccount', 'message_signal'));
// }
if ($http_code < 300) {
    $users = explode('\n', $response->body);
    foreach($users as $user) {
        $status = explode(':', $user);
        $verified = trim($status[1]);
        if(trim($status[0]) === $botaccount) {
            break;
        }
    }
}

if($verified == 'true') {
    print_r('verified');
} else {
    
    print_r('not verified');
}