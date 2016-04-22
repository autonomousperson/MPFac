<?php

namespace MPFac;

use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\Player;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\utils\TextFormat;
use pocketmine\scheduler\PluginTask;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\block\BlockPlaceEvent;


class FactionListener implements Listener {
	
	public $plugin;
	
	public function __construct(FactionMain $pg) {
		$this->plugin = $pg;
	}
	
	public function factionPVP(EntityDamageEvent $factionDamage) {
		if($factionDamage instanceof EntityDamageByEntityEvent) {
			if(!($factionDamage->getEntity() instanceof Player) or !($factionDamage->getDamager() instanceof Player)) {
				return true;
			}
			if(($this->plugin->isInFaction($factionDamage->getEntity()->getPlayer()->getName()) == false) or ($this->plugin->isInFaction($factionDamage->getDamager()->getPlayer()->getName()) == false) ) {
				return true;
			}
			if(($factionDamage->getEntity() instanceof Player) and ($factionDamage->getDamager() instanceof Player)) {
				$player1 = $factionDamage->getEntity()->getPlayer()->getName();
				$player2 = $factionDamage->getDamager()->getPlayer()->getName();
				if($this->plugin->sameFaction($player1, $player2) == true) {
					$factionDamage->setCancelled(true);
				}
			}
		}
	}
    public function factionBlockBreakProtect(BlockBreakEvent $event)
    {
        if ($this->plugin->isInPlot($event->getPlayer())) {
            if ($this->plugin->inOwnPlot($event->getPlayer())) {
                return true;
            } elseif ($event->getPlayer()->hasPermission("f.override")) {
                $event->getPlayer()->sendPopup("§b[MPFac] §cADMIN OVERRIDE: Place");
                return true;
            } else
                $event->setCancelled(true);
            $event->getPlayer()->sendPopup("§b[MPFac] §cThis area is already claimed. - Run /f help");
            return true;
        }
    }


    public function factionBlockPlaceProtect(BlockPlaceEvent $event)
    {
        $whosplot = $this->plugin->cplotName($event->getPlayer());
        if ($this->plugin->isInPlot($event->getPlayer())) {
            if ($this->plugin->inOwnPlot($event->getPlayer())) {
                return true;
            } elseif ($event->getPlayer()->hasPermission("f.override")) {
                $event->getPlayer()->sendPopup("§b[MPFac] §cADMIN OVERRIDE: Break");
                return true;
            } else
                $event->setCancelled(true);
            $event->getPlayer()->sendPopup("§b[MPFac] §cThis area is claimed by §b".$whosplot);
            return true;
			}
	}
    
    public function onPlayerMove(PlayerMoveEvent $event)
    {
        $whosplot = $this->plugin->cplotName($event->getPlayer());
        if ($this->plugin->isInPlot($event->getPlayer()))
        {
            if ($this->plugin->inOwnPlot($event->getPlayer())) {
                $event->getPlayer()->sendPopup("§b[MPFac] §aYou have entered your plot!");
                return true;
            } elseif ($event->getPlayer()->hasPermission("f.override")) {
                $event->getPlayer()->sendPopup("§b[MPFac] §cThis area is claimed by §b".$whosplot);
                return true;
            } else
            $event->getPlayer()->sendPopup("§b[MPFac] §cThis area is claimed by §b".$whosplot);
            return true;
		}
	}
    
    public function onPlayerInteract(PlayerInteractEvent $event)
    {
        $whosplot = $this->plugin->cplotName($event->getPlayer());
        if ($this->plugin->isInPlot($event->getPlayer())) {
            if ($this->plugin->inOwnPlot($event->getPlayer())) {
                return true;
            } elseif ($event->getPlayer()->hasPermission("f.override")) {
                $event->getPlayer()->sendPopup("§b[MPFac] §cADMIN OVERRIDE: Interact");
                return true;
            } else
                $event->setCancelled(true);
            $event->getPlayer()->sendPopup("§b[MPFac] §cThis area is claimed by §b".$whosplot);
            return true;
			}
	}
}
    
