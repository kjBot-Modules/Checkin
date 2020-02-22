<?php
namespace kjBotModule\kj415j45\Checkin;

use kjBot\Framework\Module;
use kjBot\Framework\Event\MessageEvent;
use kjBotModule\kj415j45\CoreModule\Access;
use kjBotModule\kj415j45\CoreModule\AccessLevel;
use kjBotModule\kj415j45\CoreModule\Economy;

class Checkin extends Module{
    public function process(array $args, MessageEvent $event){
        $id = $event->getId();
        $user = new Model($id);
        $userEconomy = new Economy($id);
        $isSupporter = (Access::Control($event))->getLevel() >= AccessLevel::Supporter;
        if($user->checkin()){
            $bonus = rand(40, 60);
            $extraBonus = $isSupporter?rand(0.4*$bonus, 0.6*$bonus):0;
            $bonus += $extraBonus;
            $extraBonusStr = ($extraBonus == 0)?'':("（含 Supporter 额外奖励 {$extraBonus} 个）");
            $userEconomy->addBalance($bonus);
            $data = $user->getData();
            Access::Log($this, $event, "Get {$bonus}");
            return $event->sendBack(
                "签到成功，获得 {$bonus} 个金币{$extraBonusStr}，已连续签到 {$data['days_keep']} 天"
            );
        }else{
            return $event->sendBack('今天已经签到过了');
       }
    }
}