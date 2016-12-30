<?php

namespace CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use CoreBundle\Entity\UserRelation;
use CoreBundle\Form\UserRelationType;

/**
 * UserRelation controller.
 *
 * @Route("/userRelation")
 */
class UserRelationController extends Controller
{
    /**
     * Lists all UserRelation entities.
     *
     * @Route("/", name="userRelation_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $userRelations = $em->getRepository('CoreBundle:UserRelation')->findAll();

        return $this->render('userrelation/index.html.twig', array(
            'userRelations' => $userRelations,
        ));
    }

    /**
     * Creates a new UserRelation entity.
     *
     * @Route("/new", name="userRelation_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $userRelation = new UserRelation();
        $form = $this->createForm('CoreBundle\Form\UserRelationType', $userRelation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($userRelation);
            $em->flush();

            return $this->redirectToRoute('userRelation_show', array('id' => $userRelation->getId()));
        }

        return $this->render('userrelation/new.html.twig', array(
            'userRelation' => $userRelation,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a UserRelation entity.
     *
     * @Route("/{id}", name="userRelation_show")
     * @Method("GET")
     */
    public function showAction(UserRelation $userRelation)
    {
        $deleteForm = $this->createDeleteForm($userRelation);

        return $this->render('userrelation/show.html.twig', array(
            'userRelation' => $userRelation,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing UserRelation entity.
     *
     * @Route("/{id}/edit", name="userRelation_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, UserRelation $userRelation)
    {
        $deleteForm = $this->createDeleteForm($userRelation);
        $editForm = $this->createForm('CoreBundle\Form\UserRelationType', $userRelation);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($userRelation);
            $em->flush();

            return $this->redirectToRoute('userRelation_edit', array('id' => $userRelation->getId()));
        }

        return $this->render('userrelation/edit.html.twig', array(
            'userRelation' => $userRelation,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a UserRelation entity.
     *
     * @Route("/{id}", name="userRelation_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, UserRelation $userRelation)
    {
        $form = $this->createDeleteForm($userRelation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($userRelation);
            $em->flush();
        }

        return $this->redirectToRoute('userRelation_index');
    }

    /**
     * Creates a form to delete a UserRelation entity.
     *
     * @param UserRelation $userRelation The UserRelation entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(UserRelation $userRelation)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('userRelation_delete', array('id' => $userRelation->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
