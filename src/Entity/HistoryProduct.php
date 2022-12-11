<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HistoryProduct
 *
 * @ORM\Table(name="historyproduct", indexes={@ORM\Index(name="fk_HistoryProduct_historyId", columns={"historyId"})})
 * @ORM\Entity(repositoryClass="App\Repository\HistoryProductRepository")
 */
class HistoryProduct
{
    /**
     * @var int
     *
     * @ORM\Column(name="historyProductId", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $historyProductId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", precision=10, scale=0, nullable=false)
     */
    private $price;

    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="integer", nullable=false)
     */
    private $amount;

    /**
     * @var History
     *
     * @ORM\ManyToOne(targetEntity="History")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="historyId", referencedColumnName="historyId")
     * })
     */
    private $historyId;

    public function getHistoryProductId(): ?int
    {
        return $this->historyProductId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
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

    public function getHistoryId(): ?History
    {
        return $this->historyId;
    }

    public function setHistoryId(?History $historyId): self
    {
        $this->historyId = $historyId;

        return $this;
    }


}
