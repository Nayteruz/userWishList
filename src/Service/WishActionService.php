<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Wishes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;

class WishActionService
{

    private EntityManagerInterface $entityManager;

    private string $imageDir;

    private ImageUpload $imageUpload;


    public function __construct(EntityManagerInterface $entityManager, string $imageDir, ImageUpload $imageUpload)
    {
        $this->entityManager = $entityManager;
        $this->imageDir = $imageDir;
        $this->imageUpload = $imageUpload;
    }

    public function createWish($form, User $user)
    {
        $createWish = $form->getData();
        $createWish->setUser($user);
        $image = $form->get('image')->getData();
        if ($image) {
            $fileName = $this->imageUpload->upload($image);
            $createWish->setWishImageFilename($fileName);
        }
        $this->entityManager->persist($createWish);
        $this->entityManager->flush();

        return true;
    }

    public function editWish($form)
    {
        $editWish = $form->getData();
        $editWish->setUpdatedAtValue();
        $image = $form->get('image')->getData();
        if ($image) {

            if ($editWish->getWishImageFilename()) {
                $oldImageFilename = $editWish->getWishImageFilename();
                $filesystem = new Filesystem();
                $filesystem->remove($this->getImageDir() . '/' . $oldImageFilename);
            }

            $fileName = $this->imageUpload->upload($image);
            $editWish->setWishImageFilename($fileName);
        }

        $this->entityManager->persist($editWish);
        $this->entityManager->flush();
        return true;
    }

    public function deleteWish(Wishes $wish): bool
    {
        if ($wish->getWishImageFilename() != null) {
            $imageFilename = $wish->getWishImageFilename();
            $filesystem = new Filesystem();
            $filesystem->remove($this->getImageDir() . '/' . $imageFilename);
        }
        $this->entityManager->remove($wish);
        $this->entityManager->flush();

        return true;
    }

    public function getImageDir()
    {
        return $this->imageDir;
    }
}