<?php

namespace CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use CoreBundle\Entity\Configuration;
use CoreBundle\Form\ConfigurationType;

/**
 * Configuration controller.
 *
 * @Route("/configuration")
 */
class ConfigurationController extends Controller
{
    /**
     * Lists all Configuration entities.
     *
     * @Route("/", name="configuration_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $configurations = $em->getRepository('CoreBundle:Configuration')->findAll();

        return $this->render('configuration/index.html.twig', array(
            'configurations' => $configurations,
        ));
    }

    /**
     * Creates a new Configuration entity.
     *
     * @Route("/new", name="configuration_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $configuration = new Configuration();
        $form = $this->createForm('CoreBundle\Form\ConfigurationType', $configuration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($configuration);
            $em->flush();

            return $this->redirectToRoute('configuration_show', array('id' => $configuration->getId()));
        }

        return $this->render('configuration/new.html.twig', array(
            'configuration' => $configuration,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Configuration entity.
     *
     * @Route("/{id}", name="configuration_show")
     * @Method("GET")
     */
    public function showAction(Configuration $configuration)
    {
        $deleteForm = $this->createDeleteForm($configuration);

        return $this->render('configuration/show.html.twig', array(
            'configuration' => $configuration,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Configuration entity.
     *
     * @Route("/{id}/edit", name="configuration_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Configuration $configuration)
    {
        $deleteForm = $this->createDeleteForm($configuration);
        $editForm = $this->createForm('CoreBundle\Form\ConfigurationType', $configuration);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($configuration);
            $em->flush();

            return $this->redirectToRoute('configuration_edit', array('id' => $configuration->getId()));
        }

        return $this->render('configuration/edit.html.twig', array(
            'configuration' => $configuration,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Configuration entity.
     *
     * @Route("/{id}", name="configuration_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Configuration $configuration)
    {
        $form = $this->createDeleteForm($configuration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($configuration);
            $em->flush();
        }

        return $this->redirectToRoute('configuration_index');
    }

    /**
     * Creates a form to delete a Configuration entity.
     *
     * @param Configuration $configuration The Configuration entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Configuration $configuration)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('configuration_delete', array('id' => $configuration->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
