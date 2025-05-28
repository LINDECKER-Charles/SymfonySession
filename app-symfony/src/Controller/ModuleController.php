<?php

namespace App\Controller;

use App\Repository\ModuleRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ModuleController extends AbstractController
{
    #[Route('/module', name: 'app_module')]
    public function index(ModuleRepository $moduleRepository): Response
    {
        $modules = $moduleRepository->findAll();
        return $this->render('module/index.html.twig', [
            'modules' => $modules,
        ]);
    }

    #[Route('/module/{id}/', name: 'detail_module')]
    public function moduleDetail(ModuleRepository $moduleRepository, int $id): Response
    {
        $module = $moduleRepository->find($id);
        return $this->render('module/detail.html.twig', [
            'module' => $module,
        ]);
    }

    #[Route('/category', name: 'app_category')]
    public function indexbis(CategoryRepository $categoryRepository): Response
    {
        $categorys = $categoryRepository->findAll();
        return $this->render('category/index.html.twig', [
            'categorys' => $categorys,
        ]);
    } 
    
    #[Route('/category/{id}/', name: 'detail_category')]
    public function categoryDetail(CategoryRepository $categoryRepository, int $id): Response
    {
        $category = $categoryRepository->find($id);
        return $this->render('category/detail.html.twig', [
            'category' => $category,
        ]);
    } 

}
