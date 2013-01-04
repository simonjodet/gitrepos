<?php

namespace Gitrepos\Controllers;

use \Silex\Application,
    \Symfony\Component\HttpFoundation\Request,
    \Symfony\Component\Validator\Constraints as Assert;


class KeyController
{
    public function addAction(Request $request, Application $app)
    {
        /**
         * @var $form \Symfony\Component\Form\Form
         */
        $form = $app['form.factory']->createBuilder('form')
            ->add(
            'title',
            'text',
            array(
                'constraints' => array(
                    new Assert\MinLength(1),
                    new Assert\MaxLength(128)
                )
            )
        )
            ->add(
            'key',
            'text',
            array(
                'constraints' => array(
                    new Assert\MinLength(1),
                    new Assert\MaxLength(512)
                )
            )
        )
            ->getForm();
        return $app['twig']->render('key/add.twig', array('form' => $form->createView()));
    }
}