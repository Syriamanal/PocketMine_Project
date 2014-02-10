<?php

/*
__PocketMine Plugin__
name=LifeEX
description=
version=0.3 Major
author=Milphy
class=LifeEX
apiversion=8,9,10,11
*/

class LifeEX implements Plugin{

  private $api;
  public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}
	public function __destruct(){}	
	public function init(){
		$this->readConfig();
		
		$this->api->ban->cmdWhitelist("Marriage");
		$this->api->ban->cmdWhitelist("Tendency");
		$this->api->ban->cmdWhitelist("Selection");
		$this->api->ban->cmdWhitelist("Job");
		$this->api->ban->cmdWhitelist("Divorce");
		$this->api->addHandler("player.join", array($this, "handler"), 5);
		$this->api->addHandler("player.quit", array($this, "handler"), 5);
		$this->api->addHandler("player.spawn", array($this, "handler"), 5);
		$this->api->addHandler("player.respawn", array($this, "handler"), 5);
		$this->api->addHandler("player.block.place", array($this, "handler"), 15);
		$this->api->addHandler("player.block.break", array($this, "handler"), 15);
		$this->api->console->register("Divorce", "", array($this, "defaultCommands"));
		$this->api->console->register("Job", "", array($this, "defaultCommands"));
		$this->api->console->register("Tendency", "", array($this, "defaultCommands"));
		$this->api->console->register("Marriage", "<Opponent>", array($this, "defaultCommands"));
		$this->api->console->register("Selection", "<천족,마족> <남,여>", array($this, "defaultCommands"));
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
							'Tribe' => "Blank",
							'Sex' => "Blank",
							'Age' => "5",
							'School' => "X",
							'Job' => "No",
							'Marriage' => "X",
						));
					break;
			case "player.quit":
				if($this->data[$data->username] instanceof Config){
					$this->data[$data->username]->save();
				}
				break;
			case "player.spawn":
					if($this->data[$data->username]->get("Sex") === "Blank"){
					$data->sendChat("[LifeEX]Please select a gender and race \ n select your choice :/ <Elyos, Asmodians> <M, F>\n");
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
					if($player->gamemode !== CREATIVE and $this->data[$player]->get("Tribe") === "Elyos"){
						$this->api->entity->drop(new Position($player->entity->x - 0.5, $player->entity->y, $player->entity->z - 0.5, $player->entity->level), BlockAPI::getItem($target->getID()));
						break;
					}
				break;
				}else if($target->getID() === 56){
					if($player->gamemode !== CREATIVE and $this->data[$player]->get("Tribe") === "Elyos"){
						$this->api->entity->drop(new Position($player->entity->x - 0.5, $player->entity->y, $player->entity->z - 0.5, $player->entity->level), BlockAPI::getItem(264));
						break;
					}
				break;
				}
				break;
			case "LifeEX.SelectSystem":
      			foreach($this->api->player->online() as $online){
        			$play = $this->api->player->get($online);
        			if($this->data[$play->username]->get("Sex") === "Blank"){
						$play->sendChat("[LifeEX]Please select a gender and race \ n select your choice :/ <Elyos, Asmodians> <M, F>");
					}
				}
				break;
			case "LifeEX.YearSystem":
      			foreach($this->api->player->online() as $online){
        			$play = $this->api->player->get($online);
        			$this->data[$play->username]->set("Age", $this->data[$play->username]->get("Age")+1);
					$play->sendChat("[LifeEX]Your age has risen(".$this->data[$play->username]->get("Age")."살)");
					if($this->data[$play->username]->get("Age") === 7){
						$play->sendChat("[LifeEX]Congratulations on your admission! You are on elementary");
						$this->data[$play->username]->set("School", 초등학생);
					}elseif($this->data[$play->username]->get("Age") === 13){
						$play->sendChat("[LifeEX]Congratulations on your admission! You are a junior");
						$this->data[$play->username]->set("School", 중학생);
					}elseif($this->data[$play->username]->get("Age") === 16){
						$play->sendChat("[LifeEX]Congratulations on your admission! Are you a high school student");
						$this->data[$play->username]->set("School", 고등학생);
					}elseif($this->data[$play->username]->get("Age") === 19){
						$play->sendChat("[LifeEX]Congratulations on your admission! Are you a college student?");
						$this->data[$play->username]->set("School", 대학생);
					}
					}
					break;
				}
			}
			
	public function defaultCommands($cmd, $params, $issuer, $alias){
		$output = "";
		switch($cmd){
			case "Selection":
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
