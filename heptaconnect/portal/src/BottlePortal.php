<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Playground\Portal;

use Heptacom\HeptaConnect\Dataset\Base\Translatable\TranslatableString;
use Heptacom\HeptaConnect\Playground\Dataset\Bottle;
use Heptacom\HeptaConnect\Playground\Dataset\BottleCollection;
use Heptacom\HeptaConnect\Playground\Dataset\BottleShape;
use Heptacom\HeptaConnect\Playground\Dataset\Cap;
use Heptacom\HeptaConnect\Playground\Dataset\Label;
use Heptacom\HeptaConnect\Playground\Dataset\LabelCollection;
use Heptacom\HeptaConnect\Playground\Dataset\Volume;
use Heptacom\HeptaConnect\Portal\Base\Contract\PortalNodeInterface;
use Heptacom\HeptaConnect\Portal\Base\EmitterCollection;
use Heptacom\HeptaConnect\Portal\Base\ExplorerCollection;
use Heptacom\HeptaConnect\Portal\Base\ReceiverCollection;

class BottlePortal implements PortalNodeInterface
{
    private ?BottleCollection $bottles = null;

    public function getExplorers(): ExplorerCollection
    {
        return new ExplorerCollection([
            new BottleExplorer(),
        ]);
    }

    public function getEmitters(): EmitterCollection
    {
        return new EmitterCollection([
            new BottleEmitter(),
        ]);
    }

    public function getReceivers(): ReceiverCollection
    {
        return new ReceiverCollection([
            new BottleReceiver(),
        ]);
    }

    public function getBottleStorage(): BottleCollection
    {
        if (is_null($this->bottles)) {
            $this->bottles = new BottleCollection([
                $this->generateBottle('pecl', Cap::TYPE_CROWN_CORK, 2, BottleShape::TYPE_ROUND, [
                    $this->generateInternationalLabel('#b1900f', 'Pecl Juice'),
                    $this->generateInternationalLabel('#80600a', 'Refreshing source to experience your inner beauty'),
                ]),
                $this->generateBottle('composer', Cap::TYPE_FLIP_TOP, 1, BottleShape::TYPE_ANGULAR, [
                    $this->generateInternationalLabel('#00ff00', 'Calming composition'),
                    $this->generateInternationalLabel('#ff0000', 'Contains some supplements'),
                ]),
                $this->generateBottle('psysh', Cap::TYPE_SCREW, 0.05, BottleShape::TYPE_ROUND, [
                    $this->generateInternationalLabel('#ffffff', 'REPL shot'),
                    $this->generateInternationalLabel('#000000', 'POWER POWER POWER'),
                ]),
            ]);
        }

        return $this->bottles;
    }

    private function generateInternationalLabel(string $color, string $text): Label
    {
        $translatable = new TranslatableString();
        $translatable['en'] = $text;

        return (new Label())
            ->setColor($color)
            ->setText($translatable);
    }

    private function generateBottle(string $key, string $cap, float $volumeAmount, string $bottleShape, array $labels): Bottle
    {
        $bottle = new Bottle();
        $bottle->setPrimaryKey($key);

        return $bottle
            ->setCap((new Cap())->setType($cap))
            ->setCapacity((new Volume())->setAmount($volumeAmount, Volume::UNIT_LITER))
            ->setShape((new BottleShape())->setType($bottleShape))
            ->setLabels(new LabelCollection($labels));
    }
}
