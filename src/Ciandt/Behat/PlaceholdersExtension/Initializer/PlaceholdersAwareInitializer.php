<?php

namespace Ciandt\Behat\PlaceholdersExtension\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use Ciandt\Behat\PlaceholdersExtension\Config\PlaceholdersRepository;

class PlaceholdersAwareInitializer implements ContextInitializer
{
    private $repository;

    /**
     * Initializes initializer with placeholders repository.
     * @param PlaceholdersRepository $repository
     */
    public function __construct(PlaceholdersRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Injects the placeholders repository on context.
     *
     * @param Context $context
     */
    public function initializeContext(Context $context)
    {
        if ($context instanceof PlaceholdersAwareInterface) {
            $context->setPlaceholdersRepository($this->repository);
        }
    }
}
