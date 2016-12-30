<?php

namespace CoreBundle\Entity;

/**
 * Parameter
 */
class Parameter
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
     * @var text
     */
    private $value;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $configurations;

    /**
     * @var \CoreBundle\Entity\Application
     */
    private $application;

    private $serialedFields = array('icon','applicationLogo','applicationName');

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->configurations = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Parameter
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
     * Set value
     *
     * @param string $value
     *
     * @return Parameter
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
     * Add configuration
     *
     * @param \CoreBundle\Entity\Configuration $configuration
     *
     * @return Parameter
     */
    public function addConfiguration(\CoreBundle\Entity\Configuration $configuration)
    {
        $this->configurations[] = $configuration;

        return $this;
    }

    /**
     * Remove configuration
     *
     * @param \CoreBundle\Entity\Configuration $configuration
     */
    public function removeConfiguration(\CoreBundle\Entity\Configuration $configuration)
    {
        $this->configurations->removeElement($configuration);
    }

    /**
     * Get configurations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConfigurations()
    {
        return $this->configurations;
    }

    /**
     * Set application
     *
     * @param \CoreBundle\Entity\Application $application
     *
     * @return Parameter
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

    /**
     * Get application
     *
     * @return \CoreBundle\Entity\Application
     */
    public function createParameter($name, $value,  $Application = NULL , $Configuration = NULL)
    {
        $this->setName($name);
        if(is_array($value)){
            $this->setValue(serialize($value));
        }else{
            $this->setValue($value);
        }
        if($Configuration != NULL){
            $this->addConfiguration($Configuration);
        }
        if($Application != NULL){
            $this->getApplication($Application);
        }

        return $this;
    }

    public function buildFromArray($array)
    {
        foreach($this->serialedFields as $valueField){
            foreach ($array as $key => $value) {
                if ($key == $valueField) {
                    $temp[$key] = $value;
                }
            }
        }
        $this->setValue(serialize($temp));
        return $this;
    }



}

