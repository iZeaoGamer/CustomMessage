<?php
 
namespace Assassiner354\CustomMessage;

use pocketmine\plugin\PluginBase;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerPreLoginEvent;

use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

class Main extends PluginBase implements Listener {
	
	public function onEnable(){
        $this->getLogger()->info("Custom Whitelist Reason enabled by Assassiner354");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->getResource("config.yml");
    }    
    public function onPreLogin(PlayerPreLoginEvent $event) {
		$cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
		$reason = $cfg->get("whitelist.reason");
	    	
		$player = $event->getPlayer();
		$name = $player->getName();
	    
		if($cfg->get("custom-whitelist") == true){
		if(!$player->isWhitelisted($name)) {
			$whitelistedMessage = str_replace(["{reason}", "{line}", "&"], [$reason, "\n", ""], $cfg->get("whitelist.message"));
			$whitelistedMessage = str_replace(["{line}", "&"], ["\n", ""], $cfg->get("whitelist.reason")); //To-do see if this method works.
			$player->close("", $whitelistedMessage);
		} else {
			if($cfg->get("custom-whitelist") == false){
				if(!$player->isWhitelisted($name)){
					$player->kick("", $event->getKickMessage());
		}
	    //Custom banned system:
		if($cfg->get("custom-ban") == true){
	     $banList = $player->getServer()->getNameBans();
	        if ($banList->isBanned(strtolower($player->getName()))) {
	    $banEntry = $banList->getEntries();
            $entry = $banEntry[strtolower($player->getName())];
                $reason = $entry->getReason();
                if ($reason != null || $reason != "") {
                       $bannedMessage = str_replace(["{line}", "&", "{reason}"], ["\n", "", $reason], $cfg->get("banned.message")); 
		} else {
			$bannedMessage = str_replace(["{line}", "&"], ["\n", ""], $cfg->get("no.banned.reason.message"));
			$player->close("", $bannedMessage);
                }
			} else {
				if($cfg->get("custom-ban") == false){
					$banList = $player->getServer()->getNameBans();
	        if ($banList->isBanned(strtolower($player->getName()))) {
	             $banEntry = $banList->getEntries();
            $entry = $banEntry[strtolower($player->getName())];
                $reason = $entry->getReason();
                if ($reason != null || $reason != "") {
					$player->kick("", $event->getKickMessage());
				}
			}
		}
			}
		}
		}
		}
    }
}
}