<?php

/*
__PocketMine Plugin__
name=LifeEX
description=Make by SXBox
version=0.1.1
author=SXBox
class=LifeEX
apiversion=8,9,10,11
*/
/* 
업데이트 내역
=============
[Plugin]0.1
최초 릴리스
[Plugin]0.1.1
mygen 작동(원본님)
chgen,marry 소스 변경(추후 구현)
LifeEX/Data 삭제(config.yml로 변경)
LifeEx/chat.yml삭제
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
		$this->api->addHandler("player.quit", array($this, "Handler"), 5);
		$this->api->addHandler("player.chat", array($this, "Handler"), 5);
		$this->api->addHandler("player.death", array($this, "Handler"), 5);
		$this->api->console->register("marry", "", array($this, "defaultCommands"));
		$this->api->console->register("chgen", "", array($this, "defaultCommands"));
		$this->api->console->register("mygen", "", array($this, "defaultCommands"));
		$this->path = $this->api->plugin->createConfig($this, array());
	}
	
	public function Handler(&$data, $event){
		$cfg = $this->api->plugin->readYAML($this->path . "config.yml");
		switch($event){
			case "player.join":
				$target = $data->username;
				if(!array_key_exists($target, $cfg))
				{
					$this->api->plugin->createConfig($this,array(
							$target => array(
									'userjob' => "Human",
									'school' => "Elementary",
									'year' => "7",
									'gender' => "Male",
									'marry' => false,
							)
					));
				}
				break;
			case "player.quit":
				break;
			case "player.chat":
				break;
			case "player.death":
				break;
				}
			}

	public function defaultCommands($cmd, $params, $issuer, $alias){
		$output = "";
		switch($cmd){
			case "chgen":
				$subCommand = $args[0];
				$cfg = $this->api->plugin->readYAML($this->path . "config.yml");
				switch($subCommand){
					case "":
				if(!($issuer instanceof Player)){
					$output .= "Please run this command in-game.\n";
					break;
				}
					break;
					case "male":
				if(!($issuer instanceof Player)){
					$output .= "Please run this command in-game.\n";
					break;
				}
						break;
					case "female":
				if(!($issuer instanceof Player)){
					$output .= "Please run this command in-game.\n";
					break;
				}
			}
			case "marry":
				break;
			case "mygen":
				$cfg = $this->api->plugin->readYAML($this->path . "config.yml");
				if(!($issuer instanceof Player)){
					$output .= "Please run this command in-game.\n";
					break;
				}
				if(!array_key_exists($issuer->username, $cfg)){
						$output .= "[LifeEX]You not human.";
						break;
					}
				$gender = $cfg[$issuer->username]['gender'];
				$output .= "[LifeEX]You are $gender";
				break;
				}
			 return $output;
			}
		}
