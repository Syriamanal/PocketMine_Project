<?php

/*
__PocketMine Plugin__
name=LifeEX
description=Make by SXBox
version=0.1.3
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
진급&성별선택&결혼 수정중(모든 명령어는 0.2부터 사용가능) 
----------------
[Plugin]0.1.2.1
모든명령어 메시지 활성화
----------------
[Plugin]0.1.2.2
이혼명령어 추가(작동하진 않습니다)
*/

class LifeEX implements Plugin{

  private $api;
  public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}
	public function __destruct(){}
	private function overwriteConfig($dat){
			$cfg = array();
			$cfg = $this->api->plugin->readYAML($this->path . "config.yml");
			$result = array_merge($cfg, $dat);
			$this->api->plugin->writeYAML($this->path."config.yml", $result);
		}	
	public function init(){
		$this->api->addHandler("player.join", array($this, "Handler"), 5);		
		$this->api->addHandler("player.chat", array($this, "Handler"), 5);
		$this->api->addHandler("player.quit", array($this, "Handler"), 5);
		$this->api->console->register("결혼", "", array($this, "defaultCommands"));
		$this->api->console->register("이혼", "", array($this, "defaultCommands"));
		$this->api->console->register("성별선택", "<남자/여자>", array($this, "defaultCommands"));
		$this->api->console->register("내성별", "", array($this, "defaultCommands"));
		$this->api->console->register("진급", "<초/중/고/대>>", array($this, "defaultCommands"));
		$this->api->console->register("종족선택", "<사람/A/B/C>", array($this, "defaultCommands"));
		$this->path = $this->api->plugin->createConfig($this, array());
	}

	public function Handler(&$data, $event){
		$cfg = $this->api->plugin->readYAML($this->path . "config.yml");
		switch($event){
			case "player.join":
				$target = $data->username;
				if(!array_key_exists($target, $cfg)){
					$this->api->plugin->createConfig($this,array($target => array(
							'종족' => "선택안함",
							'학교' => "어린이",
							'나이' => "5",
							'성별' => "선택안함",
							'결혼' => "선택안함",
							)));
				}
			//if($cfg[$issuer->username]['결혼'] !== 선택안함){
			//}else{}
				break;
			case "player.chat":
			//if($cfg[$issuer->username]['결혼'] !== 선택안함){
			//}else{}
				break;
			case "player.quit":
			//if($cfg[$issuer->username]['결혼'] !== 선택안함){
			//}else{}
			break;
				}
			}

	public function defaultCommands($cmd, $params, $issuer, $alias){
		$output = "";
		$cfg = $this->api->plugin->readYAML($this->path . "config.yml");
		switch($cmd){
			case "종족선택":
				switch($params[0]){
			default:
					$output .= "사용법:/종족선택  <사람/A/B/C>\n";
					break;
			case "":
					$output .= "사용법:/종족선택  <사람/A/B/C>\n";
					break;
			case "사람":
				if($cfg[$issuer->username]['종족'] !== 선택안함){
					$output .= "[LifeEX]당신은 이미 성별을 선택하셧습니다\n";
					break;					
				}else{
					$cfg[$issuer->username]['종족'];
					$output  .= "[LifeEX]당신은 사람입니다\n";
					break;
				}
				break;
			case "A":
				if($cfg[$issuer->username]['종족'] !== 선택안함){
					$output .= "[LifeEX]당신은 이미 종족을 선택하셧습니다\n";
					break;					
				}else{
					$cfg[$issuer->username]['종족'];
					$output  .= "[LifeEX]당신은 A입니다\n";
					break;
				}
					break;
			case "B":
				if($cfg[$issuer->username]['종족'] !== 선택안함){
					$output .= "[LifeEX]당신은 이미 종족을 선택하셧습니다\n";
					break;					
				}else{
					$cfg[$issuer->username]['종족'];
					$output  .= "[LifeEX]당신은 B입니다\n";
					break;
				}
					break;
			case "C":
				if($cfg[$issuer->username]['종족'] !== 선택안함){
					$output .= "[LifeEX]당신은 이미 종족을 선택하셧습니다\n";
					break;					
				}else{
					$cfg[$issuer->username]['종족'];
					$output  .= "[LifeEX]당신은 C입니다\n";
					break;
				}
					break;
			}
					break;
			case "성별선택":
				switch($params[0]){
			default:
				$output .="[LifeEX]성별은 남자 또는 여자밖에 없잖아요\n";
				break;
			case "":
					$output .= "사용법:/성별선택  <남자/여자>\n";
					break;
			case "여자":
				if($cfg[$issuer->username]['성별'] !== 선택안함){
					$output .= "[LifeEX]당신은 이미 성별을 선택하셧습니다\n";
					break;					
				}else{
					$cfg[$issuer->username]['성별'];
					$output  .= "[LifeEX]당신은 여자입니다\n";
					break;
				}
				break;
			case "남자":
				if($cfg[$issuer->username]['성별'] !== 선택안함){
					$output .= "[LifeEX]당신은 이미 성별을 선택하셧습니다\n";
					break;					
				}else{
					$cfg[$issuer->username]['성별'] = '남자';
					$output  .= "[LifeEX]당신은 남자입니다\n";
					break;
				}
			}
					break;
			case "진급":
				switch($params[0]){
			default:
					$output .= "사용법:/진급  <초/중/고/대>\n";
					break;
			case "초":
				if($cfg[$issuer->username]['나이'] < 7){
					$output  .= "[LifeEX]아직 어려서 초등학교에 입학할수 없어요\n";
					break;
				}else if($cfg[$issuer->username]['학교'] === 어린이 and $cfg[$issuer->username]['나이'] >= 7){
					$cfg[$issuer->username]['학교'] = '초등학교';
					$output  .= "[LifeEX]당신은 이제 초등학생입니다! 입학 축하드려요 ^_^\n";
					break;
				}else if($cfg[$issuer->username]['학교'] === 중학생){
					$output .= "[LifeEX]중학생 이시면서 초등학교 다시오실려구요?\n";
					break;					
				}else if($cfg[$issuer->username]['학교'] === 초등학생){
					$output  .= "[LifeEX]당신은 이미 초등학생입니다\n";
					break;
				}else if($cfg[$issuer->username]['학교'] === 고등학생){
					$output .= "[LifeEX]고등학생 이시면서 초등학교 다시오실려구요?\n";
					break;					
				}else if($cfg[$issuer->username]['학교'] === 대학생){
					$output .= "[LifeEX]대학생 이시면서 초등학교 다시오실려구요?\n";
					break;					
				}
				break;
			case "중":
				if($cfg[$issuer->username]['나이'] < 13){
					$output  .= "[LifeEX]아직 어려서 중학교에 입학할수 없어요\n";
					break;
				}else if($cfg[$issuer->username]['학교'] === 어린이){
					$output  .= "[LifeEX]초등학교부터 입학하셔야죠\n";
					break;
				}else if($cfg[$issuer->username]['학교'] === 초등학생 and $cfg[$issuer->username]['나이'] >= 13){
					$cfg[$issuer->username]['학교'] = '중학교';
					$output  .= "[LifeEX]당신은 이제 중학생입니다! 입학 축하드려요 ^_^\n";
					break;
				}else if($cfg[$issuer->username]['학교'] === 중학생){
					$output  .= "[LifeEX]당신은 이미 중학생입니다\n";
					break;
				}else if($cfg[$issuer->username]['학교'] === 고등학생){
					$output  .= "[LifeEX]$school 이시면서 초등학교 다시오실려구요?\n";
					break;
				}else if($cfg[$issuer->username]['학교'] === 대학생){
					$output .= "[LifeEX]대학생 이시면서 중학교 다시오실려구요?\n";
					break;					
				}
					break;
			case "고":
				if($cfg[$issuer->username]['나이'] < 16){
					$output  .= "[LifeEX]아직 어려서 고등학교에 입학할수 없어요\n";
					break;
				}else if($cfg[$issuer->username]['학교'] === 어린이){
					$output  .= "[LifeEX]초등학교부터 입학하셔야죠\n";
					break;
				}else if($cfg[$issuer->username]['학교'] === 초등학생){
					$output  .= "[LifeEX]중학교부터 입학하셔야죠\n";
					break;
				}else if($cfg[$issuer->username]['학교'] === 중학생 and $cfg[$issuer->username]['나이'] >= 16){
					$cfg[$issuer->username]['학교'] = '고등학교';
					$output  .= "[LifeEX]당신은 이제 고등학생입니다! 입학 축하드려요 ^_^\n";
					break;
				}else if($cfg[$issuer->username]['학교'] === 고등학생){
					$output  .= "[LifeEX]당신은 이미 고등학생입니다\n";
					break;
				}else if($cfg[$issuer->username]['학교'] === 대학생){
					$output .= "[LifeEX]대학생 이시면서 고등학교 다시오실려구요?\n";
					break;					
				}
					break;
			case "대":
				if($cfg[$issuer->username]['나이'] < 19){
					$output  .= "[LifeEX]아직 어려서 중학교에 입학할수 없어요\n";
					break;
				}else if($cfg[$issuer->username]['학교'] === 어린이){
					$output  .= "[LifeEX]초등학교부터 입학하셔야죠\n";
					break;
				}else if($cfg[$issuer->username]['학교'] === 초등학생){
					$output  .= "[LifeEX]중학교부터 입학하셔야죠\n";
					break;
				}else if($cfg[$issuer->username]['학교'] === 중학생){
					$output  .= "[LifeEX]고등학교부터 입학하셔야죠\n";
					break;
				}else if($cfg[$issuer->username]['학교'] === 고등학생 and $cfg[$issuer->username]['나이'] >= 19){
					$cfg[$issuer->username]['학교'] = '대학교';
					$output  .= "[LifeEX]당신은 이제 대학생입니다! 입학 축하드려요 ^_^\n";
					break;
				}else if($cfg[$issuer->username]['학교'] === 대학생){
					$output  .= "[LifeEX]당신은 이미 대학생입니다\n";
					break;
				}
					break;
			case "내성별":
				if($cfg[$issuer->username]['성별'] === 선택안함){
					$output .= "[LifeEX]먼저 성별을 선택해주세요\n";
					break;				
				}else{
					$gender = $cfg[$issuer->username]['성별'];
					$output .= "[LifeEX]당신은 $gender 입니다\n";
					break;
				}
				break;
			case "결혼":
				$output .= "[LifeEX]현재 결혼은 아직 준비중입니다";
				break;
			case "이혼":
				$output .= "[LifeEX]현재 이혼은 아직 준비중입니다";
				break;
				}
			}
			 return $output;
			}
		}
