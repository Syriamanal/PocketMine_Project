<?php


/*
__PocketMine Plugin__
name=LifeEXSub
description=Made by SXBox
version=0.1.1
author=SXBox
class=LifeSub
apiversion=8,9,10
*/


/* 
업데이트 내역
=============
[Plugin]0.1
최초 릴리스
*/


class LifeSub implements Plugin{


  private $api;
  public function __construct(ServerAPI $api, $server = false){
          $this->api = $api;
	}


	public function init(){
      	foreach($this->api->plugin->getList() as $p){
			if($p["name"] === "LifeEX"){
				$found = true;
				break;
			}
		}
		if(!isset($found)){
			console("[Error] LifeEX plugin not found");
		}
	    $this->path = $this->api->plugin->configPath($this);
		$this->config = new Config($this->path."config.yml", CONFIG_YAML, array(		
		));
		$this->api->addHandler("player.join", array($this, "Handler"), 5);			
	}


	public function Handler(&$data, $event){
		$cfg = $this->api->plugin->readYAML($this->path . "config.yml");
		switch($event){
			case "player.join":
				break;
		}
	}
}

