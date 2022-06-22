<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Wishes;
use App\Form\WishFormType;
use App\Repository\UserRepository;
use App\Repository\WishesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\WishActionService;

class WishController extends AbstractController
{

    /**
     * @Route("/", name="app_wish", methods={"GET"})
     * @param WishesRepository $wishesRepository
     * @return Response
     */
    public function wishList
    (
        WishesRepository $wishesRepository
    ): Response
    {
        return $this->render('wish/list.html.twig', [
            'wishes' => $wishesRepository->getWishList(),
        ]);
    }

    /**
     * @Route("/wishlist/{uid}", name="app_wish.user")
     * @param int $uid
     * @param WishesRepository $wishesRepository
     * @param UserRepository $userRepository
     * @return Response
     */
    public function wishListUser
    (
        int              $uid,
        WishesRepository $wishesRepository,
        UserRepository   $userRepository
    ): Response
    {
        $userSelected = $userRepository->findOneBy(['id' => $uid]);
//        if ($userSelected == null) {
//            return $this->redirectToRoute('app_wish');
//        }
        // Добавил страницу ошибки для примера

        $wishes = $wishesRepository->getWishListByUser($userSelected);

        return $this->render('wish/list.html.twig', [
            'wishes' => $wishes,
            'userSelected' => $userSelected,
        ]);
    }

    /**
     * @Route("/wish/create", name="app_wish.create")
     * @param Request $request
     * @param WishActionService $wishActionService
     * @return Response
     */
    public function addWishItem
    (
        Request           $request,
        WishActionService $wishActionService
    ): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if ($user == null) {
            return $this->redirectToRoute('app_wish');
        }

        $wish = new Wishes();
        $form = $this->createForm(WishFormType::class, $wish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $wishActionService->createWish($form, $user);
            return $this->redirectToRoute('app_wish.user', ['uid' => $user->getId()]);
        }

        return $this->renderForm('wish/form_wish.html.twig', [
            'form' => $form,
        ]);

    }

    /**
     * @Route("/wish/{id}", name="app_wish.show", methods={"GET"})
     * @param int $id
     * @param WishesRepository $wishesRepository
     * @return Response
     */
    public function showWishItem
    (
        int              $id,
        WishesRepository $wishesRepository
    ): Response
    {
        $wish = $wishesRepository->findOneBy(['id' => $id]);
        if ($wish == null) {
            return $this->redirectToRoute('app_wish');
        }

        return $this->render('wish/card.html.twig', [
            'wish' => $wish,
        ]);
    }

    /**
     * @Route("/wish/{id}/edit", name="app_wish.edit")
     * @param int $id
     * @param Request $request
     * @param WishesRepository $wishesRepository
     * @param WishActionService $wishService
     * @return RedirectResponse|Response
     */
    public function editWishItem
    (
        int               $id,
        Request           $request,
        WishesRepository  $wishesRepository,
        WishActionService $wishService
    )
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if ($user == null) {
            return $this->redirectToRoute('app_wish');
        }

        $editWish = $wishesRepository->findOneBy(['id' => $id]);
        if ($editWish == null) {
            return $this->redirectToRoute('app_wish');
        }

        $form = $this->createForm(WishFormType::class, $editWish);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $wishService->editWish($form);
            return $this->redirectToRoute('app_wish.user', ['uid' => $user->getId()]);
        }

        return $this->renderForm('wish/form_wish.html.twig', [
            'form' => $form,
            'image' => $editWish->getWishImageFilename(),
            'is_edit' => true,
        ]);
    }

    /**
     * @Route("/wish/{id}/remove", name="app_wish.delete")
     * @param $id
     * @param WishesRepository $wishesRepository
     * @param WishActionService $wishService
     * @return RedirectResponse
     */
    public function removeWishItem
    (
        $id,
        WishesRepository $wishesRepository,
        WishActionService $wishService

    ): RedirectResponse
    {
        $deleteWish = $wishesRepository->findOneBy(['id' => $id]);
        if ($deleteWish == null) {
            return $this->redirectToRoute("app_wish");
        }
        $wishService->deleteWish($deleteWish);
        return $this->redirectToRoute("app_wish");
    }
}
