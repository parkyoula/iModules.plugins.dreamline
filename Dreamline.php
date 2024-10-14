<?php
/**
 * 이 파일은 아이모듈 드림라인 SMS 플러그인의 일부입니다. (https://www.imodules.io)
 *
 * 드림라인 SMS 플러그인 클래스 정의한다.
 *
 * @file /plugins/dreamline/Dreamline.php
 * @author Arzz <arzz@arzz.com>
 * @license MIT License
 * @modified 2024. 10. 13.
 */
namespace plugins\dreamline;
class Dreamline extends \Plugin
{
    /**
     * 드림라인 API 를 통해 SMS 를 전송한다.
     *
     * @param string $to 수신번호
     * @param string $content 전송내용
     * @param ?string $from 발송번호
     * @return bool|string $success
     */
    public function send(string $to, string $content, string $from = null): bool|string
    {
        /**
         * @var \modules\sms\Sms $mSms
         */
        $mSms = \Modules::get('sms');
        $type = $mSms->getContentLength($to) > 80 ? 'LMS' : 'SMS';

        $params = [
            'id_type' => $this->getConfigs('id_type'),
            'id' => $this->getConfigs('id'),
            'auth_key' => $this->getConfigs('auth_key'),
            'msg_type' => $type,
            'callback_number' => $from ?? $this->getConfigs('cellphone'),
            'send_id_receive_number' => '0|' . $to,
            'content' => $content,
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://ums.dreamline.co.kr/API/send.php');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $results = curl_exec($ch);
        $content_type = explode(';', curl_getinfo($ch, CURLINFO_CONTENT_TYPE));
        $content_type = array_shift($content_type);

        curl_close($ch);

        if ($results != '0') {
            return '[ERROR] ' . $results;
        } else {
            return true;
        }
    }
}
