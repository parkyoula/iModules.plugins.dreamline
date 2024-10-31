<?php
/**
 * 이 파일은 아이모듈 드림라인 SMS 플러그인의 일부입니다. (https://www.imodules.io)
 *
 * SMS모듈의 이벤트리스너를 정의한다.
 *
 * @file /plugins/dreamline/listeners/modules/sms/Listeners.php
 * @author Arzz <arzz@arzz.com>
 * @license MIT License
 * @modified 2024. 10. 28.
 */
namespace plugins\dreamline\listeners\modules\sms;
class Listeners extends \modules\sms\Event
{
    public static int $_priority = 1;

    /**
     * SMS를 전송할 때 발생한다.
     *
     * @param \modules\sms\Sender $sender SMS를 보내고 있는 전송자 객체
     * @param int $sended_at 전송시각
     * @return bool|string|null $success 발송성공여부 또는 실패메시지 (NULL 인 경우, 다른 이벤트리스너나 이메일 모듈에서 이어서 발송한다.)
     */
    public static function send(\modules\sms\Sender $sender, int $sended_at): bool|string|null
    {
        /**
         * @var \plugins\dreamline\Dreamline $pDreamline
         */
        $pDreamline = \Plugins::get('dreamline');
        if (
            $sender
                ->getTo()
                ->getCountry()
                ?->getCode() == 'KR'
        ) {
            $success = $pDreamline->send(
                $sender->getTo()->getCellphone(),
                $sender->getContent(),
                $sender->getFrom()->getCellphone()
            );
            return $success;
        }

        return null;
    }
}
