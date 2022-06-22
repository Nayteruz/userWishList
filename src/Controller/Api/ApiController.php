<?php

namespace App\Controller\Api;


use App\Entity\Wishes;
use App\Repository\UserRepository;
use App\Repository\WishesRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use \JMS\Serializer\SerializerBuilder;

class ApiController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/wishlist", name="api.wishlist")
     * @param WishesRepository $wishesRepository
     * @return Response
     */
    public function apiGetWishlist(WishesRepository $wishesRepository): Response
    {
        return $this->serializeData($wishesRepository->getWishList());
    }


    /**
     * @Route("/api/wishlist/{uid}", name="api.wishlistuser")
     * @param int $uid
     * @param WishesRepository $wishesRepository
     * @param UserRepository $userRepository
     * @return Response
     */
    public function apiGetWishlistUser
    (
        int              $uid,
        WishesRepository $wishesRepository,
        UserRepository   $userRepository
    ): Response
    {
        $userSelected = $userRepository->findOneBy(['id' => $uid]);
        if ($userSelected == null) {
            throw new \InvalidArgumentException('User not found');
        }

        return $this->serializeData($wishesRepository->getWishListByUser($userSelected));
    }

    /**
     * @Route("/api/wish/create", name="api.wishcreate")
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Response
     * @throws \Exception
     */
    public function apiAddWishItem(Request $request, UserRepository $userRepository): Response
    {

        $user = $userRepository->findOneBy(['id' => $request->get('user_id')]);
        if ($user == null) {
            throw new \Exception('User not found');
        }

        if (!$request->get('title')) {
            throw new \Exception('Title is required');
        }
        $wish = new Wishes();
        $wish->setTitle($request->get('title'));
        $wish->setDescription($request->get('description'));
        $wish->setUser($user);

        $this->entityManager->persist($wish);
        $this->entityManager->flush();

        return $this->serializeData($wish);
    }

    /**
     * @Route("/api/wish/{id}", name="api.wish")
     * @param int $id
     * @param WishesRepository $wishesRepository
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function showWishItem(int $id, WishesRepository $wishesRepository)
    {
        $wish = $wishesRepository->findOneBy(['id' => $id]);
        if ($wish == null) {
            throw new \Exception('Wish not found');
        }

        return $this->serializeData($wish);
    }


    /**
     * @Route("/api/wish/{id}/edit", name="api.wishedit", methods={"POST"})
     * @param int $id
     * @param Request $request
     * @param WishesRepository $wishesRepository
     * @return Response
     * @throws \Exception
     */
    public function apiEditWishItem(int $id, Request $request, WishesRepository $wishesRepository): Response
    {

        $wish = $wishesRepository->findOneBy(['id' => $id]);
        if ($wish == null) {
            throw new \Exception('Wish not found');
        }
        if (!$request->get('title')) {
            throw new \Exception('Title is required');
        }
        $wish->setTitle($request->get('title'));
        if ($request->get('description')) {
            $wish->setDescription($request->get('description'));
        }

        $this->entityManager->persist($wish);
        $this->entityManager->flush();

        return $this->serializeData($wish);
    }

    /**
     * @Route("/api/wish/{id}/remove", name="api.wishedit", methods={"POST"})
     * @param int $id
     * @param WishesRepository $wishesRepository
     * @return Response
     * @throws \Exception
     */
    public function apiDeleteWishItem(int $id, WishesRepository $wishesRepository): Response
    {

        $wish = $wishesRepository->findOneBy(['id' => $id]);
        if ($wish == null) {
            throw new \Exception('Wish not found');
        }
        $this->entityManager->remove($wish);
        $this->entityManager->flush();

        return $this->serializeData($wish);
    }


    private function serializeData($data): Response
    {
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($data, 'json');

        $response = new Response($jsonContent, 200);
        $response->setCharset('UTF-8');
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

}