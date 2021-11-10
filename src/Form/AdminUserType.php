<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminUserType extends AbstractType
{
    /** @var ContainerInterface */
    protected $container;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator,ContainerInterface $container)
    {
        $this->translator = $translator;
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $requestStack = $this->container->get('request_stack');
        $route = $requestStack->getMasterRequest() ? $requestStack->getMasterRequest()->attributes->get('_route') : null;

        $builder
            ->add('email', EmailType::class, [
                'required' => true,
                'constraints' => [
//                    new Unique([
//                        'message' => $this->translator->trans('validation.unique'),
//                    ]),
                    new NotBlank([
                        'message' => $this->translator->trans('validation.empty'),
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'required' => $route === 'admin_users_add',
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => $this->translator->trans('login.password'),
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('validation.empty'),
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => $this->translator->trans('validation.length',['limit'=>6]),
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => $this->translator->trans('TYPE_USER'),
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices' => [
                    $this->translator->trans('ROLE_DEKANAT') => 'ROLE_DEKANAT',
                    $this->translator->trans('ROLE_STUDENT') => 'ROLE_STUDENT',
                    $this->translator->trans('ROLE_ADMIN') => 'ROLE_ADMIN',
                ],
            ])
//            ->add('type', ChoiceType::class, [
//                'label' => $this->translator->trans('registration.type'),
//                'required' => true,
//                'multiple' => false,
//                'expanded' => true,
//                'choices' => [
//                    $this->translator->trans('TYPE_USER_1') => 1,
//                    $this->translator->trans('TYPE_USER_2') => 2,
//                    $this->translator->trans('TYPE_USER_3') => 3,
//                ],
//                'attr' => ['class' => 'radio_buttons'],
//                'choice_attr' => function($choice, $key, $value) {
//                    // adds a class like attending_yes, attending_no, etc
//                    return ['class' => 'attending_'.$value];
//                },
//            ])
            ->add('submit', SubmitType::class, [
                'label' => $this->translator->trans('admin.users.save'),
                'attr' => [
                    'class' => 'submit'
                ],
            ]);
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // transform the array to a string
                    return count($rolesArray) ? $rolesArray[0] : null;
                },
                function ($rolesString) {
                    // transform the string back to an array
                    return [$rolesString];
                }
            ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}