<?php

namespace App\Form;

use App\Entity\Wishes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;

class WishFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Название желания',
                'required' => true,
                'attr' => ['placeholder' => 'Название желания', 'class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Описание желания',
                'attr' => ['placeholder' => 'Описание желания', 'class' => 'form-control'],
            ])
            ->add('image', FileType::class, [
                'label' => 'Картинка желания',
                'attr' => ['class' => 'form-control'],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxWidth' => 1980,
                        'maxHeight' => 1080,
                        'maxHeightMessage' => 'Высота картинки ({{ height }}px) больше максимальной {{ max_height }}px',
                        'maxWidthMessage' => 'Ширина картинки ({{ width }}px) больше максимальной {{ max_width }}px',
                    ]),
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'image/jpeg', 'image/jpg', 'image/png'
                        ],
                        'mimeTypesMessage' => 'Формат файла не соответствует изображению',
                        'maxSizeMessage' => 'Файл больше разрешенного размера {{ limit }} {{ suffix }}'
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Wishes::class,
        ]);
    }
}
