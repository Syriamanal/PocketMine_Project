<?php

/*
__PocketMine Plugin__
name=LifeEX
description=
version=0.3 Major
author=Milphy
class=LifeEX
apiversion=8,9,10,11
-------------
업데이트 내역
=============
[Plugin]0.1
최초 릴리스
---------------
[Plugin]0.1.1
명령어 오류 수정(by 원본) 
LifeEX/Data 삭제(config.yml로 변경)
----------------
[Plugin]0.1.2
config.yml 잡 옵션 제거
명령어&안내문&config.yml 한글화
진급&성별선택&결혼 수정중(모든 명령어는 0.2부터 사용가능) 
----------------
[Plugin]0.1.2.2
이혼명령어 추가
모든명령어 메시지 활성화
----------------
[Plugin]0.2
일부 오류및 자잘한버그 수정
내성별->성향(나이,성별 등 내성향 모두 확인)
성별*종족선택->선택 <종족/성별> <사람/남,여>
선택,성향,진급,결혼 구현(종족->사람밖에없음)
결혼은 아직 거절은 없습니다
나이는 아직 수동적(yml수정)
이제 op가 아니어도 명령어를 사용 가능합니다
----------------
[Plugin]0.2.1
Lifedata->Player(폴더 변경)
결혼이 안되던점 수정
일부 오류 수정
----------------
[Plugin]0.3 Major
이혼 구현
종족 활성화(천족,마족)
종족별 특성 추가
나이 시스템 추가(시간이되면 오름)
명령어 변경
선택-> 종족,성별
상대의 성향 확인가능(/성향 <상대>)
=============
*/

class LifeEX implements Plugin{

