<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Category;
use App\Form\ModuleForm;
use App\Form\CategoryForm;
use App\Repository\ModuleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/module/{id?0}/form', name: 'form_module')]
    public function formModule(Module $module = null, Request $request, EntityManagerInterface $em, ModuleRepository $repo): Response
    {
        if (!($this->getUser() && (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || in_array('FORMATEUR', $this->getUser()->getRoles())))) {
            return $this->redirectToRoute('app_home');
        }

        $isEdit = $module !== null;

        if (!$module) {
            $module = new Module();
        }

        $form = $this->createForm(ModuleForm::class, $module);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$isEdit) {
                $existing = $repo->findOneBy(['mudleName' => $module->getMudleName()]);
                if ($existing) {
                    $existing->setMudleName($module->getMudleName());
                    $existing->setModuleCategory($module->getModuleCategory());
                    $em->flush();
                    return $this->redirectToRoute('detail_module', ['id' => $existing->getId()]);
                }
                $em->persist($module);
            }

            $em->flush();
            return $this->redirectToRoute('detail_module', ['id' => $module->getId()]);
        }

        return $this->render('module/add.html.twig', [
            'form' => $form->createView(),
            'editMode' => $isEdit,
        ]);
    }


    #[Route('/module/edit/{id}', name: 'edit_module')]
    public function edit(Module $module, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ModuleForm::class, $module);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('detail_module', ['id' => $module->getId()]);
        }

        return $this->render('module/add.html.twig', [
            'form' => $form,
            'editMode' => true,
        ]);
    }

    #[Route('/module/assign-category/{id}', name: 'assign_category_to_module', methods: ['POST'])]
    public function assignCategoryToModule(
        Module $module,
        Request $request,
        EntityManagerInterface $em,
        CategoryRepository $categoryRepository
    ): Response {
        $categoryId = $request->request->get('categoryId');

        $category = $categoryId ? $categoryRepository->find($categoryId) : null;
        $module->setModuleCategory($category);

        $em->persist($module);
        $em->flush();

        return $this->redirectToRoute('detail_module', ['id' => $module->getId()]);
    }

    #[Route('/module/{id}/', name: 'detail_module')]
    public function moduleDetail(ModuleRepository $moduleRepository, int $id, CategoryRepository $categoryRepository): Response
    {
        $module = $moduleRepository->find($id);
        return $this->render('module/detail.html.twig', [
            'module' => $module,
            'categories' => $categoryRepository->findAll(),
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

    #[Route('/creationCategory', name: 'creation_category')]
    public function creationCategory(Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository): Response
    {
        if (!($this->getUser() && (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || in_array('FORMATEUR', $this->getUser()->getRoles())))) {
            return $this->redirectToRoute('app_home');
        }

        $category = new Category();
        $form = $this->createForm(CategoryForm::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existing = $categoryRepository->findOneBy(['categoryName' => $category->getCategoryName()]);

            if ($existing) {
                $form->addError(new \Symfony\Component\Form\FormError('Catégorie déjà existante'));
            } else {
                $entityManager->persist($category);
                $entityManager->flush();

                return $this->redirectToRoute('app_category');
            }
        }

        return $this->render('category/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    
    #[Route('/category/{id}/', name: 'detail_category')]
    public function categoryDetail(CategoryRepository $categoryRepository, ModuleRepository $moduleRepository, int $id): Response
    {
        $category = $categoryRepository->find($id);
        return $this->render('category/detail.html.twig', [
            'category' => $category,
            'notModules' => $moduleRepository->findByNotInCategory($id),
        ]);
    }

/*     #[Route('/session/add_module_from_cat/{idC}/{idM}', name: 'add_module_from_category')]
    public function addModuleFromCat(int $idC, int $idM, EntityManagerInterface $em, ModuleRepository $moduleRepository, CategoryRepository $categoryRepository): Response
    {
        if (!($this->getUser() && (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || in_array('FORMATEUR', $this->getUser()->getRoles())))) {
            return $this->redirectToRoute('app_home');
        }
        $category = $categoryRepository->find($idC);
        $module = $moduleRepository->find($idM);

        $category->addModule($module);
        $em->persist($category);
        $em->flush();

        return $this->redirectToRoute('detail_category', ['id' => $idC]);
    }

    #[Route('/session/del_module_from_cat/{idC}/{idM}', name: 'del_module_from_category')]
    public function delModuleFromCat(int $idC, int $idM, EntityManagerInterface $em, ModuleRepository $moduleRepository, CategoryRepository $categoryRepository): Response
    {
        if (!($this->getUser() && (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || in_array('FORMATEUR', $this->getUser()->getRoles())))) {
            return $this->redirectToRoute('app_home');
        }
        $category = $categoryRepository->find($idC);
        $module = $moduleRepository->find($idM);

        $category->removeModule($module);
        $em->persist($category);
        $em->flush();

        return $this->redirectToRoute('detail_category', ['id' => $idC]);
    } */

}
