<?php

declare(strict_types=1);

namespace syouyu\ch;

use pocketmine\entity\InvalidSkinException;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use syouyu\command\HeadCommand;

class Main extends PluginBase{

	private Config $config;

	private static Main $instance;

	public const SKIN_LENGTH_32 = 64 * 32 * 4;
	public const SKIN_LENGTH_64 = 64 * 64 * 4;
	public const SKIN_LENGTH_128 = 128 * 128 * 4;

	protected function onEnable() : void{
		self::$instance = $this;
		new Skin();
		if(!file_exists($this->getDataFolder()."image")) mkdir($this->getDataFolder()."image");
		if(!file_exists($this->getDataFolder()."setting.yml")) file_put_contents($this->getDataFolder()."setting.yml", "# opの値がtrueのときはopのみ、falseのときは全員が使用可能になります。\nop: true");
		$this->config = new Config($this->getDataFolder()."setting.yml", Config::YAML);
		$this->getServer()->getCommandMap()->registerAll("ch-command", [
			new HeadCommand(),
		]);
		$this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
	}

	public static function getInstance(): Main{
		return self::$instance;
	}

	public function canUse(Player $player): bool{
		if(Server::getInstance()->isOp($player->getName())) return true;
		return (string) $this->config->get("op") === "false";
	}

	public function onGenerate(Player $player, string $file): ?string{
		if(!file_exists($this->getDataFolder() ."image/". $file . ".png")) return null;
		$skin = $this->convert($file);
		switch(strlen($skin)){
			case self::SKIN_LENGTH_32:
			case self::SKIN_LENGTH_64:
				$return = $player->getSkin()->getSkinData();
				for($y = 8; $y <= 15; $y++){
					for($x = 0; $x <= 63; $x++){
						$index = ($y * 64 + $x) * 4;
						$return[$index] = $skin[$index];
						$return[$index + 1] = $skin[$index + 1];
						$return[$index + 2] = $skin[$index + 2];
						$return[$index + 3] = $skin[$index + 3];
					}
				}
				for($y = 0; $y <= 7; $y++){
					for($x = 8; $x <= 23; $x++){
						$index = ($y * 64 + $x) * 4;
						$return[$index] = $skin[$index];
						$return[$index + 1] = $skin[$index + 1];
						$return[$index + 2] = $skin[$index + 2];
						$return[$index + 3] = $skin[$index + 3];
					}
				}
				for($y = 0; $y <= 7; $y++){
					for($x = 40; $x <= 55; $x++){
						$index = ($y * 64 + $x) * 4;
						$return[$index] = $skin[$index];
						$return[$index + 1] = $skin[$index + 1];
						$return[$index + 2] = $skin[$index + 2];
						$return[$index + 3] = $skin[$index + 3];
					}
				}
				return $return;
			case self::SKIN_LENGTH_128:
				$return = $player->getSkin()->getSkinData();
				for($y = 16; $y <= 31; $y++){
					for($x = 0; $x <= 127; $x++){
						$index = ($y * 128 + $x) * 4;
						$index64 = (($y/2) * 64 + $x/2) * 4;
						$return[$index] = $skin[$index64];
						$return[$index + 1] = $skin[$index64 + 1];
						$return[$index + 2] = $skin[$index64 + 2];
						$return[$index + 3] = $skin[$index64 + 3];
					}
				}
				for($y = 0; $y <= 15; $y++){
					for($x = 16; $x <= 47; $x++){
						$index = ($y * 128 + $x) * 4;
						$index64 = (($y/2) * 64 + $x/2) * 4;
						$return[$index] = $skin[$index64];
						$return[$index + 1] = $skin[$index64 + 1];
						$return[$index + 2] = $skin[$index64 + 2];
						$return[$index + 3] = $skin[$index64 + 3];
					}
				}
				for($y = 0; $y <= 15; $y++){
					for($x = 80; $x <= 111; $x++){
						$index = ($y * 128 + $x) * 4;
						$index64 = (($y/2) * 64 + $x/2) * 4;
						$return[$index] = $skin[$index64];
						$return[$index + 1] = $skin[$index64 + 1];
						$return[$index + 2] = $skin[$index64 + 2];
						$return[$index + 3] = $skin[$index64 + 3];
					}
				}
				return $return;
		}
		return $skin;
	}

	public function convert(string $file): string{
		$img = imagecreatefrompng($this->getDataFolder() ."image/". $file . ".png");
		$skinBytes = "";
		$heightY = imagesy($img);
		$heightX = imagesx($img);
		for($y = 0; $y < $heightY; ++$y){
			for($x = 0; $x < $heightX; ++$x){
				$color = imagecolorat($img, $x, $y);
				$a = ((~((int) ($color >> 24))) << 1) & 0xff;
				$r = ($color >> 16) & 0xff;
				$g = ($color >> 8) & 0xff;
				$b = $color & 0xff;
				$skinBytes .= chr($r) . chr($g) . chr($b) . chr($a);
			}
		}
		@imagedestroy($img);
		return $skinBytes;
	}
}