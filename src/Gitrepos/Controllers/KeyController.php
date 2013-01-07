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
        $form = $this->buildAddForm($app);

        if ($app['request']->getMethod() == 'POST') {
            $form->bind($app['request']);
            if ($form->isValid()) {
                //Add key
                $data = $form->getData();
                $KeyModel = $app['model.factory']->get('Key');

                $Key = new \Gitrepos\Entities\Key($data);

                $KeyModel->add($Key);
            }
        }

        return $app['twig']->render('key/add.twig', array('form' => $form->createView()));
    }

    public function buildAddForm(Application $app)
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
        return $form;
    }
}