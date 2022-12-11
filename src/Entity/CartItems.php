<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CartItems
 *
 * @ORM\Table(name="cartitems", indexes={@ORM\Index(name="fk_CartItems_productId", columns={"productId"}), @ORM\Index(name="fk_CartItems_cartId", columns={"cartId"})})
 * @ORM\Entity(repositoryClass="App\Repository\CartItemsRepository")
 */
class CartItems
{
    /**
     * @var int
     *
     * @ORM\Column(name="cartItemsId", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $cartItemsId;

    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="integer", nullable=false)
     */
    private $amount;

    /**
     * @var Cart
     *
     * @ORM\ManyToOne(targetEntity="Cart")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cartId", referencedColumnName="cartId")
     * })
     */
    private $cartId;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="productId", referencedColumnName="productId")
     * })
     */
    private $productId;

    public function getCartItemsId(): ?int
    {
        return $this->cartItemsId;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCartId(): ?Cart
    {
        return $this->cartId;
    }

    public function setCartId(?Cart $cartId): self
    {
        $this->cartId = $cartId;

        return $this;
    }

    public function getProductId(): ?Product
    {
        return $this->productId;
    }

    public function setProductId(?Product $productId): self
    {
        $this->productId = $productId;

        return $this;
    }


}
