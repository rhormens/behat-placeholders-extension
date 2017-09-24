<?php
/**
 * Created by PhpStorm.
 * User: bwowk
 * Date: 23/09/17
 * Time: 18:18
 */

namespace Ciandt\Behat\PlaceholdersExtension\Initializer;
use Ciandt\Behat\PlaceholdersExtension\Config\PlaceholdersRepository;


interface PlaceholderConsumerInterface
{
    public function setPlaceholdersRepository(PlaceholdersRepository $repository);
}