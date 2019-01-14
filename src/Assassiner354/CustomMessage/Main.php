<?php
 
namespace Assassiner354\CustomMessage;

use pocketmine\plugin\PluginBase;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerPreLoginEvent;

use pocketmine\utils\Config;

class Main extends PluginBase implements Listener {
	
	/** Enables the plugin on server startup.
	*
	* @return void
	*/
	public function onEnable(): void{
        $this->getLogger()->info("CustomMessage enabled by Assassiner354");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->getResource("config.yml");
    }
	/** Before a player joins the server. This'll be where: custom-ban, and custom-whitelist will be detected.
	*
	* @return void
	*/
    public function onPreLogin(PlayerPreLoginEvent $event): void {
		$cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
	    	
		$player = $event->getPlayer();
		$name = $player->getName();
	    
		if($cfg->get("custom-whitelist") == true){
		if(!$player->isWhitelisted($name)) {
			$whitelistedMessage = str_replace(["{line}", "&"], ["\n", "§"], $cfg->get("whitelist.message"));
			$whitelistedMessage = str_replace(["{line}", "&"], ["\n", ""], $cfg->get("whitelist.reason"));
        }else{
          $event->setKickMsssage($whitelistedMessage);
          $event->setCancelled(true);
        }
		} else {
			if($cfg->get("custom-whitelist") == false){
				if(!$player->isWhitelisted($name)){
					$event->setKickMessage("Server is white-listed"); //To-Do change methods to make this system look better. 
					$event->setCancelled(true);
					
				}
			}
		}
		if($cfg->get("custom-ban") == true){
        $banList = $player->getServer()->getNameBans();
        if($banList->isBanned(strtolower($player->getName()))){
          $banEntry = $banList->getEntries();
          $entry = $banEntry[strtolower($player->getName())];
          $reason = $entry->getReason();
          if($reason != null || $reason != ""){
            $bannedMessage = str_replace(["{line}", "&", "{reason}"], ["\n", "", $reason], $cfg->get("banned.message")); 
          }else{
            $bannedMessage = str_replace(["{line}", "&"], ["\n", ""], $cfg->get("no.banned.reason.message"));
            $event->setKickMessage($bannedMessage);
            $event->setCancelled(true);
                }
			} else {
				if($cfg->get("custom-ban") == false){
					$banList = $player->getServer()->getNameBans();
	        if ($banList->isBanned(strtolower($player->getName()))) {
	             $banEntry = $banList->getEntries();
            $entry = $banEntry[strtolower($player->getName())];
                $reason = $entry->getReason();
                if ($reason != null || $reason != "") {
					$event->setKickMessage("You are banned"); //To-Do change methods to make this system work better!
            $event->setCancelled(true);
				}
			}
		}
			}
		}
		}
		}