  private $api;
  public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}
	public function __destruct(){}	
	public function init(){
		$this->readConfig();
		$this->api->ban->cmdWhitelist("결혼");
		$this->api->ban->cmdWhitelist("성향");
		$this->api->ban->cmdWhitelist("선택");
		$this->api->ban->cmdWhitelist("직업");
		$this->api->ban->cmdWhitelist("이혼");
		$this->api->addHandler("player.join", array($this, "handler"), 5);
		$this->api->addHandler("player.quit", array($this, "handler"), 5);
		$this->api->addHandler("player.spawn", array($this, "handler"), 5);
		$this->api->addHandler("player.respawn", array($this, "handler"), 5);
		$this->api->addHandler("player.block.place", array($this, "handler"), 15);
		$this->api->addHandler("player.block.break", array($this, "handler"), 15);
		$this->api->console->register("이혼", "", array($this, "defaultCommands"));
		$this->api->console->register("직업", "", array($this, "defaultCommands"));
		$this->api->console->register("성향", "", array($this, "defaultCommands"));
		$this->api->console->register("결혼", "<상대>", array($this, "defaultCommands"));
		$this->api->console->register("선택", "<천족,마족> <남,여>", array($this, "defaultCommands"));
		$this->config = new Config("./plugins/LifeEX/config.yml", CONFIG_YAML, array("나이상승" => 45));
		$this->api->schedule(8400, array($this, "handler"), array(), true, "LifeEX.SelectSystem");
		$this->api->schedule(1200 * $this->config->get("나이상승"), array($this, "handler"), array(), true, "LifeEX.YearSystem");
	}
	
	public function readConfig(){
		if(is_dir(DATA_PATH."/plugins/LifeEX/") === false or is_dir(DATA_PATH."/plugins/LifeEX/Player/") === false){
			mkdir(DATA_PATH."/plugins/LifeEX/");
			mkdir(DATA_PATH."/plugins/LifeEX/player/");
		}
	}
				
	public function handler($data, $event){
		switch($event){
			case "player.join":
					$this->data[$data->username] = new Config(DATA_PATH."/plugins/LifeEX/Player/".$data->username.".yml", CONFIG_YAML, array(
							'종족' => "선택안함",
							'성별' => "선택안함",
							'나이' => "5",
							'학교' => "X",
							'직업' => "없음",
							'결혼' => "X",
						));
					break;
			case "player.quit":
				if($this->data[$data->username] instanceof Config){
					$this->data[$data->username]->save();
				}
				break;
			case "player.spawn":
					if($this->data[$data->username]->get("성별") === "선택안함"){
					$data->sendChat("[LifeEX]성별및 종족을 선택해주세요\n선택하는법:/선택 <천족,마족> <남,여>\n");
						break;
					}
					break;
			case "player.respawn":
					if($this->data[$data->username]->get("나이") >= 60){
					$this->data[$data->username]->set("나이", 20);
					}
					break;
			case "player.block.place":
				$item = $data["item"];
				$player = $this->api->player->get($data["player"]);
				if($item->getID() === 14 or $item->getID() === 15){ if($player->gamemode !== CREATIVE and !$this->api->ban->isOp($data["player"]) and $this->data[$player]->get("종족") === "천족") return false; }
					break;
			case "player.block.break":
				$target  = $data["target"];
				$player = $this->api->player->get($data["player"]);
				if($target->getID() === 14 or $target->getID() === 15){
					if($player->gamemode !== CREATIVE and $this->data[$player]->get("종족") === "천족"){
						$this->api->entity->drop(new Position($player->entity->x - 0.5, $player->entity->y, $player->entity->z - 0.5, $player->entity->level), BlockAPI::getItem($target->getID()));
						break;
					}
				break;
				}else if($target->getID() === 56){
					if($player->gamemode !== CREATIVE and $this->data[$player]->get("종족") === "천족"){
						$this->api->entity->drop(new Position($player->entity->x - 0.5, $player->entity->y, $player->entity->z - 0.5, $player->entity->level), BlockAPI::getItem(264));
						break;
					}
				break;
				}
				break;
			case "LifeEX.SelectSystem":
      			foreach($this->api->player->online() as $online){
        			$play = $this->api->player->get($online);
        			if($this->data[$play->username]->get("성별") === "선택안함"){
						$play->sendChat("[LifeEX]성별및 종족을 선택해주세요\n선택하는법:/선택 <천족,마족> <남,여>");
					}
				}
				break;
			case "LifeEX.YearSystem":
      			foreach($this->api->player->online() as $online){
        			$play = $this->api->player->get($online);
        			$this->data[$play->username]->set("나이", $this->data[$play->username]->get("나이")+1);
					$play->sendChat("[LifeEX]당신의 나이가 올랐습니다(".$this->data[$play->username]->get("나이")."살)");
					if($this->data[$play->username]->get("나이") === 7){
						$play->sendChat("[LifeEX]입학을 축하합니다! 당신은 초등학생입니다");
						$this->data[$play->username]->set("학교", 초등학생);
					}elseif($this->data[$play->username]->get("나이") === 13){
						$play->sendChat("[LifeEX]입학을 축하합니다! 당신은 중학생입니다");
						$this->data[$play->username]->set("학교", 중학생);
					}elseif($this->data[$play->username]->get("나이") === 16){
						$play->sendChat("[LifeEX]입학을 축하합니다! 당신은 고등학생입니다");
						$this->data[$play->username]->set("학교", 고등학생);
					}elseif($this->data[$play->username]->get("나이") === 19){
						$play->sendChat("[LifeEX]입학을 축하합니다! 당신은 대학생입니다!");
						$this->data[$play->username]->set("학교", 대학생);
					}
					}
					break;
				}
			}
			
	public function defaultCommands($cmd, $params, $issuer, $alias){
		$output = "";
		switch($cmd){
			case "선택":
				switch($params[0]){
			case "":
				$output .= "사용법:/선택 <천족,마족> <남,여>\n";
				break;
			default:
				$issuer->sendChat("사용법:/선택 <천족,마족> <남,여>\n");
				break;
			case "천족":
				switch($params[1]){
			case "":
				$output .= "사용법:/선택 천족 <남,여>";
				break;
			default:
				$output .= "사용법:/선택 천족 <남,여>";
				break;
			case "여":
				if($this->data[$issuer->username]->get("성별") !== 선택안함){
					$output .= "[LifeEX]당신은 이미 성별과 종족을 선택하셧습니다\n";
					break;					
				}else{
					$this->data[$issuer->username]->set("성별", 여자);
					$this->data[$issuer->username]->set("종족", 천족);
					$output  .= "[LifeEX]성별: 여자 종족: 천족\n[LifeEX]선택이 완료 되었습니다\n";
					break;
				}
				break;
			case "남":
				if($this->data[$issuer->username]->get("성별") !== 선택안함){
					$output .= "[LifeEX]당신은 이미 성별과 종족을 선택하셧습니다\n";
					break;					
				}else{
					$this->data[$issuer->username]->set("성별", 남자);
					$this->data[$issuer->username]->set("종족", 천족);
					$output  .= "[LifeEX]성별: 남자 종족: 천족\n[LifeEX]선택이 완료 되었습니다\n";
					break;
				}
				break;
				}
				break;
			case "마족":
				switch($params[1]){
			case "":
				$output .= "사용법:/선택 마족 <남,여>";
				break;
			default:
				$output .= "사용법:/선택 마족 <남,여>";
			case "여":
				if($this->data[$issuer->username]->get("성별") !== 선택안함){
					$output .= "[LifeEX]당신은 이미 성별과 종족을 선택하셧습니다\n";
					break;					
				}else{
					$this->data[$issuer->username]->set("성별", 여자);
					$this->data[$issuer->username]->set("종족", 마족);
					$output  .= "[LifeEX]성별: 여자 종족: 마족\n[LifeEX]선택이 완료 되었습니다";
					break;
				}
				break;
			case "남":
				if($this->data[$issuer->username]->get("성별") !== 선택안함){
					$output .= "[LifeEX]당신은 이미 성별과 종족을 선택하셧습니다\n";
					break;					
				}else{
					$this->data[$issuer->username]->set("성별", 남자);
					$this->data[$issuer->username]->set("종족", 마족);
					$output  .= "[LifeEX]성별: 남자 종족: 마족\n[LifeEX]선택이 완료 되었습니다\n";
					break;
				}
				break;
				}
				break;
				}
				break;
			case "성향":
				$parm = $this->api->player->get($params[0]);
				if($params[0] == ""){
					$성별 = $this->data[$issuer->username]->get("성별");
					$나이 = $this->data[$issuer->username]->get("나이");
					$학교 = $this->data[$issuer->username]->get("학교");
					$결혼 = $this->data[$issuer->username]->get("결혼");
					$종족 = $this->data[$issuer->username]->get("종족");
					$직업 = $this->data[$issuer->username]->get("직업");
				if($this->data[$issuer->username]->get("성별") === 선택안함){
					$output .= "[LifeEX]성별과 종족을 선택해주세요\n선택하는법:/선택 <천족,마족> <남,여>\n";
					break;
				}else{
					$output .= "[LifeEX]종족:$종족 성별:$성별 나이:$나이\n[LifeEX]".$학교."재학 직업:$직업 결혼:$결혼\n";
					break;				
				}
					break;
				}else if($parm === false){
					$output .= "[LifeEX]없는 분이거나 접속하지 않으셧습니다";
					break;
				}else{
					$성별 = $this->data[$parm->username]->get("성별");
					$나이 = $this->data[$parm->username]->get("나이");
					$학교 = $this->data[$parm->username]->get("학교");
					$결혼 = $this->data[$parm->username]->get("결혼");
					$종족 = $this->data[$parm->username]->get("종족");
					$직업 = $this->data[$parm->username]->get("직업");
				if($this->data[$parm->username]->get("성별") === 선택안함){
					$output .= "[LifeEX]상대가 성별과 종족을 선택안했습니다\n";
					break;
				}else{
					$output .= "[LifeEX]종족:$종족 성별:$성별 나이:$나이\n[LifeEX]".$학교."재학 직업:$직업 결혼:$결혼\n";
					break;				
				}
				}
				break;
			case "결혼":
				if($params[0] == ""){
					$output .= "사용법: /$cmd <상대>\n";
					break;
				}
				$parm = $this->api->player->get($params[0]);
				if($parm === false){
					$output .= "[LifeEX]없는 분이거나 접속하지 않으셧습니다";
					break;
				}else if($parm === $issuer){
					$output .= "[LifeEX]자기자신하고 결혼하려구요?";
					break;
				}else if($this->data[$issuer->username]->get("나이") <= 19){
					$output .= "[LifeEX]결혼은 20살부터 가능합니다";
					break;
				}else if($this->data[$parm->username]->get("나이") <= 19){
					$parmage = $this->data[$parm->username]->get("나이");
					$output .= "[LifeEX]아쉽지만".$parm."님은 ".$parmage."살 이십니다";
					break;
				}else if($this->data[$issuer->username]->get("결혼") !== X){
					$output .= "[LifeEX]또 결혼하시게요?";
					break;
				}else if($this->data[$parm->username]->get("결혼") !== X){
					$output .= "[LifeEX]아쉽지만 ".$parm."님은 이미 결혼하셧습니다\n";
					break;
				}else if($this->data[$issuer->username]->get("성별") === 여자 and $this->data[$parm->username]->get("성별") === 여자){
					$output .= "[LifeEX]아쉽지만 ".$parm."님은 여자입니다";
					break;
				}else if($this->data[$issuer->username]->get("성별") === 남자 and $this->data[$parm->username]->get("성별") === 남자){
					$output .= "[LifeEX]아쉽지만 ".$parm."님은 남자입니다";
					break;
				}else{
					$this->api->chat->broadcast("[LifeEX]축하합니다 ".$issuer."님은 ".$parm ."님과 결혼하셧습니다!");
					$this->data[$parm->username]->set("결혼", $issuer);
					$this->data[$issuer->username]->set("결혼", $parm);
					break;
				}
				break;
			case "이혼":
				$data = $this->api->player->get($this->data[$issuer->username]->get("결혼"));
				if($this->data[$issuer->username]->get("결혼") === X){
					$output .= "[LifeEX]결혼 안하시고 이혼하시게요?";
					break;
				}else{
					$this->api->chat->broadcast("[LifeEX]".$issuer."님이 ".$data."님과 이혼하셧습니다");
					$this->data[$data2->username]->set("결혼", X);
					$this->data[$issuer->username]->set("결혼", X);
					break;
				}
				break;
			case "직업":
				if($params[0] == ""){
					$output .= "사용법: /$cmd <직업>\n";
					break;
				}else{
					$직업 = $params[0];
					$issuer->sendChat("[LifeEX]당신은 ".$직업."입니다");
					$this->data[$issuer->username]->set("직업", $직업);
					break;
				}
				break;
			}
			 return $output;
			}
		}
