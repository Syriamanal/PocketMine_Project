<?php

/*
__PocketMine Plugin__
name=LifeEX
description=Make by SXBox
version=0.1.2
author=SXBox
class=LifeEX
apiversion=8,9,10,11
*/
/* 
업데이트 내역
=============
[Plugin]0.1
최초 릴리스
---------------
[Plugin]0.1.1
명령어 오류 수정(by 원본) 
LifeEX/Data 삭제(config.yml로 변경)
LifeEx/chat.yml삭제
----------------
[Plugin]0.1.2
config.yml 잡 옵션 제거
명령어&안내문&config.yml 한글화
진급&성별선택&결혼 수정중(0.2부터 사용가능합니다) 
op아니어도 명령어 사용가능 
*/

class LifeEX implements Plugin{

  private $api;
  public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}
	public function __destruct(){}
	private function overwriteConfig($dat){
			$cfg = array();
			$cfg = $this->api->plugin->readYAML($this->path . "LifeEXdata.yml");
			$result = array_merge($cfg, $dat);
			$this->api->plugin->writeYAML($this->path."LifeEXdata.yml", $result);
		}	
	public function init(){
	if(!file_exists("./plugins/LifeEX")){
		mkdir("./plugins/LifeEX");
		}
		$this->api->addHandler("player.join", array($this, "Handler"), 5);		
		$this->api->addHandler("player.chat", array($this, "Handler"), 5);
		$this->api->addHandler("player.quit", array($this, "Handler"), 5);
		$this->server->api->event("server.close", array($this, "handle"));
		$this->api->console->register("결혼", "", array($this, "defaultCommands"));
		$this->api->console->register("성별선택", "", array($this, "defaultCommands"));
		$this->api->console->register("내성별", "", array($this, "defaultCommands"));
		$this->api->console->register("진급", "", array($this, "defaultCommands"));
		$this->lifedata = new Config("./plugins/LifeEX/LifeEXdata.yml", CONFIG_YAML);
		$lifedata = $this->server->api->plugin->readYAML("./plugins/LifeEX/LifeEXdata.yml");
		$this->server->api->ban->cmdWhitelist("takedebt");
	}
	
	public function Handler(&$data, $event){
		$cfg = $this->api->plugin->readYAML($this->path . "LifeEXdata.yml");
		switch($event){
			case "player.join":
				$target = $data->username;
				if(!array_key_exists($target, $cfg)){
					$this->api->plugin->createConfig($this,array(
						$target => array(
							'종족' => "선택안함",
							'학교' => "초등학생",
							'나이' => "7",
							'성별' => "선택안함",
							'결혼' => "X",
							)
					));
				}
			/*if($cfg[$issuer->username]['결혼'] !== X){
			}*/
				break;
			case "player.chat":
			/*if($cfg[$issuer->username]['결혼'] !== X){
			}*/
				break;
			case "player.quit":
			/*if($cfg[$issuer->username]['결혼'] !== X){
			}*/
			break;
            case "server.close":
            $this->lifedata->save();
				}
			}

	public function defaultCommands($cmd, $params, $issuer, $alias){
		$output = "";
		$cfg = $this->api->plugin->readYAML($this->path . "LifeEXdata.yml");
		switch($cmd){
			case "성별선택":
				$command= $params[0];
				switch($command){
			default:
				if(!($issuer instanceof Player)){
					$output .= "Please run this command in-game.\n";
					break;
				}
				$output .="[LifeEX]성별은 남자 또는 여자밖에 없잖아요\n";
				break;
			case "":
				if(!($issuer instanceof Player)){
					$output .= "Please run this command in-game.\n";
					break;
				}
					$output .= "사용법:/성별선택  <남자/여자>\n";
					break;
			case "여자":
				if(!($issuer instanceof Player)){
					$output .= "Please run this command in-game.\n";
					break;
				}
				if($cfg[$issuer->username]['성별'] !== 선택안함){
					$output .= "[LifeEX]당신은 이미 성별을 선택하셧습니다\n";
					break;					
				}else{
					$cfg[$issuer->username]['성별'];
					$output  .= "[LifeEX]당신의 성별은 여자입니다\n";
					break;
				}
				break;
			case "남자":
				if(!($issuer instanceof Player)){
					$output .= "Please run this command in-game.\n";
					break;
				}
				if($cfg[$issuer->username]['성별'] !== 선택안함){
					$output .= "[LifeEX]당신은 이미 성별을 선택하셧습니다\n";
					break;					
				}else{
					$cfg[$issuer->username]['성별'];
					$output  .= "[LifeEX]당신의 성별은 남자입니다\n";
					break;
				}
			}
					break;
			case "결혼":
				if(!($issuer instanceof Player)){
					$output .= "Please run this command in-game.\n";
					break;
				}
				$output .= "[LifeEX]현재 결혼은 아직 준비중입니다";
				break;
			case "내성별":
				if(!($issuer instanceof Player)){
					$output .= "Please run this command in-game.\n";
					break;
				}
				if($cfg[$issuer->username]['성별'] === 선택안함){
				$output .= "[LifeEX]먼저 성별을 선택해주세요\n";
				break;				
				}else{
				$gender = $cfg[$issuer->username]['성별'];
				$output .= "[LifeEX]당신은 $gender 입니다\n";
				break;
				}
			case "진급":
				if(!($issuer instanceof Player)){
					$output .= "Please run this command in-game.\n";
					break;
				}
				if($cfg[$issuer->username]['나이'] === 13){
				$cfg[$issuer->username]['학교'] = '중학생'
				$output .= "[LifeEX]당신은 이제 중학생입니다";
				}else if($cfg[$issuer->username]['나이'] === 16){
				$output .= "[LifeEX]당신은 이제 고등학생입니다";
				}else if($cfg[$issuer->username]['나이'] === 19){
				$output .= "[LifeEX]당신은 이제 대학생입니다";
				}else{
				$output .= "[LifeEX]현재 진급은 준비중입니다";
				}
				}
			 return $output;
			}
		}
