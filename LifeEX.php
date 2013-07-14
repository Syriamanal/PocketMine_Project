<?php

/*
__PocketMine Plugin__
name=LifeEX
description=Make by SXBox
version=0.2
author=SXBox
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
LifeEx/chat.yml삭제
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
성별*종족선택->선택 <종족/성별> <사람,A,B,C/남,여>
선택,성향,진급,결혼 구현(종족->사람밖에없음)
결혼 거절은 없습니다(추후 이혼으로 거절하십시오)
나이는 아직 수동적(yml수정)
이제 op가 아니어도 명령어를 사용 가능합니다
=============
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
		$this->api->addHandler("player.spawn", array($this, "Handler"), 6);
		$this->api->ban->cmdWhitelist("결혼");
		$this->api->ban->cmdWhitelist("성향");
		$this->api->ban->cmdWhitelist("진급");
		$this->api->ban->cmdWhitelist("선택");
		$this->api->ban->cmdWhitelist("이혼");
		$this->api->console->register("결혼", "", array($this, "defaultCommands"));
		$this->api->console->register("이혼", "", array($this, "defaultCommands"));
		$this->api->console->register("성향", "", array($this, "defaultCommands"));
		$this->api->console->register("진급", "<초/중/고/대>", array($this, "defaultCommands"));
		$this->api->console->register("선택", "<종족/성별> <사람,A,B,C/남,여>", array($this, "defaultCommands"));
		$this->readConfig();
	}
	
	public function readConfig(){
		$this->path = $this->api->plugin->createConfig($this, array(
			"LifeEX설정" => array(
				"" => "",
			),
		));
		if(is_dir("./plugins/LifeEX/") === false){
			mkdir("./plugins/LifeEX/");
		}
		if(is_dir("./plugins/LifeEX/LifeData/") === false){
			mkdir("./plugins/LifeEX/LifeData/");
		}
	}
	
	public function Handler(&$data, $event){
		switch($event){
			case "player.spawn":
					if($this->data[$data->username]->get("종족") === 선택안함 or $this->data[$data->username]->get("성별") === 선택안함){
					$data->sendChat("[LifeEX]종족 또는 성별을 선택해주세요\n/선택 <종족/성별> <사람,A,B,C/남,여>\n");
						break;
					}else{
					$data->sendChat("[LifeEX]환영합니다.즐거운 시간되세요 ^_^\n");
					}
					break;
			case "player.join":
					$this->data[$data->username] = new Config(DATA_PATH."/plugins/LifeEX/LifeData/".$data->username.".yml", CONFIG_YAML, array(
							"이름" =>  $data->username,
							'종족' => "선택안함",
							'성별' => "선택안함",
							'결혼' => "X",
							'나이' => "5",
							'학교' => "X",
						));
				break;
			case "player.chat":
				break;
			case "player.quit":
				if($this->data[$data->iusername] instanceof Config){
					$this->data[$data->iusername]->save();
				}
				break;
				}
			}
			
	public function defaultCommands($cmd, $params, $issuer, $alias){
		$output = "";
		$cf = $this->api->plugin->readYAML(DATA_PATH."/plugins/PocketMoney/" . "config.yml");
		$cfg = $this->data;
		switch($cmd){
			case "선택":
				switch($params[0]){
			default:
					$output .= "사용법:/선택 <종족/성별> <사람,A,B,C/여,남>\n";
					break;
			case "":
					$output .= "사용법:/선택 <종족/성별> <사람,A,B,C/여,남>\n";
					break;
			case "성별":
				switch($params[1]){
			default:
				$output .="[LifeEX]성별은 남자 또는 여자밖에 없잖아요\n";
				break;
			case "":
					$output .= "사용법:/선택 성별  <남/여>\n";
					break;
			case "여":
				if($cfg[$issuer->username]->get("성별") !== 선택안함){
					$output .= "[LifeEX]당신은 이미 성별을 선택하셧습니다\n";
					break;					
				}else{
					$cfg[$issuer->username]->set("성별", 여자);
					$output  .= "[LifeEX]당신은 여자입니다\n";
					break;
				}
				break;
			case "남":
				if($cfg[$issuer->username]->get("성별") !== 선택안함){
					$output .= "[LifeEX]당신은 이미 성별을 선택하셧습니다\n";
					break;					
				}else{
					$cfg[$issuer->username]->set("성별", 남자);
					$output  .= "[LifeEX]당신은 남자입니다\n";
					break;
				}
					break;
				}
					break;
			case "종족":
				switch($params[1]){
			default:
					$output .= "[LifeEX]종족은 사람,A,B,C 밖에없습니다\n";
					break;
			case "":
					$output .= "사용법:/선택 종족 <사람/A/B/C>\n";
					break;
			case "사람":
				if($cfg[$issuer->username]->get("종족") !== 선택안함){
					$output .= "[LifeEX]당신은 이미 종족을 선택하셧습니다\n";
					break;					
				}else{
					$cfg[$issuer->username]->set("종족", 사람);
					$output  .= "[LifeEX]당신은 사람입니다\n";
					break;
				}
				break;
			case "A":
				if($cfg[$issuer->username]->get("종족") !== 선택안함){
					$output .= "[LifeEX]당신은 이미 종족을 선택하셧습니다\n";
					break;					
				}else{
					$cfg[$issuer->username]->set("종족", A);
					$output  .= "[LifeEX]당신은 A입니다\n";
					break;
				}
				break;
			case "B":
				if($cfg[$issuer->username]->get("종족") !== 선택안함){
					$output .= "[LifeEX]당신은 이미 종족을 선택하셧습니다\n";
					break;					
				}else{
					$cfg[$issuer->username]->set("종족", B);
					$output  .= "[LifeEX]당신은 B입니다\n";
					break;
				}
				break;
			case "C":
				if($cfg[$issuer->username]->get("종족") !== 선택안함){
					$output .= "[LifeEX]당신은 이미 종족을 선택하셧습니다\n";
					break;					
				}else{
					$cfg[$issuer->username]->set("종족", C);
					$output  .= "[LifeEX]당신은 C입니다\n";
					break;
				}
					break;
				}
					break;
				}	
					break;
			case "진급":
				switch($params[0]){
			default:
					$output .= "사용법:/진급  <초/중/고/대>\n";
					break;
			case "초":
				if($cfg[$issuer->username]->get("나이") < 7){
					$output  .= "[LifeEX]아직 어려서 초등학교에 입학할수 없어요\n";
					break;
				}else if($cfg[$issuer->username]->get("학교") === X and $cfg[$issuer->username]->get("나이") >= 7){
					$this->api->entity->drop(new Position($issuer->entity->x - 0.5, $issuer->entity->y, $issuer->entity->z - 0.5, $issuer->entity->level), BlockAPI::getItem("298"));
					$this->api->entity->drop(new Position($issuer->entity->x - 0.5, $issuer->entity->y, $issuer->entity->z - 0.5, $issuer->entity->level), BlockAPI::getItem("299"));
					$this->api->entity->drop(new Position($issuer->entity->x - 0.5, $issuer->entity->y, $issuer->entity->z - 0.5, $issuer->entity->level), BlockAPI::getItem("300"));
					$this->api->entity->drop(new Position($issuer->entity->x - 0.5, $issuer->entity->y, $issuer->entity->z - 0.5, $issuer->entity->level), BlockAPI::getItem("301"));
					$cfg[$issuer->username]->set("학교", 초등학교);
					$output  .= "[LifeEX]당신은 이제 초등학생입니다! 입학 축하드려요 ^_^\n";
					break;
				}else if($cfg[$issuer->username]->get("학교") === 중학생){
					$output .= "[LifeEX]중학생 이시면서 초등학교 다시오실려구요?\n";
					break;					
				}else if($cfg[$issuer->username]->get("학교") === 초등학생){
					$output  .= "[LifeEX]당신은 이미 초등학생입니다\n";
					break;
				}else if($cfg[$issuer->username]->get("학교") === 고등학생){
					$output .= "[LifeEX]고등학생 이시면서 초등학교 다시오실려구요?\n";
					break;					
				}else if($cfg[$issuer->username]->get("학교") === 대학생){
					$output .= "[LifeEX]대학생 이시면서 초등학교 다시오실려구요?\n";
					break;					
				}
				break;
			case "중":
				if($cfg[$issuer->username]->get("나이") < 13){
					$output  .= "[LifeEX]아직 어려서 중학교에 입학할수 없어요\n";
					break;
				}else if($cfg[$issuer->username]->get("학교") === X){
					$output  .= "[LifeEX]초등학교부터 입학하셔야죠\n";
					break;
				}else if($cfg[$issuer->username]->get("학교") === 초등학생 and $cfg[$issuer->username]->get("나이") >= 13){
					$this->api->entity->drop(new Position($issuer->entity->x - 0.5, $issuer->entity->y, $issuer->entity->z - 0.5, $issuer->entity->level), BlockAPI::getItem("306"));
					$this->api->entity->drop(new Position($issuer->entity->x - 0.5, $issuer->entity->y, $issuer->entity->z - 0.5, $issuer->entity->level), BlockAPI::getItem("307"));
					$this->api->entity->drop(new Position($issuer->entity->x - 0.5, $issuer->entity->y, $issuer->entity->z - 0.5, $issuer->entity->level), BlockAPI::getItem("308"));
					$this->api->entity->drop(new Position($issuer->entity->x - 0.5, $issuer->entity->y, $issuer->entity->z - 0.5, $issuer->entity->level), BlockAPI::getItem("309"));
					$cfg[$issuer->username]->set("학교", 중학교);
					$output  .= "[LifeEX]당신은 이제 중학생입니다! 입학 축하드려요 ^_^\n";
					break;
				}else if($cfg[$issuer->username]->get("학교") === 중학생){
					$output  .= "[LifeEX]당신은 이미 중학생입니다\n";
					break;
				}else if($cfg[$issuer->username]->get("학교") === 고등학생){
					$output  .= "[LifeEX]$school 이시면서 초등학교 다시오실려구요?\n";
					break;
				}else if($cfg[$issuer->username]->get("학교") === 대학생){
					$output .= "[LifeEX]대학생 이시면서 중학교 다시오실려구요?\n";
					break;					
				}
					break;
			case "고":
				if($cfg[$issuer->username]->get("나이") < 16){
					$output  .= "[LifeEX]아직 어려서 고등학교에 입학할수 없어요\n";
					break;
				}else if($cfg[$issuer->username]->get("학교") === X){
					$output  .= "[LifeEX]초등학교부터 입학하셔야죠\n";
					break;
				}else if($cfg[$issuer->username]->get("학교") === 초등학생){
					$output  .= "[LifeEX]중학교부터 입학하셔야죠\n";
					break;
				}else if($cfg[$issuer->username]->get("학교") === 중학생 and $cfg[$issuer->username]->get("나이") >= 16){
					$this->api->entity->drop(new Position($issuer->entity->x - 0.5, $issuer->entity->y, $issuer->entity->z - 0.5, $issuer->entity->level), BlockAPI::getItem("314"));
					$this->api->entity->drop(new Position($issuer->entity->x - 0.5, $issuer->entity->y, $issuer->entity->z - 0.5, $issuer->entity->level), BlockAPI::getItem("315"));
					$this->api->entity->drop(new Position($issuer->entity->x - 0.5, $issuer->entity->y, $issuer->entity->z - 0.5, $issuer->entity->level), BlockAPI::getItem("316"));
					$this->api->entity->drop(new Position($issuer->entity->x - 0.5, $issuer->entity->y, $issuer->entity->z - 0.5, $issuer->entity->level), BlockAPI::getItem("317"));
					$cfg[$issuer->username]->set("학교", 고등학교);
					$output  .= "[LifeEX]당신은 이제 고등학생입니다! 입학 축하드려요 ^_^\n";
					break;
				}else if($cfg[$issuer->username]->get("학교") === 고등학생){
					$output  .= "[LifeEX]당신은 이미 고등학생입니다\n";
					break;
				}else if($cfg[$issuer->username]->get("학교") === 대학생){
					$output .= "[LifeEX]대학생 이시면서 고등학교 다시오실려구요?\n";
					break;					
				}
					break;
			case "대":
				if($cfg[$issuer->username]->get("나이") < 19){
					$output  .= "[LifeEX]아직 어려서 대학교에 입학할수 없어요\n";
					break;
				}else if($cfg[$issuer->username]->get("학교") === X){
					$output  .= "[LifeEX]초등학교부터 입학하셔야죠\n";
					break;
				}else if($cfg[$issuer->username]->get("학교") === 초등학생){
					$output  .= "[LifeEX]중학교부터 입학하셔야죠\n";
					break;
				}else if($cfg[$issuer->username]->get("학교") === 중학생){
					$output  .= "[LifeEX]고등학교부터 입학하셔야죠\n";
					break;
				}else if($cfg[$issuer->username]->get("학교") === 고등학생 and $cfg[$issuer->username]->get("나이") >= 19){
					$this->api->entity->drop(new Position($issuer->entity->x - 0.5, $issuer->entity->y, $issuer->entity->z - 0.5, $issuer->entity->level), BlockAPI::getItem("310"));
					$this->api->entity->drop(new Position($issuer->entity->x - 0.5, $issuer->entity->y, $issuer->entity->z - 0.5, $issuer->entity->level), BlockAPI::getItem("311"));
					$this->api->entity->drop(new Position($issuer->entity->x - 0.5, $issuer->entity->y, $issuer->entity->z - 0.5, $issuer->entity->level), BlockAPI::getItem("312"));
					$this->api->entity->drop(new Position($issuer->entity->x - 0.5, $issuer->entity->y, $issuer->entity->z - 0.5, $issuer->entity->level), BlockAPI::getItem("313"));
					$cfg[$issuer->username]->set("학교", 대학교);
					$output  .= "[LifeEX]당신은 이제 대학생입니다! 입학 축하드려요 ^_^\n";
					break;
				}else if($cfg[$issuer->username]['학교'] === 대학생){
					$output  .= "[LifeEX]당신은 이미 대학생입니다\n";
					break;
				}
					break;
				}
				break;
			case "성향":
				if($cfg[$issuer->username]->get("종족") === 선택안함 or $cfg[$issuer->username]->get("성별") === 선택안함){
					$output .= "[LifeEX]종족 또는 성별을 선택해주세요\n/선택 <종족/성별> <사람,A,B,C/남,여>\n";
						break;
				}else if($cfg[$issuer->username]->get("결혼") === X){
					$성별 = $cfg[$issuer->username]->get("성별");
					$나이 = $cfg[$issuer->username]->get("나이");
					$학교 = $cfg[$issuer->username]->get("학교");
					$결혼 = $cfg[$issuer->username]->get("결혼");
					$종족 = $cfg[$issuer->username]->get("종족");
					$output .= "[LifeEX]종족:$종족 성별:$성별 나이:$나이 학교:$학교 결혼여부:$결혼\n";
					break;				
				}else if($cfg[$issuer->username]->get("성별") === 여자 and $cfg[$issuer->username]->get("결혼") !== X){
					$성별 = $cfg[$issuer->username]->get("성별");
					$나이 = $cfg[$issuer->username]->get("나이");
					$학교 = $cfg[$issuer->username]->get("학교");
					$결혼 = $cfg[$issuer->username]->get("결혼");
					$종족 = $cfg[$issuer->username]->get("종족");
					$output .= "[LifeEX]종족:$종족 성별:$성별 나이:$나이 학교:$학교 남편:$결혼\n";
					break;
				}else if($cfg[$issuer->username]->get("성별") === 남자 and $cfg[$issuer->username]->get("결혼") !== X){
					$성별 = $cfg[$issuer->username]->get("성별");
					$나이 = $cfg[$issuer->username]->get("나이");
					$학교 = $cfg[$issuer->username]->get("학교");
					$결혼 = $cfg[$issuer->username]->get("결혼");
					$종족 = $cfg[$issuer->username]->get("종족");
					$output .= "[LifeEX]종족:$종족 성별:$성별 나이:$나이 학교:$학교 아내:$결혼\n";
					break;
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
				}
				if($cfg[$parm->username]->get("성별") === 선택안함){
				$output .= "[LifeEX]$parm 님은 아직 성별을 선택하지 않으셧습니다\n";
				break;
				}else if($cfg[$issuer->username]->get("성별") === 선택안함){
				$output .= "[LifeEX]성별을 먼저 선택해주세요\n";
				break;
				}else if($cfg[$issuer->username]->get("나이") <= 19){
				$output .= "[LifeEX]결혼은 20살부터 가능합니다";
				break;
				}else if($cfg[$parm->username]->get("나이") <= 19){
				$output .= "[LifeEX]아쉽지만 $parm 님은 20살이 아니십니다";
				break;
				}else if($cfg[$issuer->username]->get("결혼") !== X){
				$output .= "[LifeEX]또 결혼하시게요?";
				break;
				}else if($cfg[$parm->username]->get("결혼") !== X){
				$output .= "[LifeEX]아쉽지만 $parm 님은 이미 결혼하셧습니다\n";
				break;
				}else if($cfg[$issuer->username]->get("성별") === 여자 and $cfg[$parm->username]->get("성별") === 여자){
				$output .= "[LifeEX]아쉽지만 $parm 님은 여자입니다";
				break;
				}else if($cfg[$issuer->username]->get("성별") === 남자 and $cfg[$parm->username]->get("성별") === 남자){
				$output .= "[LifeEX]아쉽지만 $parm 님은 남자입니다";
				break;
				}else if($cfg[$issuer->username]->get("성별") === 남자 and $cfg[$parm->username]->get("성별") === 여자){
				$output .= "[LifeEX]당신은 $parm 님과 결혼하셧습니다. 축하합니다!";
				$this->api->chat->broadcast("[LifeEX]축하합니다 $issuer 님은 $parm 님과 결혼하셧습니다!");
				$cfg[$parm->username]->set("결혼", $issuer);
				$cfg[$issuer->username]->set("결혼", $parm);
				break;
				}
				break;
			case "이혼":
				$output .= "[LifeEX]현재 이혼은 아직 준비중입니다";
				break;
			}
			 return $output;
			}
		}
