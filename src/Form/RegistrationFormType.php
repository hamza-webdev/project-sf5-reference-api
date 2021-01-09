<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;


class RegistrationFormType extends AbstractType
{
    /**
     * [buildForm description]
     *
     * @param   FormBuilderInterface<array>  $builder  [$builder description]
     * @param   Array<string>              $options  [$options description]
     *
     * @return  void                            [return description]
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label'     => "Email : ",
                'required'  => true,
                'attr'      => [
                    'autofocus' => true
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label'         => "J'accepte les conditions d'utilisation.",
                'mapped'        => false,
                'constraints'   => [
                    new IsTrue([
                        'message' => "Vous devez accepter les conditions d'utilisation de ce site pour vous inscrire",
                    ]),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type'              => PasswordType::class, 
                'invalid_message'   => "Le mots de passe saisie ne correspond pas.",
                'required'          => true,
                'first_options'     => [
                    'label'      => "Mot de passe : ",
                    'label_attr' => [
                        'title'  => 'Pour de raison de sécurités votre mot de passe doit contenir entre 8 et 15 carecteres lettre et  alphanumerique maj et minuscule ou moin 1 chacun. '
                    ],
                    'attr'          => [
                        'pattern'       => '^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[-+!*$@%_])([-+!*$@%_\w]{8,15})$',
                        'title'         => "Pour de raison de securité votre mot de passe doit contenir lettre et  alphanumerique maj et minuscule ou moin 1 ",
                        'maxlenght'     => 255
                    ]
                ],
                'second_options' => [
                    'label'      => "Confirmer le mot de passe : ",
                    'label_attr' => [
                        'title'     => "Confirmez le mot de passe"                        
                    ],
                    'attr' => [
                        'pattern'   => '^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[-+!*$@%_])([-+!*$@%_\w]{8,15})$',
                        'title'     => "Pour de raison de securité votre mot de passe doit contenir lettre et  alphanumerique maj et minuscule ou moin 1 ",
                        'maxlenght' => 255
                    ]
                ]


            ])
            // ->add('plainPassword', PasswordType::class, [
            //     // instead of being set onto the object directly,
            //     // this is read and encoded in the controller
            //     'mapped' => false,
            //     'constraints' => [
            //         new NotBlank([
            //             'message' => 'Please enter a password',
            //         ]),
            //         new Length([
            //             'min' => 6,
            //             'minMessage' => 'Your password should be at least {{ limit }} characters',
            //             // max length allowed by Symfony for security reasons
            //             'max' => 4096,
            //         ]),
            //     ],
            // ])
        ;
    }

/**
 * [configureOptions description]
 *
 * @param   OptionsResolver  $resolver  [$resolver description]
 *
 * @return  void                        [return description]
 */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
