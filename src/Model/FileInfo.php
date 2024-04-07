<?php

namespace BitrixModels\Model;

class FileInfo implements \JsonSerializable
{
    protected ?string $link;
    protected ?string $extension;
    protected ?string $formatSize;
    protected ?int $size;
    protected ?string $originalName;

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): void
    {
        $this->originalName = $originalName;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): void
    {
        $this->size = $size;
    }

    public function getFormatSize(): ?string
    {
        return $this->formatSize;
    }

    public function setFormatSize(?string $formatSize): void
    {
        $this->formatSize = $formatSize;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): void
    {
        $this->extension = $extension;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): void
    {
        $this->link = $link;
    }

    public function jsonSerialize(): array
    {
        return [
            'link' => $this->link,
            'extension' => $this->extension,
            'formatSize' => $this->formatSize,
            'size' => $this->size,
            'originalName' => $this->originalName,
        ];
    }
}
