<?php

namespace App\Controller;

use App\Entity\Sale;
use App\Entity\Shop;
use App\Entity\Product;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\SaleRepository;
use App\Repository\ShopRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{

    public function __construct(
        private ManagerRegistry $doctrine
    ) {}

    /**
     * @Route("/", name="index")
     * @Route("/index_controller/{categoryId}", name="index_controller")
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param ProductRepository $productRepository
     * @param CategoryRepository $categoryRepository
     * @param ShopRepository $shopRepository
     * @param SaleRepository $saleRepository
     * @param null $categoryId
     * @return Response
     */
    public function productData(
        Request $request,
        TranslatorInterface $translator,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        ShopRepository $shopRepository,
        SaleRepository $saleRepository,
        $categoryId = null
    ): Response
    {
        $entityManager = $this->doctrine->getManager();
        $user = $this->getUser();
        $isAdmin = (in_array("ROLE_ADMIN", $user ? $user->getRoles() : []));

        $sortType = $request->query->get('sortType');
        if (null !== $sortType) {
            $products = $productRepository->findBy([], Product::SORT_TYPE[$sortType]);
        } else {
            $products = $productRepository->findAll();
        }

        $searchProduct = $request->query->get('search');
        if (null !== $searchProduct && '' !== $searchProduct) {
            $products = array_filter($products, function ($product) use ($searchProduct) {
                $productName = strtolower($product->getName());
                $searchPhrase = strtolower($searchProduct);
                return strpos($productName, $searchPhrase) !== false;
            });
        } else if (null !== $categoryId && "0" !== $categoryId) {
            $products = array_filter($products,
                function ($product) use ($categoryId) {
                    $productCategoryId = null !== $product->getCategoryId() ? $product->getCategoryId()->getCategoryId() : null;
                    return (int) $categoryId === $productCategoryId;
                });
        }

        $toggleCategoryId = $request->query->get('toggleCategoryId');
        if (null !== $toggleCategoryId) {
            $toggleCategory = $categoryRepository->find($toggleCategoryId);
            $toggleCategory->setIsActive(!$toggleCategory->getIsActive());
            $entityManager->persist($toggleCategory);
            $entityManager->flush();
            $this->addFlash('success', $translator->trans('flash.category').' '.$toggleCategory->getName().' '.$translator->trans('flash.category_toggle'));
        }

        $categories = $categoryRepository->findAll();

        $newCategory = new Category();
        $categoryForm = $this->createForm(CategoryType::class, $newCategory);
        $categoryForm->handleRequest($request);
        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            if (empty(array_filter($categories, function ($category) use ($newCategory) { return $category->getName() === $newCategory->getName(); }))) {
                $newCategory->setIsActive(true);
                $entityManager->persist($newCategory);
                $entityManager->flush();
                $categories = $categoryRepository->findAll();
                $this->addFlash('success', $translator->trans('flash.add_cat').' '.$newCategory->getName());
            } else {
                $this->addFlash('error', $translator->trans('flash.cat_exist'));
            }
        }

        if ($isAdmin) {
            /** @var Shop | null $lang */
            $lang = $shopRepository->findOneBy(['attrName' => 'adminLangView']);
            $lang = null !== $lang ? $lang->getAttrValue() : null;
        } else {
            $categories = array_filter($categories, function ($category) { return $category->getIsActive(); });
            $products = $this->filterProductsAvailability($products);
            $lang = $request->getLocale();
        }

        $categories = $this->languageFilter($categories, $lang);
        $products = $this->languageFilter($products, $lang);

        if (empty($products)) {
            $this->addFlash('error', $translator->trans('flash.no_products'));
        }

        $sales = $saleRepository->findAll();
        $sales = $this->languageFilter($sales, $lang);
        $sales = array_filter($sales ?? [], function ($sale) {
            return $this->isInDate($sale);
        });
        $salesImages = [];
        /** @var Sale $sale */
        foreach ($sales as &$sale) {
            $product = $productRepository->find($sale->getProductId());
            if (null !== $product && null !== $product->getImg()) {
                $salesImages[$sale->getId()] = $product->getImg();
            }
        }

        return $this->render('index/index.html.twig', [
            'categoryForm' => $categoryForm->createView(),
            'isAdmin' => $isAdmin, 'products' => $products,
            'categories' => $categories,
            'categoryId' => (int) $categoryId,
            'sortType' => $sortType,
            'sales' => $sales,
            'salesImages' => $salesImages,
            'search' => $searchProduct ?? null,
        ]);
    }

    /**
     * @param Product[] $products
     * @return array
     */
    private function filterProductsAvailability(array $products): array
    {
        return array_filter(
            $products,
            function ($product) {
                return $product->getIsActive()
                    && 0 < $product->getAmount()
                    && $this->isInDate($product);
            });
    }

    /**
     * @param Product | Sale $item
     * @return bool
     */
    private function isInDate($item): bool
    {
        $startDate = $item->getStartDate()->format(Product::DATE_FORMAT);
        $now = date(Product::DATE_FORMAT);
        $endDate = $item->getEndDate();
        return $startDate <= $now && (null === $endDate || $now <= $endDate->format(Product::DATE_FORMAT));
    }

    /**
     * @param Category[] | Product[] | Sale[] $items
     * @param string|null $lang
     * @return array
     */
    private function languageFilter(array $items, ?string $lang): array
    {
        return array_filter(
            $items,
            function ($item) use ($lang) {
                return null === $lang
                    || null === $item->getLang()
                    || '' === $item->getLang()
                    || $lang === $item->getLang();
            });
    }
}
