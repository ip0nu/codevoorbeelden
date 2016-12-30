<?php

namespace CoreBundle\Entity;

/**
 * Configuration
 */
class Configuration
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $value;

    /**
     * @var \CoreBundle\Entity\User
     */
    private $user;

    /**
     * @var \CoreBundle\Entity\Parameter
     */
    private $parameter;

    /**
     * @var \CoreBundle\Entity\Application
     */
    private $application;


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
     * Set value
     *
     * @param string $value
     *
     * @return Configuration
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set user
     *
     * @param \CoreBundle\Entity\User $user
     *
     * @return Configuration
     */
    public function setUser(\CoreBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \CoreBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set parameter
     *
     * @param \CoreBundle\Entity\Parameter $parameter
     *
     * @return Configuration
     */
    public function setParameter(\CoreBundle\Entity\Parameter $parameter = null)
    {
        $this->parameter = $parameter;

        return $this;
    }

    /**
     * Get parameter
     *
     * @return \CoreBundle\Entity\Parameter
     */
    public function getParameter()
    {
        return $this->parameter;
    }

    /**
     * Set application
     *
     * @param \CoreBundle\Entity\Application $application
     *
     * @return Configuration
     */
    public function setApplication(\CoreBundle\Entity\Application $application = null)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get application
     *
     * @return \CoreBundle\Entity\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    public function createConfiguration($value,$Application = NULL,$User = NULL, $Parameter = NULL )
    {
        $this->setValue($value);
        if($Application != NULL){
            $this->setApplication($Application);
        }
        if($User != NULL){
            $this->setUser($User);
        }

    }
}

