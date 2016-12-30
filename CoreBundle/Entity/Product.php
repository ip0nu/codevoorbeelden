<?php

namespace CoreBundle\Entity;

/**
 * Product
 */
class Product
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $uniqueProductGuid;
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $applications;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->applications = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get uniqueProductGuid
     *
     * @return string
     */
    public function getUniqueProductGuid()
    {
        return $this->uniqueProductGuid;
    }


    /**
     * Set uniqueProductGuid
     *
     * @param string $uniqueProductGuid
     *
     * @return Product
     */
    public function setUniqueProductGuid($uniqueProductGuid)
    {
        $this->name = $uniqueProductGuid;

        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add application
     *
     * @param \CoreBundle\Entity\Application $application
     *
     * @return Product
     */
    public function addApplication(\CoreBundle\Entity\Application $application)
    {
        $this->applications[] = $application;

        return $this;
    }

    /**
     * Remove application
     *
     * @param \CoreBundle\Entity\Application $application
     */
    public function removeApplication(\CoreBundle\Entity\Application $application)
    {
        $this->applications->removeElement($application);
    }

    /**
     * Get applications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getApplications()
    {
        return $this->applications;
    }


    public function buildFromArray($array){

        foreach($array as $key => $value){
            $this->$key = $value;
        }
        return $this;
    }
}

