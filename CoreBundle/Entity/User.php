<?php

namespace CoreBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class User
 * @package CoreBundle\Entity
 */
class User extends BaseUser
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    private $uniqueProfileGuid;


    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $parents;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $childs;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $configurations;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $applications;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->parents = new \Doctrine\Common\Collections\ArrayCollection();
        $this->childs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->configurations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->applications = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Add parent
     *
     * @param \CoreBundle\Entity\UserRelation $parent
     *
     * @return User
     */
    public function addParent(\CoreBundle\Entity\UserRelation $parent)
    {
        $this->parents[] = $parent;

        return $this;
    }

    /**
     * Remove parent
     *
     * @param \CoreBundle\Entity\UserRelation $parent
     */
    public function removeParent(\CoreBundle\Entity\UserRelation $parent)
    {
        $this->parents->removeElement($parent);
    }

    /**
     * Get parents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParents()
    {
        return $this->parents;
    }

    /**
     * Add child
     *
     * @param \CoreBundle\Entity\UserRelation $child
     *
     * @return User
     */
    public function addChild(\CoreBundle\Entity\UserRelation $child)
    {
        $this->childs[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \CoreBundle\Entity\UserRelation $child
     */
    public function removeChild(\CoreBundle\Entity\UserRelation $child)
    {
        $this->childs->removeElement($child);
    }

    /**
     * Get childs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChilds()
    {
        return $this->childs;
    }

    /**
     * Add configuration
     *
     * @param \CoreBundle\Entity\Configuration $configuration
     *
     * @return User
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
     * Add application
     *
     * @param \CoreBundle\Entity\Application $application
     *
     * @return User
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

    public function getId()
    {
        return $this->id;
    }

    public function getUniqueProfileGuid()
    {
        return $this->uniqueProfileGuid;
    }

    public function setUniqueProfileGuid($uniqueProfileGuid)
    {
        $this->uniqueProfileGuid = $uniqueProfileGuid;

        return $this;
    }

    public function buildFromArray($array)
    {
        foreach ($array as $key => $value) {
            if ($key == 'password') {
                $this->setPlainPassword($value);
            } elseif($key != 'id' ) {
                if($key == 'name' || $key == 'username'){
                    $this->username = $value;
                    $this->usernameCanonical = $value;
                }else{
                    $this->$key = $value;
                }
            }
        }
        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->username;
    }

}

