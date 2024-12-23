<?php

require_once __DIR__ . '/aoc9-common.php';

/**
 * Part 2: Defragment per full file
 */
class PerFileDefragmenter implements DefragmentationMethod
{
    public function defragment(Filesystem $filesystem): void
    {
        $fileMap = $this->buildFileMap($filesystem);

        foreach ($fileMap as $fileID => $file) {
            $requiredSpace = $file['size'];
            $indexes = $file['indexes'];
            $firstFileIndex = min($file['indexes']);
        
            for ($x = 0; $x < $firstFileIndex; $x++) {
                if ($this->isFreeSpace($filesystem, $x, $x + $requiredSpace)) {
                    for ($i = $x; $i < ($x + $requiredSpace); $i++) {
                        foreach ($indexes as $fileIndex) {
                            $freeSpace = $filesystem->getSector($i);
                            $file = $filesystem->getSector($fileIndex);
                            $filesystem->setSector($i, $file);
                            $filesystem->setSector($fileIndex, $freeSpace);
                        }
                    }
                    break;
                }
            }
        }
    }

    private function buildFileMap(Filesystem $filesystem): array
    {
        $fullFileMap = [];

        foreach ($filesystem->getFiles() as $index => $file) {
            $fileId = $file->getId();

            if (!isset($fullFileMap[$fileId])) {
                $fullFileMap[$fileId] = [
                    'indexes' => [], 
                    'size' => 0
                ];
            }

            $fullFileMap[$fileId]['indexes'][] = $index;
            $fullFileMap[$fileId]['size']++;
            rsort($fullFileMap[$fileId]['indexes']);
        }

        krsort($fullFileMap);

        return $fullFileMap;
    }

    private function isFreeSpace(Filesystem $filesystem, int $startIndex, int $endIndex): bool
    {
        for ($x = $startIndex; $x < $endIndex; $x++) {
            if ($filesystem->getSector($x) instanceof File) {
                return false;
            }
        }

        return true;
    }
}

$filesystem = new Filesystem(__DIR__ . '/day9.txt', new PerFileDefragmenter());

echo $filesystem->defragment()->getChecksum() . "\n";