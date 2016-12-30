<?php

namespace CoreBundle\Entity;

/**
 * Application
 */
class Application
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
     * @var
     */
    private $uniqueApplicationGuid;

    /**
     * @var \CoreBundle\Entity\User
     */
    private $parameters;

    /**
     * @var \CoreBundle\Entity\User
     */
    private $products;

    /**
     * @var \CoreBundle\Entity\User
     */
    private $configurations;


    /**
     * @var \CoreBundle\Entity\User
     */
    private $users;
    /**
     * Constructor
     */

    private $ManagebleParameterFields =
        array ('manageble'=>
            array('icon'=> '',
                'applicationLogo' => '',
                'applicationName' => ''));


    public function __construct()
    {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
        $this->configurations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->parameters = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Application
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
     * Set uniqueApplicationGuid
     *
     * @param string uniqueApplicationGuid
     *
     * @return Application
     */
    public function setUniqueApplicationGuid($uniqueApplicationGuid)
    {
        $this->uniqueApplicationGuid = $uniqueApplicationGuid;

        return $this;
    }

    /**
     * Get uniqueApplicationGuid
     *
     * @return string
     */
    public function getUniqueApplicationGuid()
    {
        return $this->uniqueApplicationGuid;
    }


    /**
     * Add parameter
     *
     * @param \CoreBundle\Entity\Parameter $parameter
     *
     * @return parameter
     */
    public function addParameter(\CoreBundle\Entity\Parameter $parameter)
    {
        $this->parameters[] = $parameter;

        return $this;
    }

    /**
     * Remove parameter
     *
     * @param \CoreBundle\Entity\Parameter $parameter
     */
    public function removeParameter(\CoreBundle\Entity\Parameter $parameter)
    {
        $this->parameters->removeElement($parameter);
    }

    /**
     * Get parameters
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Add configuration
     *
     * @param \CoreBundle\Entity\Configuration $configuration
     *
     * @return configuration
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
     * Get configurations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add User
     *
     * @param \CoreBundle\Entity\User $User
     *
     * @return User
     */
    public function addUser(\CoreBundle\Entity\User $User)
    {
        $this->users[] = $User;

        return $this;
    }
    /**
     * Remove User
     *
     * @param \CoreBundle\Entity\User $user
     */
    public function removeUser(\CoreBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Add Product
     *
     * @param \CoreBundle\Entity\User $User
     *
     * @return User
     */
    public function addProduct(\CoreBundle\Entity\Product $Product)
    {
        $this->products[] = $Product;

        return $this;
    }

    public function buildFromArray($array,$Product = Null ){

        foreach($array as $key => $value){
            $this->$key = $value;
        }
        if($Product !=  NULL){
            $this->addProduct($Product);
        }

        return $this;
    }

    public function getManagebleParameters()
    {

        foreach ($this->ManagebleParameterFields as $field => $subField) {
            $found = false;
            foreach ($this->getParameters() as $key => $value) {
                if ($field == $value->getName()) {
                    if (is_string($value->getValue())) {
                        if ($results = unserialize($value->getValue())) {
                            $id = array('id' => $value->getId());
                            $ManagebleParameters[$field] =array_merge($id, $results);
                            $found = true;
                        }
                        if (empty($value->getValue())){
                            $id = array('id' => $value->getId());
                            $ManagebleParameters[$field] =array_merge($id, $subField);
                        }
                    }
                }
            }
        }
        return $ManagebleParameters;
    }
}

