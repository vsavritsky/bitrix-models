<?php

namespace BitrixModels\Manager;

use BitrixModels\Entity;
use BitrixModels\Helper\DirectoryHelper;
use BitrixModels\Repository;
use RecursiveDirectoryIterator;

class EntityManager
{
    /** @var DirectoryHelper */
    protected $directoryHelper;

    public function __construct()
    {
        $this->directoryHelper = new DirectoryHelper();
    }

    protected $repositories = [];

    public function autoRegister(string $directory)
    {
        $iterator = new \RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
        $allFiles = array_filter(iterator_to_array($iterator), function ($file) {
            return $file->isFile();
        });

        foreach ($allFiles as $file) {
            $class = $this->directoryHelper->getClassFullNameFromFile($file);

            try {
                $reflectionClass = new \ReflectionClass($class);
                if (!$reflectionClass->isAbstract() && is_a($class, Repository\BaseRepository::class, true)) {
                    require_once $file;
                    $this->addRepository(new $class());
                }
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    public function addRepository(Repository\BaseRepository $baseRepository)
    {
        $this->repositories[] = $baseRepository;
    }

    public function getRepository($class)
    {
        foreach ($this->repositories as $repositoryItem) {
            if ($repositoryItem->getClassModel() === $class) {
                return $repositoryItem;
            }
        }

        switch (true) {
            case is_a($class, Entity\ElementModel::class, true):
                $repository = new Repository\ElementRepository($class);
                break;
            case is_a($class, Entity\SectionModel::class, true):
                $repository = new Repository\SectionRepository($class);
                break;
            case is_a($class, Entity\UserModel::class, true):
                $repository = new Repository\UserRepository($class);
                break;
            case is_a($class, Entity\HighloadModel::class, true):
                $repository = new Repository\HighloadRepository($class);
                break;
        }

        return $repository;
    }
}