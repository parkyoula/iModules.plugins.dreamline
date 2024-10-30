<?php
/**
 * 이 파일은 아이모듈 드림라인 SMS 플러그인의 일부입니다. (https://www.imodules.io)
 *
 * 드림라인 SMS 플러그인 클래스 정의한다.
 *
 * @file /plugins/dreamline/Dreamline.php
 * @author parkyoula <youlapark@naddle.net>
 * @license MIT License
 * @modified 2024. 10. 30.
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
                    return '시스템 오류가 발생했습니다.';
                case '11':
                    return '메세지 포맷이 잘못되었습니다.';
                case '14':
                    return '허용되지 않은 IP입니다.';
                case '21':
                    return '일일 전송량을 초과하였습니다.';
                case '22':
                    return '월간 전송량을 초과하였습니다.';
                case '30':
                    return '메세지 디코딩에 실패했습니다.';
                case '32':
                    return '메세지 길이가 너무 깁니다.';
                case '33':
                    return 'MMS 제목 길이가 너무 깁니다.';
                case '34':
                    return '수신 거부된 번호입니다. (예: 080 차단)';
                case '50':
                    return 'MMS 파일 처리에 실패했습니다.';
                case '51':
                    return '이미지 파일 용량이 너무 큽니다.';
                case '52':
                    return '지원되지 않는 메세지 유형입니다.';
                case '55':
                    return '사업자 식별 코드가 잘못되었습니다.';
                case '100':
                    return '발신 번호 형식이 올바르지 않습니다. (전화번호 규칙 미준수)';
                case '101':
                    return '등록되지 않은 발신 번호입니다.';
                case '111':
                    return '메세지 처리 시간이 초과되었습니다. (통신사 보고서 NULL)';
                case '200':
                    return '기타 오류가 발생했습니다.';
                case '201':
                    return '통신사에서 기타 오류가 발생했습니다.';
                case '1013':
                    return '수신 번호 오류로 발송에 실패했습니다. (자리 수 오류)';
                case '1025':
                    return '단말기 전원이 꺼져 있어 전송에 실패했습니다.';
                case '1027':
                    return '수신 거부로 인해 전송에 실패했습니다.';
                case '2007':
                    return '번호 이동된 가입자여서 전송에 실패했습니다.';
                case '4002':
                    return '발신 번호 또는 착신 번호가 잘못되었습니다.';
                case '10888':
                    return '카카오톡 서버와의 연결 또는 응답을 받지 못했습니다.';
                case '10910':
                    return '입력한 값의 형식이 유효하지 않습니다.';
                case '11003':
                    return '발신 프로필 키가 유효하지 않습니다.';
                case '13004':
                    return '템플릿 확인 중 내부 오류가 발생했습니다.';
                case '13005':
                    return '메시지가 발송되었으나 수신 확인이 되지 않았습니다. 전송성공이 불확실하며, 카카오측 내부 운영방침으로 인해 3일 이내 수신 가능합니다.';
                case '13008':
                    return '전화번호에 오류가 있습니다.';
                case '13010':
                    return 'JSON 형식 파싱 오류가 발생했습니다.';
                case '13014':
                    return '메시지 길이 제한을 초과하였습니다. (텍스트 최대 1000자 가능, 이미지 타입 최대 400자 가능)';
                case '13015':
                    return '전송 템플릿을 찾을 수 없습니다.';
                case '13016':
                    return '메시지 내용이 템플릿과 일치하지 않습니다.';
                case '13018':
                    return '메시지를 전송할 수 없습니다.';
                case '13022':
                    return '메시지를 발송할 수 없는 시간입니다. (발송 가능 시간: 08시~20시)';
                default:
                    return '[ERROR] 기타 오류 : ' . $results;
            }
        }
    }
}
