<?php

namespace App\Service;

use App\Repository\ShopRepository;

class ShopDataGenerator
{

    /** @var ShopRepository $shopRepository*/
    private $shopRepository;

    function __construct(ShopRepository $shopRepository)
    {
        $this->shopRepository = $shopRepository;
    }

    public function getName(): ?string
    {
        $name = $this->shopRepository->findOneBy(['attrName' => 'name']);
        return null !== $name ? $name->getAttrValue() : null;
    }

    public function getLogo(): ?string
    {
        $logo = $this->shopRepository->findOneBy(['attrName' => 'logo']);
        return null !== $logo ? 'productsImages/' . $logo->getAttrValue() : null;
    }
}