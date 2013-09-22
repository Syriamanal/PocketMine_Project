<?php

/*
__PocketMine Plugin__
name=SetSlot
description=
version=1.0 Major
author=Milphy
class=SetSlot
apiversion=9,10
*/


class SetSlot implements Plugin{

	private $api;
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}

	public function __destruct(){}
	public function init(){
		$this->api->console->register("setslot", "", array($this, "Commands"));
		}
		
	public function Commands($cmd, $params, $issuer, $alias){
		$output = "";
		switch($cmd){
			case "setslot":
					$param0 = $params[0];
					$param1 = $params[1];
					if($param0 === "" or $param1 === ""){
						$issuer->sendChat("사용법: /$cmd <슬롯> <아이템>\n");
						break;
					}else if($issuer->gamemode === CREATIVE and $param0 !== "" and $param1 !== ""){
						$issuer->setSlot($param, BlockAPI::fromString($param1));
						break;
				}
			}
		}
	}
