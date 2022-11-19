<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product", indexes={@ORM\Index(name="fk_Product_categoryId", columns={"categoryId"})})
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product {
    public const SORT_TYPE = [
        'priceAsc' => ['price' => 'ASC'],
        'priceDesc' => ['price' => 'DESC'],
        'timeAsc' => ['startDate' => 'ASC'],
        'timeDesc' => ['startDate' => 'DESC'],
    ];

    public const PRICE_UNIT = [
        'zł/szt' => 'zł/szt',
        'zł/kg' => 'zł/kg',
        '€/szt' => '€/szt',
        '€/kg' => '€/kg',
    ];

    public const DATE_FORMAT = "Y-m-d";

    /**
     * @var int
     *
     * @ORM\Column(name="productId", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $productId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="date", nullable=false)
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="date", nullable=true)
     */
    private $endDate;

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
     * @var string
     *
     * @ORM\Column(name="img", type="string", length=255, nullable=true)
     */
    private $img;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="amountOfBought", type="integer", nullable=true)
     */
    private $amountOfBought;

    /**
     * @var bool
     *
     * @ORM\Column(name="isActive", type="boolean", nullable=false, options={"default": true})
     */
    private $isActive;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categoryId", referencedColumnName="categoryId", nullable=true)
     * })
     */
    private $categoryId;

    /**
     * @ORM\Column(name="producer", type="string", length=255, nullable=true)
     */
    private $producer;

    /**
     * @ORM\Column(name="priceUnit", type="string", length=255, nullable=true)
     */
    private $priceUnit;

    /**
     * @ORM\Column(name="lang", type="string", length=255, nullable=true)
     */
    private $lang;

    /**
     * @ORM\OneToMany(targetEntity=Sale::class, mappedBy="productId")
     */
    private $sales;

    public function __construct() {
        $this->sales = new ArrayCollection();
    }

    public function getProductId(): ?int {
        return $this->productId;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self {
        $this->endDate = $endDate;

        return $this;
    }

    public function getPrice(): ?float {
        return $this->price;
    }

    public function setPrice(float $price): self {
        $this->price = $price;

        return $this;
    }

    public function getAmount(): ?int {
        return $this->amount;
    }

    public function setAmount(int $amount): self {
        $this->amount = $amount;

        return $this;
    }

    public function getImg(): ?string {
        return $this->img;
    }

    public function setImg(string $img): self {
        $this->img = $img;

        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(string $description): self {
        $this->description = $description;

        return $this;
    }

    public function getAmountOfBought(): ?int {
        return $this->amountOfBought;
    }

    public function setAmountOfBought(int $amountOfBought): self {
        $this->amountOfBought = $amountOfBought;

        return $this;
    }

    public function getIsActive(): ?bool {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self {
        $this->isActive = $isActive;

        return $this;
    }

    public function getCategoryId(): ?Category {
        return $this->categoryId;
    }

    public function setCategoryId(?Category $categoryId): self {
        $this->categoryId = $categoryId;

        return $this;
    }

    public function getProducer(): ?string {
        return $this->producer;
    }

    public function setProducer(?string $producer): self {
        $this->producer = $producer;

        return $this;
    }

    public function getPriceUnit(): ?string {
        return $this->priceUnit;
    }

    public function setPriceUnit(?string $priceUnit): self {
        $this->priceUnit = $priceUnit;

        return $this;
    }

    public function getLang(): ?string {
        return $this->lang;
    }

    public function setLang(?string $lang): self {
        $this->lang = $lang;

        return $this;
    }

    /**
     * @return Collection|Sale[]
     */
    public function getSales(): Collection {
        return $this->sales;
    }

    public function addSale(Sale $sale): self {
        if (!$this->sales->contains($sale)) {
            $this->sales[] = $sale;
            $sale->setProductId($this);
        }

        return $this;
    }

    public function removeSale(Sale $sale): self {
        if ($this->sales->removeElement($sale)) {
            // set the owning side to null (unless already changed)
            if ($sale->getProductId() === $this) {
                // TODO Line below will throw error
                // $sale->setProductId(null);
            }
        }

        return $this;
    }
}
