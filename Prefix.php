<?php

/*
__PocketMine Plugin__
name=Prefix
description=
version=1.0 Major
author=Milphy
class=Prefix
apiversion=9,10
*/


class Prefix implements Plugin{

	private $api;
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}

	public function __destruct(){}
	public function init(){
		$this->api->addHandler("player.chat", array($this, "han"), 5);
		$this->api->addHandler("player.join", array($this, "han"), 5);
		$this->api->console->register("prefix", "", array($this, "Commands"));
		$this->config = new Config($this->api->plugin->configPath($this) . "config.yml", CONFIG_YAML);
	}
	
	public function han($data, $event){
		switch($event){
			case "player.chat":
				$player = $this->api->player->get($data["player"]);
				$play = $player->username;
				$data = array("player" => $data["player"], "message" => str_replace(array("{DISPLAYNAME}", "{MESSAGE}", "{WORLDNAME}", "{PREFIX}"), array($data["player"]->username, $data["message"], $data["player"]->level->getName(), $this->config->get("$play")["prefix"]), "[{PREFIX}]{DISPLAYNAME} | {MESSAGE}"));
				$this->api->chat->broadcast($data["message"]);
				return false;
			case "player.join":
				$target = $data->username;
				if(!$this->config->exists($target) and !$this->api->ban->isOp($data->username)){
					$this->config->set($target, array('prefix' => "밀피"));
				}else if(!$this->config->exists($target) and $this->api->ban->isOp($data->username)){
					$this->config->set($target, array('prefix' => "GM"));
				}
				$this->config->save();
				break;
		}
	}

	public function Commands($cmd, $params, $issuer, $alias){
		$output = "";
		switch($cmd){
			case "prefix":
					$param0 = $params[0];
					$param1 = $params[1];
					if($param0 === "" or $param1 === ""){
						$issuer->sendChat("사용법: /$cmd <player> <prefix>\n");
						break;
					}else if($param0 !== "" and $param1 !== ""){
						$user = $this->api->player->get($param0);
						$target = $user->username;
						$issuer->sendChat("[Prefix]당신의 Prefix:$param1");
						$this->config->set($target, array('prefix' => $param1));
						$this->config->save();
						break;
				}
			}
		}
	}
