<?php

declare(strict_types=1);

namespace src\syouyu\ChangeHead;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener{

	protected function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onJoin(PlayerJoinEvent $event){
		$skin = $event->getPlayer()->getSkin();
		$data = $skin->getSkinData();
		var_dump($data);
	}
}