<?php

namespace ManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use ManagementBundle\Controller\StoreController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use CoreBundle\Entity\Product as Product;
use CoreBundle\Entity\Application as Application;
use CoreBundle\Entity\Parameter as Parameter;

/**
 * Application controller.
 *
 * @Route("/Parameter")
 */


class ParameterController extends StoreController
{

    /**
     * @Route("/update")
     */
    public function UpdateParameterAction(request $request)
    {

        $this->em = $this->getDoctrine()->getManager();
        $Products = $this->em->getRepository('CoreBundle:Product')->findAll();

        return $this->render('ManagementBundle:Parameter:index.html.twig', array('Products' => $Products));
    }

    /**
     * @Route("/makeParametersByProductId", name="make_Parameters_selectbox_By_Product_Id")
     */
    public function makeParametersSelectboxByProductIdAction(request $request)
    {

        $ProductId = $request->query->get('id');
        $this->em = $this->getDoctrine()->getManager();
        $Product = $this->em->getRepository('CoreBundle:Product')->findOneBy(array('id' => $ProductId));
        $Applications =  $Product->getApplications();

        return $this->render('ManagementBundle:Ajax:select.html.twig', array('Entities' => $Applications, 'name'=> 'application' ));
    }

    /**
     * @Route("/makeConfigurationInputformByParameterId", name="make_Configuration_Inputform_By_Parameter_Id")
     */
    public function makeConfigurationInputformByParameterId(request $request)
    {
        $applicationId = $request->query->get('id');
        $this->em = $this->getDoctrine()->getManager();
        $Application = $this->em->getRepository('CoreBundle:Application')->findOneBy(array('id' => $applicationId));
        $ManagebleParameters= $Application->getManagebleParameters();

        return $this->render('ManagementBundle:Ajax:input.html.twig', array('fields' => $ManagebleParameters ));
    }
}
