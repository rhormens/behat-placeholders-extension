<?php

namespace Ciandt\Behat\PlaceholdersExtension\Initializer;

use Ciandt\Behat\PlaceholdersExtension\Config\PlaceholdersRepository;

interface PlaceholdersAwareInterface
{
    public function setPlaceholdersRepository(PlaceholdersRepository $repository);
}