<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Shop
 *
 * @ORM\Table(name="shop")
 * @ORM\Entity(repositoryClass="App\Repository\ShopRepository")
 */
class Shop
{
    /**
     * @var int
     *
     * @ORM\Column(name="shopId", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $shopId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $attrName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $attrValue;

    public function getShopId(): ?int
    {
        return $this->shopId;
    }

    public function getAttrName(): ?string
    {
        return $this->attrName;
    }

    public function setAttrName(string $attrName): self
    {
        $this->attrName = $attrName;

        return $this;
    }

    public function getAttrValue(): ?string
    {
        return $this->attrValue;
    }

    public function setAttrValue(?string $attrValue): self
    {
        $this->attrValue = $attrValue;

        return $this;
    }


}
