<?php

class Sector {}
class FreeSpace extends Sector {}

class File extends Sector 
{
    public function __construct(private int $id) {}

    public function getId(): int
    {
        return $this->id;
    }
}

interface DefragmentationMethod
{
    public function defragment(Filesystem $filesystem): void;
}

class Filesystem
{
    private array $partition = [];

    public function __construct(
        string $filename, 
        private DefragmentationMethod $defragmenter
    ) {
       $this->buildPartition($filename);
    }

    private function buildPartition(string $filename): void
    {
        $disk = str_split(file_get_contents($filename));
        $diskLength = count($disk);
        $isFileLength = false;
        $fileId = -1;
        
        for ($i = 0; $i < $diskLength; $i++) {
            $isFileLength = !$isFileLength;
            $fileId += (int) $isFileLength;
            $value = $disk[$i];
        
            for ($j = 0; $j < $value; $j++) {
                $index = $i + $j;
                $this->partition[] = $isFileLength ? new File($fileId) : new FreeSpace();
            }
        }
    }

    public function getFiles(): array
    {
        return array_filter($this->partition, fn (Sector $sector) => $sector instanceof File);
    }

    public function getFreeSpace(): array
    {
        return array_filter($this->partition, fn (Sector $sector) => $sector instanceof FreeSpace);
    }

    public function getPartition(): array
    {
        return $this->partition;
    }

    public function getSector(int $index): ?Sector
    {
        return $this->partition[$index] ?? null;
    }

    public function setSector(int $index, Sector $sector): self
    {
        $this->partition[$index] = $sector;

        return $this;
    }

    public function defragment(): self
    {
        $this->defragmenter->defragment($this);

        return $this;
    }

    public function getChecksum(): int
    {
        $sum = 0;

        foreach ($this->getFiles() as $index => $file) {
            $sum += ($index * $file->getId());
        }

        return $sum;
    }
}
