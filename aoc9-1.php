<?php

require_once __DIR__ . '/aoc9-common.php';

/**
 * Part 1: Defragment per block
 */
class PerBlockDefragmenter implements DefragmentationMethod
{
    private function gapsExist(Filesystem $filesystem): bool
    {
        $gaps = 0;
        $partitionCount = count($filesystem->getPartition());

        for ($i = 0; $i < ($partitionCount - 1); $i++) {
            if ($filesystem->getSector($i) instanceof File && $filesystem->getSector($i + 1) instanceof FreeSpace) {
                $gaps++;
                if ($gaps > 1) return true;
            }
        }

        return false;
    }

    public function defragment(Filesystem $filesystem): void
    {
        $free = $filesystem->getFreeSpace();
        $files = $filesystem->getFiles();

        while ($this->gapsExist($filesystem)) {
            $freeData = reset($free);
            $freeIdx = key($free);
            unset($free[$freeIdx]);
            $fileData = end($files);
            $fileIdx = key($files);
            unset($files[$fileIdx]);
            $filesystem->setSector($freeIdx, $fileData);
            $filesystem->setSector($fileIdx, $freeData);
        }
    }
}

$filesystem = new Filesystem(__DIR__ . '/day9.txt', new PerBlockDefragmenter());

echo $filesystem->defragment()->getChecksum() . "\n";