<?php

namespace App\Controller;

use App\Entity\Sale;
use App\Entity\Shop;
use App\Form\ShopType;
use App\Form\NewSaleType;
use App\Repository\SaleRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ShopController extends AbstractController
{

    public function __construct(private ManagerRegistry $doctrine) {}
    
    /**
     * @Route("/shop", name="shop")
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param SaleRepository $saleRepository
     * @return Response
     */
    public function index(Request $request, TranslatorInterface $translator, SaleRepository $saleRepository): Response
    {
        $user = $this->getUser();
        if (!$user || !(in_array("ROLE_ADMIN", $user->getRoles()))) {
            return $this->redirectToRoute('app_login');
        }

        $em = $this->doctrine->getManager();

        // MAIN
        $form = $this->createForm(ShopType::class, $this->getShopData());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();


            $logoName = $form->get('logo')->getData();
            if ($logoName) {
                try {
                    $originalLogoName = pathinfo($logoName->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeLogoName = transliterator_transliterate('Any-latin; Latin-ASCII; [^A-Za-z0-9] remove; Lower()', $originalLogoName);
                    $newLogoName = $safeLogoName.'-'.uniqid().'.'.$logoName->guessExtension();
                    $logoName->move('productsImages', $newLogoName);
                    /** @var Shop[] $formData */
                    $formData['logo']->setAttrValue($newLogoName);
                } catch (\Exception $e) {
                    $this->addFlash('error', $translator->trans('flash.error'));
                }
            }

            foreach ($formData as $shopConfig) {
                $em->persist($shopConfig);
            }

            $em->flush();
            $this->addFlash('success', $translator->trans('flash.shop_data'));
        }

        // SALES
        $newSale = new Sale();
        $saleForm = $this->createForm(NewSaleType::class, $newSale);
        $saleForm->handleRequest($request);
        if ($saleForm->isSubmitted() && $saleForm->isValid()) {
            $backImgName = $saleForm->get('backImg')->getData();
            if ($backImgName) {
                try {
                    $originalImgName = pathinfo($backImgName->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeImgName = transliterator_transliterate('Any-latin; Latin-ASCII; [^A-Za-z0-9] remove; Lower()', $originalImgName);
                    $newImgName = $safeImgName.'-'.uniqid().'.'.$backImgName->guessExtension();
                    $backImgName->move('productsImages', $newImgName);
                    /** @var Sale $newSale */
                    $newSale->setBackImg($newImgName);
                } catch (\Exception $e) {
                    $this->addFlash('error', $translator->trans('flash.error'));
                }
            }

            $em->persist($newSale);
            $em->flush();
            $this->addFlash('success', 'flash.sale_added');
        }

        $deleteSaleId = $request->query->get('deleteSaleId');
        if (null !== $deleteSaleId) {
            $deleteSale = $saleRepository->find($deleteSaleId);
            if (null !== $deleteSale) {
                $em->remove($deleteSale);
                $em->flush();
            }
        }
        $sales = $saleRepository->findAll();

        return $this->render('shop/index.html.twig', [
            'controller_name' => 'ShopController',
            'shopForm' => $form->createView(),
            'saleForm' => $saleForm->createView(),
            'sales' => $sales,
        ]);
    }

    private function getShopData(): array
    {
        $shopRepository = $this->doctrine->getRepository(Shop::class);
        /** @var Shop[] | null $shopData */
        $shopData = [
            'name' => $shopRepository->findOneBy(['attrName' => 'name']),
            'logo' =>  $shopRepository->findOneBy(['attrName' => 'logo']),
            'accountNumber' => $shopRepository->findOneBy(['attrName' => 'accountNumber']),
            'adminLangView' => $shopRepository->findOneBy(['attrName' => 'adminLangView']),
        ];
        foreach ($shopData as $key => &$datum) {
            if (null === $datum) {
                $datum = new Shop();
                $datum->setAttrName($key);
            }
        }

        return $shopData;
    }

}
