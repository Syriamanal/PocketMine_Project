<?php

/*
__PocketMine Plugin__
name=LifeEX
description=Make by SXBox
version=0.1
author=SXBox
class=LifeEX
apiversion=8,9,10,11
*/
/* 
업데이트 내역
=============
[Plugin]0.1
최초 릴리스
*/

class LifeEX implements Plugin{

  private $api;
  public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}
	public function __destruct(){}
	public function init(){
		$this->api->addHandler("player.join", array($this, "Handler"), 5);		
		$this->api->addHandler("player.quit", array($this, "Handler"), 5);
		$this->api->addHandler("player.chat", array($this, "Handler"), 5);
		$this->api->console->register("marry", "", array($this, "defaultCommands"));
		$this->api->console->register("chgen", "", array($this, "defaultCommands"));
		$this->api->console->register("mygen", "", array($this, "defaultCommands"));
		$this->readConfig();
	}
	
	public function readConfig(){
			if(!file_exists(DATA_PATH."/plugins/LifeEX/chat.yml")){
			console("[ERROR] \"chat.yml\" file not found!");
		}else{
			$this->lang = new Config(DATA_PATH."/plugins/LifeEX/chat.yml", CONFIG_YAML);
		}
		if(is_dir("./plugins/LifeEX/Data/") === false){
			mkdir("./plugins/LifeEX/Data/");
		}
	}
	
	public function Handler(&$data, $event){
		switch($event){
			case "player.join":
					$spawn = $data->level->getSpawn();
					$this->data[$data->iusername] = new Config(DATA_PATH."/plugins/LifeEX/Data/".$data->iusername.".yml", CONFIG_YAML, array(
									'userjob' => "People",
									'school' => "Elementary",
									'year' => "7",
									'gender' => "Male",
									'marry' => false,
					));
				break;
			case "player.quit":
				if($this->data[$data->iusername] instanceof Config){
					$this->data[$data->iusername]->save();
				}
				break;
			case "player.chat":
				break;
				}
			}

	public function defaultCommands($cmd, $params, $issuer, $alias){
		$output = "";
		switch($cmd){
			case "chgen":
				if($params[0] == ""){
					$output .= "Usage: /$cmd <player>\n";
					break;
				}
				$target = $this->api->player->get($params[0]);
				if($target === false){
					$output = "[LifeEX]Player not found";
					break;
				}
				if($this->data[$target->iusername]->get("gender") === Female){
					$target->sendChat($this->getMessage("yourmale"));
					$this->data[$target->iusername]->set("gender", Male);
				}else{
					$target->sendChat($this->getMessage("yourfemale"));
					$this->data[$target->iusername]->set("gender", Female);
				}
				break;
			case "marry":
				if($params[0] == ""){
					$output .= "Usage: /$cmd <player>\n";
					break;
				}
				$target = $this->api->player->get($params[0]);
				if($target === false){
					$output = "[LifeEX]Player not found";
					break;
				}
				if($this->data[$target->iusername]->get("marry") === false){
					$target->sendChat($this->getMessage("youmarry"));
					$this->data[$target->iusername]->set("marry", true);
				}else{
					$target->sendChat($this->getMessage("youdivorce"));
					$this->data[$target->iusername]->set("marry", false);
				}
				break;
			case "mygen":
				if($params[0] == ""){
					$output .= "Usage: /$cmd <player>\n";
					break;
				}
				$target = $this->api->player->get($params[0]);
				if($target === false){
					$output = "[LifeEX]Player not found";
					break;
				}
				if($this->data[$target->iusername]->get("gender") === Male){
					$target->sendChat($this->getMessage("yourmale"));
				}else{
					$target->sendChat($this->getMessage("yourfemale"));
				}
				break;
			}
		}	

	public function getMessage($msg, $params = array("%1", "%2", "%3", "%4")){
		$msgs = array_merge($this->lang->get("LifeEX"));
		if(!isset($msgs[$msg])){
			return $this->getMessage("noMessages", array($msg));
		}
		return str_replace(array("%1", "%2", "%3", "%4"), array($params[0], $params[1], $params[2], $params[3]), $msgs[$msg])."\n";
	}
}
