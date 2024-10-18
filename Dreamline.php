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
            switch ($results) {
                case '5':
                    return '[ERROR] 시스템 오류';
                case '11':
                    return '[ERROR] 메세지 포맷 오류';
                case '14':
                    return '[ERROR] 사용불가 IP';
                case '32':
                    return '[ERROR] 메세지 길이 초과';
                case '33':
                    return '[ERROR] MMS 제목 길이 초과';
                case '100':
                    return '[ERROR] 발신번호 포멧오류(전화번호 세칙 미준수)';
                case '101':
                    return '[ERROR] 등록되지 않은 발신번호(발신번호 사전등록제)';
                default:
                    return '[ERROR] ' . $results;
            }
        } else {
            return true;
        }
    }

    /**
     * 드림라인 API 를 통해 카카오 알림톡을 전송한다.
     *
     * @param string $to 수신번호
     * @param string $content 전송내용
     * @param ?string $from 발송번호
     * @return bool|string $success
     */
    public function sendKakao(string $to, string $content, string $from = null): bool|string
    {
        $params = [
            'id_type' => $this->getConfigs('id_type'),
            'id' => $this->getConfigs('id'),
            'auth_key' => $this->getConfigs('auth_key'),
            'msg_type' => 'KAT',
            'resend' => 'LMS',
            'callback_number' => $from ?? $this->getConfigs('cellphone'),
            'send_id_receive_number' => '0|' . $to,
            'template_code' => 'MOMO_01',
            'callback_key' => '1a52c11f6b291fa96e15da3d2f5a932e5b101cd4',
            'content' => $content,
            'sms_msg' => $content,
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://ums.dreamline.co.kr/API/send_kkt.php');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $results = curl_exec($ch);
        $content_type = explode(';', curl_getinfo($ch, CURLINFO_CONTENT_TYPE));
        $content_type = array_shift($content_type);

        curl_close($ch);

        if ($results != '0') {
            switch ($results) {
                case '5':
                    return '[ERROR] 시스템 오류';
                case '11':
                    return '[ERROR] 메세지 포맷 오류';
                case '14':
                    return '[ERROR] 사용불가 IP';
                case '32':
                    return '[ERROR] 메세지 길이 초과';
                case '33':
                    return '[ERROR] MMS 제목 길이 초과';
                case '100':
                    return '[ERROR] 발신번호 포멧오류(전화번호 세칙 미준수)';
                case '101':
                    return '[ERROR] 등록되지 않은 발신번호(발신번호 사전등록제)';
                default:
                    return '[ERROR] ' . $results;
            }
        } else {
            return true;
        }
    }
}
