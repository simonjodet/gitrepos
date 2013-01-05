<?php

namespace Gitrepos\Controllers;

use \Silex\Application,
    \Symfony\Component\HttpFoundation\Request,
    \Symfony\Component\Form\FormError,
    \Symfony\Component\Validator\Constraints as Assert,
    \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserController
{
    public function loginAction(Request $request, Application $app)
    {
        return $app['twig']->render(
            'user/login.twig',
            array(
                'error' => $app['security.last_error']($request),
                'last_username' => $app['session']->get('_security.last_username'),
            )
        );
    }

    public function signinAction(Request $request, Application $app)
    {
        /**
         * @var $form \Symfony\Component\Form\Form
         */
        $form = $this->buildSigninForm($app);

        if ($app['request']->getMethod() == 'POST') {
            $form->bind($app['request']);
            $data = $form->getData();
            if ($data['password2'] != $data['password']) {
                $form->get('password2')->addError(new FormError('The two password fields don\'t match.'));
            }

            if ($form->isValid()) {
                $UserModel = $app['model.factory']->get('User');
                try {
                    $User = $UserModel->create(new \Gitrepos\Entities\User($data));
                    $app['security']->setToken(
                        new UsernamePasswordToken($User, $User->getPassword(), 'user_firewall', array('ROLE_USER'))
                    );
                    return $app->redirect('/');
                } catch (\Gitrepos\Exceptions\DuplicateUsername $e) {
                    $form->get('username')->addError(new FormError('This username is already used.'));
                }
                catch (\Gitrepos\Exceptions\DuplicateEmail $e) {
                    $form->get('email')->addError(new FormError('This email address is already used.'));
                }
            }
        }

        return $app['twig']->render('user/signin.twig', array('form' => $form->createView()));
    }

    public function buildSigninForm($app)
    {
        /**
         * @var $form \Symfony\Component\Form\Form
         */
        $form = $app['form.factory']->createBuilder('form')
            ->add(
            'username',
            'text',
            array(
                'constraints' => array(
                    new Assert\MinLength(3),
                    new Assert\MaxLength(64)
                )
            )
        )
            ->add(
            'email',
            'text',
            array(
                'constraints' => array(
                    new Assert\Email()
                )
            )
        )
            ->add(
            'password',
            'password',
            array(
                'always_empty' => false,
                'constraints' => array(
                    new Assert\MinLength(6),
                    new Assert\MaxLength(128)
                )
            )
        )
            ->add(
            'password2',
            'password',
            array(
                'label' => 'Retype password',
                'always_empty' => false
            )
        )
            ->getForm();

        return $form;
    }
}