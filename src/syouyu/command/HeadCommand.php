<?php

declare(strict_types=1);

namespace syouyu\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\item\VanillaItems;
use pocketmine\lang\Translatable;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use syouyu\ch\Main;

class HeadCommand extends Command{

	public function __construct(string $name = "head", Translatable|string $description = "頭の見た目を変えるアイテムを取得します", Translatable|string|null $usageMessage = null, array $aliases = []){
		parent::__construct($name, $description, $usageMessage, $aliases);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if($sender instanceof ConsoleCommandSender){
			$sender->sendMessage(TextFormat::RED."コンソールから実行はできません。");
			return;
		}
		if($sender instanceof Player){
			if(!Main::getInstance()->canUse($sender)){
				$sender->sendMessage(TextFormat::RED."実行する権限がありません。");
				return;
			}
			if(!isset($args[0])){
				$sender->sendMessage(TextFormat::RED."ファイル名が指定されていません。");
				return;
			}
			if($args[0] === "reset"){
				$name = $sender->getName();
				$sender->setSkin(new Skin($sender->getSkin()->getSkinId(), \syouyu\ch\Skin::getInstance()->$name, $sender->getSkin()->getCapeData(), $sender->getSkin()->getGeometryName(), $sender->getSkin()->getGeometryData()));
				$sender->sendMessage(TextFormat::YELLOW."スキンを戻しました。");
				return;
			}
			$data = Main::getInstance()->onGenerate($sender, $args[0]);
			if($data === null){
				$sender->sendTip(TextFormat::RED."エラーが発生しましsた。");
				return;
			}
			$item = VanillaItems::COMPASS();
			$tag = new CompoundTag();
			$tag->setString("ch-name", $args[0]);
			$item->setNamedTag($tag);
			$item->setCustomName(TextFormat::YELLOW.$args[0]."の頭");
			if(!$sender->getInventory()->canAddItem($item)){
				$sender->sendMessage(TextFormat::RED."アイテムを追加するスペースがありません。");
				return;
			}
			$sender->getInventory()->addItem($item);
			$sender->sendMessage(TextFormat::YELLOW."アイテムを追加しました。");
		}
	}
}