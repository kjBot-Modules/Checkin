<?php
namespace kjBotModule\kj415j45\Checkin;

use DateTime;
use kjBot\Framework\DataStorage;

class Model{
    const BaseDir = 'Checkin/';
    private $userId;
    private $data;

    static protected function Init($userId){
        $data = [
            'days_keep' => 0,
            'last_checkin' => (new DateTime('yesterday'))->format('c'),
        ];
        DataStorage::SetData(static::BaseDir.$userId, json_encode($data));
        return $data;
    }

    protected function save(){
        return DataStorage::SetData(static::BaseDir.$this->userId, json_encode($this->data));
    }

    public function getData(){
        return $this->data;
    }

    public function __construct($userId){
        $this->userId = $userId;
        $json = DataStorage::GetData(static::BaseDir.$userId);
        if($json === false){
            $this->data = static::Init($userId);
            $this->save();
        }else{
            $this->data = json_decode($json, true);
        }
    }

    public function checkin(): bool{
        $today = new DateTime('today');
        $last_checkin = new DateTime($this->data['last_checkin']);

        if($today > $last_checkin){
            if($today->diff($last_checkin, true)->format('%a') <= 1){ //如果签到间隔小于一天
                $this->data['days_keep']++;
            }else{
                $this->data['days_keep'] = 1;
            }
            $this->data['last_checkin'] = $today->format('c');
            $this->save();
            return true;
        }else{
            return false;
        }
    }
}