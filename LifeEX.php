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
				$output .= "Choose how :/ <Elyos, Asmodians> <M, F>\n";
				break;
			default:
				$issuer->sendChat("Choose how :/ <Elyos, Asmodians> <M, F>\n");
				break;
			case "Elyos":
				switch($params[1]){
			case "":
				$output .= "Choose how Elyos :/ <M, F>";
				break;
			default:
				$output .= "Choose how Elyos :/ <M, F>";
				break;
			case "Female":
				if($this->data[$issuer->username]->get("Sex") !== Blank){
					$output .= "[LifeEX]If you already have the sex and race selection hasyeotseup\n";
					break;					
				}else{
					$this->data[$issuer->username]->set("Sex", Woman);
					$this->data[$issuer->username]->set("Tribe", Elyos);
					$output  .= "[LifeEX]Gender: Female Race: Elyos \ n [LifeEX] has been completed, select\n";
					break;
				}
				break;
			case "Man":
				if($this->data[$issuer->username]->get("Sex") !== Blank){
					$output .= "[LifeEX]If you already have the sex and race selection hasyeotseup\n";
					break;					
				}else{
					$this->data[$issuer->username]->set("Sex", Man);
					$this->data[$issuer->username]->set("Tribe", Elyos);
					$output  .= "[LifeEX]Gender: Male Race: Elyos \ n [LifeEX] has been completed, select\n";
					break;
				}
				break;
				}
				break;
			case "Asmodians":
				switch($params[1]){
			case "":
				$output .= "Choose how Elyos :/ <M, F>";
				break;
			default:
				$output .= "Choose how Elyos :/ <M, F>";
			case "Female":
				if($this->data[$issuer->username]->get("Sex") !== Blank){
					$output .= "[LifeEX]If you already have the sex and race selection hasyeotseup\n";
					break;					
				}else{
					$this->data[$issuer->username]->set("Sex", Woman);
					$this->data[$issuer->username]->set("Tribe", Asmodians);
					$output  .= "[LifeEX]Gender: Female Race: Elyos \ n [LifeEX] has been completed, select";
					break;
				}
				break;
			case "Man":
				if($this->data[$issuer->username]->get("Sex") !== Blank){
					$output .= "[LifeEX]If you already have the sex and race selection hasyeotseup\n";
					break;					
				}else{
					$this->data[$issuer->username]->set("Sex", Man);
					$this->data[$issuer->username]->set("Tribe", Asmodians);
					$output  .= "[LifeEX]Gender: Male Race: Elyos \ n [LifeEX] has been completed, select\n";
					break;
				}
				break;
				}
				break;
				}
				break;
			case "Tendency":
				$parm = $this->api->player->get($params[0]);
				if($params[0] == ""){
					$Sex = $this->data[$issuer->username]->get("Sex");
					$Age = $this->data[$issuer->username]->get("Age");
					$School = $this->data[$issuer->username]->get("School");
					$Marriage = $this->data[$issuer->username]->get("Marriage");
					$Tribe = $this->data[$issuer->username]->get("Tribe");
					$Job = $this->data[$issuer->username]->get("Job");
				if($this->data[$issuer->username]->get("Sex") === Blank){
					$output .= "[LifeEX]Please select a gender and race \ n select your choice :/ <Elyos, Asmodians> <M, F>\n";
					break;
				}else{
					$output .= "[LifeEX]Species: $ species Gender: $ gender Age: $ age \ n [LifeEX] ". $ School" school Occupation: Occupation Marital $ $ marriage\n";
					break;				
				}
					break;
				}else if($parm === false){
					$output .= "[LifeEX]If you do not want no minute or shut up connection";
					break;
				}else{
					$Sex = $this->data[$parm->username]->get("Sex");
					$Age = $this->data[$parm->username]->get("Age");
					$School = $this->data[$parm->username]->get("School");
					$Marriage = $this->data[$parm->username]->get("Marriage");
					$Tribe = $this->data[$parm->username]->get("Tribe");
					$Job = $this->data[$parm->username]->get("Job");
				if($this->data[$parm->username]->get("Sex") === Blank){
					$output .= "[LifeEX]Dealing with gender and race was not selected\n";
					break;
				}else{
					$output .= "[LifeEX]Species: $ species Gender: $ gender Age: $ age \ n [LifeEX] ". $ School" school Occupation: Occupation Marital $ $ marriage\n";
					break;				
				}
				}
				break;
			case "Marriage":
				if($params[0] == ""){
					$output .= "How to use: /$cmd <Opponent>\n";
					break;
				}
				$parm = $this->api->player->get($params[0]);
				if($parm === false){
					$output .= "[LifeEX]If you do not want no minute or shut up connection";
					break;
				}else if($parm === $issuer){
					$output .= "[LifeEX]And married his own doing?";
					break;
				}else if($this->data[$issuer->username]->get("Age") <= 19){
					$output .= "[LifeEX]Be available from the 20-year-old married";
					break;
				}else if($this->data[$parm->username]->get("Age") <= 19){
					$parmage = $this->data[$parm->username]->get("Age");
					$output .= "[LifeEX]Unfortunately,".$parm."By the ".$parmage."Brunner-year-old";
					break;
				}else if($this->data[$issuer->username]->get("Marriage") !== X){
					$output .= "[LifeEX]I'll marry again?";
					break;
				}else if($this->data[$parm->username]->get("Marriage") !== X){
					$output .= "[LifeEX]Unfortunately, ".$parm."It is already Marital hasyeotseup\n";
					break;
				}else if($this->data[$issuer->username]->get("Sex") === Woman and $this->data[$parm->username]->get("Sex") === Woman){
					$output .= "[LifeEX]Unfortunately, ".$parm."Said the woman.Said the woman.";
					break;
				}else if($this->data[$issuer->username]->get("Sex") === Man and $this->data[$parm->username]->get("Sex") === Man){
					$output .= "[LifeEX]Unfortunately, ".$parm."Said the man.";
					break;
				}else{
					$this->api->chat->broadcast("[LifeEX]Congratulations ".$issuer."By the ".$parm ."S hasyeotseup be married!");
					$this->data[$parm->username]->set("Marriage", $issuer);
					$this->data[$issuer->username]->set("Marriage", $parm);
					break;
				}
				break;
			case "Divorce":
				$data = $this->api->player->get($this->data[$issuer->username]->get("Marriage"));
				if($this->data[$issuer->username]->get("Marriage") === X){
					$output .= "[LifeEX]He has not married a divorce would you do?";
					break;
				}else{
					$this->api->chat->broadcast("[LifeEX]".$issuer."By ".$data."By the divorce hasyeotseup");
					$this->data[$data2->username]->set("Marriage", X);
					$this->data[$issuer->username]->set("Marriage", X);
					break;
				}
				break;
			case "Job":
				if($params[0] == ""){
					$output .= "How to use: /$cmd <Job>\n";
					break;
				}else{
					$Job = $params[0];
					$issuer->sendChat("[LifeEX]You are ".$Job."It is");
					$this->data[$issuer->username]->set("Job", $Job);
					break;
				}
				break;
			}
			 return $output;
			}
		}
