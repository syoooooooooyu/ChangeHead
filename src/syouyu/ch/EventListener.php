<?php

declare(strict_types=1);

namespace syouyu\ch;

use pocketmine\entity\Human;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemTransactionData;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class EventListener implements Listener{

	public function onJoin(PlayerJoinEvent $event){
		$name = $event->getPlayer()->getName();
		Skin::getInstance()->$name = $event->getPlayer()->getSkin()->getSkinData();
	}

	public function onQuit(PlayerQuitEvent $event){
		$name = $event->getPlayer()->getName();
		unset(Skin::getInstance()->$name);
	}

	public function receive(DataPacketReceiveEvent $event){
		$pk = $event->getPacket();
		$player = $event->getOrigin()->getPlayer();
		if(!$player instanceof Player) return;
		if(!$pk instanceof InventoryTransactionPacket) return;
		$tr = $pk->trData;
		if($tr instanceof UseItemOnEntityTransactionData or $tr instanceof UseItemTransactionData){
			$item = $player->getInventory()->getItemInHand();
			if(($tag = $item->getNamedTag()->getTag("ch-name")) === null) return;
			$skin = $player->getSkin();
			$data = Main::getInstance()->onGenerate($player, $tag->getValue());
			if($data === null){
				$player->sendTip(TextFormat::RED."エラーが発生しました。");
				return;
			}
			$player->setSkin(new \pocketmine\entity\Skin($skin->getSkinId(), $data, $skin->getCapeData(), $skin->getGeometryName(), $skin->getGeometryData()));
			$player->sendTip(TextFormat::YELLOW.$tag->getValue()."の頭を装備しました。");
		}
	}
}