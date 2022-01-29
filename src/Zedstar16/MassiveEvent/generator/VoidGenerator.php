<?php

namespace Zedstar16\MassiveEvent\generator;

use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;
use pocketmine\world\generator\Generator;

class VoidGenerator extends Generator
{

    /** @var ChunkManager */
    protected ChunkManager $level;
    /** @var Random */
    protected $random;

    public function __construct(int $seed, string $preset)
    {
        parent::__construct($seed, $preset);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return "void";
    }

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void
    {

    }

    public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void
    {
    }

}