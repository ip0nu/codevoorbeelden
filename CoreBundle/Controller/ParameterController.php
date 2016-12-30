<?php

namespace CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use CoreBundle\Entity\Parameter;
use CoreBundle\Form\ParameterType;

/**
 * Parameter controller.
 *
 * @Route("/parameter")
 */
class ParameterController extends Controller
{
    /**
     * Lists all Parameter entities.
     *
     * @Route("/", name="parameter_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $parameters = $em->getRepository('CoreBundle:Parameter')->findAll();

        return $this->render('parameter/index.html.twig', array(
            'parameters' => $parameters,
        ));
    }

    /**
     * Creates a new Parameter entity.
     *
     * @Route("/new", name="parameter_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $parameter = new Parameter();
        $form = $this->createForm('CoreBundle\Form\ParameterType', $parameter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($parameter);
            $em->flush();

            return $this->redirectToRoute('parameter_show', array('id' => $parameter->getId()));
        }

        return $this->render('parameter/new.html.twig', array(
            'parameter' => $parameter,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Parameter entity.
     *
     * @Route("/{id}", name="parameter_show")
     * @Method("GET")
     */
    public function showAction(Parameter $parameter)
    {
        $deleteForm = $this->createDeleteForm($parameter);

        return $this->render('parameter/show.html.twig', array(
            'parameter' => $parameter,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Parameter entity.
     *
     * @Route("/{id}/edit", name="parameter_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Parameter $parameter)
    {
        $deleteForm = $this->createDeleteForm($parameter);
        $editForm = $this->createForm('CoreBundle\Form\ParameterType', $parameter);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($parameter);
            $em->flush();

            return $this->redirectToRoute('parameter_edit', array('id' => $parameter->getId()));
        }

        return $this->render('parameter/edit.html.twig', array(
            'parameter' => $parameter,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Parameter entity.
     *
     * @Route("/{id}", name="parameter_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Parameter $parameter)
    {
        $form = $this->createDeleteForm($parameter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($parameter);
            $em->flush();
        }

        return $this->redirectToRoute('parameter_index');
    }

    /**
     * Creates a form to delete a Parameter entity.
     *
     * @param Parameter $parameter The Parameter entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Parameter $parameter)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('parameter_delete', array('id' => $parameter->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
